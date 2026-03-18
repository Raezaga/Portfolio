<?php
include "config.php";

// 1. PAGINATION & DATABASE LOGIC
$limit = 4; 
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
    error_log($e->getMessage());
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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:ital,wght@0,400;1,400&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'Gonzaga';
            src: url('fonts/Gonzaga.woff2') format('woff2'),
                 url('fonts/Gonzaga.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--slate); line-height: 1.7; overflow-x: hidden; }

        /* Navigation */
        nav { 
            position: fixed; 
            top: 0; 
            width: 100%; 
            padding: 15px 8%; 
            background: rgba(7, 10, 19, 0.95); 
            backdrop-filter: blur(20px); 
            display: flex; 
            justify-content: space-between; 
            z-index: 1000; 
            align-items: center; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
        }

        nav h1 { 
            font-family: 'Gonzaga', 'Cinzel', serif; 
            font-size: 2.2rem; 
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav ul { display: flex; list-style: none; gap: 30px; align-items: center; }
        nav ul a { text-decoration: none; color: var(--slate); font-weight: 400; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        nav ul a:hover { color: var(--gold); }
        .nav-admin { border: 1px solid var(--gold); padding: 8px 15px; color: var(--gold) !important; border-radius: 4px; }

        section { padding: 120px 10% 80px; max-width: 1500px; margin: 0 auto; }

        /* Hero Section */
        .hero { display: flex; align-items: center; gap: 60px; min-height: 100vh; }
        .hero-text { flex: 1.1; }
        .hero-text h2 { font-family: 'Playfair Display', serif; font-size: 5.5rem; color: var(--white); line-height: 1.1; font-weight: 700; letter-spacing: -3px; }
        .hero-text h2 span { color: var(--gold); font-style: italic; }
        .hero-image { flex: 0.9; position: relative; display: flex; justify-content: center; }
        .img-wrapper { position: relative; width: 100%; max-width: 580px; aspect-ratio: 1/1; border-radius: 50%; border: 3px solid var(--gold); overflow: hidden; background: var(--bg); z-index: 2; box-shadow: 0 0 80px rgba(0,0,0,0.6); }
        .hero-image img { width: 115%; height: 115%; object-fit: cover; object-position: center 20%; margin-left: -7.5%; }

        .highlight-gold { color: var(--gold); text-transform: uppercase; font-weight: 600; }

        /* Buttons */
        .btn-gold { padding: 22px 45px; background: var(--gold); color: var(--bg); border: none; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition); display: inline-block; text-align: center; }
        .btn-outline { padding: 22px 45px; border: 2px solid var(--gold); color: var(--gold); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; text-decoration: none; transition: var(--transition); display: inline-block; }
        .btn-gold:hover, .btn-outline:hover { background: var(--white); color: var(--bg); border-color: var(--white); transform: translateY(-5px); }

        /* Tech Grid */
        .sw-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 40px; }
        .sw-pill { 
            background: rgba(197, 160, 89, 0.05); 
            border: 1px solid var(--gold); 
            padding: 25px; 
            text-align: center; 
            transition: var(--transition); 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
        }
        .sw-pill img { margin-bottom: 15px; width: 100px; transition: 0.3s; }
        .sw-pill span { color: var(--white); font-weight: 400; font-size: 0.65rem; letter-spacing: 2px; }

        /* Feedback Grid - Fixed Squashing */
        .feedback-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            gap: 40px; 
            margin-top: 60px; 
            align-items: stretch;
        }

        .feedback-item { 
            background: var(--card-bg); 
            padding: 50px; 
            border: 1px solid rgba(255,255,255,0.03); 
            transition: 0.3s; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 480px;
        }

        .review-content {
            font-family: 'Playfair Display', serif; 
            font-size: 1.25rem; 
            color: var(--white); 
            font-style: italic; 
            font-weight: 400; /* No Bold */
            line-height: 1.8; 
            margin-bottom: 30px;
            max-height: 280px;
            overflow-y: auto;
            padding-right: 15px;
        }

        /* Gold Scrollbar */
        .review-content::-webkit-scrollbar { width: 3px; }
        .review-content::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .review-content::-webkit-scrollbar-thumb { background: var(--gold); }

        .feedback-author {
            margin-top: auto;
            padding-top: 25px;
            border-top: 1px solid rgba(197, 160, 89, 0.15);
        }

        .author-name {
            font-weight: 400; /* No Bold */
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 3px; 
            color: var(--gold); 
            display: flex; 
            align-items: center; 
            gap: 10px;
        }

        /* Connect Section */
        .glass-card { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: var(--card-bg); 
            padding: 80px; 
            border: 1px solid rgba(255,255,255,0.05); 
            box-shadow: 0 40px 100px rgba(0,0,0,0.5); 
        }
        .form-box { width: 100%; max-width: 800px; }
        .form-box input, .form-box textarea, .form-box select { width: 100%; padding: 20px 0; margin-bottom: 30px; border: none; border-bottom: 1px solid rgba(255,255,255,0.1); background: transparent; color: var(--white); outline: none; font-family: inherit; }
        .form-box select option { background: #070a13; color: white; }

        .pagination { margin-top: 50px; display: flex; justify-content: center; gap: 10px; }
        .pagination a { text-decoration: none; padding: 12px 18px; border: 1px solid rgba(255,255,255,0.1); color: var(--white); font-weight: 600; font-size: 0.7rem; transition: 0.3s; }
        .pagination a.active { background: var(--gold); color: var(--bg); border-color: var(--gold); }

        footer { padding: 60px 10%; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; }

        @media (max-width: 1100px) { 
            .hero { flex-direction: column; text-align: center; padding-top: 150px; } 
            .hero-text h2 { font-size: 3.5rem; } 
            .feedback-grid { grid-template-columns: 1fr; }
            .glass-card { padding: 40px; }
            nav h1 { font-size: 1.5rem; }
            nav ul { display: none; }
        }
    </style>
</head>
<body>

<nav>
    <h1>AFRYL LOU OKIT</h1>
    <ul>
        <li><a href="#hero">Overview</a></li>
        <li><a href="#connect">Connect</a></li>
        <li><a href="#feedback">Reviews</a></li>
        <li><a href="admin.php" class="nav-admin">Admin Portal</a></li>
    </ul>
</nav>

<section id="hero" class="hero">
    <div class="hero-text">
        <div style="border: 1px solid var(--gold); padding: 10px 20px; display: inline-block; color: var(--gold); margin-bottom: 25px; letter-spacing: 4px; font-weight: 600; font-size: 0.7rem;">
           17+ Years Delivering Audit-Ready Financials for Growing Businesses
        </div>
        <h2>Transforming <span>Complex</span> Financials.</h2>
        <p style="font-size: 1.3rem; font-weight: 300; margin-bottom: 45px; max-width: 650px; color: var(--slate);">
            Senior Accountant & Financial Operations Partner helping global businesses gain 
            <span class="highlight-gold">clarity</span>, 
            <span class="highlight-gold">control</span>, and 
            <span class="highlight-gold">confidence</span> in their numbers.
        </p>
        <div class="hero-btns">
            <a href="#connect" class="btn-gold">Secure Partnership</a>
            <a href="#" target="_blank" class="btn-outline"><i class="fas fa-file-download" style="margin-right: 10px;"></i>Download CV</a>
        </div>
    </div>
    <div class="hero-image">
        <div class="img-wrapper"><img src="afryl.jpg" alt="Afryl Lou Okit"></div>
    </div>
</section>

<section id="feedback" style="border-top: 1px solid rgba(255,255,255,0.05);">
    <h3 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white;">What Clients and Leadership Say</h3>
    <div class="feedback-grid">
        <?php if(!empty($comments)): foreach ($comments as $row): ?>
            <div class="feedback-item">
                <div class="review-content">
                    "<?php echo htmlspecialchars($row['comment_text']); ?>"
                </div>
                
                <div class="feedback-author">
                    <p class="author-name">
                        <?php if(!empty($row['country_code'])): ?>
                            <img src="https://flagcdn.com/w20/<?php echo strtolower(htmlspecialchars($row['country_code'])); ?>.png" width="20" alt="Flag">
                        <?php endif; ?>
                        — <?php echo htmlspecialchars($row['name']); ?> 
                    </p>
                    <p style="margin-top: 5px; font-size: 0.7rem; color: var(--slate);">
                        <span style="color:var(--white); opacity:0.8;"><?php echo htmlspecialchars($row['position'] ?? ''); ?></span> 
                        / <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p style="text-align: center; grid-column: 1/-1; opacity: 0.5;">Awaiting professional Reviews.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>#feedback">← PREV</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>#feedback" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>#feedback">NEXT →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<section id="connect">
    <div class="glass-card">
        <div class="form-box">
            <form action="send_email.php" method="POST">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: white; line-height: 1; text-align:center;">Let's Connect</h3>
                <p style="margin: 25px 0 45px; font-size: 1.1rem; text-align:center;">Secure financial partnership for international entities.</p>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="company" placeholder="Organization" required>
                <input type="email" name="email" placeholder="Professional Email" required>
                <textarea name="message" rows="4" placeholder="How can I assist your financials?" required></textarea>
                <button type="submit" class="btn-gold" style="width: 100%;">Send Inquiry</button>
            </form>
        </div>
    </div>
</section>

<section style="background: rgba(255, 255, 255, 0.02); border-top: 1px solid rgba(255,255,255,0.05);">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <h4 style="color: white; font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 20px;">Leave a Review</h4>
        <form id="reviewForm" class="form-box">
            <input type="text" name="name" placeholder="Display Name" required>
            <input type="text" name="company" placeholder="Organization" required>
            <input type="text" name="position" placeholder="Professional Title" required>
            <select name="country_code" id="countrySelect" required>
                <option value="" disabled selected>Select Your Country</option>
            </select>
            <textarea name="comment_text" rows="3" placeholder="Write your testimonial..." required></textarea>
            <button type="submit" id="reviewBtn" class="btn-gold">Post Review</button>
        </form>
        <p id="submissionNote" style="margin-top: 15px; font-size: 0.8rem; color: var(--gold); display: none;">Submitted for admin approval.</p>
    </div>
</section>

<footer>
    <p>PH: +63 999 586 6190 | E: afryllou.consulting@gmail.com</p>
    <p style="margin-top: 20px;">&copy; <?php echo date("Y"); ?> AFRYL LOU OKIT. SENIOR FINANCIAL OPERATIONS PARTNER.</p>
</footer>

<script>
    // Country logic and form submission script remain the same
    const countries = { "af": "Afghanistan", "al": "Albania", "dz": "Algeria", "as": "American Samoa", "ad": "Andorra", "ao": "Angola", "ai": "Anguilla", "ag": "Antigua and Barbuda", "ar": "Argentina", "am": "Armenia", "au": "Australia", "at": "Austria", "az": "Azerbaijan", "bs": "Bahamas", "bh": "Bahrain", "bd": "Bangladesh", "bb": "Barbados", "by": "Belarus", "be": "Belgium", "bz": "Belize", "bj": "Benin", "bm": "Bermuda", "bt": "Bhutan", "bo": "Bolivia", "ba": "Bosnia and Herzegovina", "bw": "Botswana", "br": "Brazil", "bn": "Brunei", "bg": "Bulgaria", "bf": "Burkina Faso", "bi": "Burundi", "kh": "Cambodia", "cm": "Cameroon", "ca": "Canada", "cv": "Cape Verde", "ky": "Cayman Islands", "cf": "Central African Republic", "td": "Chad", "cl": "Chile", "cn": "China", "co": "Colombia", "km": "Comoros", "cg": "Congo", "ck": "Cook Islands", "cr": "Costa Rica", "hr": "Croatia", "cu": "Cuba", "cy": "Cyprus", "cz": "Czech Republic", "dk": "Denmark", "dj": "Djibouti", "dm": "Dominica", "do": "Dominican Republic", "ec": "Ecuador", "eg": "Egypt", "sv": "El Salvador", "gq": "Equatorial Guinea", "er": "Eritrea", "ee": "Estonia", "et": "Ethiopia", "fj": "Fiji", "fi": "Finland", "fr": "France", "ga": "Gabon", "gm": "Gambia", "ge": "Georgia", "de": "Germany", "gh": "Ghana", "gr": "Greece", "gd": "Grenada", "gu": "Guam", "gt": "Guatemala", "gn": "Guinea", "gw": "Guinea-Bissau", "gy": "Guyana", "ht": "Haiti", "hn": "Honduras", "hk": "Hong Kong", "hu": "Hungary", "is": "Iceland", "in": "India", "id": "Indonesia", "ir": "Iran", "iq": "Iraq", "ie": "Ireland", "il": "Israel", "it": "Italy", "jm": "Jamaica", "jp": "Japan", "jo": "Jordan", "kz": "Kazakhstan", "ke": "Kenya", "ki": "Kiribati", "kp": "North Korea", "kr": "South Korea", "kw": "Kuwait", "kg": "Kyrgyzstan", "la": "Laos", "lv": "Latvia", "lb": "Lebanon", "ls": "Lesotho", "lr": "Liberia", "ly": "Libya", "li": "Liechtenstein", "lt": "Lithuania", "lu": "Luxembourg", "mo": "Macao", "mk": "North Macedonia", "mg": "Madagascar", "mw": "Malawi", "my": "Malaysia", "mv": "Maldives", "ml": "Mali", "mt": "Malta", "mh": "Marshall Islands", "mq": "Martinique", "mr": "Mauritania", "mu": "Mauritius", "mx": "Mexico", "fm": "Micronesia", "md": "Moldova", "mc": "Monaco", "mn": "Mongolia", "me": "Montenegro", "ms": "Montserrat", "ma": "Morocco", "mz": "Mozambique", "mm": "Myanmar", "na": "Namibia", "nr": "Nauru", "np": "Nepal", "nl": "Netherlands", "nz": "New Zealand", "ni": "Nicaragua", "ne": "Niger", "ng": "Nigeria", "nu": "Nuue", "no": "Norway", "om": "Oman", "pk": "Pakistan", "pw": "Palau", "ps": "Palestine", "pa": "Panama", "pg": "Papua New Guinea", "py": "Paraguay", "pe": "Peru", "ph": "Philippines", "pl": "Poland", "pt": "Portugal", "pr": "Puerto Rico", "qa": "Qatar", "re": "Reunion", "ro": "Romania", "ru": "Russia", "rw": "Rwanda", "kn": "Saint Kitts and Nevis", "lc": "Saint Lucia", "vc": "Saint Vincent", "ws": "Samoa", "sm": "San Marino", "st": "Sao Tome and Principe", "sa": "Saudi Arabia", "sn": "Senegal", "rs": "Serbia", "sc": "Seychelles", "sl": "Sierra Leone", "sg": "Singapore", "sk": "Slovakia", "si": "Slovenia", "sb": "Solomon Islands", "so": "Somalia", "za": "South Africa", "es": "Spain", "lk": "Sri Lanka", "sd": "Sudan", "sr": "Suriname", "sz": "Swaziland", "se": "Sweden", "ch": "Switzerland", "sy": "Syria", "tw": "Taiwan", "tj": "Tajikistan", "tz": "Tanzania", "th": "Thailand", "tl": "Timor-Leste", "tg": "Togo", "tk": "Tokelau", "to": "Tonga", "tt": "Trinidad and Barbuda", "tn": "Tunisia", "tr": "Turkey", "tm": "Turkmenistan", "tv": "Tuvalu", "ug": "Uganda", "ua": "Ukraine", "ae": "United Arab Emirates", "gb": "United Kingdom", "us": "United States", "uy": "Uruguay", "uz": "Uzbekistan", "vu": "Vanuatu", "ve": "Venezuela", "vn": "Vietnam", "vg": "Virgin Islands, British", "vi": "Virgin Islands, U.S.", "ye": "Yemen", "zm": "Zambia", "zw": "Zimbabwe" };

    const countrySelect = document.getElementById('countrySelect');
    for (const [code, name] of Object.entries(countries)) {
        const option = document.createElement('option');
        option.value = code;
        option.textContent = name;
        countrySelect.appendChild(option);
    }

    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('reviewBtn');
        btn.innerHTML = 'SUBMITTING...';
        btn.disabled = true;

        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(response => {
            if(response.ok) {
                btn.innerHTML = 'SUBMITTED';
                document.getElementById('submissionNote').style.display = 'block';
                this.reset();
            } else {
                alert("Submission failed. Please try again.");
                btn.disabled = false;
                btn.innerHTML = 'Post Review';
            }
        });
    });
</script>
</body>
</html>
