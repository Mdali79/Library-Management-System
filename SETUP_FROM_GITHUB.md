# Setup from GitHub

Step-by-step commands to set up this Laravel Library Management System from a GitHub repo.

---

## 1. Clone the repo

```bash
git clone https://github.com/YOUR_USERNAME/Laravel-libraray-management-system.git
cd Laravel-libraray-management-system
```

Replace `YOUR_USERNAME` and the repo name with your actual GitHub repo URL.

---

## 2. Install PHP dependencies

```bash
composer install
```

If you hit memory limits:

```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

---

## 3. Environment file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at least:

- `APP_NAME`, `APP_URL` (e.g. `http://127.0.0.1:8000`)
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Mail settings (if using OTP / forgot password)
- SSLCommerz (if using online fine payment): `SSLCOMMERZ_STORE_ID`, `SSLCOMMERZ_STORE_PASSWORD`, `SSLCOMMERZ_SANDBOX=true`

---

## 4. Create database

Create a MySQL/MariaDB database (e.g. via phpMyAdmin or CLI):

```sql
CREATE DATABASE your_database_name;
```

Use that name in `.env` as `DB_DATABASE`.

---

## 5. Run migrations and seed (optional)

```bash
php artisan migrate
php artisan db:seed
```

If a seeder class is not found, run:

```bash
composer dump-autoload
php artisan db:seed
```

---

## 6. Storage link

```bash
php artisan storage:link
```

---

## 7. Frontend assets (optional)

```bash
npm install
npm run dev
```

For production build:

```bash
npm run production
```

---

## 8. Run the app

```bash
php artisan serve
```

Open **http://127.0.0.1:8000** in your browser.

---

## Quick copy-paste sequence

```bash
git clone https://github.com/YOUR_USERNAME/Laravel-libraray-management-system.git
cd Laravel-libraray-management-system
composer install
cp .env.example .env
php artisan key:generate
# Edit .env (DB_*, APP_URL, etc.)
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install && npm run dev
php artisan serve
```

---

## Requirements

- **PHP** >= 8.0 (with extensions: mbstring, openssl, pdo, tokenizer, xml, ctype, json, bcmath, fileinfo)
- **Composer**
- **MySQL** or **MariaDB**
- **Node.js** and **npm** (if you run frontend build)
