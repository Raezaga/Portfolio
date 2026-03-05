<?php
session_start();
include "config.php";

// 1. LOGOUT LOGIC: Clears session and redirects to index.php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Simple Login Protection (Use your actual password here)
$admin_password = "Afryl2026"; 

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

// Handling Approvals/Deletions
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

$list = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel | Afryl Lou Okit</title>
    <style>
        body { background: #070a13; color: #94a3b8; font-family: sans-serif; padding: 40px; }
        
        /* HEADER BAR FIX */
        .header-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #c5a059; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        h1 { color: white; margin: 0; font-size: 1.5rem; letter-spacing: 1px; }
        
        .nav-group { display: flex; gap: 15px; }
        .btn-nav { 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 0.75rem; 
            padding: 10px 20px; 
            border-radius: 4px; 
            transition: 0.3s;
            text-transform: uppercase;
        }
        .view-site { color: #c5a059; border: 1px solid #c5a059; }
        .view-site:hover { background: #c5a059; color: #070a13; }
        
        /* THE LOGOUT BUTTON */
        .logout-btn { color: #ef4444; border: 1px solid #ef4444; }
        .logout-btn:hover { background: #ef4444; color: white; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #c5a059; font-size: 0.7rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #222; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid #222; }
        
        .status-pending { color: #fbbf24; font-weight: bold; font-size: 0.8rem; }
        .status-approved { color: #4ade80; font-weight: bold; font-size: 0.8rem; }
        
        .action-btn { padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
        .btn-app { background: #c5a059; color: #070a13; margin-right: 8px; }
        .btn-del { border: 1px solid #ef4444; color: #ef4444; }
    </style>
</head>
<body>

    <div class="header-bar">
        <h1>Review Moderation Center</h1>
        <div class="nav-group">
            <a href="index.php" class="btn-nav view-site">View Website</a>
            <a href="?logout=true" class="btn-nav logout-btn">Logout</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name & Company</th>
                <th>Review Text</th>
                <th>Current Status</th>
                <th>Your Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list as $item): ?>
            <tr>
                <td>
                    <strong style="color:white;"><?= htmlspecialchars($item['name']) ?></strong><br>
                    <small><?= htmlspecialchars($item['company']) ?></small>
                </td>
                <td style="max-width:400px; font-style:italic;">"<?= htmlspecialchars($item['comment_text']) ?>"</td>
                <td>
                    <span class="<?= $item['status'] == 'approved' ? 'status-approved' : 'status-pending' ?>">
                        <?= strtoupper($item['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if($item['status'] == 'pending'): ?>
                        <a href="?action=approve&id=<?= $item['id'] ?>" class="action-btn btn-app">Approve to Live</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?= $item['id'] ?>" class="action-btn btn-del" onclick="return confirm('Delete this review permanently?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
