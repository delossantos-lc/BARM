const { SerialPort } = require('serialport');
const { ReadlineParser } = require('@serialport/parser-readline');
const { WebSocketServer, WebSocket } = require('ws');

const serialPath = process.env.FINGERPRINT_PORT || 'COM10';
const serialBaudRate = Number(process.env.FINGERPRINT_BAUD || 9600);
const bridgePort = Number(process.env.FINGERPRINT_BRIDGE_PORT || 8765);

const clients = new Set();
let serial = null;
let reconnectTimer = null;

function broadcast(message) {
    const body = JSON.stringify(message);

    for (const client of clients) {
        if (client.readyState === WebSocket.OPEN) {
            client.send(body);
        }
    }
}

function scheduleReconnect() {
    if (reconnectTimer) return;

    reconnectTimer = setTimeout(() => {
        reconnectTimer = null;
        openSerialPort();
    }, 2000);
}

function openSerialPort() {
    if (serial?.isOpen) return;

    serial = new SerialPort({
        path: serialPath,
        baudRate: serialBaudRate,
        autoOpen: false,
    });

    const parser = serial.pipe(new ReadlineParser({ delimiter: '\n' }));

    parser.on('data', (value) => {
        const line = String(value).trim();
        if (line) broadcast({ type: 'serial', line });
    });

    serial.on('open', () => {
        console.log(`Fingerprint scanner connected on ${serialPath}.`);
        broadcast({ type: 'status', connected: true });
    });

    serial.on('close', () => {
        broadcast({ type: 'status', connected: false });
        scheduleReconnect();
    });

    serial.on('error', (error) => {
        console.error(`Serial error: ${error.message}`);
        broadcast({ type: 'status', connected: false, message: error.message });
        if (serial.isOpen) serial.close();
        else scheduleReconnect();
    });

    serial.open((error) => {
        if (error) {
            console.error(`Cannot open ${serialPath}: ${error.message}`);
            broadcast({ type: 'status', connected: false, message: error.message });
            scheduleReconnect();
        }
    });
}

const server = new WebSocketServer({ host: '127.0.0.1', port: bridgePort });

server.on('connection', (socket) => {
    clients.add(socket);
    socket.send(JSON.stringify({
        type: 'status',
        connected: Boolean(serial?.isOpen),
    }));

    socket.on('message', (value) => {
        let message;

        try {
            message = JSON.parse(String(value));
        } catch (_) {
            return;
        }

        if (message.type !== 'command' || typeof message.command !== 'string') {
            return;
        }

        const command = message.command.trim();

        if (!/^(ENROLL|DELETE):([1-9]|[1-9][0-9]|1[01][0-9]|12[0-7])$/.test(command)) {
            socket.send(JSON.stringify({
                type: 'command-error',
                message: 'Invalid fingerprint scanner command.',
            }));
            return;
        }

        if (!serial?.isOpen) {
            socket.send(JSON.stringify({
                type: 'command-error',
                message: 'Fingerprint scanner is not connected.',
            }));
            return;
        }

        serial.write(command + '\n', (error) => {
            if (error) {
                socket.send(JSON.stringify({
                    type: 'command-error',
                    message: error.message,
                }));
            }
        });
    });

    socket.on('close', () => clients.delete(socket));
});

server.on('listening', () => {
    console.log(`Fingerprint bridge running at ws://127.0.0.1:${bridgePort}`);
    openSerialPort();
});

process.on('SIGINT', () => {
    if (serial?.isOpen) serial.close();
    server.close(() => process.exit(0));
});
