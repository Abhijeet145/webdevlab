const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: 'root',
  database: 'chat_db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

async function saveMessage({ sender, type, content }) {
  const [result] = await pool.query(
    'INSERT INTO messages (sender, type, content) VALUES (?, ?, ?)',
    [sender, type, content]
  );
  return result.insertId;
}

async function getMessages() {
  const [rows] = await pool.query(
    'SELECT * FROM messages ORDER BY timestamp ASC'
  );
  return rows;
}

module.exports = { saveMessage, getMessages };
