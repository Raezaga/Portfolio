<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $company = $_POST['company'] ?? '';
    $comment_text = $_POST['comment_text'] ?? '';

    if (!empty($name) && !empty($comment_text)) {
        try {
            // Inserting into the specific columns from your screenshot
            $stmt = $pdo->prepare("INSERT INTO comments (name, company, comment_text, status) VALUES (?, ?, ?, 'approved')");
            $stmt->execute([$name, $company, $comment_text]);
            echo "success";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>
