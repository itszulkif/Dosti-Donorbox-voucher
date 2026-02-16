<?php
include __DIR__ . '/../config.php';

$sql = "ALTER TABLE admins ADD COLUMN IF NOT EXISTS restaurant_id INT NULL AFTER role";

if ($conn->query($sql) === TRUE) {
    // Add foreign key if it doesn't exist
    $fkCheck = $conn->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = 'fk_admin_restaurant' AND TABLE_SCHEMA = '$dbname'");
    if ($fkCheck->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD CONSTRAINT fk_admin_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE SET NULL");
    }
    echo "Table 'admins' updated successfully with 'restaurant_id' column.";
} else {
    echo "Error updating table: " . $conn->error;
}
$conn->close();
?>
