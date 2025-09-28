# Rating-employee

A Laravel + Vue 3 (Vite) app to manage **Departments**, **Employees**, and **Employee Ratings**.

---

## 🧰 Tech Stack
- **Backend:** Laravel (PHP 8.2+)
- **Frontend:** Vue 3 + Vite, Sass
- **Database:** MySQL/MariaDB
- **Runtime:** Node.js 20.19+ (or 22.12+)
- **Tools:** Composer, NPM

---

## ✅ Features
- CRUD for **Departments**, **Employees**, and **Ratings**
- Vue 3 SPA components inside Laravel Blade
- RESTful routes with Laravel controllers
- Vite for fast builds & HMR
- Environment-based configuration

---

## 📦 Requirements
- PHP 8.2+ (extensions: `pdo_mysql`, `mbstring`, `bcmath`, `intl`)
- Composer 2.x
- Node.js 20.19+ (use `nvm install 20.19.0 && nvm use 20.19.0`)
- MySQL or MariaDB
- (Windows) XAMPP / Laragon recommended

---

## 🚀 Quick Start

### 1. Clone the project
```bash
git clone https://github.com/<your-username>/Rating-employee.git
cd Rating-employee


# 0) Clone
git clone https://github.com/<your-username>/Rating-employee.git
cd Rating-employee

# 1) Backend
cp .env.example .env
php artisan key:generate
# Edit .env → set DB_* credentials
php artisan migrate  # add --seed if you have seeders

# 2) Frontend
npm install
npm run dev   # starts Vite (shows http://localhost:5173)

# 3) Run Laravel
php artisan serve  # http://127.0.0.1:8000

