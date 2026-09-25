-- ============================================================
-- FOODIEHUB - COMPLETE UPDATED DATABASE
-- Users + Categories + Foods + Cart + Orders + Order Items + Payments
-- Compatible with XAMPP / MySQL / phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS foodiehub_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE foodiehub_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. USERS
-- ============================================================

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS foods;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(20) NULL,
    address TEXT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. CATEGORIES
-- ============================================================

CREATE TABLE categories (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. FOODS
-- ============================================================

CREATE TABLE foods (
    food_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    food_name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) NULL,
    status ENUM('available','unavailable') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_food_category (category_id),
    INDEX idx_food_status (status),

    CONSTRAINT fk_food_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. CART
-- One cart item belongs to one logged-in user and one food.
-- ============================================================

CREATE TABLE cart (
    cart_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    food_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_user_food (user_id, food_id),
    INDEX idx_cart_user (user_id),
    INDEX idx_cart_food (food_id),

    CONSTRAINT fk_cart_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_cart_food
        FOREIGN KEY (food_id)
        REFERENCES foods(food_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. ORDERS
-- ============================================================

CREATE TABLE orders (
    order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_mobile VARCHAR(20) NOT NULL,
    delivery_address TEXT NOT NULL,

    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    delivery_charge DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    payment_method ENUM(
        'cash_on_delivery',
        'card',
        'mobile_banking'
    ) NOT NULL DEFAULT 'cash_on_delivery',

    payment_status ENUM(
        'pending',
        'paid',
        'failed',
        'refunded'
    ) NOT NULL DEFAULT 'pending',

    order_status ENUM(
        'pending',
        'confirmed',
        'preparing',
        'out_for_delivery',
        'delivered',
        'cancelled'
    ) NOT NULL DEFAULT 'pending',

    order_note TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_order_user (user_id),
    INDEX idx_order_status (order_status),
    INDEX idx_order_payment (payment_status),
    INDEX idx_order_created (created_at),

    CONSTRAINT fk_order_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. ORDER ITEMS
-- Store food details at the time of ordering.
-- This keeps old orders correct even if food price/name changes.
-- ============================================================

CREATE TABLE order_items (
    order_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    food_id INT UNSIGNED NULL,

    food_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_order_item_order (order_id),
    INDEX idx_order_item_food (food_id),

    CONSTRAINT fk_order_item_order
        FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_order_item_food
        FOREIGN KEY (food_id)
        REFERENCES foods(food_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 7. PAYMENTS
-- Optional payment transaction information.
-- ============================================================

CREATE TABLE payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,

    payment_method ENUM(
        'cash_on_delivery',
        'card',
        'mobile_banking'
    ) NOT NULL,

    transaction_id VARCHAR(150) NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    payment_status ENUM(
        'pending',
        'paid',
        'failed',
        'refunded'
    ) NOT NULL DEFAULT 'pending',

    paid_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_transaction_id (transaction_id),
    INDEX idx_payment_order (order_id),
    INDEX idx_payment_status (payment_status),

    CONSTRAINT fk_payment_order
        FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 8. DEFAULT CATEGORIES
-- ============================================================

INSERT INTO categories
    (category_name, description, status)
VALUES
    ('Burgers', 'Fresh and delicious burgers', 'active'),
    ('Pizza', 'Hot and cheesy pizzas', 'active'),
    ('Chicken', 'Crispy and tasty chicken items', 'active'),
    ('Rice', 'Rice and meal items', 'active'),
    ('Drinks', 'Cold drinks and beverages', 'active'),
    ('Desserts', 'Sweet desserts and treats', 'active')
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    status = VALUES(status);

-- ============================================================
-- 9. SAMPLE FOODS
-- Delete these INSERT statements if you want an empty menu.
-- ============================================================

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Classic Beef Burger',
    'Juicy beef patty with fresh vegetables and special sauce.',
    250.00,
    'uploads/classic-beef-burger.jpg',
    'available'
FROM categories
WHERE category_name = 'Burgers'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Chicken Burger',
    'Crispy chicken burger with fresh lettuce and sauce.',
    220.00,
    'uploads/chicken-burger.jpg',
    'available'
FROM categories
WHERE category_name = 'Burgers'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Margherita Pizza',
    'Classic pizza with tomato sauce, mozzarella and herbs.',
    450.00,
    'uploads/margherita-pizza.jpg',
    'available'
FROM categories
WHERE category_name = 'Pizza'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Crispy Fried Chicken',
    'Crispy golden fried chicken.',
    180.00,
    'uploads/fried-chicken.jpg',
    'available'
FROM categories
WHERE category_name = 'Chicken'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Chicken Fried Rice',
    'Fried rice with chicken, vegetables and special seasoning.',
    280.00,
    'uploads/chicken-fried-rice.jpg',
    'available'
FROM categories
WHERE category_name = 'Rice'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Cold Coffee',
    'Refreshing chilled coffee.',
    150.00,
    'uploads/cold-coffee.jpg',
    'available'
FROM categories
WHERE category_name = 'Drinks'
LIMIT 1;

INSERT INTO foods
    (category_id, food_name, description, price, image, status)
SELECT
    category_id,
    'Chocolate Cake',
    'Rich and creamy chocolate cake.',
    180.00,
    'uploads/chocolate-cake.jpg',
    'available'
FROM categories
WHERE category_name = 'Desserts'
LIMIT 1;

-- ============================================================
-- 10. OPTIONAL ADMIN ACCOUNT
-- IMPORTANT:
-- Replace the password hash below with a hash generated by PHP
-- password_hash() before using this account.
--
-- Example PHP:
-- echo password_hash('YourPassword', PASSWORD_DEFAULT);
-- ============================================================

-- INSERT INTO users
-- (name, email, mobile, address, password, role)
-- VALUES
-- ('Admin', 'admin@foodiehub.com', '01700000000',
--  'FoodieHub Admin Office',
--  'PASTE_YOUR_PASSWORD_HASH_HERE',
--  'admin');

-- ============================================================
-- 11. CHECK TABLES
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;

SHOW TABLES;

-- ============================================================
-- END OF FOODIEHUB DATABASE
-- ============================================================
