<?php
include "config.php";

// 1. PAGINATION & DATABASE LOGIC
$limit = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$page : 1;
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
} catch (Exception $e) { 
    $comments = []; 
    $total_pages = 0; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afryl Lou Okit | Senior Financial Operations Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'Gonzaga';
            src: url('fonts/Gonzaga.woff2') format('woff2'),
                 url('fonts/Gonzaga.ttf') format('truetype');
            font-weight: bold;
        }

        :root { 
            --bg: #070a13; 
            --card-bg: rgba(17, 24, 39, 0.7); 
            --gold: #c5a059; 
            --slate: #94a3b8; 
            --white: #ffffff; 
            --transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--slate); line-height: 1.7; overflow-x: hidden; }

        /* Navigation Responsive */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 15px 5%; 
            background: rgba(7, 10, 19, 0.95); backdrop-filter: blur(20px); 
            display: flex; justify-content: space-between; z-index: 1000; 
            align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); 
        }
        
        nav h1 { 
            font-family: 'Gonzaga', 'Cinzel', serif; font-size: clamp(1.2rem, 4vw, 2.2rem); 
            font-weight: 900; text-transform: uppercase;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .nav-menu { display: flex; list-style: none; gap: clamp(15px, 3vw, 30px); align-items: center; }
        .nav-menu a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        .nav-menu a:hover { color: var(--gold); }
        .nav-admin { border: 1px solid var(--gold); padding: 8px 15px; color: var(--gold) !important; border-radius: 4px; }

        /* Effects */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        section { padding: 100px 5% 60px; max-width: 1400px; margin: 0 auto; }

        /* Hero Responsive */
        .hero { display: flex; align-items: center; gap: 40px; min-height: 100vh; padding-top: 120px; }
        .hero-text { flex: 1; text-align: left; animation: fadeInLeft 1s ease; }
        .hero-text h2 { font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 8vw, 5.5rem); color: var(--white); line-height: 1.1; font-weight: 700; letter-spacing: -2px; }
        .hero-text h2 span { color: var(--gold); font-style: italic; }
        
        .hero-image { flex: 1; position: relative; display: flex; justify-content: center; animation: fadeInRight 1s ease; }
        .img-wrapper { position: relative; width: 100%; max-width: 500px; aspect-ratio: 1/1; border-radius: 50%; border: 2px solid var(--gold); overflow: hidden; box-shadow: 0 0 50px rgba(197, 160, 89, 0.2); }
        .hero-image img { width: 110%; height: 110%; object-fit: cover; object-position: center 10%; margin-left: -5%; }

        /* Buttons Responsive */
        .btn-gold, .btn-outline { 
            padding: 18px 35px; font-weight: 800; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 2px; cursor: pointer; 
            text-decoration: none; transition: var(--transition); display: inline-block;
            margin: 10px 5px;
        }
        .btn-gold { background: var(--gold); color: var(--bg); border: none; }
        .btn-outline { border: 1px solid var(--gold); color: var(--gold); }
        .btn-gold:hover, .btn-outline:hover { background: var(--white); color: var(--bg); transform: translateY(-5px); border-color: var(--white); }

        /* Grid Responsive */
        .sw-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; margin-top: 40px; }
        .sw-pill { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 20px; text-align: center; transition: 0.3s; }
        .sw-pill img { width: 80px; height: 40px; object-fit: contain; filter: grayscale(1); transition: 0.5s; }
        .sw-pill:hover img { filter: grayscale(0); transform: scale(1.1); }

        /* Connect Responsive */
        .glass-card { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 50px; 
            background: var(--card-bg); padding: clamp(30px, 5vw, 80px); 
            border: 1px solid rgba(255,255,255,0.05); 
        }
        
        /* Feedback Grid Responsive */
        .feedback-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }

        /* Animations */
        @keyframes fadeInLeft { from { opacity:0; transform: translateX(-50px); } to { opacity:1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity:0; transform: translateX(50px); } to { opacity:1; transform: translateX(0); } }

        /* Mobile Breakpoints */
        @media (max-width: 968px) {
            .hero { flex-direction: column-reverse; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .glass-card { grid-template-columns: 1fr; }
            .hero-text { text-align: center; }
        }

        @media (max-width: 480px) {
            nav h1 { font-size: 1.2rem; }
            .nav-menu { display: none; } /* Could add hamburger toggle here */
            .hero-text h2 { font-size: 2.8rem; }
            .btn-gold, .btn-outline { width: 100%; margin: 5px 0; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT</h1>
    <ul class="nav-menu">
        <li><a href="#hero">Overview</a></li>
        <li><a href="#connect">Connect</a></li>
        <li><a href="#feedback">Validation</a></li>
        <li><a href="admin.php" class="nav-admin">Portal</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div style="border: 1px solid var(--gold); padding: 8px 15px; display: inline-block; color: var(--gold); margin-bottom: 20px; letter-spacing: 2px; font-weight: 800; font-size: 0.6rem;">17+ YEARS OF FINANCIAL STRATEGY</div>
        <h2>Transforming <span>Complex</span> Financials.</h2>
        <p style="font-size: 1.1rem; margin-bottom: 40px; color: var(--slate);">Senior Accountant & Financial Operations Partner helping global businesses gain clarity and control.</p>
        <div class="hero-btns">
            <a href="#connect" class="btn-gold">Secure Partnership</a>
            <a href="Afryl_Lou_Okit_CV.pdf" target="_blank" class="btn-outline">Download CV</a>
        </div>
    </div>
    <div class="hero-image">
        <div class="img-wrapper"><img src="afryl.jpg" alt="Afryl Lou Okit"></div>
    </div>
</section>

<section class="reveal">
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="color: var(--gold); letter-spacing: 4px; font-size: 0.6rem; font-weight: 800;">TECHNOLOGY INFRASTRUCTURE</span>
    </div>
    <div class="sw-grid">
        <div class="sw-pill"><img src="Netsuite.png" alt="NetSuite"></div>
        <div class="sw-pill"><img src="Quickbooks.png" alt="QuickBooks"></div>
        <div class="sw-pill"><img src="xero.png" alt="Xero"></div>
        <div class="sw-pill"><img src="Billcom.png" alt="Bill.com"></div>
        <div class="sw-pill"><img src="Salesforce.png" alt="Salesforce"></div>
        <div class="sw-pill"><img src="clickup.png" alt="ClickUp"></div>
    </div>
</section>

<section id="connect" class="reveal">
    <div class="glass-card">
        <div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3.5rem); color: white;">Let's Connect</h3>
            <p style="margin: 20px 0;">Secure financial partnership for international entities.</p>
        </div>
        <div class="form-box">
            <form action="send_email.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" style="width: 100%; padding: 15px; background: transparent; border: none; border-bottom: 1px solid rgba(255,255,255,0.1); color: white; margin-bottom: 20px;" required>
                <input type="email" name="email" placeholder="Professional Email" style="width: 100%; padding: 15px; background: transparent; border: none; border-bottom: 1px solid rgba(255,255,255,0.1); color: white; margin-bottom: 20px;" required>
                <textarea name="message" rows="4" placeholder="How can I assist?" style="width: 100%; padding: 15px; background: transparent; border: none; border-bottom: 1px solid rgba(255,255,255,0.1); color: white; margin-bottom: 20px;" required></textarea>
                <button type="submit" class="btn-gold" style="width: 100%; margin: 0;">Send Inquiry</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback" class="reveal">
    <h3 style="text-align: center; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem); color: white; margin-bottom: 50px;">Executive Validation</h3>
    <div class="feedback-grid">
        <?php foreach ($comments as $row): ?>
            <div class="feedback-item" style="background: var(--card-bg); padding: 30px; border: 1px solid rgba(255,255,255,0.03);">
                <p style="font-style: italic; color: white; margin-bottom: 20px;">"<?= htmlspecialchars($row['comment_text']) ?>"</p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="https://flagcdn.com/w20/<?= strtolower($row['country_code']) ?>.png" width="20">
                    <span style="color: var(--gold); font-size: 0.7rem; font-weight: 800;"><?= htmlspecialchars($row['name']) ?></span>
                </div>
                <small style="display: block; margin-top: 5px; opacity: 0.6;"><?= htmlspecialchars($row['position']) ?> @ <?= htmlspecialchars($row['company']) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer style="text-align: center; padding: 40px; border-top: 1px solid rgba(255,255,255,0.05); font-size: 0.7rem; letter-spacing: 1px;">
    <p>&copy; <?= date("Y") ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.</p>
</footer>



<script>
    // Reveal animation on scroll
    window.addEventListener('scroll', reveal);
    function reveal(){
        var reveals = document.querySelectorAll('.reveal');
        for(var i = 0; i < reveals.length; i++){
            var windowheight = window.innerHeight;
            var revealtop = reveals[i].getBoundingClientRect().top;
            var revealpoint = 150;
            if(revealtop < windowheight - revealpoint){
                reveals[i].classList.add('active');
            }
        }
    }
    reveal(); // Run once on load
</script>
</body>
</html>
