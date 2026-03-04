<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $company = $_POST['company'];
    $comment_text = $_POST['comment_text']; // Matches the new SQL column

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (name, company, comment_text, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$name, $company, $comment_text]);
        
        echo "success";
    } catch (Exception $e) {
        echo "error";
    }
}
?>