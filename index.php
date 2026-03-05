<?php
include "config.php";

// DATABASE LOGIC: Fetch Reviews for the Page
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);

    $stmt = $pdo->prepare("SELECT * FROM comments WHERE status = 'approved' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $comments = []; $total_pages = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afryl Lou Okit | Senior Financial Operations Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #070a13; --card-bg: rgba(17, 24, 39, 0.7); --gold: #c5a059; --slate: #94a3b8; --white: #ffffff; --transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--slate); line-height: 1.7; overflow-x: hidden; }

        nav { position: fixed; top: 0; width: 100%; padding: 25px 8%; background: rgba(7, 10, 19, 0.9); backdrop-filter: blur(20px); display: flex; justify-content: space-between; z-index: 1000; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        nav h1 { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--white); letter-spacing: 2px; text-transform: uppercase; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        nav ul a:hover { color: var(--gold); }

        section { padding: 120px 10% 80px; max-width: 1500px; margin: 0 auto; }

        .hero { display: flex; align-items: center; gap: 60px; min-height: 100vh; }
        .hero-text { flex: 1.1; }
        .hero-text h2 { font-family: 'Playfair Display', serif; font-size: 5.5rem; color: var(--white); line-height: 1.1; font-weight: 700; letter-spacing: -3px; }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        .hero-image { flex: 0.9; position: relative; display: flex; justify-content: center; }
        .img-wrapper { position: relative; width: 100%; max-width: 580px; aspect-ratio: 1/1; border-radius: 50%; border: 3px solid var(--gold); overflow: hidden; background: var(--bg); z-index: 2; box-shadow: 0 0 80px rgba(0,0,0,0.6); }
        .hero-image img { width: 115%; height: 115%; object-fit: cover; object-position: center 20%; margin-left: -7.5%; }

        .btn-gold { padding: 22px 45px; background: var(--gold); color: var(--bg); border: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition); display: inline-block; }
        .btn-outline { padding: 22px 45px; border: 2px solid var(--gold); color: var(--gold); font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition); display: inline-block; margin-left: 15px; }
        .btn-gold:hover, .btn-outline:hover { background: var(--white); color: var(--bg); border-color: var(--white); transform: translateY(-5px); }

        .sw-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 40px; }
        .sw-pill { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 25px; text-align: center; transition: var(--transition); }
        .sw-pill i { color: var(--gold); font-size: 1.8rem; margin-bottom: 12px; display: block; }
        .sw-pill span { color: var(--white); font-weight: 700; font-size: 0.7rem; letter-spacing: 2px; }

        .glass-card { display: grid; grid-template-columns: 1fr 1fr; gap: 100px; background: var(--card-bg); padding: 80px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 40px 100px rgba(0,0,0,0.5); }
        .form-box input, .form-box textarea { width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; color: var(--white); outline: none; font-family: inherit; }

        .feedback-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 60px; }
        .feedback-item { background: var(--card-bg); padding: 50px; border: 1px solid rgba(255,255,255,0.03); transition: 0.3s; }
        .pagination a { padding: 12px 20px; border: 1px solid rgba(255,255,255,0.1); color: var(--white); text-decoration: none; margin: 0 5px; }
        .pagination a.active { background: var(--gold); color: var(--bg); }

        footer { padding: 60px 10%; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; }

        @media (max-width: 1100px) { .hero { flex-direction: column; text-align: center; } .btn-outline { margin-left: 0; margin-top: 15px; } .glass-card { grid-template-columns: 1fr; } .hero-text h2 { font-size: 3.5rem; } }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT / PARTNER</h1>
    <ul><li><a href="#hero">Overview</a></li><li><a href="#connect">Connect</a></li><li><a href="#feedback">Validation</a></li></ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div style="border: 1px solid var(--gold); padding: 10px 20px; display: inline-block; color: var(--gold); margin-bottom: 25px; letter-spacing: 4px; font-weight: 800; font-size: 0.7rem;">17+ YEARS EXECUTIVE EXPERTISE</div>
        <h2>Transforming <span>Complex</span> Financials.</h2>
        <p style="font-size: 1.3rem; font-weight: 300; margin-bottom: 45px; max-width: 650px; color: var(--slate);">
           Senior Financial Operations Partner specializing in audit-ready financials, intercompany reconciliations, and structured growth for international businesses.
        </p>
        <a href="#connect" class="btn-gold">Secure Partnership</a>
        <a href="Afryl_Lou_Okit_CV.pdf" target="_blank" class="btn-outline"><i class="fas fa-file-download" style="margin-right: 10px;"></i>Download CV</a>
    </div>
    <div class="hero-image">
        <div class="img-wrapper"><img src="afryl.jpg" alt="Afryl Lou Okit"></div>
    </div>
</section>

<section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 50px; border-top: 1px solid rgba(255,255,255,0.05);">
    <div>
        <h4 style="color: var(--gold); letter-spacing: 3px; font-size: 0.8rem; margin-bottom: 20px;">01. MULTI-ENTITY STRUCTURE</h4>
        <p>I specialize in bringing clarity to international portfolios across the US, Australia, and the Middle East.</p>
    </div>
    <div>
        <h4 style="color: var(--gold); letter-spacing: 3px; font-size: 0.8rem; margin-bottom: 20px;">02. AUDIT-READY BOOKS</h4>
        <p>Transforming disorganized records into decision-ready financials every single month—no surprises.</p>
    </div>
    <div>
        <h4 style="color: var(--gold); letter-spacing: 3px; font-size: 0.8rem; margin-bottom: 20px;">03. OPERATIONAL INSIGHT</h4>
        <p>Identifying risks and inefficiencies early through budget vs. actual analysis.</p>
    </div>
</section>

<section style="background: rgba(255,255,255,0.01); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="text-align: center; margin-bottom: 40px;"><span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">TECHNOLOGY INFRASTRUCTURE</span></div>
    <div class="sw-grid">
        <div class="sw-pill"><i class="fas fa-database"></i><span>NETSUITE</span></div>
        <div class="sw-pill"><i class="fas fa-calculator"></i><span>QUICKBOOKS</span></div>
        <div class="sw-pill"><i class="fas fa-chart-line"></i><span>XERO ADVISOR</span></div>
        <div class="sw-pill"><i class="fas fa-file-invoice-dollar"></i><span>BILL.COM / DEXT</span></div>
        <div class="sw-pill"><i class="fas fa-credit-card"></i><span>STRIPE / VOYAGE</span></div>
        <div class="sw-pill"><i class="fas fa-tasks"></i><span>CLICKUP / FLOQAST</span></div>
    </div>
</section>

<section id="connect">
    <div class="glass-card">
        <div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white; line-height: 1;">Let's Connect</h3>
            <p style="margin: 25px 0; font-size: 1.1rem;">Secure financial partnership for international entities.</p>
            <?php if(isset($_GET['mail']) && $_GET['mail'] == 'sent'): ?>
                <p style="color: var(--gold); font-weight: 800; border: 1px solid var(--gold); padding: 15px; display: inline-block;">✓ MESSAGE SENT TO GMAIL</p>
            <?php endif; ?>
        </div>
        <div class="form-box">
            <form action="send_email.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Organization" required>
                <input type="email" name="email" placeholder="Professional Email" required>
                <textarea name="message" rows="4" placeholder="How can I assist your financials?" required></textarea>
                <button type="submit" class="btn-gold" style="width: 100%;">Send Inquiry</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback" style="border-top: 1px solid rgba(255,255,255,0.05);">
    <h3 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white;">Executive Proof</h3>
    <div class="feedback-grid">
        <?php if(!empty($comments)): foreach ($comments as $row): ?>
            <div class="feedback-item">
                <p style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--white); font-style: italic; line-height: 1.6; margin-bottom: 30px;">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                <p style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold);">— <?php echo htmlspecialchars($row['name']); ?> / <?php echo htmlspecialchars($row['company']); ?></p>
            </div>
        <?php endforeach; else: ?>
            <p style="text-align: center; grid-column: 1/-1;">Awaiting professional validation.</p>
        <?php endif; ?>
    </div>
</section>

<section style="background: rgba(255, 255, 255, 0.02); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <h4 style="color: white; font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 20px;">Leave a Review</h4>
        <form id="reviewForm" class="form-box">
            <input type="text" name="name" placeholder="Display Name" required>
            <input type="text" name="company" placeholder="Organization" required>
            <textarea name="comment_text" rows="3" placeholder="Write your testimonial..." required></textarea>
            <button type="submit" id="reviewBtn" class="btn-gold">Post to Website</button>
        </form>
    </div>
</section>

<footer>
    <p>PH: +63 999 586 6190 | E: afryllou.consulting@gmail.com</p>
    <p style="margin-top: 20px;">&copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.</p>
</footer>

<script>
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('reviewBtn');
        btn.innerHTML = 'POSTING...';
        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(() => { alert("Review saved to database!"); location.reload(); });
    });
</script>
</body>
</html>
