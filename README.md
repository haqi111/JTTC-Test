# 🛒 JTTC Fullactack Test

A RESTful API built with Laravel to manage **users**, **division**, and **contract** . This API supports secure authentication, CRUD operations, and user-specific data access.

---

## 📑 Table of Contents

- [🛒 JTTC Fullactack Test](#-jttc-fullactack-test)
  - [📑 Table of Contents](#-table-of-contents)
  - [🚀 Features](#-features)
  - [🛠️ Installation \& Setup](#️-installation--setup)
    - [📄 Set up `.env`](#-set-up-env)
    - [🧱 Run Laravel Migration](#-run-laravel-migration)
    - [🚀 Start the Server](#-start-the-server)
  - [📡 API Endpoints](#-api-endpoints)
    - [📘 License](#-license)
      

---

## 🚀 Features

- Session-based authentication
- CRUD for employee
- CRUD Division
- CRUD Contract
- MySQL compatible

---

## 🛠️ Installation & Setup

```bash
git clone https://github.com/haqi111/JTTC-Test.git
cd JTTC-Test
composer install
```

---

### 📄 Set up `.env`

Create a `.env` file in the root directory and add the following:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

---

### 🧱 Run Laravel Migration

```bash
php artisan migrate --seed
```

---

### 🚀 Start the Server

```bash
php artisan serve
```

---
## 📡 API Endpoints

The server will run at:
```bash
http://localhost:8000
```

Access the Admin Dashboard at:
```bash
http://localhost:8000/admin
```



### 📘 License

This project is licensed under the **MIT License**.

You are free to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, subject to the following conditions:

- The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
- The software is provided "as is", without warranty of any kind.






