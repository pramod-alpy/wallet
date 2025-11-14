
Laravel Wallet App

A secure and real-time digital wallet built with Laravel, Vue.js, Pusher/WebSockets, and Laravel Sanctum.
This application allows users to manage funds, transfer money, view transaction history, and receive instant real-time updates whenever money is sent or received.

📦 Installation


1️⃣ Clone the Repository
```
git clone https://github.com/pramod-alpy/wallet.git
cd wallet-app
```


2️⃣ Install PHP Dependencies
```
composer install
```
3️⃣ Install JS Dependencies
```
npm install
```
4️⃣ Copy .env File
```
cp .env.example .env
```
5️⃣ Generate App Key
```
php artisan key:generate
```
6️⃣ Configure Database in .env
```
DB_DATABASE=wallet
DB_USERNAME=root
DB_PASSWORD=
```
7️⃣ Setup Pusher (or WebSockets)
```
PUSHER_APP_ID=xxxx
PUSHER_APP_KEY=xxxx
PUSHER_APP_SECRET=xxxx
PUSHER_APP_CLUSTER=ap2
BROADCAST_DRIVER=pusher
```
8️⃣ Run Migrations
```

php artisan migrate
```
9️⃣ Start Backend
```
php artisan serve
```
🔟 Start Frontend
```
npm run dev
```
