<?php
try {

    $db = new SQLite3(__DIR__ . "/../sirket.db");


    $db->exec("
        CREATE TABLE IF NOT EXISTS User (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user'
        )
    ");


} catch (Exception $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>