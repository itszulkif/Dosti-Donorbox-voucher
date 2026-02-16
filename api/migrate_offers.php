<?php
// Fix path to config
include __DIR__ . '/../config.php';

// One-time script to add the new table
$sql = "
CREATE TABLE IF NOT EXISTS donor_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    restaurant_name VARCHAR(255) NOT NULL,
    restaurant_address TEXT,
    offer_type ENUM('percentage', 'fixed') NOT NULL,
    offer_value DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
);
";

if ($conn->query($sql) === TRUE) {
    echo "Table 'donor_offers' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
