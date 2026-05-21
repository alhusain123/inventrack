-- ============================================
-- INVENTORY MANAGEMENT SYSTEM - DATABASE SCHEMA
-- ============================================

CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    low_stock_threshold INT NOT NULL DEFAULT 10,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Stock logs table (tracks stock changes)
CREATE TABLE IF NOT EXISTS stock_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    change_amount INT NOT NULL,
    note VARCHAR(255),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- SEED DATA
-- ============================================

-- Default admin user (password: admin123)
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', 'staff');

-- Sample categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Gadgets, devices, and electronic components'),
('Clothing', 'Apparel, shoes, and accessories'),
('Food & Beverage', 'Consumable goods and drinks'),
('Office Supplies', 'Stationery and office equipment'),
('Tools & Hardware', 'Workshop and home improvement tools');

-- Sample products
INSERT INTO products (category_id, name, description, price, stock, low_stock_threshold) VALUES
(1, 'Wireless Mouse', 'Ergonomic wireless mouse with USB receiver', 450.00, 35, 10),
(1, 'USB-C Hub', '7-in-1 USB-C hub with HDMI and SD card reader', 1200.00, 8, 10),
(1, 'Mechanical Keyboard', 'Tenkeyless mechanical keyboard, blue switches', 2500.00, 15, 5),
(2, 'Cotton T-Shirt (M)', 'Plain white cotton t-shirt, medium size', 250.00, 50, 15),
(2, 'Denim Jeans (32)', 'Classic straight-cut denim jeans', 899.00, 20, 8),
(3, 'Instant Coffee 200g', 'Premium instant coffee, 200g pack', 180.00, 60, 20),
(3, 'Mineral Water 500ml', 'Purified mineral water, 500ml bottle', 25.00, 5, 30),
(4, 'Ballpen Box', 'Box of 12 ballpens, blue ink', 75.00, 40, 10),
(4, 'A4 Bond Paper (ream)', '80gsm A4 bond paper, 500 sheets', 220.00, 12, 5),
(5, 'Screwdriver Set', '6-piece magnetic screwdriver set', 350.00, 18, 5);
