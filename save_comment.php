<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $company = htmlspecialchars($_POST['company'] ?? '');
    $comment_text = htmlspecialchars($_POST['comment_text'] ?? '');

    if (!empty($name) && !empty($comment_text)) {
        try {
            // UPDATED: Changed status to 'pending' so it requires manual admin approval
            $stmt = $pdo->prepare("INSERT INTO comments (name, company, comment_text, status) VALUES (?, ?, ?, 'pending')");
            $result = $stmt->execute([$name, $company, $comment_text]);

            if ($result) {
                // Trigger Resend Notification to your email
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
    // Your provided Resend API Key
    $apiKey = 're_cRp2zLxY_bxKmdgM25fHeBK8PFrvgrbUq'; 
    $url = 'https://api.resend.com/emails';

    $data = [
        'from' => 'Portfolio <onboarding@resend.dev>',
        'to' => ['afryllou.consulting@gmail.com'],
        'subject' => 'New Review Awaiting Approval: ' . $name,
        'html' => "
            <div style='font-family: sans-serif; padding: 20px; color: #1e293b;'>
                <h2 style='color: #0f172a;'>New Review Submitted</h2>
                <p>A new testimonial has been submitted and is currently <strong>PENDING</strong>.</p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Company:</strong> {$company}</p>
                <p style='background: #f1f5f9; padding: 15px; border-radius: 8px;'>
                    <strong>Review Text:</strong><br>{$text}
                </p>
                <p style='margin-top: 20px;'>
                    <a href='http://yourdomain.com/admin.php' style='background: #c5a059; color: #070a13; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 5px;'>Log in to Approve Review</a>
                </p>
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
