const socket = io();

// Ask for username on page load
let username = '';
while (!username || username.trim() === '') {
  username = prompt('Enter your name:');
}

const msgInput = document.getElementById('msgInput');
const imgInput = document.getElementById('imgInput');
const sendBtn = document.getElementById('sendBtn');
const messagesDiv = document.getElementById('messages');

function renderMessage(msg) {
  const div = document.createElement('div');
  div.classList.add('message');

  if (msg.type === 'text') {
    div.textContent = `${msg.sender || 'Unknown'}: ${msg.content}`;
  } else if (msg.type === 'image') {
    div.innerHTML = `<strong>${msg.sender || 'Unknown'}:</strong><br>`;
    const img = document.createElement('img');
    img.src = msg.content;
    img.classList.add('chat-image');
    div.appendChild(img);
  }

  messagesDiv.appendChild(div);
  messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

sendBtn.addEventListener('click', () => {
  const message = msgInput.value.trim();
  if (message) {
    socket.emit('chatMessage', {
      sender: username,
      type: 'text',
      content: message
    });
    msgInput.value = '';
  }
});

imgInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = () => {
    socket.emit('chatMessage', {
      sender: username,
      type: 'image',
      content: reader.result
    });
  };
  reader.readAsDataURL(file);
});

socket.on('loadHistory', (msgs) => {
  msgs.forEach(renderMessage);
});

socket.on('newMessage', (msg) => {
  renderMessage(msg);
});

socket.on('messageReceived', (data) => {
  console.log('Message saved to DB with ID:', data.id);
});
