<?php
$host = 'localhost';
$dbname = 'dosti_vouchers';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Check if tables exist to determine if fresh setup is needed
$tableCheck = $conn->query("SHOW TABLES LIKE 'donors'");
if ($tableCheck->num_rows == 0) {
    // Tables don't exist, create them and seed data
    $sql = "
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

    INSERT INTO donors (name, email, phone, voucher_id, status) VALUES 
    ('John Doe', 'john@example.com', '1234567890', 'DV-1001', 'Active'),
    ('Jane Smith', 'jane@example.com', '0987654321', 'DV-1002', 'Redeemed'),
    ('Alice Brown', 'alice@example.com', '5551234567', 'DV-1003', 'Active'),
    ('Bob Wilson', 'bob@example.com', '4449876543', 'DV-1004', 'Redeemed');

    INSERT INTO voucher_usage (donor_id, restaurant) VALUES 
    (2, 'MS'),
    (2, 'D9'),
    (4, 'GO');
    ";

    // Execute multi-query
    if ($conn->multi_query($sql)) {
        do {
            // Consume all results to clear the stack
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}

// Check for Donation Box tables
$boxTableCheck = $conn->query("SHOW TABLES LIKE 'donation_shops'");
if ($boxTableCheck->num_rows == 0) {
    $sql = "
    CREATE TABLE IF NOT EXISTS donation_shops (
        id INT AUTO_INCREMENT PRIMARY KEY,
        box_number VARCHAR(50) UNIQUE NOT NULL,
        shop_name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        installation_date DATE,
        contact_person VARCHAR(255),
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS donation_visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_id INT NOT NULL,
        visit_date DATE NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        amount_1 DECIMAL(10,2) NOT NULL DEFAULT 0,
        amount_2 DECIMAL(10,2) NOT NULL DEFAULT 0,
        received_from VARCHAR(255),
        received_by VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (shop_id) REFERENCES donation_shops(id) ON DELETE CASCADE
    );

    INSERT INTO donation_shops (box_number, shop_name, installation_date, contact_person, phone, address) VALUES 
    ('BOX-101', 'City General Store', '2025-01-10', 'Mr. Ahmed', '0300-1234567', 'Main Market, Block A'),
    ('BOX-102', 'Bismillah Pharmacy', '2025-01-15', 'Sharif Bhai', '0321-7654321', 'Station Road'),
    ('BOX-103', 'Food Palace', '2025-02-01', 'Zaid', '0333-1112223', 'University Road');
    
    INSERT INTO donation_visits (shop_id, visit_date, amount, received_from, received_by) VALUES 
    (1, '2025-01-30', 4500.00, 'Ahmed', 'Admin'),
    (1, '2025-02-28', 5200.00, 'Ahmed', 'Staff Ali'),
    (2, '2025-02-15', 3000.00, 'Sharif', 'Admin'),
    (3, '2025-02-25', 1500.00, 'Zaid', 'Staff Ali');
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}

// Check for Restaurants table
$restaurantTableCheck = $conn->query("SHOW TABLES LIKE 'restaurants'");
if ($restaurantTableCheck->num_rows == 0) {
    $sql = "
    CREATE TABLE IF NOT EXISTS restaurants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL,
        address TEXT,
        discount_percentage INT DEFAULT 0,
        custom_price DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    INSERT INTO restaurants (name, address, discount_percentage, custom_price) VALUES 
    ('Savour Foods', 'Main Boulevard, Gulberg', 15, 0),
    ('Bundu Khan', 'MM Alam Road', 0, 500),
    ('Pizza Hut', 'Liberty Market', 20, 0);
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}

// Check for Multi-Redemption tables
$offerTableCheck = $conn->query("SHOW TABLES LIKE 'donor_offers'");
if ($offerTableCheck->num_rows == 0) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS donor_offers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            donor_id INT NOT NULL,
            restaurant_name VARCHAR(255) NOT NULL,
            restaurant_address TEXT,
            offer_type ENUM('percentage', 'fixed') NOT NULL,
            offer_value DECIMAL(10,2) NOT NULL,
            status ENUM('Pending', 'Redeemed') DEFAULT 'Pending',
            FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
        )
    ");
} else {
    // Ensure status column exists
    $cols = $conn->query("SHOW COLUMNS FROM donor_offers LIKE 'status'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE donor_offers ADD COLUMN status ENUM('Pending', 'Redeemed') DEFAULT 'Pending'");
    }
}

// Check for Admins table
$adminTableCheck = $conn->query("SHOW TABLES LIKE 'admins'");
if ($adminTableCheck->num_rows == 0) {
    $pass = password_hash('password123', PASSWORD_BCRYPT);
    $sql = "
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin') DEFAULT 'admin',
        restaurant_id INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    INSERT INTO admins (username, email, password_hash, role) VALUES ('admin', 'admin@dostiwelfare.org', '$pass', 'super_admin');
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
} else {
    // Migration: Add restaurant_id to admins if missing
    $cols = $conn->query("SHOW COLUMNS FROM admins LIKE 'restaurant_id'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD COLUMN restaurant_id INT DEFAULT NULL AFTER role");
    }

    // Migration: Add full_name and designation to admins if missing
    $cols = $conn->query("SHOW COLUMNS FROM admins LIKE 'full_name'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD COLUMN full_name VARCHAR(255) DEFAULT NULL AFTER id");
    }
    $cols = $conn->query("SHOW COLUMNS FROM admins LIKE 'designation'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD COLUMN designation VARCHAR(100) DEFAULT NULL AFTER full_name");
    }

    // Migration: Update role ENUM to include new roles
    $result = $conn->query("SHOW COLUMNS FROM admins LIKE 'role'");
    if ($result && $row = $result->fetch_assoc()) {
        $type = $row['Type'];
        if (strpos($type, 'voucher_editor') === false || strpos($type, 'box_editor') === false) {
            $conn->query("ALTER TABLE admins MODIFY COLUMN role ENUM('super_admin', 'voucher_editor', 'box_editor') DEFAULT 'super_admin'");
        }
    }
}

// Migration: Add email to donation_shops if missing
$cols = $conn->query("SHOW COLUMNS FROM donation_shops LIKE 'email'");
if ($cols->num_rows == 0) {
    $conn->query("ALTER TABLE donation_shops ADD COLUMN email VARCHAR(255) AFTER shop_name");
}

// Migration: Add amount_1, amount_2 to donation_visits if missing
$cols = $conn->query("SHOW COLUMNS FROM donation_visits LIKE 'amount_1'");
if ($cols->num_rows == 0) {
    $conn->query("ALTER TABLE donation_visits ADD COLUMN amount_1 DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount, ADD COLUMN amount_2 DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount_1");
}

// Check for Email Campaign Analytics tables
$campaignTableCheck = $conn->query("SHOW TABLES LIKE 'email_campaigns'");
if ($campaignTableCheck->num_rows == 0) {
    $sql = "
    CREATE TABLE IF NOT EXISTS email_campaigns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL,
        audience_type VARCHAR(50) NOT NULL,
        sent_count INT DEFAULT 0,
        fail_count INT DEFAULT 0,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS campaign_opens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        recipient_email VARCHAR(255) NOT NULL,
        opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45),
        user_agent TEXT,
        FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS campaign_clicks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        recipient_email VARCHAR(255) NOT NULL,
        url TEXT NOT NULL,
        clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45),
        user_agent TEXT,
        FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE
    );
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}

// Check for Email Logs table
$emailLogsTableCheck = $conn->query("SHOW TABLES LIKE 'email_logs'");
if ($emailLogsTableCheck->num_rows == 0) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS email_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient_email VARCHAR(255) NOT NULL,
            recipient_name VARCHAR(255),
            subject VARCHAR(255) NOT NULL,
            body TEXT,
            status ENUM('sent', 'failed') NOT NULL,
            error_message TEXT,
            email_type VARCHAR(50) NOT NULL,
            campaign_id INT DEFAULT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sent_at (sent_at),
            INDEX idx_status (status),
            INDEX idx_email_type (email_type),
            FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE SET NULL
        )
    ");
} else {
    // Migration: Add campaign_id to email_logs if missing
    $cols = $conn->query("SHOW COLUMNS FROM email_logs LIKE 'campaign_id'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE email_logs ADD COLUMN campaign_id INT DEFAULT NULL AFTER email_type");
        $conn->query("ALTER TABLE email_logs ADD CONSTRAINT fk_email_logs_campaign FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE SET NULL");
    }
}

// Include Email Logger Helper
require_once __DIR__ . '/lib/email_logger.php';

