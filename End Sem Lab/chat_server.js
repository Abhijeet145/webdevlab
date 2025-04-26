const WebSocket = require('ws');
const server = new WebSocket.Server({ port: 8080 });

let clients = [];

server.on('connection', socket => {
    clients.push(socket);
    socket.on('message', msg => {
        // Broadcast to all clients (you can filter by user role later)
        clients.forEach(client => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(msg);
            }
        });
    });

    socket.on('close', () => {
        clients = clients.filter(c => c !== socket);
    });
});
