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
        section { padding: 120px 8% 80px; max-width: 1300px; margin: 0 auto; }
        .hero { display: flex; align-items: center; gap: 50px; min-height: 80vh; }
        .hero-text { flex: 1; }
        .hero-text h2 { font-family: 'Plus Jakarta Sans'; font-size: 3.5rem; color: var(--primary); margin-bottom: 20px; line-height: 1.1; }
        .hero-text h2 span { color: var(--accent); }
        .badge { color: var(--accent); font-weight: 800; font-size: 0.75rem; letter-spacing: 2px; display: block; margin-bottom: 10px; }
        .hero-image img { width: 380px; height: 380px; border-radius: 50%; object-fit: cover; border: 8px solid white; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px; }
        .card { padding: 40px; background: white; border: 1px solid var(--border); border-radius: 20px; transition: 0.3s; }
        .card i { font-size: 2rem; color: var(--accent); margin-bottom: 20px; display: block; }
        .tag-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 30px; }
        .tag { padding: 8px 20px; background: var(--light); border: 1px solid var(--border); border-radius: 50px; font-weight: 600; font-size: 0.8rem; }
        .btn-primary { padding: 15px 35px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
        .form-box input, .form-box textarea { width: 100%; padding: 15px; margin-bottom: 15px; border: 1px solid var(--border); border-radius: 10px; background: var(--light); font-family: inherit; }
        footer { text-align: center; padding: 40px; border-top: 1px solid var(--border); color: var(--text-dim); font-size: 0.8rem; }
        @media (max-width: 900px) { .hero { flex-direction: column-reverse; text-align: center; } .hero-image img { width: 280px; height: 280px; } }
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
        <span class="badge">SENIOR ACCOUNTANT | FINANCIAL OPERATIONS PARTNER</span>
        <h2>High-Stakes <span>Financial</span> Clarity.</h2>
        <p>I manage a structured portfolio of businesses, ensuring each client receives accurate, timely, and decision-ready financials without compromise.</p>
        <p style="margin-top: 10px; color: var(--text-dim);">17+ years of experience supporting international businesses across the US, Australia, and the Middle East.</p>
        <a href="#contact" class="btn-primary" style="margin-top: 25px;">SECURE A PARTNERSHIP</a>
    </div>
    <div class="hero-image">
        <img src="Afryl.jpg" alt="Afryl Lou Okit">
    </div>
</section>

<section id="services" style="background: var(--light);">
    <h2 style="text-align: center; font-family: 'Plus Jakarta Sans';">Core Strategic Values</h2>
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
    <h2 style="margin-bottom: 20px; font-family: 'Plus Jakarta Sans';">System Mastery</h2>
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
    
    <div style="margin-top: 60px; padding: 40px; background: var(--primary); color: white; border-radius: 24px;">
        <h3 style="color: var(--accent); margin-bottom: 10px;">Featured Engagement: BLUESKY INVESTMENTS LLC</h3>
        <p><strong>Finance/Accounting Lead (Nov 2018 - Present)</strong></p>
        <ul style="margin-top: 15px; padding-left: 20px; color: #cbd5e1;">
            <li>Lead end-to-end accounting and financial operations.</li>
            <li>Oversee multi-entity accounting and intercompany transactions.</li>
            <li>Perform budget vs. actual analysis to identify financial drivers.</li>
        </ul>
    </div>
</section>

<section id="contact">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px;">
        <div>
            <h2 style="font-size: 2.5rem; font-family: 'Plus Jakarta Sans'; margin-bottom: 20px;">Let's Connect.</h2>
            <p>Supporting international clients across real estate, e-commerce, and service-based industries.</p>
            <p style="margin-top: 20px;"><strong>Email:</strong> afryllou.consulting@gmail.com</p>
            <p><strong>Phone:</strong> +63 999 586 61908</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="company" placeholder="Company" required>
                <textarea name="comment_text" rows="4" placeholder="Feedback or Inquiry..." required></textarea>
                <button type="submit" id="commentBtn" class="btn-primary" style="width: 100%;">SUBMIT</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback-display" style="padding-top: 0;">
    <h3 style="margin-bottom: 30px; font-family: 'Plus Jakarta Sans';">Client Feedback</h3>
    <div id="comments-list">
        <?php if (empty($comments)): ?>
            <p style="color: var(--text-dim);">No feedback available yet.</p>
        <?php else: ?>
            <?php foreach ($comments as $row): ?>
                <div class="card" style="margin-bottom: 15px; padding: 25px;">
                    <p style="font-style: italic;">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                    <p style="margin-top: 10px; font-weight: 700; font-size: 0.85rem;">
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SUBMITTING...';
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
