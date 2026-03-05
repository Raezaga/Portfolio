<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Your Credentials
    $apiKey = 're_cRp2zLxY_bxKmdgM25fHeBK8PFrvgrbUq'; 
    $myEmail = 'afryllou.consulting@gmail.com';

    // 2. Sanitize form data from "Let's Connect"
    $name = strip_tags(trim($_POST["name"]));
    $company = strip_tags(trim($_POST["company"]));
    $sender_email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"]));

    // 3. Prepare the Email Payload for Resend
    $data = [
        "from" => "Portfolio Inquiry <onboarding@resend.dev>", 
        "to" => [$myEmail],
        "subject" => "New Project Inquiry: $name",
        "html" => "
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #eee; background-color: #f9f9f9;'>
                <h2 style='color: #c5a059;'>New Business Inquiry</h2>
                <p><strong>From:</strong> $name</p>
                <p><strong>Company:</strong> $company</p>
                <p><strong>Email:</strong> $sender_email</p>
                <hr style='border: 0; border-top: 1px solid #ddd;'>
                <p><strong>Message:</strong></p>
                <p style='white-space: pre-wrap;'>$message</p>
            </div>
        "
    ];

    // 4. Send via cURL to Resend API
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 5. Redirect back to the site with a status
    if ($httpCode == 200 || $httpCode == 201) {
        header("Location: index.php?mail=sent#connect");
    } else {
        header("Location: index.php?mail=error#connect");
    }
    exit;
}