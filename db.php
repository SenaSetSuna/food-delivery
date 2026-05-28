<?php
// db.php
try {
    // Creates or connects to a file named 'foodel.sqlite' in the same folder
    $pdo = new PDO('sqlite:' . __DIR__ . '/fdl.db');
    
    // Set error mode to exceptions so we can catch bugs easily
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Turn on Foreign Key support
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Ensure admin table exists and seed a default admin if none exists.
    $pdo->exec("CREATE TABLE IF NOT EXISTS User_admin (
        id_admin INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        admin_profile TEXT
    )");

    // Ensure the Transaction table has a payment method column for checkout.
    $transactionCols = $pdo->query('PRAGMA table_info("Transaction")')->fetchAll(PDO::FETCH_ASSOC);
    $hasPaymentMethod = false;
    foreach ($transactionCols as $col) {
        if ($col['name'] === 'payment_method') {
            $hasPaymentMethod = true;
            break;
        }
    }
    if (!$hasPaymentMethod) {
        $pdo->exec('ALTER TABLE "Transaction" ADD COLUMN payment_method TEXT DEFAULT "Qris"');
    }

    $adminCount = $pdo->query("SELECT COUNT(*) FROM User_admin")->fetchColumn();
    if ($adminCount == 0) {
        $stmtAdmin = $pdo->prepare("INSERT INTO User_admin (name, email, password, admin_profile) VALUES (?, ?, ?, ?)");
        $stmtAdmin->execute(['Administrator', 'admin@gmail.com', password_hash('admin123', PASSWORD_DEFAULT), '']);
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>