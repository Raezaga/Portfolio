<?php
session_start();
include "config.php";

if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

// Simple Login Protection
$admin_password = "AfrylAdmin2024!"; 
if (!isset($_SESSION['admin_auth'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $admin_password) { $_SESSION['admin_auth'] = true; } 
    else { die('<body style="background:#070a13; color:white; display:flex; justify-content:center; align-items:center; height:100vh;"><form method="POST" style="border:1px solid #c5a059; padding:40px; text-align:center;"><h2 style="color:#c5a059">Admin Access</h2><input type="password" name="pass" placeholder="Password" style="padding:10px; margin-bottom:10px; display:block; width:200px;"><button type="submit" style="background:#c5a059; border:none; padding:10px 20px; cursor:pointer;">Login</button></form></body>'); }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'approve') { $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$id]); } 
    elseif ($_GET['action'] == 'delete') { $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]); }
    header("Location: admin.php"); exit;
}

$list = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Moderation | Afryl Lou Okit</title>
    <style>
        body { background: #070a13; color: #94a3b8; font-family: sans-serif; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c5a059; padding-bottom: 20px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #c5a059; padding: 15px; border-bottom: 2px solid #222; font-size: 0.8rem; }
        td { padding: 15px; border-bottom: 1px solid #222; }
        .flag-box { display: flex; align-items: center; gap: 8px; font-weight: bold; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color:white;">Review Moderation</h1>
        <div>
            <a href="index.php" style="color:#c5a059; text-decoration:none; margin-right:20px;">View Site</a>
            <a href="?logout=true" style="color:#ef4444; text-decoration:none;">Logout</a>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>User & Company</th>
                <th>Country</th> <th>Review</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list as $item): ?>
            <tr>
                <td><strong><?= htmlspecialchars($item['name']) ?></strong><br><small><?= htmlspecialchars($item['company']) ?></small></td>
                <td>
                    <div class="flag-box">
                        <?php if(!empty($item['country_code'])): ?>
                            <img src="https://flagcdn.com/w20/<?= strtolower($item['country_code']) ?>.png" width="20">
                            <?= strtoupper($item['country_code']) ?>
                        <?php else: ?>
                            <span style="opacity:0.3;">None</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="font-style:italic;">"<?= htmlspecialchars($item['comment_text']) ?>"</td>
                <td style="color:<?= $item['status']=='approved'?'#4ade80':'#fbbf24' ?>"><?= strtoupper($item['status']) ?></td>
                <td>
                    <?php if($item['status'] == 'pending'): ?>
                        <a href="?action=approve&id=<?= $item['id'] ?>" style="color:#c5a059;">Approve</a> | 
                    <?php endif; ?>
                    <a href="?action=delete&id=<?= $item['id'] ?>" style="color:#ef4444;" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
