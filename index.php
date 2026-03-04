<?php
include "config.php";

// Pagination Logic
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    [cite_start]// Count fresh comments from the new table [cite: 1]
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);

    // Fetch fresh comments using the 'comment_text' column
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
    <link rel="stylesheet" href="style.css">
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
        <span class="badge">SENIOR ACCOUNTANT | [cite_start]FINANCIAL OPERATIONS PARTNER</span> [cite: 1]
        [cite_start]<h2>Decision-Ready <span>Financials</span> Without Compromise.</h2> [cite: 1]
        <p>I manage a structured portfolio of businesses, ensuring each client receives accurate, timely, and decision-ready financials. [cite_start]With 17+ years of experience across the US, Australia, and the Middle East.</p> [cite: 1]
        <a href="#contact" class="btn-primary">SECURE A PARTNERSHIP</a>
    </div>
    <div class="hero-image">
        <img src="Afryl.jpg" alt="Afryl Lou Okit">
    </div>
</section>

<section id="services">
    [cite_start]<h2 class="section-title">Core Strategic Values</h2> [cite: 1]
    <div class="grid">
        <div class="card">
            <i class="fas fa-magic"></i>
            <h3>Financial Transformation</h3>
            [cite_start]<p>Transform disorganized books into decision-ready financials with no surprises.</p> [cite: 1]
        </div>
        <div class="card">
            <i class="fas fa-search-dollar"></i>
            <h3>Risk Identification</h3>
            [cite_start]<p>Identify risks, discrepancies, and inefficiencies early through clear financial insights.</p> [cite: 1]
        </div>
        <div class="card">
            <i class="fas fa-chart-line"></i>
            <h3>Scalable Systems</h3>
            [cite_start]<p>Maintain systems that support scaling businesses without operational breakdowns.</p> [cite: 1]
        </div>
    </div>
</section>

<section id="portfolio" class="light-bg">
    [cite_start]<h2 class="section-title">System Mastery</h2> [cite: 1]
    <div class="tag-container">
        <span class="tag">QuickBooks Online</span> <span class="tag">Xero</span> 
        <span class="tag">NetSuite</span> <span class="tag">Bill.com</span> 
        <span class="tag">Dext</span> <span class="tag">Stripe</span> 
        <span class="tag">Salesforce</span> <span class="tag">ClickUp</span> 
        [cite_start]<span class="tag">Floqast</span> [cite: 1]
    </div>

    <div class="featured-work">
        [cite_start]<h3>Selected Engagement: BLUESKY INVESTMENTS LLC</h3> [cite: 1]
        [cite_start]<p><strong>Finance/Accounting Lead (Nov 2018 - Present)</strong></p> [cite: 1]
        <ul>
            [cite_start]<li>Lead end-to-end accounting and financial operations.</li> [cite: 1]
            [cite_start]<li>Oversee multi-entity accounting and intercompany transactions.</li> [cite: 1]
            [cite_start]<li>Perform budget vs. actual analysis to identify financial drivers.</li> [cite: 1]
        </ul>
    </div>
</section>

<section id="comments">
    <div class="contact-container">
        <div>
            <h2 class="section-title">Client Feedback</h2>
            <?php if (empty($comments)): ?>
                <p>No feedback available yet.</p>
            <?php else: ?>
                <?php foreach ($comments as $row): ?>
                    <div class="card" style="margin-bottom: 15px; padding: 20px;">
                        <p style="font-style: italic;">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                        <p style="margin-top: 10px; font-weight: 700; color: var(--primary);">
                            — <?php echo htmlspecialchars($row['name']); ?>, <?php echo htmlspecialchars($row['company']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="form-box">
            <h3>Leave a Comment</h3>
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Company" required>
                <textarea name="comment_text" rows="4" placeholder="Your Message..." required></textarea>
                <button type="submit" id="commentBtn" class="btn-primary" style="width: 100%;">SUBMIT</button>
            </form>
        </div>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. ALL RIGHTS RESERVED.
</footer>

<script src="script.js"></script>
</body>
</html>