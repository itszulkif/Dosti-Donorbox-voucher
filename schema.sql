CREATE DATABASE IF NOT EXISTS dosti_vouchers;
USE dosti_vouchers;

CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    voucher_id VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('Active', 'Inactive', 'Redeemed') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS voucher_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    restaurant ENUM('MS', 'D9', 'GO') NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
);

-- Mock Data for Analytics
INSERT INTO donors (name, email, phone, voucher_id, status) VALUES 
('John Doe', 'john@example.com', '1234567890', 'DV-1001', 'Active'),
('Jane Smith', 'jane@example.com', '0987654321', 'DV-1002', 'Redeemed'),
('Alice Brown', 'alice@example.com', '5551234567', 'DV-1003', 'Active'),
('Bob Wilson', 'bob@example.com', '4449876543', 'DV-1004', 'Redeemed');

INSERT INTO voucher_usage (donor_id, restaurant) VALUES 
(2, 'MS'),
(2, 'D9'),
(4, 'GO');
