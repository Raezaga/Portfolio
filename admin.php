<?php
session_start();
include "config.php";

// CHANGE THIS PASSWORD FOR YOUR SECURITY
$admin_password = "AfrylAdmin2024!"; 

// 1. Simple Login Protection
if (!isset($_SESSION['admin_auth'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $admin_password) {
        $_SESSION['admin_auth'] = true;
    } else {
        die('
        <body style="background:#070a13; color:white; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh;">
            <form method="POST" style="border:1px solid #c5a059; padding:40px; text-align:center;">
                <h2 style="color:#c5a059">Executive Access</h2>
                <input type="password" name="pass" placeholder="Admin Password" style="padding:10px; margin-bottom:10px; display:block; width:200px; background:#000; border:1px solid #333; color:white;">
                <button type="submit" style="background:#c5a059; border:none; padding:10px 20px; cursor:pointer; font-weight:bold;">Login</button>
            </form>
        </body>');
    }
}

// 2. Handling Your Approvals
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'approve') {
        $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] == 'delete') {
        $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
    }
    header("Location: admin.php");
    exit;
}

// 3. Fetch all reviews for you to see
$list = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Review Manager</title>
    <style>
        body { background: #070a13; color: #94a3b8; font-family: sans-serif; padding: 50px; }
        h1 { color: white; border-bottom: 1px solid #c5a059; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th { text-align: left; color: #c5a059; font-size: 0.8rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #222; }
        td { padding: 15px; border-bottom: 1px solid #222; }
        .status-pending { color: #fbbf24; font-weight: bold; }
        .status-live { color: #4ade80; font-weight: bold; }
        .btn { padding: 5px 12px; text-decoration: none; border-radius: 3px; font-size: 0.75rem; font-weight: bold; }
        .btn-app { background: #c5a059; color: #070a13; margin-right: 5px; }
        .btn-del { border: 1px solid #ef4444; color: #ef4444; }
    </style>
</head>
<body>
    <h1>Review Moderation Center</h1>
    <table>
        <tr>
            <th>Name & Company</th>
            <th>Review Text</th>
            <th>Current Status</th>
            <th>Your Action</th>
        </tr>
        <?php foreach($list as $item): ?>
        <tr>
            <td><strong><?= htmlspecialchars($item['name']) ?></strong><br><small><?= htmlspecialchars($item['company']) ?></small></td>
            <td style="max-width:400px; font-style:italic;">"<?= htmlspecialchars($item['comment_text']) ?>"</td>
            <td>
                <span class="<?= $item['status'] == 'approved' ? 'status-live' : 'status-pending' ?>">
                    <?= strtoupper($item['status']) ?>
                </span>
            </td>
            <td>
                <?php if($item['status'] == 'pending'): ?>
                    <a href="?action=approve&id=<?= $item['id'] ?>" class="btn btn-app">APPROVE TO LIVE</a>
                <?php endif; ?>
                <a href="?action=delete&id=<?= $item['id'] ?>" class="btn btn-del" onclick="return confirm('Permanently delete this?')">DELETE</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
