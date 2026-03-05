<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $company = htmlspecialchars($_POST['company'] ?? '');
    $comment_text = htmlspecialchars($_POST['comment_text'] ?? '');

    if (!empty($name) && !empty($comment_text)) {
        try {
            // 1. Save to Supabase
            $stmt = $pdo->prepare("INSERT INTO comments (name, company, comment_text, status) VALUES (?, ?, ?, 'approved')");
            $result = $stmt->execute([$name, $company, $comment_text]);

            if ($result) {
                // 2. Trigger Resend Notification
                sendNotification($name, $company, $comment_text);
                echo "success";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo "Database error.";
        }
    } else {
        echo "missing_fields";
    }
}

function sendNotification($name, $company, $text) {
    // Your provided API Key
    $apiKey = 're_cRp2zLxY_bxKmdgM25fHeBK8PFrvgrbUq'; 
    $url = 'https://api.resend.com/emails';

    $data = [
        'from' => 'Portfolio <onboarding@resend.dev>',
        'to' => ['afryllou.consulting@gmail.com'],
        'subject' => 'New Portfolio Inquiry from ' . $name,
        'html' => "
            <div style='font-family: sans-serif; padding: 20px; color: #1e293b;'>
                <h2 style='color: #0f172a;'>New Inquiry Received</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Company:</strong> {$company}</p>
                <p style='background: #f1f5f9; padding: 15px; border-radius: 8px;'>
                    <strong>Message:</strong><br>{$text}
                </p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='font-size: 12px; color: #64748b;'>Sent via Resend API from your Portfolio Website.</p>
            </div>
        "
    ];

    $options = [
        'http' => [
            'header'  => [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ],
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
