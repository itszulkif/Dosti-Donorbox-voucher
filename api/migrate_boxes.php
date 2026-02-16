<?php
include __DIR__ . '/../config.php';

$sql = "
CREATE TABLE IF NOT EXISTS donation_shops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    box_number VARCHAR(50) UNIQUE NOT NULL,
    shop_name VARCHAR(255) NOT NULL,
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
    received_from VARCHAR(255),
    received_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES donation_shops(id) ON DELETE CASCADE
);
";

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "Tables 'donation_shops' and 'donation_visits' created successfully.\n";

    // Seed some mock data if empty
    $check = $conn->query("SELECT id FROM donation_shops LIMIT 1");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO donation_shops (box_number, shop_name, installation_date, contact_person, phone, address) VALUES 
        ('BOX-101', 'City General Store', '2025-01-10', 'Mr. Ahmed', '0300-1234567', 'Main Market, Block A'),
        ('BOX-102', 'Bismillah Pharmacy', '2025-01-15', 'Sharif Bhai', '0321-7654321', 'Station Road'),
        ('BOX-103', 'Food Palace', '2025-02-01', 'Zaid', '0333-1112223', 'University Road')");
        
        $conn->query("INSERT INTO donation_visits (shop_id, visit_date, amount, received_from, received_by) VALUES 
        (1, '2025-01-30', 4500.00, 'Ahmed', 'Admin'),
        (1, '2025-02-28', 5200.00, 'Ahmed', 'Staff Ali'),
        (2, '2025-02-15', 3000.00, 'Sharif', 'Admin'),
        (3, '2025-02-25', 1500.00, 'Zaid', 'Staff Ali')");
        
        echo "Mock data seeded successfully.\n";
    }
} else {
    echo "Error creating tables: " . $conn->error;
}
$conn->close();
?>
