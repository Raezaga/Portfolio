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
                body { background: #070a13; font-family: sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; overflow: hidden; }
                .login-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); padding: 50px; border: 1px solid rgba(197, 160, 89, 0.3); border-radius: 20px; text-align: center; width: 100%; max-width: 350px; animation: fadeIn 0.8s ease; }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                h2 { color: #c5a059; margin-bottom: 30px; font-weight: 300; letter-spacing: 5px; text-transform: uppercase; font-size: 1.2rem; }
                input { width: 100%; padding: 15px; margin-bottom: 20px; background: rgba(0,0,0,0.3); border: 1px solid #333; color: white; border-radius: 8px; box-sizing: border-box; outline: none; transition: 0.3s; }
                input:focus { border-color: #c5a059; box-shadow: 0 0 10px rgba(197, 160, 89, 0.2); }
                button { background: #c5a059; border: none; padding: 15px; width: 100%; color: #070a13; font-weight: 800; cursor: pointer; border-radius: 8px; transition: 0.4s; letter-spacing: 2px; }
                button:hover { background: white; transform: scale(1.02); }
            </style>
        </head>
        <body>
            <div class="login-card">
                <h2>Portal Access</h2>
                <form method="POST">
                    <input type="password" name="pass" placeholder="••••••••" autofocus>
                    <button type="submit">UNLOCK</button>
                </form>
            </div>
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
        :root { --gold: #c5a059; --bg: #070a13; --card: rgba(255,255,255,0.02); --text: #94a3b8; --border: rgba(255,255,255,0.07); }
        
        body { background: var(--bg); color: var(--text); font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding: 30px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; animation: slideUp 0.6s ease-out; }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; margin-bottom: 40px; }
        .header h1 { color: white; font-size: 1.8rem; letter-spacing: -1px; font-weight: 800; }
        .header span { color: var(--gold); }
        .nav-links a { color: var(--text); text-decoration: none; margin-left: 25px; font-size: 0.75rem; font-weight: 800; letter-spacing: 2px; transition: 0.3s; text-transform: uppercase; }
        .nav-links a:hover { color: white; }
        .logout { color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.2); padding: 8px 15px; border-radius: 5px; }
        .logout:hover { background: #ef4444; color: white !important; }

        /* Table Design with Effects */
        .table-container { background: var(--card); backdrop-filter: blur(10px); border: 1px solid var(--border); border-radius: 15px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { text-align: left; padding: 25px 20px; background: rgba(255,255,255,0.03); color: var(--gold); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid var(--border); }
        
        tr { transition: 0.3s; }
        tr:hover { background: rgba(255,255,255,0.03); }
        td { padding: 25px 20px; border-bottom: 1px solid var(--border); vertical-align: top; }
        
        /* Elements */
        .reviewer-name { color: white; font-weight: 700; font-size: 1.1rem; }
        .reviewer-pos { color: var(--gold); font-size: 0.7rem; text-transform: uppercase; font-weight: 800; display: block; margin-top: 5px; letter-spacing: 1px; }
        .reviewer-co { font-size: 0.8rem; opacity: 0.5; display: block; margin-top: 2px; }
        
        .review-text { color: #cbd5e1; font-style: italic; max-width: 450px; font-size: 0.95rem; line-height: 1.8; }

        .status-badge { padding: 6px 12px; border-radius: 100px; font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; display: inline-block; }
        .status-pending { background: rgba(251, 191, 36, 0.1); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3); }
        .status-approved { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }

        .btn-circle { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; border: 1px solid var(--border); }
        .btn-approve { background: rgba(197, 160, 89, 0.1); color: var(--gold); }
        .btn-approve:hover { background: var(--gold); color: var(--bg); transform: rotate(15deg); }
        .btn-delete { color: #ef4444; margin-left: 10px; }
        .btn-delete:hover { background: #ef4444; color: white; }

        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; gap: 20px; }
            .nav-links a { margin: 0 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AFRYL <span>MODERATION</span></h1>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-external-link-alt"></i> Public Site</a>
                <a href="?logout=true" class="logout">Logout</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Executive</th>
                        <th>Origin</th>
                        <th>Testimonial</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                            <div style="display:flex; align-items:center; gap:10px;">
                                <?php if(!empty($item['country_code'])): ?>
                                    <img src="https://flagcdn.com/w40/<?= strtolower($item['country_code']) ?>.png" width="25" style="border-radius:3px; filter: grayscale(20%);">
                                    <span style="color:white; font-size:0.75rem; font-weight:800;"><?= strtoupper($item['country_code']) ?></span>
                                <?php else: ?>
                                    <span style="opacity:0.2;">N/A</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="review-text">"<?= htmlspecialchars($item['comment_text']) ?>"</td>
                        <td>
                            <div class="status-badge status-<?= $item['status'] ?>">
                                <?= strtoupper($item['status']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center;">
                                <?php if($item['status'] == 'pending'): ?>
                                    <a href="?action=approve&id=<?= $item['id'] ?>" class="btn-circle btn-approve" title="Approve Review">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?= $item['id'] ?>" class="btn-circle btn-delete" title="Delete Review" onclick="return confirm('Confirm permanent deletion?')">
                                    <i class="fas fa-trash-alt"></i>
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
