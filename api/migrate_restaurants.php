<?php
include __DIR__ . '/../config.php';

$sql = "
CREATE TABLE IF NOT EXISTS restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    discount_percentage INT DEFAULT 0,
    custom_price DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if ($conn->query($sql) === TRUE) {
    echo "Table 'restaurants' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
