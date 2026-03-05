<?php
// config.php
$host = 'aws-1-ap-northeast-2.pooler.supabase.com';
$db   = 'postgres';
$user = 'postgres.vgqgvnkjbknadykyqmoa';
$pass = 'Ohana!1210012';
$port = '5432';

// We use "sslmode=require" because Supabase often blocks non-SSL connections
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // This will print the error on the screen so you can see why it's failing
    echo "CONNECTION ERROR: " . $e->getMessage();
    exit;
}
?>
