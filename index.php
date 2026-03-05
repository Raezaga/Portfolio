<?php
include "config.php";

// Pagination Logic
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
    <title>Afryl Lou Okit | Financial Operations Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --accent: #38bdf8; --bg: #ffffff; --light: #f8fafc; --border: #e2e8f0; --text: #1e293b; --text-dim: #64748b; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--bg); line-height: 1.6; }
        
        nav { position: fixed; top: 0; width: 100%; padding: 20px 5%; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; z-index: 100; align-items: center; }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1.1rem; font-weight: 800; color: var(--primary); }
        nav ul { display: flex; list-style: none; gap: 25px; }
        nav ul a { text-decoration: none; color: var(--text-dim); font-weight: 600; font-size: 0.85rem; }

        /* MAXIMIZED HERO SECTION */
        section { padding: 120px 8% 80px; max-width: 1400px; margin: 0 auto; }
        .hero { display: flex; align-items: center; gap: 60px; min-height: 90vh; }
        .hero-text { flex: 1.2; }
        .hero-text h2 { font-family: 'Plus Jakarta Sans'; font-size: 4.5rem; color: var(--primary); margin-bottom: 25px; line-height: 1; letter-spacing: -2px; }
        .hero-text h2 span { color: var(--accent); }
        
        /* ENLARGED PROFESSIONAL TITLE */
        .badge { color: var(--accent); font-weight: 800; font-size: 1.1rem; letter-spacing: 3px; display: block; margin-bottom: 15px; text-transform: uppercase; }
        
        .hero-image { flex: 0.8; display: flex; justify-content: center; }
        .hero-image img { width: 450px; height: 450px; border-radius: 50%; object-fit: cover; border: 12px solid white; box-shadow: 0 30px 60px rgba(0,0,0,0.12); }
        
        .hero-actions { display: flex; gap: 20px; margin-top: 40px; align-items: center; }
        
        .btn-primary { padding: 20px 45px; background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 1.1rem; cursor: pointer; text-decoration: none; transition: 0.3s; }
        .btn-primary:hover { background: var(--accent); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3); }
        
        /* CV DOWNLOAD BUTTON STYLES */
        .btn-cv { padding: 18px 40px; background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: 12px; font-weight: 800; font-size: 1.1rem; cursor: pointer; text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; }
        .btn-cv:hover { background: var(--primary); color: white; transform: translateY(-3px); }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px; }
        .card { padding: 40px; background: white; border: 1px solid var(--border); border-radius: 20px; transition: 0.3s; }
        .card i { font-size: 2.5rem; color: var(--accent); margin-bottom: 20px; display: block; }

        .tag-container { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .tag { padding: 10px 24px; background: var(--light); border: 1px solid var(--border); border-radius: 50px; font-weight: 600; font-size: 0.9rem; }

        .form-box input, .form-box textarea { width: 100%; padding: 18px; margin-bottom: 15px; border: 1px solid var(--border); border-radius: 12px; background: var(--light); font-family: inherit; }
        
        footer { text-align: center; padding: 60px; border-top: 1px solid var(--border); color: var(--text-dim); font-size: 0.9rem; }
        
        @media (max-width: 1100px) { 
            .hero { flex-direction: column-reverse; text-align: center; padding-top: 150px; } 
            .hero-text h2 { font-size: 3.5rem; }
            .hero-actions { justify-content: center; flex-direction: column; }
            .hero-image img { width: 320px; height: 320px; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT.</h1>
    <ul>
        <li><a href="#hero">Summary</a></li>
        <li><a href="#services">Values</a></li>
        <li><a href="#portfolio">Experience</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <span class="badge">Senior Accountant | Financial Operations Partner</span>
        <h2>High-Stakes <span>Financial</span> Clarity.</h2>
        <p style="font-size: 1.25rem; color: var(--text-dim); margin-bottom: 10px;">I manage a structured portfolio of businesses, ensuring each client receives accurate, timely, and decision-ready financials without compromise.</p>
        <p style="font-size: 1.1rem; font-weight: 600;">17+ years of experience supporting international businesses across the US, Australia, and the Middle East.</p>
        
        <div class="hero-actions">
            <a href="#contact" class="btn-primary">SECURE A PARTNERSHIP</a>
            <a href="Afryl_Lou_Okit_CV.pdf" download class="btn-cv">
                <i class="fas fa-file-download"></i> DOWNLOAD CV
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Afryl.jpg" alt="Afryl Lou Okit">
    </div>
</section>

<section id="services" style="background: var(--light);">
    <h2 style="text-align: center; font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; margin-bottom: 40px;">Core Strategic Values</h2>
    <div class="grid">
        <div class="card">
            <i class="fas fa-file-invoice-dollar"></i>
            <h3>Audit-Ready Books</h3>
            <p>Transforming disorganized books into decision-ready financials. Ensuring accurate, fully reconciled accounts every month.</p>
        </div>
        <div class="card">
            <i class="fas fa-chart-line"></i>
            <h3>Decision Intelligence</h3>
            <p>Providing clear financial insights, not just reports. Identifying risks, discrepancies, and inefficiencies early.</p>
        </div>
        <div class="card">
            <i class="fas fa-network-wired"></i>
            <h3>Scalable Systems</h3>
            <p>Maintaining systems that support scaling businesses without breakdowns through structured workflows.</p>
        </div>
    </div>
</section>

<section id="portfolio">
    <h2 style="margin-bottom: 25px; font-family: 'Plus Jakarta Sans'; font-size: 2rem;">System Mastery</h2>
    <div class="tag-container">
        <span class="tag">QuickBooks Online</span> 
        <span class="tag">Xero</span> 
        <span class="tag">NetSuite</span> 
        <span class="tag">Bill.com</span> 
        <span class="tag">Dext</span> 
        <span class="tag">Stripe</span> 
        <span class="tag">Salesforce</span> 
        <span class="tag">ClickUp</span> 
        <span class="tag">Floqast</span>
    </div>
    
    <div style="margin-top: 60px; padding: 50px; background: var(--primary); color: white; border-radius: 30px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);">
        <h3 style="color: var(--accent); margin-bottom: 15px; font-size: 1.5rem;">Featured Engagement: BLUESKY INVESTMENTS LLC</h3>
        <p style="font-size: 1.1rem;"><strong>Finance/Accounting Lead (Nov 2018 - Present)</strong></p>
        <ul style="margin-top: 20px; padding-left: 20px; color: #cbd5e1; font-size: 1.05rem;">
            <li style="margin-bottom: 10px;">Lead end-to-end accounting and financial operations.</li>
            <li style="margin-bottom: 10px;">Oversee multi-entity accounting and intercompany transactions.</li>
            <li>Perform budget vs. actual analysis to identify financial drivers.</li>
        </ul>
    </div>
</section>

<section id="contact">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px; align-items: center;">
        <div>
            <h2 style="font-size: 3rem; font-family: 'Plus Jakarta Sans'; margin-bottom: 20px; line-height: 1.1;">Let's Connect.</h2>
            <p style="font-size: 1.1rem; color: var(--text-dim); margin-bottom: 30px;">Supporting international clients across real estate, e-commerce, and service-based industries.</p>
            <p style="margin-bottom: 10px;"><strong>Email:</strong> afryllou.consulting@gmail.com</p>
            <p><strong>Phone:</strong> +63 999 586 61908</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Company Name" required>
                <textarea name="comment_text" rows="5" placeholder="How can I help with your financial operations?" required></textarea>
                <button type="submit" id="commentBtn" class="btn-primary" style="width: 100%;">SEND MESSAGE</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback-display" style="padding-top: 0;">
    <h3 style="margin-bottom: 30px; font-family: 'Plus Jakarta Sans'; font-size: 1.8rem;">Client Feedback</h3>
    <div id="comments-list">
        <?php if (empty($comments)): ?>
            <p style="color: var(--text-dim);">No feedback available yet. Be the first to leave a review.</p>
        <?php else: ?>
            <?php foreach ($comments as $row): ?>
                <div class="card" style="margin-bottom: 20px; padding: 30px; border-left: 5px solid var(--accent);">
                    <p style="font-style: italic; font-size: 1.1rem; color: var(--primary);">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                    <p style="margin-top: 15px; font-weight: 800; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                        — <?php echo htmlspecialchars($row['name']); ?>, <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. ALL RIGHTS RESERVED.
</footer>

<script>
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('commentBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SENDING...';
        btn.disabled = true;

        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.text())
        .then(data => {
            alert("Feedback submitted successfully.");
            location.reload();
        })
        .catch(() => {
            alert("Error submitting feedback.");
            btn.disabled = false;
            btn.innerHTML = 'RETRY';
        });
    });
</script>
</body>
</html>
