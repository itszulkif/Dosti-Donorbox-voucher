<?php
include __DIR__ . '/../config.php';

// Add status column to donor_offers
$sql = "ALTER TABLE donor_offers ADD COLUMN status ENUM('Pending', 'Redeemed') DEFAULT 'Pending'";

if ($conn->query($sql) === TRUE) {
    echo "Column 'status' added to 'donor_offers' successfully.";
} else {
    // Check if column already exists to avoid error
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "Column 'status' already exists in 'donor_offers'.";
    } else {
        echo "Error updating table: " . $conn->error;
    }
}
$conn->close();
?>
