<?php
session_start();
include "config.php";

// Simple Password Protection (Change 'admin123' to your preferred password)
$admin_password = "admin123"; 

if (!isset($_SESSION['loggedin'])) {
    if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
        $_SESSION['loggedin'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login</title>
            <style>
                body { background: #070a13; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .login-box { background: rgba(255,255,255,0.05); padding: 40px; border: 1px solid #c5a059; text-align: center; }
                input { padding: 10px; margin-bottom: 20px; width: 200px; display: block; margin: 10px auto; border: 1px solid #333; background: #000; color: white; }
                button { background: #c5a059; color: #070a13; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2 style="color:#c5a059">Executive Access</h2>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter Admin Password">
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Handle Status Updates (Approve/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'unapprove') {
        $pdo->prepare("UPDATE comments SET status = 'pending' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
        $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
    }
    header("Location: admin.php");
    exit;
}

// Fetch all comments
$stmt = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC");
$all_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Afryl Lou Okit</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --bg: #070a13; --gold: #c5a059; --white: #ffffff; }
        body { background: var(--bg); color: #94a3b8; font-family: 'Inter', sans-serif; padding: 50px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; padding-bottom: 20px; margin-bottom: 40px; }
        h1 { color: var(--white); font-family: serif; letter-spacing: 2px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.02); }
        th { text-align: left; padding: 20px; border-bottom: 1px solid #333; color: var(--gold); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; }
        td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #ccc; vertical-align: top; }
        
        .status-pill { padding: 5px 10px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; text-transform: uppercase; }
        .approved { background: #1a2e1a; color: #4ade80; }
        .pending { background: #2e261a; color: #fbbf24; }
        
        .btn { padding: 8px 15px; text-decoration: none; font-size: 0.7rem; font-weight: bold; border-radius: 3px; transition: 0.3s; margin-right: 5px; }
        .btn-approve { background: #c5a059; color: #070a13; }
        .btn-delete { border: 1px solid #ef4444; color: #ef4444; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
        
        .logout { color: #ef4444; text-decoration: none; font-size: 0.8rem; border: 1px solid #ef4444; padding: 5px 15px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Review Management</h1>
    <a href="?logout=true" class="logout">Logout</a>
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Name / Company</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_comments as $c): ?>
        <tr>
            <td><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
            <td>
                <strong style="color:white"><?php echo htmlspecialchars($c['name']); ?></strong><br>
                <small><?php echo htmlspecialchars($c['company']); ?></small>
            </td>
            <td style="max-width: 400px; font-style: italic;">"<?php echo htmlspecialchars($c['comment_text']); ?>"</td>
            <td>
                <span class="status-pill <?php echo $c['status'] == 'approved' ? 'approved' : 'pending'; ?>">
                    <?php echo $c['status']; ?>
                </span>
            </td>
            <td>
                <?php if ($c['status'] == 'pending'): ?>
                    <a href="?action=approve&id=<?php echo $c['id']; ?>" class="btn btn-approve">Approve</a>
                <?php else: ?>
                    <a href="?action=unapprove&id=<?php echo $c['id']; ?>" class="btn" style="border: 1px solid #94a3b8; color: #94a3b8;">Hide</a>
                <?php endif; ?>
                <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this review permanently?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>