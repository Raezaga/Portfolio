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
    <title>Afryl Lou Okit | Financial Operations Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #0a0a0b; /* Obsidian */
            --accent: #d4af37;  /* Metallic Gold */
            --accent-soft: #f4e8c1;
            --bg: #ffffff; 
            --light: #f9f9fb; 
            --border: #ececed; 
            --text: #1a1a1c; 
            --text-dim: #6b7280; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--bg); line-height: 1.7; overflow-x: hidden; }

        /* Premium Navigation */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 25px 8%; 
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); 
            border-bottom: 1px solid var(--border); display: flex; 
            justify-content: space-between; z-index: 1000; align-items: center; 
        }
        nav h1 { font-family: 'Plus Jakarta Sans'; font-size: 1rem; font-weight: 800; color: var(--primary); letter-spacing: 2px; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--text); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        nav ul a:hover { color: var(--accent); }

        section { padding: 160px 10% 100px; max-width: 1600px; margin: 0 auto; }

        /* Hero: Ultra-Modern */
        .hero { display: flex; align-items: center; gap: 80px; min-height: 95vh; }
        .hero-text { flex: 1.4; }
        .hero-text h2 { 
            font-family: 'Plus Jakarta Sans'; font-size: 5.5rem; color: var(--primary); 
            margin-bottom: 30px; line-height: 0.95; letter-spacing: -4px; font-weight: 800;
        }
        .hero-text h2 span { 
            background: linear-gradient(90deg, #b8860b, #d4af37, #f4e8c1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .badge { 
            color: var(--accent); font-weight: 800; font-size: 0.9rem; 
            letter-spacing: 5px; display: block; margin-bottom: 20px; text-transform: uppercase; 
        }

        .hero-image { flex: 0.6; position: relative; }
        .hero-image img { 
            width: 100%; aspect-ratio: 1/1; border-radius: 40px; 
            object-fit: cover; filter: grayscale(10%) contrast(110%);
            box-shadow: 40px 40px 0px var(--light); transition: 0.5s;
        }
        .hero-image:hover img { transform: translate(-10px, -10px); box-shadow: 50px 50px 0px var(--accent-soft); }

        /* Buttons */
        .hero-actions { display: flex; gap: 20px; margin-top: 50px; }
        .btn-primary { 
            padding: 24px 50px; background: var(--primary); color: white; 
            border: none; border-radius: 4px; font-weight: 700; font-size: 0.9rem; 
            text-transform: uppercase; letter-spacing: 2px; cursor: pointer; text-decoration: none; transition: 0.4s; 
        }
        .btn-primary:hover { background: var(--accent); color: var(--primary); transform: scale(1.02); }
        
        .btn-cv { 
            padding: 24px 50px; background: transparent; color: var(--primary); 
            border: 1px solid var(--primary); border-radius: 4px; font-weight: 700; 
            font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; 
            text-decoration: none; transition: 0.4s; display: inline-flex; align-items: center; gap: 12px; 
        }
        .btn-cv:hover { border-color: var(--accent); color: var(--accent); }

        /* Grid & Cards */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px; margin-top: 60px; }
        .card { 
            padding: 60px; background: white; border: 1px solid var(--border); 
            border-radius: 0; transition: 0.4s ease; position: relative;
        }
        .card:hover { border-color: var(--accent); transform: translateY(-10px); }
        .card i { font-size: 2rem; color: var(--accent); margin-bottom: 30px; display: block; }
        .card h3 { font-family: 'Plus Jakarta Sans'; font-size: 1.5rem; margin-bottom: 15px; font-weight: 800; }

        /* System Mastery Tags */
        .tag-container { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 40px; }
        .tag { 
            padding: 12px 28px; background: var(--light); color: var(--primary); 
            border-radius: 0; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 1px; border: 1px solid transparent; transition: 0.3s;
        }
        .tag:hover { border-color: var(--accent); background: white; }

        /* Featured Block */
        .featured-box { 
            margin-top: 100px; padding: 80px; background: var(--primary); 
            color: white; border-radius: 0; position: relative; overflow: hidden;
        }
        .featured-box::after {
            content: ""; position: absolute; top: -50px; right: -50px; 
            width: 200px; height: 200px; background: var(--accent); 
            opacity: 0.1; border-radius: 50%;
        }

        /* Forms */
        .form-box input, .form-box textarea { 
            width: 100%; padding: 22px; margin-bottom: 20px; border: none; 
            border-bottom: 2px solid var(--border); background: var(--light); 
            font-family: inherit; font-weight: 600; outline: none; transition: 0.3s;
        }
        .form-box input:focus, .form-box textarea:focus { border-color: var(--accent); background: white; }

        /* Feedback Section */
        .feedback-card { 
            background: var(--light); padding: 40px; margin-bottom: 30px; 
            border-left: 4px solid var(--accent); 
        }

        footer { text-align: center; padding: 100px 5%; border-top: 1px solid var(--border); color: var(--text-dim); font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }

        @media (max-width: 1200px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-text h2 { font-size: 3.5rem; letter-spacing: -2px; }
            .hero-image img { width: 350px; height: 350px; box-shadow: none; }
            .hero-actions { flex-direction: column; align-items: center; }
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
        <li><a href="#contact">Inquiry</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <span class="badge">Mastery in Financial Operations</span>
        <h2>High-Stakes <span>Financial</span> Clarity.</h2>
        <p style="font-size: 1.4rem; color: var(--text-dim); margin-bottom: 20px; max-width: 800px;">
            Structured management for high-growth portfolios. I deliver the accurate, timely, and decision-ready financials required for aggressive scaling.
        </p>
        <p style="font-size: 1.1rem; font-weight: 400; color: var(--text-dim);">
            17+ Years Experience | US • Australia • Middle East
        </p>
        
        <div class="hero-actions">
            <a href="#contact" class="btn-primary">Initiate Partnership</a>
            <a href="Afryl_Lou_Okit_CV.pdf" download class="btn-cv">
                <i class="fas fa-file-download"></i> Executive CV
            </a>
        </div>
    </div>
    <div class="hero-image">
        <img src="Afryl.jpg" alt="Afryl Lou Okit">
    </div>
</section>

<section id="services" style="background: var(--light);">
    <h2 style="text-align: center; font-family: 'Plus Jakarta Sans'; font-size: 3rem; margin-bottom: 60px; font-weight: 800; letter-spacing: -2px;">Core Strategic Values</h2>
    <div class="grid">
        <div class="card">
            <i class="fas fa-shield-check"></i>
            <h3>Audit-Ready Integrity</h3>
            <p>We move past simple bookkeeping. I build reconciled, audit-ready financial foundations that stand up to institutional scrutiny.</p>
        </div>
        <div class="card">
            <i class="fas fa-gem"></i>
            <h3>Decision Intelligence</h3>
            <p>Data without insight is noise. I provide the clarity needed to identify risks and capital inefficiencies before they impact the bottom line.</p>
        </div>
        <div class="card">
            <i class="fas fa-infinity"></i>
            <h3>Scalable Infrastructure</h3>
            <p>Implementing rigorous workflows and systems (NetSuite, Floqast) that ensure your finance function grows faster than your revenue.</p>
        </div>
    </div>
</section>

<section id="portfolio">
    <h2 style="margin-bottom: 40px; font-family: 'Plus Jakarta Sans'; font-size: 2.5rem; font-weight: 800; letter-spacing: -2px;">System Mastery</h2>
    <div class="tag-container">
        <span class="tag">NetSuite</span> 
        <span class="tag">QuickBooks Online</span> 
        <span class="tag">Xero</span> 
        <span class="tag">Floqast</span>
        <span class="tag">Bill.com</span> 
        <span class="tag">Dext</span> 
        <span class="tag">Stripe</span> 
        <span class="tag">Salesforce</span> 
        <span class="tag">ClickUp</span> 
    </div>
    
    <div class="featured-box">
        <h3 style="color: var(--accent); margin-bottom: 20px; font-size: 1.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;">Engagement Spotlight: BLUESKY INVESTMENTS LLC</h3>
        <p style="font-size: 1.2rem; color: var(--accent-soft);"><strong>Finance/Accounting Lead</strong></p>
        <ul style="margin-top: 30px; padding-left: 20px; color: #cbd5e1; font-size: 1.1rem; line-height: 2;">
            <li>Full-spectrum multi-entity financial oversight.</li>
            <li>Precision intercompany transaction mapping.</li>
            <li>Predictive budget vs. actual modeling for executive decision-making.</li>
        </ul>
    </div>
</section>

<section id="contact">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 100px; align-items: start;">
        <div>
            <h2 style="font-size: 4rem; font-family: 'Plus Jakarta Sans'; margin-bottom: 30px; font-weight: 800; letter-spacing: -3px; line-height: 1;">Connect.</h2>
            <p style="font-size: 1.2rem; color: var(--text-dim); margin-bottom: 40px;">Bespoke financial partnership for international real estate and e-commerce portfolios.</p>
            <p style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Direct Contact</p>
            <p style="font-weight: 800; font-size: 1.1rem;">afryllou.consulting@gmail.com</p>
            <p style="font-weight: 800; font-size: 1.1rem;">+63 999 586 61908</p>
        </div>
        <div class="form-box">
            <form id="commentForm">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Entity / Company Name" required>
                <textarea name="comment_text" rows="5" placeholder="Inquiry Details..." required></textarea>
                <button type="submit" id="commentBtn" class="btn-primary" style="width: 100%;">Submit Inquiry</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback-display" style="padding-top: 0;">
    <h3 style="margin-bottom: 50px; font-family: 'Plus Jakarta Sans'; font-size: 2rem; font-weight: 800; letter-spacing: -1px;">Executive Feedback</h3>
    <div id="comments-list">
        <?php if (empty($comments)): ?>
            <p style="color: var(--text-dim);">Awaiting public testimonials.</p>
        <?php else: ?>
            <?php foreach ($comments as $row): ?>
                <div class="feedback-card">
                    <p style="font-style: italic; font-size: 1.2rem; color: var(--primary); font-family: 'Inter';">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                    <p style="margin-top: 20px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent);">
                        — <?php echo htmlspecialchars($row['name']); ?> / <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. PRECISE FINANCIAL OPERATIONS.
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
