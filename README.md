Ini adalah sebuah web messager real-time mengunakan protocol WebSocket, di dalam web ini anda bisa mengirim pesan membuat percakapan secara real-time dengan semua percakan terencrypt dan hanya bisa di lihat oleh anda dan penerima dari pesan tersebut. project ini mengunakan laravel 13 dan vue 6.

Requirement:
- php 8.3.30
- node 22.17.1

Tech Stack:
- laravel 13
- vue 6
- laravel reverb
- laravel Echo

Setup Project:
```bash
#clone repository
git clone https://github.com/FarelNandaS/AlwaysChat.git
git clone git@github.com:FarelNandaS/AlwaysChat.git

#masuk directory
cd AlwaysChat

#lakukan setup
composer run setup

#configure .env
CEK FILE .env LALU ATUR AGAR SESUAI DENGAN KEBUTUHAN ANDA

#jalankan project
composer run dev