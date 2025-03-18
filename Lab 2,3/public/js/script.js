document.getElementById("loginForm").addEventListener("submit", function (e) {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  if (!username || !password) {
    alert("Please fill in all fields");
    e.preventDefault();
  }
});

document.getElementById("signupForm").addEventListener("submit", function (e) {
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  if (password !== confirmPassword) {
    alert("Passwords do not match");
    e.preventDefault();
  }
});
