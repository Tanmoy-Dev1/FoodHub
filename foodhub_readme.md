# 🍔 FoodHub - Fast & Delicious Food Ordering System

FoodHub is a full-stack dynamic web application designed for online food ordering, review management, customer support, and administrative store management. Built with modern UI components and a solid PHP/MySQL backend architecture.

---

## ✨ Features

### 👤 Customer Features
* **Interactive Menu & Categories:** Filter food items dynamically by categories (Burgers, Pizzas, Desserts, Drinks) with real-time responsive updates.
* **Product Details & Reviews:** View high-resolution food images, detailed descriptions, ratings, and customer feedback.
* **Shopping Cart & Checkout:** Seamless dynamic cart management with live price and delivery fee calculation.
* **Verified Reviews System:** Only customers with completed/delivered orders are eligible to submit reviews for purchased products.
* **Support System:** Direct communication channel for customer queries and support messages.

### 🛡️ Admin Dashboard
* **Real-time Business Analytics:** Instant metrics for Total, Pending, Completed, and Canceled orders.
* **Order Management:** Live status updates for customer orders (Pending, Completed, Canceled).
* **Product Management:** Modal form interface to easily insert new food items into the system.
* **Customer Support Desk:** View and respond to support threads per user.

---

## 🛠️ Tech Stack & Prerequisites

* **Frontend:** HTML5, Tailwind CSS (CDN), Lucide Icons, Vanilla JavaScript (Async Fetch API)
* **Backend:** PHP 8.x (Hybrid PDO / MySQLi Prepared Statements)
* **Database:** MySQL / MariaDB (`foodhub_db`)
* **Local Server:** XAMPP / WAMP / MAMP

---

## 🗄️ Database Setup

1. Open `phpMyAdmin` at `http://localhost/phpmyadmin`.
2. Create a new database named **`foodhub_db`**.
3. Import the database schema (`foodhub_db.sql`) provided in the repository.

### Key Database Tables:
* `users` — Authentication & Roles (`customer`, `admin`)
* `products` — Food catalog details, pricing, images, and base ratings
* `categories` — Food categorizations
* `orders` & `order_items` — Transaction history and line items
* `reviews` — User feedback mapped to product IDs
* `support_messages` — In-app customer support messages

---

## 🚀 Installation & Local Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/YOUR_GITHUB_USERNAME/FoodHub.git
   ```
2. Move the project folder to your local server root:
   * **XAMPP:** `C:\xampp\htdocs\foodhub`
3. Configure your database connection in `db.php`:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $dbname = 'foodhub_db';
   ```
4. Start Apache and MySQL modules in your XAMPP Control Panel.
5. Open your browser and navigate to:
   * **Storefront:** `http://localhost/foodhub/index.php`
   * **Admin Panel:** `http://localhost/foodhub/admin/dashboard.php`

---

## 🔐 Credentials (Demo Data)

* **Admin Access:**
  * **Email:** `admin@foodhub.com`
  * **Password:** `password` (or as set in your database hash)
* **Customer Access:**
  * **Email:** 

---

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.
Credit

Tanmoy Dev
B. Sc.  In CSE
Premier University