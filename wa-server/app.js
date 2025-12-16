require('dotenv').config();
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const { Boom } = require('@hapi/boom');
const express = require('express');
const bodyParser = require('body-parser');
const qrcode = require('qrcode-terminal');
const fs = require('fs');

const app = express();
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

let sock;

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: true, // Ubah false jika ingin QR tampil di respon API (opsional)
        browser: ["Sistem Magang BPS", "Chrome", "1.0.0"]
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log('SCAN QR CODE INI MENGGUNAKAN WA ANDA:');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error = lastDisconnect.error?.output?.statusCode !== DisconnectReason.loggedOut);
            console.log('Koneksi terputus karena ', lastDisconnect.error, ', mencoba reconnect: ', shouldReconnect);
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            console.log('✅ WhatsApp Terhubung Siap Menerima Request!');
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

// Jalankan Koneksi WA
connectToWhatsApp();

// --- MIDDLEWARE SECURITY ---
const verifyApiKey = (req, res, next) => {
    const apiKey = req.headers['x-api-key'];
    if (apiKey !== process.env.API_KEY) {
        return res.status(403).json({ status: false, message: 'Invalid API Key / Unauthorized Access' });
    }
    next();
};

// --- API ENDPOINT ---

// 1. Kirim Pesan Text
app.post('/send-message', verifyApiKey, async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ status: false, message: 'Parameter number dan message wajib diisi' });
    }

    try {
        // Format Nomor: 08xx -> 628xx
        let formattedNumber = number.toString().replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.substr(1);
        }
        
        // Tambahkan suffix @s.whatsapp.net
        if (!formattedNumber.endsWith('@s.whatsapp.net')) {
            formattedNumber += '@s.whatsapp.net';
        }

        // Cek apakah nomor terdaftar di WA
        const [result] = await sock.onWhatsApp(formattedNumber);
        if (result?.exists) {
            await sock.sendMessage(formattedNumber, { text: message });
            return res.json({ status: true, message: 'Pesan terkirim', target: formattedNumber });
        } else {
            return res.status(404).json({ status: false, message: 'Nomor tidak terdaftar di WhatsApp' });
        }

    } catch (error) {
        console.error(error);
        res.status(500).json({ status: false, message: 'Internal Server Error', error: error.message });
    }
});

// Start Server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`🚀 WA Server berjalan di port ${PORT}`);
});
