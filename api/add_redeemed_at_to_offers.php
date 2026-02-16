<?php
include __DIR__ . '/../config.php';

// Add redeemed_at column to donor_offers
$sql = "ALTER TABLE donor_offers ADD COLUMN redeemed_at TIMESTAMP NULL DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Column 'redeemed_at' added to 'donor_offers' successfully.";
} else {
    // Check if column already exists to avoid error
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "Column 'redeemed_at' already exists in 'donor_offers'.";
    } else {
        echo "Error updating table: " . $conn->error;
    }
}
$conn->close();
?>
