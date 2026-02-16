<?php
include __DIR__ . '/../config.php';

echo "Starting migration for donation_visits table...\n";

// Add amount_1 and amount_2 columns
$colsCheck = $conn->query("SHOW COLUMNS FROM donation_visits LIKE 'amount_1'");
if ($colsCheck->num_rows == 0) {
    if ($conn->query("ALTER TABLE donation_visits ADD COLUMN amount_1 DECIMAL(10,2) DEFAULT 0.00 AFTER amount")) {
        echo "Column 'amount_1' added successfully.\n";
    } else {
        echo "Error adding column 'amount_1': " . $conn->error . "\n";
    }
} else {
    echo "Column 'amount_1' already exists.\n";
}

$colsCheck2 = $conn->query("SHOW COLUMNS FROM donation_visits LIKE 'amount_2'");
if ($colsCheck2->num_rows == 0) {
    if ($conn->query("ALTER TABLE donation_visits ADD COLUMN amount_2 DECIMAL(10,2) DEFAULT 0.00 AFTER amount_1")) {
        echo "Column 'amount_2' added successfully.\n";
    } else {
        echo "Error adding column 'amount_2': " . $conn->error . "\n";
    }
} else {
    echo "Column 'amount_2' already exists.\n";
}

echo "Migration finished.\n";
?>
