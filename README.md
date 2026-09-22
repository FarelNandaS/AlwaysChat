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

#install composer dependence
composer i

#install npm dependence
npm i

#copy environment variable
cp .env.example .env

#generate key app
php artisan key:generate

#configure env
CEK .env YANG SUDAH DI COPY LALU ATUR AGAR SESUAI DENGAN KEBUTUHAN ANDA

#jalankan migrate dan seeder database
php artisan migrate --seed

#JALANKAN BEBERAPA COMMEND INI DI TERMINAL YANG BERBEDA
#jalankan php server
php artisan serve

#jalankan npm
npm run dev

#jalankan reverb
php artisan reverb:start