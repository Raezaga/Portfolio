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
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; }

        section { padding: 140px 10% 80px; max-width: 1600px; margin: 0 auto; }

        /* HERO AREA */
        .hero { display: flex; align-items: center; gap: 80px; min-height: 90vh; }
        .hero-text { flex: 1.3; }
        
        /* ENLARGED EXPERIENCE BADGE */
        .experience-seal {
            display: inline-flex; align-items: center; gap: 15px;
            padding: 15px 30px; border: 2px solid var(--gold);
            background: rgba(197, 160, 89, 0.1); color: var(--gold);
            font-weight: 800; font-size: 1.1rem; letter-spacing: 4px;
            text-transform: uppercase; margin-bottom: 35px;
        }

        .hero-text h2 { 
            font-family: 'Playfair Display', serif; font-size: 5.5rem; color: var(--white); 
            margin-bottom: 25px; line-height: 1; font-weight: 700; letter-spacing: -3px;
        }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        /* CIRCULAR PHOTO - NO MARGINS, NO CUT HEAD */
        .hero-image { flex: 0.7; position: relative; display: flex; justify-content: center; }
        .img-wrapper {
            position: relative; width: 100%; max-width: 480px; aspect-ratio: 1/1;
            border-radius: 50%; border: 3px solid var(--gold); overflow: hidden;
            background: var(--bg-accent); box-shadow: 0 0 50px rgba(0,0,0,0.5);
        }
        .hero-image img { 
            width: 110%; /* Slight zoom to remove side margins */
            height: 110%;
            object-fit: cover; 
            object-position: center 20%; /* Pushes the image down so head isn't cut */
            filter: contrast(105%);
            margin-left: -5%; /* Centers the zoomed image */
        }

        /* BUTTONS */
        .hero-actions { display: flex; gap: 20px; margin-top: 40px; }
        .btn-gold { 
            padding: 22px 45px; background: var(--gold); color: var(--bg-deep); 
            border: none; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: 0.4s;
        }
        .btn-cv { 
            padding: 22px 45px; background: transparent; color: var(--gold); 
            border: 1px solid var(--gold); font-weight: 700; font-size: 0.8rem; 
            text-transform: uppercase; letter-spacing: 3px; text-decoration: none; 
            transition: 0.4s; display: inline-flex; align-items: center; gap: 12px;
        }

        /* CONTACT BLOCK (NOW BEFORE FEEDBACK) */
        .contact-block { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 100px; 
            background: var(--bg-accent); padding: 100px; margin-top: 60px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .form-box input, .form-box textarea { 
            width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; 
            color: var(--white); outline: none; transition: 0.3s; font-family: inherit;
        }
        .form-box input:focus { border-bottom-color: var(--gold); }

        /* FEEDBACK ITEM */
        .feedback-item { 
            background: var(--bg-accent); padding: 50px; margin-bottom: 25px; 
            border-left: 3px solid var(--gold);
        }

        footer { text-align: center; padding: 100px 5%; font-size: 0.7rem; letter-spacing: 3px; color: var(--slate); text-transform: uppercase; }

        @media (max-width: 1100px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; }
            .contact-block { grid-template-columns: 1fr; padding: 40px; }
            .hero-actions { flex-direction: column; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT / PARTNER</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#contact">Connect</a></li>
        <li><a href="#feedback">Testimonials</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div class="experience-seal">
            <i class="fas fa-certificate"></i> 17+ YEARS OF SENIOR EXPERTISE
        </div>
        <h2>High-Stakes <span>Financial</span> Strategy.</h2>
        <p style="font-size: 1.25rem; font-weight: 300; margin-bottom: 40px;">
            Senior Accountant specializing in international portfolio management and multi-entity operations for the US, Australia, and Middle East markets.
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
            <img src="afryl.jpg" alt="Afryl Lou Okit">
        </div>
    </div>
</section>

<section id="contact">
    <div class="contact-block">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--white); margin-bottom: 30px; line-height: 1;">Let's Connect.</h2>
            <p style="font-size: 1.2rem; margin-bottom: 40px;">Ready to optimize your global financial operations?</p>
            <p style="font-size: 0.8rem; color: var(--gold); letter-spacing: 2px; margin-bottom: 5px;">DIRECT CHANNEL</p>
            <p style="font-size: 1.1rem; color: var(--white); margin-bottom: 25px;">afryllou.consulting@gmail.com</p>
            <p style="font-size: 0.8rem; color: var(--gold); letter-spacing: 2px; margin-bottom: 5px;">LOCATION</p>
            <p style="font-size: 1.1rem; color: var(--white);">Global Operations / Philippines Base</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Organization" required>
                <textarea name="comment_text" rows="4" placeholder="Briefly describe your requirements..." required></textarea>
                <button type="submit" id="commentBtn" class="btn-gold" style="width: 100%;">Initiate Discussion</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback" style="padding-top: 100px; border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="text-align: center; margin-bottom: 60px;">
        <span style="color: var(--gold); font-size: 0.8rem; letter-spacing: 4px;">TESTIMONIALS</span>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--white); margin-top: 10px;">The Executive Standard</h3>
    </div>
    
    <div id="comments-list" style="max-width: 1000px; margin: 0 auto;">
        <?php if (empty($comments)): ?>
            <p style="text-align: center; color: var(--slate); font-style: italic;">Consultations in progress. Feedback appearing soon.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
                <?php foreach ($comments as $row): ?>
                    <div class="feedback-item">
                        <p style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--white); font-style: italic; line-height: 1.6;">
                            "<?php echo htmlspecialchars($row['comment_text']); ?>"
                        </p>
                        <p style="margin-top: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold);">
                            — <?php echo htmlspecialchars($row['name']); ?> <span style="color:rgba(255,255,255,0.2); margin:0 10px;">|</span> <?php echo htmlspecialchars($row['company']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.
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
            alert("Inquiry Sent Successfully.");
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
