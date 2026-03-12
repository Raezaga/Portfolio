<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Professional Review | Afryl Lou Okit</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
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
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg); 
            color: var(--slate); 
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .glass-card { 
            width: 100%;
            max-width: 700px;
            background: var(--card-bg); 
            padding: 60px; 
            border: 1px solid rgba(255,255,255,0.05); 
            box-shadow: 0 40px 100px rgba(0,0,0,0.5); 
            text-align: center;
        }

        .form-box input, .form-box textarea, .form-box select { 
            width: 100%; 
            padding: 20px 0; 
            margin-bottom: 25px; 
            border: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            background: transparent; 
            color: var(--white); 
            outline: none; 
            font-family: inherit; 
        }

        .form-box select option { background: #070a13; color: white; }

        .btn-gold { 
            width: 100%;
            padding: 22px; 
            background: var(--gold); 
            color: var(--bg); 
            border: none; 
            font-weight: 800; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 3px; 
            cursor: pointer; 
            transition: var(--transition); 
        }

        .btn-gold:hover { background: var(--white); transform: translateY(-3px); }

        h2 { font-family: 'Playfair Display', serif; color: white; font-size: 2.5rem; margin-bottom: 10px; }
        p { margin-bottom: 40px; font-size: 0.9rem; letter-spacing: 1px; }
        
        #submissionNote { margin-top: 20px; color: var(--gold); font-weight: 700; display: none; }
    </style>
</head>
<body>

<div class="glass-card">
    <h2>Client Testimonial</h2>
    <p>Your feedback helps maintain the standard of financial excellence.</p>

    <form id="reviewForm" class="form-box">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="company" placeholder="Organization / Company" required>
        <input type="text" name="position" placeholder="Your Professional Title" required>
        
        <select name="country_code" id="countrySelect" required>
            <option value="" disabled selected>Select Your Country</option>
        </select>
        
        <textarea name="comment_text" rows="4" placeholder="Share your experience working with me..." required></textarea>
        
        <button type="submit" id="reviewBtn" class="btn-gold">Submit Review</button>
    </form>
    
    <p id="submissionNote"><i class="fas fa-check-circle"></i> Thank you. Your review has been submitted for verification.</p>
</div>

<script>
    // Reusing your country list for consistency
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

        // Uses your existing save script
        fetch('save_comment.php', { method: 'POST', body: new FormData(this) })
        .then(response => {
            if(response.ok) {
                btn.style.display = 'none';
                document.getElementById('submissionNote').style.display = 'block';
                this.style.opacity = '0.3';
                this.style.pointerEvents = 'none';
            } else {
                alert("Submission failed. Please try again.");
                btn.disabled = false;
                btn.innerHTML = 'Submit Review';
            }
        });
    });
</script>
</body>
</html>