const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const sqlite3 = require("sqlite3").verbose();
const multer = require("multer");
const path = require("path");
const fs = require("fs");

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

app.use(express.static(__dirname));
app.use(express.json());

// Database setup
const db = new sqlite3.Database("chat.db");
db.run(`
    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender TEXT,
        message TEXT,
        type TEXT
    )
`);

// Serve index.html as the main page
app.get("/", (req, res) => res.sendFile(__dirname + "/index.html"));

// Handle image uploads
const upload = multer({ dest: "uploads/" });
if (!fs.existsSync("uploads")) fs.mkdirSync("uploads");

app.post("/upload", upload.single("image"), (req, res) => {
    const filePath = `/uploads/${req.file.filename}`;
    res.json({ imageUrl: filePath });
});

app.use("/uploads", express.static(path.join(__dirname, "uploads")));

// Socket connection
io.on("connection", (socket) => {
    console.log("New user connected:", socket.id);

    // Send chat history on connection
    db.all("SELECT * FROM messages", (err, rows) => {
        if (!err) socket.emit("loadMessages", rows);
    });

    // Handle message sending (server only stores encrypted message)
    socket.on("sendMessage", (data) => {
        console.log(`Encrypted message from ${data.sender}: ${data.message}`); // Demonstration of unreadable message

        const messageData = {
            sender: data.sender,
            message: data.message,
            type: data.type,
            senderSocketId: socket.id,
        };

        db.run(
            "INSERT INTO messages (sender, message, type) VALUES (?, ?, ?)",
            [data.sender, data.message, data.type],
            function (err) {
                if (!err) {
                    messageData.messageId = this.lastID;
                    socket.broadcast.emit("receiveMessage", messageData);
                }
            }
        );
    });

    // Confirm message delivery
    socket.on("messageReceived", (messageId) => {
        io.to(messageId.senderSocketId).emit("messageDelivered", messageId.messageId);
    });

    // Confirm message read
    socket.on("messageRead", (messageId) => {
        io.to(messageId.senderSocketId).emit("messageRead", messageId.messageId);
    });

    socket.on("disconnect", () => console.log("User disconnected:", socket.id));
});

// Start server
const PORT = 3000;
server.listen(PORT, () => console.log(`Server running at http://localhost:${PORT}`));
