const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const { saveMessage, getMessages } = require('./db');

const app = express();
const server = http.createServer(app);
const io = new Server(server);

app.use(express.static('public'));

io.on('connection', async (socket) => {
  console.log('User connected:', socket.id);

  // Load chat history
  const messages = await getMessages();
  socket.emit('loadHistory', messages);

  socket.on('chatMessage', async (msg) => {
    const messageData = {
      sender: msg.sender,
      type: msg.type,
      content: msg.content
    };
    const id = await saveMessage(messageData);
    io.emit('newMessage', { ...messageData, id });
    socket.emit('messageReceived', { id });
  });

  socket.on('serverMessage', async (msg) => {
    const messageData = {
      sender: 'Server',
      type: msg.type,
      content: msg.content
    };
    const id = await saveMessage(messageData);
    io.emit('newMessage', { ...messageData, id });
  });

  socket.on('disconnect', () => {
    console.log('User disconnected:', socket.id);
  });
});

server.listen(3000, () => {
  console.log('Server running on http://localhost:3000');
});
