<?php
/**
 * DYNAMIC CORS HEADERS
 * This version automatically detects the source domain to allow 
 * the cross-domain connection to work seamlessly.
 */
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// You can eventually restrict this to your specific new domain for better security
// Example: if ($origin == "https://your-new-domain.com") { ... }
header("Access-Control-Allow-Origin: " . $origin); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight 'OPTIONS' requests (The "Security Handshake")
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $company = htmlspecialchars($_POST['company'] ?? '');
    $position = htmlspecialchars($_POST['position'] ?? '');
    $comment_text = htmlspecialchars($_POST['comment_text'] ?? '');
    $country_code = htmlspecialchars($_POST['country_code'] ?? '');

    if (!empty($name) && !empty($position) && !empty($comment_text)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (name, company, position, comment_text, country_code, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $result = $stmt->execute([$name, $company, $position, $comment_text, $country_code]);

            if ($result) {
                sendNotification($name, $company, $position, $comment_text, $country_code);
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

function sendNotification($name, $company, $position, $text, $country) {
    $apiKey = 're_cRp2zLxY_bxKmdgM25fHeBK8PFrvgrbUq'; 
    $url = 'https://api.resend.com/emails';

    $flag_label = strtoupper($country);

    $data = [
        'from' => 'Portfolio <onboarding@resend.dev>',
        'to' => ['afryllou.consulting@gmail.com'],
        'subject' => 'New Review Awaiting Approval: ' . $name . ' (' . $position . ')',
        'html' => "
            <div style='font-family: sans-serif; padding: 20px; color: #1e293b;'>
                <h2 style='color: #0f172a;'>New Review Submitted</h2>
                <p>A new testimonial has been submitted and is currently <strong>PENDING</strong>.</p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Position:</strong> {$position}</p> <p><strong>Company:</strong> {$company}</p>
                <p><strong>Location:</strong> {$flag_label}</p>
                <p style='background: #f1f5f9; padding: 15px; border-radius: 8px;'>
                    <strong>Review Text:</strong><br>{$text}
                </p>
                <p style='margin-top: 20px;'>
                    <a href='https://afrylouokit.onrender.com/' style='background: #c5a059; color: #070a13; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 5px;'>Log in to Approve Review</a>
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
