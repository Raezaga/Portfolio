<?php
include "config.php";

// DATABASE LOGIC: Pagination & Counting
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count for pagination links
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
    $total_comments = $total_stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);

    // Fetch only the comments for the current page
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
        nav h1 { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--white); letter-spacing: 2px; }
        nav ul { display: flex; list-style: none; gap: 40px; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: var(--transition); }
        nav ul a:hover { color: var(--gold); }

        section { padding: 120px 10% 80px; max-width: 1500px; margin: 0 auto; }

        /* Hero Styling */
        .hero { display: flex; align-items: center; gap: 60px; min-height: 90vh; }
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
            margin-bottom: 25px; line-height: 1; font-weight: 700; letter-spacing: -3px;
        }
        .hero-text h2 span { color: var(--gold); font-style: italic; }

        /* BIGGER PHOTO FRAME */
        .hero-image { flex: 0.9; position: relative; display: flex; justify-content: center; }
        .img-glow {
            position: absolute; width: 140%; height: 140%;
            background: radial-gradient(circle, rgba(197, 160, 89, 0.15) 0%, rgba(7, 10, 19, 0) 70%);
            z-index: 1; top: -20%;
        }
        .img-wrapper {
            position: relative; width: 100%; max-width: 580px;
            aspect-ratio: 1/1; border-radius: 50%; border: 3px solid var(--gold); 
            overflow: hidden; background: var(--bg); z-index: 2;
            box-shadow: 0 0 60px rgba(0,0,0,0.5);
        }
        .hero-image img { 
            width: 115%; height: 115%; object-fit: cover; 
            object-position: center 20%; margin-left: -7.5%;
        }

        /* Technology Stack Pills */
        .software-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 40px; }
        .software-pill { 
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);
            padding: 20px; text-align: center; border-radius: 2px; transition: var(--transition);
        }
        .software-pill:hover { border-color: var(--gold); background: rgba(197, 160, 89, 0.05); transform: translateY(-5px); }
        .software-pill i { color: var(--gold); font-size: 1.2rem; margin-bottom: 10px; display: block; }
        .software-pill span { color: var(--white); font-weight: 700; font-size: 0.7rem; letter-spacing: 2px; }

        /* Buttons */
        .hero-actions { display: flex; gap: 20px; margin-top: 40px; }
        .btn-gold { 
            padding: 22px 45px; background: var(--gold); color: var(--bg); 
            border: none; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; 
            letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition);
        }
        .btn-gold:hover { background: var(--white); transform: translateY(-5px); }

        /* Floating Contact Block */
        .glass-card { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 100px; 
            background: var(--card-bg); backdrop-filter: blur(15px);
            padding: 100px; margin-top: 60px; border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }
        .form-box input, .form-box textarea { 
            width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; 
            color: var(--white); outline: none; transition: 0.3s; font-family: inherit;
        }
        .form-box input:focus { border-bottom-color: var(--gold); }

        /* Feedback Grid */
        .feedback-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-top: 60px; }
        .feedback-item { 
            background: var(--card-bg); padding: 50px; border: 1px solid rgba(255,255,255,0.03);
            transition: var(--transition);
        }

        /* PAGINATION STYLING */
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 50px; }
        .pagination a { 
            padding: 12px 20px; border: 1px solid rgba(255,255,255,0.1); color: var(--white); 
            text-decoration: none; font-size: 0.8rem; font-weight: 700; transition: 0.3s;
        }
        .pagination a.active { background: var(--gold); color: var(--bg); border-color: var(--gold); }
        .pagination a:hover:not(.active) { border-color: var(--gold); color: var(--gold); }

        footer { text-align: center; padding: 100px 5%; font-size: 0.7rem; letter-spacing: 4px; color: var(--slate); text-transform: uppercase; }

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
        <li><a href="#software">Systems</a></li>
        <li><a href="#connect">Connect</a></li>
        <li><a href="#feedback">Validation</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div class="badge-large">
            <i class="fas fa-award"></i> 17+ YEARS OF SENIOR EXPERTISE
        </div>
        <h2>High-Stakes <span>Financial</span> Strategy.</h2>
        <p style="font-size: 1.3rem; font-weight: 300; margin-bottom: 40px; max-width: 650px;">
            Senior Accountant specializing in international portfolio management and multi-entity operations for the US, Australia, and Middle East markets.
        </p>
        
        <div class="hero-actions">
            <a href="#connect" class="btn-gold">Secure Partnership</a>
        </div>
    </div>
    <div class="hero-image">
        <div class="img-glow"></div>
        <div class="img-wrapper">
            <img src="afryl.jpg" alt="Afryl Lou Okit">
        </div>
    </div>
</section>

<section id="software" style="padding-top: 60px; padding-bottom: 60px; border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="text-align: center; margin-bottom: 40px;">
        <span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">TECHNOLOGY STACK</span>
    </div>
    <div class="software-grid">
        <div class="software-pill"><i class="fas fa-calculator"></i><span>QUICKBOOKS</span></div>
        <div class="software-pill"><i class="fas fa-chart-pie"></i><span>XERO ADVISOR</span></div>
        <div class="software-pill"><i class="fas fa-database"></i><span>NETSUITE ERP</span></div>
        <div class="software-pill"><i class="fas fa-table"></i><span>ADVANCED EXCEL</span></div>
        <div class="software-pill"><i class="fas fa-project-diagram"></i><span>SAP FICO</span></div>
        <div class="software-pill"><i class="fas fa-file-invoice"></i><span>FRESHBOOKS</span></div>
    </div>
</section>

<section id="connect">
    <div class="glass-card">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--white); margin-bottom: 30px; line-height: 1;">Let's Connect</h2>
            <p style="color: var(--slate); margin-bottom: 50px; font-size: 1.1rem;">Bespoke financial partnership for international entities.</p>
            <p style="font-size: 0.7rem; color: var(--gold); letter-spacing: 3px; margin-bottom: 5px;">DIRECT CHANNEL</p>
            <p style="font-size: 1.1rem; color: var(--white); margin-bottom: 30px;">afryllou.consulting@gmail.com</p>
            <p style="font-size: 0.7rem; color: var(--gold); letter-spacing: 3px; margin-bottom: 5px;">PHONE</p>
            <p style="font-size: 1.1rem; color: var(--white);">+63 999 586 6190</p>
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
        <span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">VALIDATION</span>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: var(--white); margin-top: 10px;">Executive Proof</h3>
    </div>
    
    <div class="feedback-grid">
        <?php if (empty($comments)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: var(--slate); font-style: italic;">Awaiting testimonials.</p>
        <?php else: ?>
            <?php foreach ($comments as $row): ?>
                <div class="feedback-item">
                    <p style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--white); font-style: italic; line-height: 1.6; margin-bottom: 30px;">
                        "<?php echo htmlspecialchars($row['comment_text']); ?>"
                    </p>
                    <p style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 3px; color: var(--gold);">
                        — <?php echo htmlspecialchars($row['name']); ?> <span style="color:rgba(255,255,255,0.1); margin:0 10px;">/</span> <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>#feedback" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
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
