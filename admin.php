<?php
session_start();
include "config.php";

if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

$admin_password = "AfrylAdmin"; 
if (!isset($_SESSION['admin_auth'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $admin_password) { $_SESSION['admin_auth'] = true; } 
    else { 
        die('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { background: #070a13; font-family: sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
                form { background: rgba(255,255,255,0.03); padding: 40px; border: 1px solid #c5a059; border-radius: 8px; text-align: center; width: 100%; max-width: 320px; }
                h2 { color: #c5a059; margin-bottom: 20px; font-weight: 300; letter-spacing: 2px; }
                input { width: 100%; padding: 12px; margin-bottom: 20px; background: transparent; border: 1px solid #222; color: white; border-radius: 4px; box-sizing: border-box; }
                button { background: #c5a059; border: none; padding: 12px; width: 100%; color: #070a13; font-weight: bold; cursor: pointer; border-radius: 4px; transition: 0.3s; }
                button:hover { background: white; }
            </style>
        </head>
        <body>
            <form method="POST">
                <h2>PORTAL ACCESS</h2>
                <input type="password" name="pass" placeholder="Enter Password" autofocus>
                <button type="submit">LOGIN</button>
            </form>
        </body>
        </html>'); 
    }
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderation | Afryl Lou Okit</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --gold: #c5a059; --bg: #070a13; --card: rgba(255,255,255,0.03); --text: #94a3b8; }
        
        body { background: var(--bg); color: var(--text); font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid rgba(197, 160, 89, 0.2); margin-bottom: 30px; }
        .header h1 { color: white; font-size: 1.5rem; letter-spacing: 1px; }
        .nav-links a { color: var(--text); text-decoration: none; margin-left: 20px; font-size: 0.8rem; font-weight: bold; transition: 0.3s; }
        .nav-links a:hover { color: var(--gold); }
        .logout { color: #ef4444 !important; }

        /* Table Design */
        .table-container { background: var(--card); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 20px; background: rgba(0,0,0,0.2); color: var(--gold); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: top; }
        
        /* Typography */
        .reviewer-name { color: white; font-weight: 700; font-size: 1rem; display: block; }
        .reviewer-pos { color: var(--gold); font-size: 0.75rem; text-transform: uppercase; font-weight: 600; margin-top: 4px; display: block; }
        .reviewer-co { font-size: 0.8rem; opacity: 0.6; }
        .review-text { font-style: italic; line-height: 1.6; max-width: 400px; }

        /* Badges & Buttons */
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 0.65rem; font-weight: 900; letter-spacing: 1px; }
        .status-pending { background: rgba(251, 191, 36, 0.1); color: #fbbf24; border: 1px solid #fbbf24; }
        .status-approved { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid #4ade80; }

        .btn { padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; }
        .btn-approve { background: var(--gold); color: var(--bg); }
        .btn-approve:hover { background: white; }
        .btn-delete { color: #ef4444; }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.1); }

        /* Responsive Fix */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header { flex-direction: column; gap: 15px; text-align: center; }
            .nav-links a { margin: 0 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-halved" style="color:var(--gold); margin-right:10px;"></i>Moderation Dashboard</h1>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-eye"></i> LIVE SITE</a>
                <a href="?logout=true" class="logout"><i class="fas fa-power-off"></i> LOGOUT</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Reviewer Identity</th>
                        <th>Location</th>
                        <th>Testimonial</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list as $item): ?>
                    <tr>
                        <td>
                            <span class="reviewer-name"><?= htmlspecialchars($item['name']) ?></span>
                            <span class="reviewer-pos"><?= htmlspecialchars($item['position'] ?? 'No Title') ?></span>
                            <span class="reviewer-co"><?= htmlspecialchars($item['company']) ?></span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <?php if(!empty($item['country_code'])): ?>
                                    <img src="https://flagcdn.com/w20/<?= strtolower($item['country_code']) ?>.png" width="20" style="border-radius:2px;">
                                    <span style="color:white; font-size:0.8rem;"><?= strtoupper($item['country_code']) ?></span>
                                <?php else: ?>
                                    <span style="opacity:0.3;">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="review-text">"<?= htmlspecialchars($item['comment_text']) ?>"</td>
                        <td>
                            <span class="badge status-<?= $item['status'] ?>">
                                <?= strtoupper($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <?php if($item['status'] == 'pending'): ?>
                                    <a href="?action=approve&id=<?= $item['id'] ?>" class="btn btn-approve">
                                        <i class="fas fa-check"></i> APPROVE
                                    </a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?= $item['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this review forever?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
