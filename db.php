<?php
// db.php
try {
    // Creates or connects to a file named 'foodel.sqlite' in the same folder
    $pdo = new PDO('sqlite:' . __DIR__ . '/fdl.db');
    
    // Set error mode to exceptions so we can catch bugs easily
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Turn on Foreign Key support
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>