<?php
// config.php

$host = 'aws-1-ap-northeast-2.pooler.supabase.com';
$db   = 'postgres';
$user = 'postgres.vgqgvnkjbknadykyqmoa';
$pass = 'Ohana!1210012';
$port = '5432';

// The Data Source Name (DSN) for PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    // This creates the $pdo variable that index.php is looking for
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // If connection fails, it will tell us why
    die("Database Connection Failed: " . $e->getMessage());
}
?>