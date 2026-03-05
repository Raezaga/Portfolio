<?php
include "config.php";

$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);

    $stmt = $pdo->prepare("SELECT * FROM comments WHERE status = 'approved' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
    <title>Afryl Lou Okit | Senior Accountant (17+ Years Experience)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg-deep: #0a101e; 
            --bg-accent: #111827; 
            --gold: #c5a059; 
            --slate: #94a3b8; 
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--slate); background: var(--bg-deep); line-height: 1.8; overflow-x: hidden; }

        nav { 
            position: fixed; top: 0; width: 100%; padding: 25px 8%; 
            background: rgba(10, 16, 30, 0.95); backdrop-filter: blur(15px); 
            display: flex; justify-content: space-between; z-index: 1000; align-items: center; 
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        nav h1 { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; color: var(--white); letter-spacing: 1px; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        nav ul a:hover { color: var(--gold); }

        section { padding: 160px 10% 100px; max-width: 1600px; margin: 0 auto; }

        /* HERO AREA */
        .hero { display: flex; align-items: center; gap: 80px; min-height: 90vh; }
        .hero-text { flex: 1.3; }
        
        .experience-seal {
            display: inline-flex; align-items: center; gap: 15px;
            padding: 10px 20px; border: 1px solid rgba(197, 160, 89, 0.3);
            background: rgba(197, 160, 89, 0.05); color: var(--gold);
            font-weight: 700; font-size: 0.8rem; letter-spacing: 3px;
            text-transform: uppercase; margin-bottom: 30px;
        }

        .hero-text h2 { 
            font-family: 'Playfair Display', serif; font-size: 5.5rem; color: var(--white); 
            margin-bottom: 25px; line-height: 1; font-weight: 700; letter-spacing: -3px;
        }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        /* CIRCULAR PHOTO WITH ORBIT */
        .hero-image { flex: 0.7; position: relative; display: flex; justify-content: center; }
        .hero-image .img-wrapper {
            position: relative; width: 100%; max-width: 450px; aspect-ratio: 1/1;
        }
        .hero-image img { 
            width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--gold); position: relative; z-index: 5;
            filter: contrast(110%);
        }
        .orbit-ring {
            position: absolute; top: -15px; left: -15px; right: -15px; bottom: -15px;
            border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 50%;
            border-top: 1px solid var(--gold); animation: spin 20s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* BUTTONS */
        .hero-actions { display: flex; gap: 20px; margin-top: 40px; }
        .btn-gold { 
            padding: 22px 45px; background: var(--gold); color: var(--bg-deep); 
            border: none; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: 0.4s;
        }
        .btn-gold:hover { background: var(--white); transform: translateY(-3px); }
        .btn-cv { 
            padding: 22px 45px; background: transparent; color: var(--gold); 
            border: 1px solid var(--gold); font-weight: 700; font-size: 0.8rem; 
            text-transform: uppercase; letter-spacing: 3px; text-decoration: none; 
            transition: 0.4s; display: inline-flex; align-items: center; gap: 12px;
        }

        /* GRID SYSTEM */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 60px; }
        .value-card { padding: 60px; background: var(--bg-accent); border: 1px solid rgba(255,255,255,0.03); transition: 0.4s; }
        .value-card:hover { border-color: var(--gold); transform: translateY(-5px); }
        .value-card h4 { color: var(--gold); font-size: 3.5rem; font-family: 'Playfair Display'; margin-bottom: 10px; opacity: 0.5; }

        /* FORM */
        .contact-block { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 100px; 
            background: var(--bg-accent); padding: 100px; margin-top: 100px; 
        }
        .form-box input, .form-box textarea { 
            width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; 
            color: var(--white); outline: none; transition: 0.3s;
        }
        .form-box input:focus { border-bottom-color: var(--gold); }

        footer { text-align: center; padding: 100px 5%; font-size: 0.7rem; letter-spacing: 3px; color: var(--slate); text-transform: uppercase; border-top: 1px solid rgba(255,255,255,0.05); }

        @media (max-width: 1100px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .contact-block { grid-template-columns: 1fr; padding: 50px; }
            .hero-actions { flex-direction: column; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT / PARTNER</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#services">Specialization</a></li>
        <li><a href="#portfolio">Systems</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div class="experience-seal">
            <i class="fas fa-award"></i> 17+ Years of Senior Expertise
        </div>
        <h2>High-Stakes <span>Financial</span> Strategy.</h2>
        <p style="font-size: 1.25rem; font-weight: 300; margin-bottom: 40px;">
            Senior Accountant specializing in international portfolio management, multi-entity intercompany dynamics, and audit-ready operations for the US, Australia, and Middle East markets.
        </p>
        
        <div class="hero-actions">
            <a href="#contact" class="btn-gold">Secure Partnership</a>
            <a href="Afryl_Lou_Okit_CV.pdf" download class="btn-cv">
                <i class="fas fa-file-pdf"></i> EXECUTIVE CV
            </a>
        </div>
    </div>
    <div class="hero-image">
        <div class="img-wrapper">
            <div class="orbit-ring"></div>
            <img src="Afryl.jpg" alt="Afryl Lou Okit">
        </div>
    </div>
</section>

<section id="services" style="background: var(--bg-accent);">
    <h2 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--white); margin-bottom: 60px;">Two Decades of Excellence</h2>
    <div class="grid">
        <div class="value-card">
            <h4>17+</h4>
            <h3 style="color: var(--white); font-size: 1.5rem; margin-bottom: 15px;">Years Experience</h3>
            <p>A career built on precision, navigating evolving global accounting standards and complex tax landscapes.</p>
        </div>
        <div class="value-card">
            <h4>03</h4>
            <h3 style="color: var(--white); font-size: 1.5rem; margin-bottom: 15px;">Global Regions</h3>
            <p>Proven track record supporting major international businesses across the United States, Australia, and the UAE.</p>
        </div>
        <div class="value-card">
            <h4>∞</h4>
            <h3 style="color: var(--white); font-size: 1.5rem; margin-bottom: 15px;">Unmatched Clarity</h3>
            <p>Transforming complex financial disorder into decision-ready data that fuels aggressive business growth.</p>
        </div>
    </div>
</section>

<section id="portfolio">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 2.8rem; color: var(--white); margin-bottom: 40px;">Expert-Level Systems</h2>
    <div class="tag-list" style="display:flex; flex-wrap:wrap; gap:15px;">
        <span style="padding:10px 25px; border:1px solid rgba(255,255,255,0.1); font-size:0.7rem; font-weight:700; letter-spacing:2px; color:var(--gold);">NETSUITE</span>
        <span style="padding:10px 25px; border:1px solid rgba(255,255,255,0.1); font-size:0.7rem; font-weight:700; letter-spacing:2px; color:var(--white);">QUICKBOOKS</span>
        <span style="padding:10px 25px; border:1px solid rgba(255,255,255,0.1); font-size:0.7rem; font-weight:700; letter-spacing:2px; color:var(--white);">XERO</span>
        <span style="padding:10px 25px; border:1px solid rgba(255,255,255,0.1); font-size:0.7rem; font-weight:700; letter-spacing:2px; color:var(--white);">FLOQAST</span>
        <span style="padding:10px 25px; border:1px solid rgba(255,255,255,0.1); font-size:0.7rem; font-weight:700; letter-spacing:2px; color:var(--white);">STRIPE</span>
    </div>

    <div style="margin-top: 100px; padding: 70px; border: 1px solid var(--gold); background: var(--bg-accent);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--white); margin-bottom: 10px;">Bluesky Investments LLC</h3>
        <p style="color: var(--gold); font-weight: 700; margin-bottom: 25px;">Finance Lead / 2018 — Present</p>
        <p style="font-weight: 300;">Management of end-to-end multi-entity financial operations, ensuring precise intercompany transactional mappings and predictive budget modeling.</p>
    </div>
</section>

<section id="contact" style="padding: 0;">
    <div class="contact-block">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--white); margin-bottom: 30px; line-height: 1;">Let's Connect.</h2>
            <p style="color: var(--slate); margin-bottom: 50px;">Bespoke financial partnership for international entities.</p>
            <p style="font-size: 0.8rem; color: var(--gold); margin-bottom: 10px;">EMAIL</p>
            <p style="font-size: 1.1rem; color: var(--white);">afryllou.consulting@gmail.com</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Company Name" required>
                <textarea name="comment_text" rows="4" placeholder="Brief details of your financial complexity..." required></textarea>
                <button type="submit" id="commentBtn" class="btn-gold" style="width: 100%;">Submit Inquiry</button>
            </form>
        </div>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS.
</footer>

<script>
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('commentBtn');
        btn.innerHTML = 'TRANSMITTING...';
        btn.disabled = true;

        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.text())
        .then(data => {
            alert("Enquiry Sent Successfully.");
            location.reload();
        })
        .catch(() => {
            alert("Error in transmission.");
            btn.disabled = false;
            btn.innerHTML = 'RETRY';
        });
    });
</script>
</body>
</html>
