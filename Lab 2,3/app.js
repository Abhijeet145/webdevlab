const express = require("express");
const mongoose = require("mongoose");
const session = require("express-session");
const MongoStore = require("connect-mongo");
const bcrypt = require("bcryptjs");
const dotenv = require("dotenv");
const User = require("./models/User.js");
const Policy = require("./models/Policy.js");
dotenv.config();

const app = express();

// Session Configuration
app.use(
  session({
    secret: process.env.SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
    store: MongoStore.create({
      mongoUrl: process.env.MONGO,
      ttl: 60 * 60,
    }),
    cookie: {
      maxAge: 60 * 60 * 1000,
    },
  })
);

// MongoDB Connection
mongoose
  .connect(process.env.MONGO)
  .then(() => console.log("MongoDB Connected"))
  .catch((err) => console.error(err));

// Middleware
app.set("view engine", "ejs");
app.use(express.urlencoded({ extended: true }));
app.use(express.static("public"));

// Routes
app.get("/", (req, res) => {
  res.redirect("/login");
});

// Signup Route
app.get("/signup", (req, res) => res.render("signup"));
app.post("/signup", async (req, res) => {
  const { fullName, username, email, password } = req.body;
  const hashedPassword = await bcrypt.hash(password, 10);
  const user = new User({
    fullName,
    username,
    email,
    password: hashedPassword,
  });
  await user.save();
  res.redirect("/login");
});

// Login Route with Session
app.get("/login", (req, res) => res.render("login"));
app.post("/login", async (req, res) => {
  const { username, password } = req.body;
  const user = await User.findOne({ username });
  if (user && (await bcrypt.compare(password, user.password))) {
    req.session.userId = user._id; // Set session
    res.redirect("/dashboard");
  } else {
    res.send("Invalid credentials");
  }
});

// Dashboard Route (Protected)
app.get("/dashboard", async (req, res) => {
  if (!req.session.userId) return res.redirect("/login");

  try {
    const user = await User.findById(req.session.userId);
    const policies = await Policy.find({ userId: req.session.userId });
    res.render("dashboard", { user, policies });
  } catch (err) {
    console.error(err);
    res.redirect("/login");
  }
});

// Claim Policy Route (Protected)
app.get("/claim-policy", (req, res) => {
  if (!req.session.userId) return res.redirect("/login");
  res.render("claim-policy");
});

app.post("/claim-policy", async (req, res) => {
  const { policyNumber, incidentDate, incidentDetails } = req.body;
  const policy = new Policy({
    userId: req.session.userId,
    policyNumber,
    incidentDate,
    incidentDetails,
  });
  await policy.save();
  res.redirect("/dashboard");
});

// Logout Route
app.get("/logout", (req, res) => {
  req.session.destroy();
  res.redirect("/login");
});

// Forgot Password Route
app.get("/forgot-password", (req, res) => res.render("forgot-password"));
app.post("/forgot-password", async (req, res) => {
  const { email } = req.body;
  const user = await User.findOne({ email });
  if (user) {
    res.send("Password reset link sent to your email");
  } else {
    res.send("Email not found");
  }
});

// Start Server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
