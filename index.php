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
    <title>Afryl Lou Okit | Senior Accountant & Financial Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --midnight: #020617; 
            --gold: #c5a059; 
            --gold-light: #e2d1a8;
            --slate: #64748b;
            --cream: #f8f9fa;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--midnight); background: var(--white); line-height: 1.8; }

        /* Navigation */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 30px 8%; 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); 
            display: flex; justify-content: space-between; z-index: 1000; align-items: center; 
            border-bottom: 1px solid #f1f1f1;
        }
        nav h1 { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; color: var(--midnight); letter-spacing: 1px; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--midnight); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        nav ul a:hover { color: var(--gold); }

        section { padding: 180px 10% 120px; max-width: 1600px; margin: 0 auto; }

        /* Hero Section */
        .hero { display: flex; align-items: center; gap: 100px; min-height: 90vh; }
        .hero-text { flex: 1.2; }
        
        .hero-text .title-badge { 
            font-size: 0.9rem; font-weight: 700; color: var(--gold); 
            text-transform: uppercase; letter-spacing: 6px; display: block; margin-bottom: 20px; 
        }

        .hero-text h2 { 
            font-family: 'Playfair Display', serif; font-size: 5rem; color: var(--midnight); 
            margin-bottom: 30px; line-height: 1.1; font-weight: 700;
        }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        .hero-text p { font-size: 1.2rem; color: var(--slate); max-width: 650px; margin-bottom: 45px; font-weight: 300; }

        .hero-image { flex: 0.8; position: relative; }
        .hero-image img { 
            width: 100%; border-radius: 2px; 
            box-shadow: 60px -60px 0px -20px var(--cream), 60px -60px 0px -19px var(--gold);
            transition: 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-image:hover img { transform: scale(1.02); }

        /* Buttons */
        .hero-actions { display: flex; gap: 25px; align-items: center; }
        .btn-gold { 
            padding: 22px 45px; background: var(--gold); color: var(--white); 
            border: none; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: 0.4s;
        }
        .btn-gold:hover { background: var(--midnight); transform: translateY(-5px); }
        
        .btn-outline { 
            padding: 22px 45px; background: transparent; color: var(--midnight); 
            border: 1px solid var(--midnight); font-weight: 700; font-size: 0.8rem; 
            text-transform: uppercase; letter-spacing: 3px; cursor: pointer; text-decoration: none; 
            transition: 0.4s; display: inline-flex; align-items: center; gap: 12px;
        }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); }

        /* Values Grid */
        .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; border: 1px solid #eee; margin-top: 80px; }
        .value-card { padding: 80px 50px; border-right: 1px solid #eee; transition: 0.4s; background: var(--white); }
        .value-card:last-child { border-right: none; }
        .value-card:hover { background: var(--cream); }
        .value-card i { font-size: 1.5rem; color: var(--gold); margin-bottom: 30px; }
        .value-card h3 { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin-bottom: 20px; }

        /* Systems Mastery */
        .tag-list { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 40px; }
        .tag { 
            padding: 10px 25px; border: 1px solid #ddd; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; font-weight: 600; 
        }

        /* Forms */
        .contact-container { display: grid; grid-template-columns: 1fr 1fr; gap: 100px; background: var(--midnight); color: var(--white); padding: 100px; }
        .form-box input, .form-box textarea { 
            width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.2); background: transparent; 
            color: white; font-family: inherit; outline: none; transition: 0.3s;
        }
        .form-box input:focus { border-bottom-color: var(--gold); }

        footer { text-align: center; padding: 80px; font-size: 0.7rem; letter-spacing: 3px; color: var(--slate); text-transform: uppercase; }

        @media (max-width: 1100px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .values-grid { grid-template-columns: 1fr; }
            .contact-container { grid-template-columns: 1fr; padding: 50px; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT.</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#services">Values</a></li>
        <li><a href="#portfolio">Systems</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <span class="title-badge">Senior Accountant & Financial Partner</span>
        <h2>Precision in <span>Financial</span> Operations.</h2>
        <p>Expertly managing complex portfolios for international entities. I provide the structural clarity required for high-stakes business decisions.</p>
        
        <div class="hero-actions">
            <a href="#contact" class="btn-gold">Enquire Now</a>
            <a href="Afryl_Lou_Okit_CV.pdf" download class="btn-outline">
                <i class="fas fa-file-pdf"></i> Download CV
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Afryl.jpg" alt="Afryl Lou Okit Portrait">
    </div>
</section>

<section id="services" style="background: var(--cream); border-top: 1px solid #eee;">
    <h2 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 3rem; margin-bottom: 60px;">The Executive Standard</h2>
    <div class="values-grid">
        <div class="value-card">
            <i class="fas fa-fingerprint"></i>
            <h3>Uncompromising Accuracy</h3>
            <p>Moving beyond bookkeeping. I establish audit-ready frameworks that ensure every cent is accounted for and strategically placed.</p>
        </div>
        <div class="value-card">
            <i class="fas fa-compass"></i>
            <h3>Strategic Insight</h3>
            <p>Raw data is just noise. I distill complex financials into actionable intelligence that drives global business growth.</p>
        </div>
        <div class="value-card">
            <i class="fas fa-layer-group"></i>
            <h3>Operational Excellence</h3>
            <p>Scalable financial systems designed to grow with your business, utilizing industry-leading tech stacks and rigorous controls.</p>
        </div>
    </div>
</section>

<section id="portfolio">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 20px;">System Architecture</h2>
    <p style="color: var(--slate); margin-bottom: 40px;">Expert-level proficiency in the world’s leading financial and operational platforms.</p>
    <div class="tag-list">
        <span class="tag">NetSuite</span> <span class="tag">QuickBooks Online</span> <span class="tag">Xero</span> 
        <span class="tag">Floqast</span> <span class="tag">Stripe</span> <span class="tag">Salesforce</span> 
        <span class="tag">Dext</span> <span class="tag">Bill.com</span> <span class="tag">ClickUp</span>
    </div>

    <div style="margin-top: 100px; padding: 80px; border: 1px solid var(--gold); position: relative;">
        <span style="position: absolute; top: -12px; left: 40px; background: white; padding: 0 15px; color: var(--gold); font-weight: 700; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Core Experience</span>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 15px;">Bluesky Investments LLC</h3>
        <p style="color: var(--gold); font-weight: 700; margin-bottom: 25px;">Finance & Accounting Lead | 2018 — Present</p>
        <p style="color: var(--slate); max-width: 800px;">Directing end-to-end financial operations for multi-entity structures, focusing on intercompany accuracy and predictive budget analysis.</p>
    </div>
</section>

<section id="contact" style="padding: 0;">
    <div class="contact-container">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 4rem; line-height: 1; margin-bottom: 30px;">Let’s Start the Conversation.</h2>
            <p style="color: rgba(255,255,255,0.6); margin-bottom: 50px;">Bespoke financial management for international partners.</p>
            <p style="font-size: 0.8rem; letter-spacing: 2px; color: var(--gold);">EMAIL</p>
            <p style="font-size: 1.2rem; margin-bottom: 30px;">afryllou.consulting@gmail.com</p>
            <p style="font-size: 0.8rem; letter-spacing: 2px; color: var(--gold);">PHONE</p>
            <p style="font-size: 1.2rem;">+63 999 586 61908</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Company" required>
                <textarea name="comment_text" rows="4" placeholder="Briefly describe your requirements" required></textarea>
                <button type="submit" id="commentBtn" class="btn-gold" style="width: 100%; border-radius: 0;">Send Message</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback" style="padding-top: 100px;">
    <h3 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 40px; text-align: center;">Client Testimonials</h3>
    <div id="comments-list">
        <?php if (empty($comments)): ?>
            <p style="text-align: center; color: var(--slate);">Consultations in progress.</p>
        <?php else: ?>
            <?php foreach ($comments as $row): ?>
                <div style="max-width: 800px; margin: 0 auto 40px; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 40px;">
                    <p style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-style: italic; color: var(--midnight);">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                    <p style="margin-top: 20px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold);">
                        — <?php echo htmlspecialchars($row['name']); ?> / <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> Afryl Lou Okit. All Rights Reserved.
</footer>

<script>
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('commentBtn');
        btn.innerHTML = 'Transmitting...';
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
            btn.innerHTML = 'Retry';
        });
    });
</script>
</body>
</html>
