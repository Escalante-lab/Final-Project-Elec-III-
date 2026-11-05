CREATE DATABASE IF NOT EXISTS meat_inventory;
USE meat_inventory;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cut_type VARCHAR(100),
    description TEXT,
    price_per_kg DECIMAL(10,2) NOT NULL,
    stock_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
    low_stock_threshold DECIMAL(10,2) DEFAULT 5.00,
    image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    pickup_time DATETIME NOT NULL,
    status ENUM('pending','processing','ready','completed','cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_kg DECIMAL(10,2) NOT NULL,
    price_per_kg DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE stock_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    change_kg DECIMAL(10,2) NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Default admin: admin@gmail.com / admin123
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@gmail.com', '$2y$10$J6PLnpbqgWHmiuZPmonhn.ItumVQOAKPoSmkH8DsPBwMo/q77fWVi', 'admin');

INSERT INTO products (name, cut_type, description, price_per_kg, stock_kg) VALUES
('Premium Beef Sirloin', 'Sirloin', 'Tender sirloin cut, perfect for grilling', 850.00, 25.00),
('Pork Belly', 'Belly', 'Juicy pork belly, ideal for roasting', 420.00, 30.00),
('Chicken Breast', 'Breast', 'Skinless boneless chicken breast', 280.00, 40.00),
('Beef Ribs', 'Ribs', 'Meaty beef ribs for BBQ', 650.00, 15.00),
('Pork Chops', 'Chop', 'Bone-in pork chops', 380.00, 20.00),
('Lamb Shoulder', 'Shoulder', 'Tender lamb shoulder cut', 900.00, 10.00);