<?php
include "config.php";

// DATABASE LOGIC: Pagination for Reviews
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--slate); background: var(--bg); line-height: 1.7; overflow-x: hidden; }

        nav { 
            position: fixed; top: 0; width: 100%; padding: 25px 8%; 
            background: rgba(7, 10, 19, 0.9); backdrop-filter: blur(20px); 
            display: flex; justify-content: space-between; z-index: 1000; align-items: center; 
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        nav h1 { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--white); letter-spacing: 2px; text-transform: uppercase; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: var(--transition); }
        nav ul a:hover { color: var(--gold); }

        section { padding: 120px 10% 80px; max-width: 1500px; margin: 0 auto; }

        /* Hero Styling - ENLARGED */
        .hero { display: flex; align-items: center; gap: 60px; min-height: 100vh; }
        .hero-text { flex: 1.1; }
        .badge-large {
            display: inline-flex; align-items: center; gap: 15px;
            padding: 15px 30px; border: 2px solid var(--gold);
            background: rgba(197, 160, 89, 0.1); color: var(--gold);
            font-weight: 800; font-size: 1.1rem; letter-spacing: 4px;
            text-transform: uppercase; margin-bottom: 35px;
        }
        .hero-text h2 { 
            font-family: 'Playfair Display', serif; font-size: 5.5rem; color: var(--white); 
            margin-bottom: 25px; line-height: 1.1; font-weight: 700; letter-spacing: -3px;
        }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        /* BIGGER PHOTO FRAME */
        .hero-image { flex: 0.9; position: relative; display: flex; justify-content: center; }
        .img-wrapper {
            position: relative; width: 100%; max-width: 580px; /* ENLARGED */
            aspect-ratio: 1/1; border-radius: 50%; border: 3px solid var(--gold); 
            overflow: hidden; background: var(--bg); z-index: 2;
            box-shadow: 0 0 80px rgba(0,0,0,0.6);
        }
        .hero-image img { 
            width: 115%; height: 115%; object-fit: cover; 
            object-position: center 20%; margin-left: -7.5%;
        }

        /* Software Infrastructure */
        .sw-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 40px; }
        .sw-pill { 
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);
            padding: 25px; text-align: center; transition: var(--transition);
        }
        .sw-pill:hover { border-color: var(--gold); background: rgba(197, 160, 89, 0.05); transform: translateY(-5px); }
        .sw-pill i { color: var(--gold); font-size: 1.8rem; margin-bottom: 12px; display: block; }
        .sw-pill span { color: var(--white); font-weight: 700; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }

        /* Forms Styling */
        .glass-card { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 100px; 
            background: var(--card-bg); backdrop-filter: blur(15px);
            padding: 80px; margin-top: 60px; border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }
        .form-box input, .form-box textarea { 
            width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; 
            color: var(--white); outline: none; transition: 0.3s; font-family: inherit;
        }
        .form-box input:focus { border-bottom-color: var(--gold); }
        .btn-gold { 
            padding: 22px 45px; background: var(--gold); color: var(--bg); 
            border: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition);
            display: inline-block;
        }
        .btn-gold:hover { background: var(--white); transform: translateY(-5px); }

        /* Testimonials */
        .feedback-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 60px; }
        .feedback-item { 
            background: var(--card-bg); padding: 50px; border: 1px solid rgba(255,255,255,0.03);
            transition: var(--transition); position: relative;
        }
        .feedback-item::before { content: "\f10d"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; top: 20px; left: 20px; color: rgba(197,160,89,0.1); font-size: 2rem; }

        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 50px; }
        .pagination a { padding: 12px 20px; border: 1px solid rgba(255,255,255,0.1); color: var(--white); text-decoration: none; font-weight: 700; transition: 0.3s; }
        .pagination a.active { background: var(--gold); color: var(--bg); border-color: var(--gold); }

        footer { text-align: center; padding: 100px 5%; font-size: 0.7rem; letter-spacing: 4px; color: var(--slate); text-transform: uppercase; border-top: 1px solid rgba(255,255,255,0.05); }

        @media (max-width: 1100px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .glass-card { grid-template-columns: 1fr; padding: 50px; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT / PARTNER</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#connect">Connect</a></li>
        <li><a href="#feedback">Validation</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div class="badge-large"><i class="fas fa-award"></i> 17+ YEARS EXPERTISE</div>
        <h2>High-Stakes <span>Financial</span> Strategy.</h2>
        <p style="font-size: 1.4rem; font-weight: 300; margin-bottom: 40px; max-width: 650px; color: var(--slate);">
            Senior Accountant directing international multi-entity operations for US and Australian markets.
        </p>
        <a href="#connect" class="btn-gold">Secure Partnership</a>
    </div>
    <div class="hero-image">
        <div class="img-wrapper">
            <img src="afryl.jpg" alt="Afryl Lou Okit">
        </div>
    </div>
</section>

<section style="background: rgba(255,255,255,0.01); border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05);">
    <div style="text-align: center; margin-bottom: 40px;">
        <span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">TECHNOLOGY INFRASTRUCTURE</span>
    </div>
    <div class="sw-grid">
        <div class="sw-pill"><i class="fas fa-calculator"></i><span>QUICKBOOKS</span></div>
        <div class="sw-pill"><i class="fas fa-chart-pie"></i><span>XERO ADVISOR</span></div>
        <div class="sw-pill"><i class="fas fa-database"></i><span>NETSUITE ERP</span></div>
        <div class="sw-pill"><i class="fas fa-table"></i><span>ADVANCED EXCEL</span></div>
        <div class="sw-pill"><i class="fas fa-project-diagram"></i><span>SAP FICO</span></div>
    </div>
</section>

<section id="connect">
    <div class="glass-card">
        <div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white; line-height: 1;">Let's Connect</h3>
            <p style="margin: 25px 0; font-size: 1.1rem;">Bespoke financial partnership inquiries. This sends directly to my Gmail.</p>
            
            <?php if(isset($_GET['mail']) && $_GET['mail'] == 'sent'): ?>
                <div style="background: rgba(197,160,89,0.1); border: 1px solid var(--gold); padding: 15px; color: var(--gold); font-weight: 800; display: inline-block;">
                   <i class="fas fa-check-circle"></i> INQUIRY DISPATCHED SUCCESSFULLY
                </div>
            <?php endif; ?>
        </div>
        <div class="form-box">
            <form action="send_email.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Organization" required>
                <input type="email" name="email" placeholder="Your Professional Email" required>
                <textarea name="message" rows="4" placeholder="How can I assist your operations?" required></textarea>
                <button type="submit" class="btn-gold" style="width: 100%;">Initiate Discussion</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback">
    <div style="text-align: center; margin-bottom: 60px;">
        <span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">VALIDATION</span>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: var(--white); margin-top: 10px;">Executive Proof</h3>
    </div>
    
    <div class="feedback-grid">
        <?php foreach ($comments as $row): ?>
            <div class="feedback-item">
                <p style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--white); font-style: italic; line-height: 1.6; margin-bottom: 30px;">
                    "<?php echo htmlspecialchars($row['comment_text']); ?>"
                </p>
                <p style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold);">
                    — <?php echo htmlspecialchars($row['name']); ?> <span style="color:rgba(255,255,255,0.1); margin:0 10px;">/</span> <?php echo htmlspecialchars($row['company']); ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>#feedback" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</section>

<section style="background: rgba(197, 160, 89, 0.03); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <h4 style="color: white; font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 20px;">Submit a Review</h4>
        <p style="margin-bottom: 40px; color: var(--slate);">Share your experience. This will appear on the website upon approval.</p>
        
        <div class="form-box">
            <form id="reviewForm">
                <input type="text" name="name" placeholder="Name to Display" required>
                <input type="text" name="company" placeholder="Role / Company" required>
                <textarea name="comment_text" rows="3" placeholder="Write your testimonial..." required></textarea>
                <button type="submit" id="reviewBtn" class="btn-gold">Post to Website</button>
            </form>
        </div>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.
</footer>

<script>
    // Handle the Review (Database) form with AJAX
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('reviewBtn');
        btn.innerHTML = 'POSTING...';
        btn.disabled = true;
        
        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.text())
        .then(data => {
            alert("Testimonial submitted for executive review!");
            location.reload();
        });
    });
</script>
</body>
</html>
