<?php
include "config.php";

// 1. PAGINATION & DATABASE LOGIC
$limit = 6; 
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
            src: url('fonts/Gonzaga.woff2') format('woff2'), url('fonts/Gonzaga.ttf') format('truetype');
            font-weight: bold;
        }

        :root { 
            --bg: #070a13; 
            --card-bg: rgba(255, 255, 255, 0.03); 
            --gold: #c5a059; 
            --slate: #94a3b8; 
            --white: #ffffff; 
            --transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--slate); line-height: 1.7; overflow-x: hidden; }

        /* --- NAVIGATION --- */
        nav { 
            position: fixed; top: 0; width: 100%; padding: 20px 8%; 
            background: rgba(7, 10, 19, 0.9); backdrop-filter: blur(15px); 
            display: flex; justify-content: space-between; align-items: center; 
            z-index: 2000; border-bottom: 1px solid rgba(255,255,255,0.05); 
        }
        
        nav h1 { 
            font-family: 'Gonzaga', 'Cinzel', serif; font-size: clamp(1.2rem, 3vw, 2.2rem); 
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        nav ul { display: flex; list-style: none; gap: 30px; align-items: center; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        nav ul a:hover { color: var(--gold); }
        .nav-admin { border: 1px solid var(--gold); padding: 8px 18px; color: var(--gold) !important; border-radius: 4px; }

        /* --- HERO SECTION --- */
        section { padding: 120px 8% 80px; max-width: 1500px; margin: 0 auto; }
        .hero { display: flex; align-items: center; gap: 60px; min-height: 90vh; }
        .hero-text { flex: 1.1; animation: fadeInUp 1s ease; }
        .hero-text h2 { font-family: 'Playfair Display', serif; font-size: clamp(2.8rem, 7vw, 5.5rem); color: var(--white); line-height: 1.1; margin-bottom: 20px; }
        .hero-text h2 span { color: var(--gold); font-style: italic; }
        
        .hero-image { flex: 0.9; position: relative; display: flex; justify-content: center; animation: fadeIn 1.5s ease; }
        .img-wrapper { 
            width: 100%; max-width: 550px; aspect-ratio: 1/1; border-radius: 50%; 
            border: 2px solid var(--gold); overflow: hidden; position: relative;
            box-shadow: 0 0 60px rgba(197, 160, 89, 0.15);
        }
        .hero-image img { width: 115%; height: 115%; object-fit: cover; object-position: center 15%; margin-left: -7.5%; transition: 0.5s; }
        .hero-image img:hover { transform: scale(1.05); }

        /* --- BUTTONS --- */
        .btn-gold, .btn-outline { 
            padding: 20px 40px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; 
            letter-spacing: 2px; cursor: pointer; text-decoration: none; display: inline-block; 
            transition: var(--transition); border-radius: 2px;
        }
        .btn-gold { background: var(--gold); color: var(--bg); border: 1px solid var(--gold); margin-right: 15px; }
        .btn-outline { border: 1px solid var(--gold); color: var(--gold); }
        .btn-gold:hover, .btn-outline:hover { background: var(--white); color: var(--bg); border-color: var(--white); transform: translateY(-5px); }

        /* --- TECH GRID --- */
        .sw-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-top: 40px; }
        .sw-pill { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 30px; text-align: center; transition: 0.3s; }
        .sw-pill:hover { border-color: var(--gold); background: rgba(197, 160, 89, 0.05); transform: translateY(-5px); }
        .sw-pill img { width: 100px; filter: grayscale(1) brightness(0.8); transition: 0.4s; }
        .sw-pill:hover img { filter: grayscale(0) brightness(1); }

        /* --- CONNECT CARD --- */
        .glass-card { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 80px; 
            background: var(--card-bg); backdrop-filter: blur(10px); padding: 80px; 
            border: 1px solid rgba(255,255,255,0.05); margin-top: 50px;
        }
        .form-box input, .form-box textarea, .form-box select { 
            width: 100%; padding: 18px 0; margin-bottom: 25px; border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; 
            color: var(--white); outline: none; font-family: inherit; transition: 0.3s;
        }
        .form-box input:focus, .form-box textarea:focus { border-color: var(--gold); }

        /* --- TESTIMONIALS --- */
        .feedback-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-top: 60px; }
        .feedback-item { 
            background: var(--card-bg); padding: 45px; border: 1px solid rgba(255,255,255,0.05); 
            transition: 0.4s; display: flex; flex-direction: column; justify-content: space-between;
        }
        .feedback-item:hover { border-color: var(--gold); transform: translateY(-10px); }
        .quote-text { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--white); font-style: italic; margin-bottom: 30px; }
        
        .client-info { display: flex; align-items: center; gap: 12px; }
        .client-meta { display: flex; flex-direction: column; }
        .client-name { color: var(--gold); font-weight: 800; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; }
        .client-title { font-size: 0.7rem; opacity: 0.7; }

        /* --- ANIMATIONS --- */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 1100px) {
            .hero { flex-direction: column-reverse; text-align: center; padding-top: 150px; }
            .hero-text h2 { font-size: 3.8rem; }
            .glass-card { grid-template-columns: 1fr; gap: 40px; padding: 40px; }
            nav { padding: 15px 5%; }
            nav ul { display: none; } /* Hide menu on mobile or use hamburger */
        }

        @media (max-width: 600px) {
            .hero-text h2 { font-size: 2.8rem; }
            .btn-gold, .btn-outline { width: 100%; margin: 10px 0; }
            .feedback-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#connect">Connect</a></li>
        <li><a href="#feedback">Validation</a></li>
        <li><a href="admin.php" class="nav-admin">Portal</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div style="border: 1px solid var(--gold); padding: 10px 20px; display: inline-block; color: var(--gold); margin-bottom: 25px; letter-spacing: 4px; font-weight: 800; font-size: 0.65rem;">17+ YEARS GLOBAL FINANCIAL STRATEGY</div>
        <h2>Transforming <span>Complex</span> Financials.</h2>
        <p style="font-size: 1.2rem; font-weight: 300; margin-bottom: 45px; max-width: 600px; color: var(--slate);">
            Senior Accountant & Financial Operations Partner helping businesses gain audit-ready clarity and executive-level control.
        </p>
        <div class="hero-btns">
            <a href="#connect" class="btn-gold">Secure Partnership</a>
            <a href="Afryl_Lou_Okit_CV.pdf" target="_blank" class="btn-outline"><i class="fas fa-file-download" style="margin-right: 10px;"></i>Download CV</a>
        </div>
    </div>
    <div class="hero-image">
        <div class="img-wrapper"><img src="afryl.jpg" alt="Afryl Lou Okit"></div>
    </div>
</section>

<section style="background: rgba(255,255,255,0.01); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="text-align: center; margin-bottom: 40px;">
        <span style="color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; font-weight: 800;">TECHNOLOGY INFRASTRUCTURE</span>
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

<section id="connect">
    <div class="glass-card">
        <div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white; line-height: 1;">Let's Connect</h3>
            <p style="margin: 25px 0; font-size: 1.1rem;">Elite financial consulting for international entities and growth-stage companies.</p>
            <?php if(isset($_GET['mail']) && $_GET['mail'] == 'sent'): ?>
                <div style="color: var(--gold); font-weight: 800; border: 1px solid var(--gold); padding: 15px; display: inline-block; margin-top: 20px;">✓ INQUIRY SENT SUCCESSFULLY</div>
            <?php endif; ?>
        </div>
        <div class="form-box">
            <form action="send_email.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Organization" required>
                <input type="email" name="email" placeholder="Professional Email" required>
                <textarea name="message" rows="4" placeholder="How can I assist your financials?" required></textarea>
                <button type="submit" class="btn-gold" style="width: 100%; margin: 0;">Send Message</button>
            </form>
        </div>
    </div>
</section>

<section id="feedback" style="border-top: 1px solid rgba(255,255,255,0.05);">
    <h3 style="text-align: center; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3.5rem); color: white; margin-bottom: 50px;">Executive Validation</h3>
    
    <div class="feedback-grid">
        <?php if(!empty($comments)): foreach ($comments as $row): ?>
            <div class="feedback-item">
                <p class="quote-text">"<?php echo htmlspecialchars($row['comment_text']); ?>"</p>
                <div class="client-info">
                    <?php if(!empty($row['country_code'])): ?>
                        <img src="https://flagcdn.com/w20/<?php echo strtolower($row['country_code']); ?>.png" width="22" style="border-radius:2px;">
                    <?php endif; ?>
                    <div class="client-meta">
                        <span class="client-name"><?php echo htmlspecialchars($row['name']); ?></span>
                        <span class="client-title">
                            <?php echo htmlspecialchars($row['position'] ?? 'Partner'); ?> @ <?php echo htmlspecialchars($row['company']); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p style="text-align: center; grid-column: 1/-1; opacity: 0.5;">Awaiting further professional validation.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 50px;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>#feedback" style="text-decoration: none; padding: 10px 18px; border: 1px solid var(--gold); color: <?php echo ($i == $page) ? 'var(--bg)' : 'var(--gold)'; ?>; background: <?php echo ($i == $page) ? 'var(--gold)' : 'transparent'; ?>; font-weight: 800; font-size: 0.7rem;">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<section style="background: rgba(255, 255, 255, 0.02); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <h4 style="color: white; font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 30px;">Professional Feedback</h4>
        <form id="reviewForm" class="form-box">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="company" placeholder="Organization" required>
            <input type="text" name="position" placeholder="Title (e.g. CEO, Finance Manager)" required>
            <select name="country_code" id="countrySelect" required>
                <option value="" disabled selected>Select Region</option>
            </select>
            <textarea name="comment_text" rows="3" placeholder="Testimonial summary..." required></textarea>
            <button type="submit" id="reviewBtn" class="btn-gold" style="width: 100%;">Submit Review</button>
        </form>
        <p id="submissionNote" style="margin-top: 20px; font-size: 0.8rem; color: var(--gold); display: none; font-weight: 800;">PENDING ADMIN APPROVAL</p>
    </div>
</section>

<footer style="padding: 60px 8%; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; font-size: 0.75rem; letter-spacing: 1px;">
    <p>PH: +63 999 586 6190 | E: afryllou.consulting@gmail.com</p>
    <p style="margin-top: 15px; opacity: 0.5;">&copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.</p>
</footer>

<script>
    // POPULATE COUNTRIES
    const countries = { "us": "United States", "gb": "United Kingdom", "ph": "Philippines", "au": "Australia", "ca": "Canada", "sg": "Singapore", "ae": "United Arab Emirates", "de": "Germany" };
    const countrySelect = document.getElementById('countrySelect');
    for (const [code, name] of Object.entries(countries)) {
        const option = document.createElement('option');
        option.value = code; option.textContent = name;
        countrySelect.appendChild(option);
    }

    // AJAX SUBMISSION
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('reviewBtn');
        btn.innerHTML = 'PROCESSING...';
        btn.disabled = true;

        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(() => { 
            btn.innerHTML = 'SUBMITTED';
            document.getElementById('submissionNote').style.display = 'block';
            this.reset();
        });
    });
</script>

</body>
</html>
