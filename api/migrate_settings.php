<?php
include __DIR__ . '/../config.php';

echo "Running migrations...\n";

// 1. Create system_settings table
$conn->query("
    CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) UNIQUE NOT NULL,
        setting_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");
echo "Table 'system_settings' checked/created.\n";

// 2. Seed initial voucher price
$check = $conn->query("SELECT * FROM system_settings WHERE setting_key = 'voucher_price'");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO system_settings (setting_key, setting_value) VALUES ('voucher_price', '500')");
    echo "Default 'voucher_price' seeded.\n";
}

// 3. Add voucher_value to donors table
$cols = $conn->query("SHOW COLUMNS FROM donors LIKE 'voucher_value'");
if ($cols->num_rows == 0) {
    $conn->query("ALTER TABLE donors ADD COLUMN voucher_value DECIMAL(10,2) DEFAULT 500.00 AFTER voucher_id");
    echo "Column 'voucher_value' added to 'donors' table.\n";
}

echo "Migration completed successfully.";
?>
