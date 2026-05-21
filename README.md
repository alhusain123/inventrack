# InvenTrack — Inventory Management System
**CST5 Final Project**

## Project Description
InvenTrack is a full-featured, database-driven web application for managing shop inventory. It allows users to track products, categories, and stock levels, with role-based authentication for admin and staff users.

---

## Technologies Used
| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons |
| Backend | PHP 8 |
| Database | MySQL 8 |
| Fonts | Syne, DM Sans (Google Fonts) |

---

## Features
### CRUD Operations
- **Products** — Create, Read, Update, Delete products with price, stock, and category
- **Categories** — Manage product categories; protected delete (no deletion if products exist)
- **Users** — Admin-only user management with role assignment

### Dynamic Features (3+)
1. **Authentication & Sessions** — Login/logout with PHP sessions; session regeneration on login
2. **Role-Based Access Control** — Admin-only pages (Users); staff restriction enforcement
3. **Search & Filter** — Search by name, filter by category, filter by stock status (low/out)
4. **Low Stock Alerts** — Dashboard cards and table rows highlight low/out-of-stock items
5. **Stock Logging** — Tracks stock changes in a log table when products are edited
6. **Animated Dashboard** — Stat counters animate on page load; stock bars animate via JS

---

## Database Structure
```
inventory_db
├── users          (id, username, password, full_name, role, created_at)
├── categories     (id, name, description, created_at)
├── products       (id, category_id, name, description, price, stock, low_stock_threshold, created_at, updated_at)
└── stock_logs     (id, product_id, user_id, change_amount, note, logged_at)
```

---

## Setup Instructions

### Requirements
- PHP 8.0+
- MySQL 8.0+
- Apache / Nginx (XAMPP, WAMP, or Laragon recommended)

### Steps

1. **Clone or download** the project into your web server's root:
   ```
   htdocs/inventory/   ← for XAMPP
   www/inventory/      ← for WAMP/Laragon
   ```

2. **Import the database:**
   - Open phpMyAdmin
   - Create a new database named `inventory_db`
   - Import `schema.sql`

3. **Configure the connection** in `includes/db.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');     // your MySQL username
   define('DB_PASS', '');         // your MySQL password
   define('DB_NAME', 'inventory_db');
   ```

4. **Start your server** and visit:
   ```
   http://localhost/inventory/
   ```

5. **Login with:**
   | Username | Password | Role |
   |----------|----------|------|
   | admin    | password | Admin |
   | staff1   | password | Staff |

---

## Project Structure
```
inventory/
├── index.php                  # Root redirect
├── login.php                  # Login page
├── logout.php                 # Session destroy
├── dashboard.php              # Main dashboard
├── schema.sql                 # Database schema + seed data
├── README.md                  # This file
│
├── includes/
│   ├── db.php                 # Database connection
│   ├── auth.php               # Auth helpers & session functions
│   ├── header.php             # Shared header/nav partial
│   └── footer.php             # Shared footer partial
│
├── products/
│   ├── index.php              # Product list (search + filter)
│   ├── add.php                # Add product form
│   ├── edit.php               # Edit product form
│   └── delete.php             # Delete handler
│
├── categories/
│   ├── index.php              # Category list
│   ├── add.php                # Add category form
│   ├── edit.php               # Edit category form
│   └── delete.php             # Delete handler (protected)
│
├── users/
│   ├── index.php              # User list (admin only)
│   ├── add.php                # Add user (admin only)
│   ├── edit.php               # Edit user
│   └── delete.php             # Delete user (admin only)
│
└── assets/
    ├── css/style.css          # Main stylesheet
    └── js/main.js             # Frontend scripts
```

---

## Links
- **Deployed App:** [Add your deployment URL here]
- **GitHub Repo:** [Add your GitHub URL here]
- **Video Presentation:** [Add your Google Drive link here]

---

## Developer
- **Name:** [Your Name]
- **Course:** CST5
- **Section:** [Your Section]
- **Instructor:** klatina@umindanao.edu.ph
