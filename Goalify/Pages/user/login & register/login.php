<?php
require('../../includes/db.php');
require '../../includes/userFunction.php';
require_once('../../includes/session.php');
if (checkCookie($pdo)) {
    $cookieData = unserialize($_COOKIE['rememberMe']);
    $path = '/Goalify/Pages/user/todo/task-overview/task-overview.php';
    if (createUserSession($cookieData['userId'], 'registered-user', $pdo, $path, false)) {
        header("location: $path");
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="../global/global.css">
    <title>Goalify Login Page</title>
</head>
<body>
    <nav id="top-nav-desktop">
        <div class="left">
            <img class="goalify-logo" src="/Goalify/Img/goalify_logo.png" alt="goalify_logo" width="130px" height="35px">
        </div>
        <div class="right">
            <div class="profile-pic"><img src="/Goalify/Img/default_profile.png" alt="user-profile"></div>
        </div>
    </nav>
    <div class="wrapper">
        <div class="left-section">
            <svg viewBox="0 0 305 243" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g filter="url(#filter0_d_11_34)">
                <path d="M110.534 55.9032C130.738 64.4659 154.166 58.7705 168.184 41.8884L171.312 38.1207C183.756 23.1347 203.061 15.6451 222.356 18.3183L224.045 18.5523C252.977 22.5608 274.522 47.0774 278.139 76.0611C278.987 82.8497 280.017 89.8918 281.269 96.8941V96.8941C285.388 119.927 277.687 145.517 260.627 161.529C253.183 168.515 244.18 173.7 228.129 174.356C210.448 175.078 193.007 182.725 185.351 198.678V198.678C179.901 210.035 169.323 218.078 156.924 220.296L144.726 222.477C133.786 224.433 122.525 223.479 112.07 219.709L96.8109 214.208C82.3507 208.994 71.7734 196.458 69.0676 181.326L68.523 178.281C66.2055 165.321 57.2661 154.523 44.9666 149.828V149.828C28.2451 143.444 18.3593 126.139 21.3527 108.492L21.6591 106.686C23.2799 97.1305 28.3352 88.4961 35.8746 82.4057L59.8925 63.0039C74.1686 51.4716 93.6368 48.7418 110.534 55.9032V55.9032Z" fill="#D8F6FA"/>
                <circle cx="42" cy="22" r="22" fill="#D8F6FA"/>
                <circle cx="252" cy="212" r="22" fill="#D8F6FA"/>
                <g clip-path="url(#clip0_11_34)">
                <g filter="url(#filter1_i_11_34)">
                <path d="M44.082 26.9046C44.7419 26.6502 45.2737 26.1441 45.5605 25.4976C45.8472 24.8511 45.8654 24.1172 45.611 23.4573C45.3566 22.7973 44.8505 22.2655 44.204 21.9788C43.5575 21.692 42.8236 21.6738 42.1637 21.9282C41.5038 22.1826 40.9719 22.6887 40.6852 23.3352C40.3984 23.9817 40.3802 24.7156 40.6346 25.3756C40.889 26.0355 41.3951 26.5673 42.0416 26.854C42.6881 27.1408 43.4221 27.159 44.082 26.9046ZM47.2304 12.8303C47.8903 12.5759 48.6243 12.5941 49.2708 12.8808C49.9173 13.1676 50.4234 13.6994 50.6778 14.3593L55.4735 26.8003C55.7279 27.4602 55.7097 28.1942 55.4229 28.8407C55.1362 29.4872 54.6044 29.9933 53.9444 30.2477L39.0152 36.0026C38.3553 36.2569 37.6214 36.2387 36.9749 35.952C36.3284 35.6652 35.8223 35.1334 35.5679 34.4735L30.7721 22.0325C30.5178 21.3726 30.536 20.6386 30.8227 19.9921C31.1095 19.3456 31.6413 18.8395 32.3012 18.5851L33.5453 18.1056L32.5862 15.6174C31.9502 13.9676 31.9957 12.1327 32.7125 10.5165C33.4294 8.90023 34.759 7.63495 36.4088 6.99899C37.2257 6.6841 38.0966 6.53319 38.9718 6.55488C39.847 6.57656 40.7094 6.77042 41.5097 7.12539C42.31 7.48035 43.0325 7.98947 43.636 8.62367C44.2396 9.25787 44.7123 10.0047 45.0272 10.8216L45.9863 13.3098L47.2304 12.8303ZM37.3679 9.48719C36.3781 9.86877 35.5803 10.6279 35.1502 11.5977C34.7201 12.5674 34.6928 13.6684 35.0744 14.6582L36.0335 17.1464L43.4981 14.269L42.539 11.7808C42.1574 10.7909 41.3982 9.99316 40.4285 9.56303C39.4587 9.1329 38.3578 9.10562 37.3679 9.48719Z" fill="#008599"/>
                </g>
                </g>
                <g clip-path="url(#clip1_11_34)">
                <g filter="url(#filter2_i_11_34)">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M263.608 207.501C263.369 211.705 259.753 214.918 255.533 214.678C254.764 214.634 253.021 214.358 252.208 213.601L251.081 214.602C250.418 215.191 250.577 215.385 250.839 215.703C250.948 215.837 251.075 215.991 251.169 216.206C251.169 216.206 251.987 217.495 251.028 218.684C250.454 219.361 248.904 220.266 247.296 218.472L246.92 218.804C246.92 218.804 247.915 220.104 246.957 221.294C246.384 221.971 244.921 222.597 243.747 221.288L242.434 222.456C241.532 223.257 240.518 222.702 240.124 222.325L239.117 221.202C238.178 220.154 238.819 219.113 239.258 218.723L249.022 210.046C249.022 210.046 248.214 208.581 248.324 206.634C248.563 202.43 252.179 199.217 256.399 199.457C260.62 199.697 263.847 203.299 263.608 207.501ZM255.816 209.722C256.521 209.764 257.214 209.523 257.742 209.054C258.27 208.585 258.59 207.925 258.632 207.22C258.651 206.87 258.601 206.52 258.485 206.19C258.369 205.86 258.19 205.556 257.956 205.295C257.723 205.035 257.44 204.823 257.125 204.671C256.809 204.52 256.467 204.432 256.118 204.413C255.768 204.392 255.418 204.441 255.088 204.555C254.757 204.67 254.453 204.849 254.191 205.081C253.929 205.314 253.716 205.596 253.564 205.911C253.411 206.225 253.322 206.567 253.302 206.917C253.263 207.622 253.506 208.314 253.978 208.84C254.449 209.366 255.11 209.684 255.816 209.722Z" fill="#008599"/>
                </g>
                </g>
                <g filter="url(#filter3_i_11_34)">
                <path d="M187.75 39.3282H115.25C103.988 39.3282 94.859 48.4574 94.859 59.7188V141.279C94.859 152.541 103.988 161.67 115.25 161.67H187.75C199.012 161.67 208.141 152.541 208.141 141.279V59.7188C208.141 48.4574 199.012 39.3282 187.75 39.3282Z" fill="#008599"/>
                </g>
                <path d="M151.501 89.1723C141.886 89.1723 132.666 92.9915 125.868 99.7898C119.069 106.588 115.25 115.808 115.25 125.423H187.751C187.751 115.808 183.932 106.588 177.133 99.7898C170.335 92.9915 161.115 89.1723 151.501 89.1723Z" fill="#383838"/>
                <path d="M210.409 95.9684H92.5937C87.5887 95.9684 83.5313 100.026 83.5313 105.031V132.219C83.5313 137.224 87.5887 141.281 92.5937 141.281H210.409C215.414 141.281 219.471 137.224 219.471 132.219V105.031C219.471 100.026 215.414 95.9684 210.409 95.9684Z" fill="#F6F5F4"/>
                <rect x="83.5313" y="95.9684" width="135.94" height="45.3128" fill="url(#pattern0_11_34)"/>
                <path d="M190.014 105.032H112.984C109.23 105.032 106.187 108.075 106.187 111.828V125.422C106.187 129.176 109.23 132.219 112.984 132.219H190.014C193.768 132.219 196.811 129.176 196.811 125.422V111.828C196.811 108.075 193.768 105.032 190.014 105.032Z" fill="white"/>
                <g opacity="0.65">
                <path d="M124.312 125.419C128.066 125.419 131.109 122.376 131.109 118.623C131.109 114.869 128.066 111.826 124.312 111.826C120.559 111.826 117.516 114.869 117.516 118.623C117.516 122.376 120.559 125.419 124.312 125.419Z" fill="black"/>
                <path d="M142.437 125.419C146.191 125.419 149.234 122.376 149.234 118.623C149.234 114.869 146.191 111.826 142.437 111.826C138.683 111.826 135.64 114.869 135.64 118.623C135.64 122.376 138.683 125.419 142.437 125.419Z" fill="black"/>
                <path d="M160.563 125.419C164.317 125.419 167.36 122.376 167.36 118.623C167.36 114.869 164.317 111.826 160.563 111.826C156.809 111.826 153.766 114.869 153.766 118.623C153.766 122.376 156.809 125.419 160.563 125.419Z" fill="black"/>
                <path d="M178.685 125.419C182.439 125.419 185.482 122.376 185.482 118.623C185.482 114.869 182.439 111.826 178.685 111.826C174.931 111.826 171.888 114.869 171.888 118.623C171.888 122.376 174.931 125.419 178.685 125.419Z" fill="black"/>
                </g>
                <path d="M151.5 84.6402C161.51 84.6402 169.625 76.5254 169.625 66.5154C169.625 56.5053 161.51 48.3906 151.5 48.3906C141.49 48.3906 133.375 56.5053 133.375 66.5154C133.375 76.5254 141.49 84.6402 151.5 84.6402Z" fill="#383838"/>
                </g>
                <defs>
                <filter id="filter0_d_11_34" x="16" y="0" width="270.359" height="242" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset dy="4"/>
                <feGaussianBlur stdDeviation="2"/>
                <feComposite in2="hardAlpha" operator="out"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_11_34"/>
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_11_34" result="shape"/>
                </filter>
                <filter id="filter1_i_11_34" x="30.5937" y="6.55283" width="25.0583" height="33.6282" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset dy="4"/>
                <feGaussianBlur stdDeviation="2"/>
                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_11_34"/>
                </filter>
                <filter id="filter2_i_11_34" x="238.633" y="199.444" width="24.9872" height="27.4218" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset dy="4"/>
                <feGaussianBlur stdDeviation="2"/>
                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_11_34"/>
                </filter>
                <filter id="filter3_i_11_34" x="94.859" y="39.3282" width="113.282" height="126.342" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset dy="4"/>
                <feGaussianBlur stdDeviation="2"/>
                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_11_34"/>
                </filter>
                <pattern id="pattern0_11_34" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0_11_34" transform="scale(0.0166667 0.05)"/>
                </pattern>
                <clipPath id="clip0_11_34">
                <rect width="32" height="32" fill="white" transform="translate(21 11.5098) rotate(-21.0806)"/>
                </clipPath>
                <clipPath id="clip1_11_34">
                <rect width="30.8636" height="30.8636" fill="white" transform="translate(236 196.693) rotate(-4.75794)"/>
                </clipPath>
                <image id="image0_11_34" width="60" height="20" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAAUCAYAAADRA14pAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAA75JREFUWIXdl2Fu20YQhd+bWXJJSY7covUFcoP4gr5LzuPcwBcwgiJIYksUd+b1hywjLdBAdOOi7feLALG78+3szJIEgJubG/vw4cO61voGwCYi1ma2bq2N7l5JdpJKZpqZEU/IjMneunpR1G+rX/wyqv60sWE70leDSimEUcTzmO9BQUKKMc/RdhOnT494+PSA6eMud79N2R6b2iGZqdOYzJSZJckmaY6IqZSyy8wHd38A8HWaps/v3r17uLm5yQKAd3d3o7v3EUF3Pyc2yIxkb1ZXxcZt1fjrSvVyjX47yoYq0g1G4TxZABBAwSBYMa/K7g24fn4H7QBLzYkZ30p/jyen/u7uLgE8luvr67Lf788OCgAko1lHdNV82HaoP48YLtcYtiPLWAErcDcBZ+b2CQIUKHdToGNZUQA4AmSotDkzIg2IjBnkedIAsN/veX19XcrV1ZUtkQUAd0fQzX3VZdkONlyu0F+MLEM1KyXpxqWyJwhQRjkMhkKsAEnMSK7mzDgkcpb7HJnLpr66urKyNB6ZUWb0bu2ol72tLkcfLir6oUt4wd+RPXGS5lHaurFPZmQcmtfHhpxCihRm4MyjfWKxsOOYXeuH4vWisr6p6eNgrB3I5cf4rzhJG4zGQkX1etFa282Yd3PO+3BkNLRF0y4Slo7ZRTc4fdNp2PSwWs07l9EMBv0I2RMEDIYEXN4X1lVv86ZXN3ZoQ5MiJVu04rL6dUAiYdXZDZ2VsUepHbw4F3bkc3nq3DQvDnYd+rFnt+6A6hKJ8y6VZxYJOxxZipV+MNZ1kfUdzU3J4zw/XBdPWQYgEFac1nes61KGwbIU84XGy2rYAWNhoLh7X+jFaW562jYKixrIEkSS5iYvLu9LoLixLN7is4UlI2R0M8o6I4pJJCQpQjS9miwAKBPHxUjCjNYZzRhmRIjkecsv7tIBwCUpWzKjZRwMsMx8jfP8DRISGcxoykxKihdMc7YwmUJSjEjkIXj4eoCZqe1nc6fEVzUmpYwQc2qav0zUvllEKlNgnt0vF2U4EPA2JfZf5gQe2R7mRGew15UFjt/SmZJhTsz7WdPXWW3KQCxqW4uEmSmzyDx8noEpMPUHt8KFl9uL8QQym6RD6jClKTIzBTs/gOU1HAF4pE2Rsj1PdfRPHOnn50wlAvGCIl4sfKxlIJFQ/GFnX7dL/ykGAgCBpXdDub+/z81m86Iglvye/Ru4v79Pu729bcMw/KcCfwnDMOj29rYZAL19+3YXEQd3/9+Ju7si4vD+/ftHAPodgR38ufKxMqEAAAAASUVORK5CYII="/>
                </defs>
            </svg>
        </div>
        <div class="login-area">
            <form action="#" method="POST" class="login">
                <h1 id="login-heading">Log In</h1>
                <div class="input email">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="login-email" class="user-email" placeholder="john@gmail.com" required autocomplete="off">
                </div>
                <div class="input password">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="login-pwd" class="user-password" placeholder="Type your password" required autocomplete="off">
                    <p><input type="checkbox" name="remember-me" id="remember-me">Remember me</p>
                </div>
                <div class="error-msg"></div>
                <button type="submit" id="login-button">Login</button>
                <!-- <input type="submit" value="Login" name="btnLogin"> -->
                <div class="link register">Don't have an account? <br/><a href="#">Register</a> now</div>
            </form>
            <form action="#" method="POST" class="register">
                <h1 id="signup-heading">Sign Up</h1>
                <div class="input name">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="register-name" class="user-name" placeholder="John Doe" required autocomplete="off">
                </div>
                <div class="input email">
                    <label for="register-email">Email</label>
                    <input id="register-email" type="email" name="register-email" class="user-email" placeholder="john@gmail.com" required autocomplete="off">
                </div>
                <div class="input password">
                    <label for="register-password">Password</label>
                    <input id="register-password" type="password" name="register-pwd" class="user-password" placeholder="Type your password" required autocomplete="off">
                </div>
                <div class="error-msg"></div>
                <button type="submit" id="register-button">Sign Up</button>
                <!-- <input type="submit" value="Sign Up" name="btnRegister"> -->
                <div class="link login">Already have an account? <br/><a href="#">Login</a> now</div>
            </form>
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginErrorDiv = document.querySelector('form.login div.error-msg');
        const loginForm = document.querySelector('form.login');
        const loginButton = document.getElementById('login-button');
        loginForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const loginFormData = new FormData(loginForm);
            fetch('/Goalify/Pages/includes/userHandler.php', {
                method: 'POST',
                body: loginFormData
            })
            .then(response => response.json())
            .then(response => {
                if (response.login) {
                    loginButton.innerHTML = `
                    <p>Login Success</p>
                    <span class="material-symbols-rounded">mood</span>
                    `
                    setTimeout(() => {
                        window.location.href = "/Goalify/Pages/user/todo/task-overview/task-overview.php";
                    }, 3000); // 4s
                } else {
                    const loginEmail = document.querySelector('form.login div.input.email input#email');
                    const loginPwd = document.querySelector('form.login div.input.password input#password');
                    loginEmail.value = '';
                    loginPwd.value = '';
                    loginErrorDiv.innerHTML = `
                    <span class="material-symbols-rounded">error</span>
                    <p>${response.msg}</p>
                    `;
                }
            })
        })

        const registerErrorDiv = document.querySelector('form.register div.error-msg');
        const registerForm = document.querySelector('form.register');
        const registerName = document.querySelector('form.register div.input.name input#register-name');
        const registerEmail = document.querySelector('form.register div.input.email input#register-email');
        const registerPwd = document.querySelector('form.register div.input.password input#register-password');
        const registerButton = document.getElementById('register-button');
        registerForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const registerFormData = new FormData(registerForm);
            fetch('/Goalify/Pages/includes/userHandler.php', {
                method: 'POST',
                body: registerFormData
            })
            .then(response => response.json())
            .then(response => {
                if (response.register) {
                    registerButton.innerHTML = `
                    <p>Register Success</p>
                    <span class="material-symbols-rounded">check_circle</span>
                    `;
                    alert("Registered Successfully");
                    setTimeout(() => {
                        const loginBox = document.querySelector('form.login');
                        const registerBox = document.querySelector('form.register');
                        loginBox.classList.remove('active');
                        registerBox.classList.remove('active');
                        registerForm.reset();
                        registerButton.innerHTML = '';
                        registerButton.textContent = 'Sign Up';
                    }, 3000); // 4s
                } else {
                    registerEmail.value = '';
                    registerPwd.value = '';
                    registerErrorDiv.innerHTML = `
                    <span class="material-symbols-rounded">error</span>
                    <p>${response.msg}</p>
                    `;
                }
            })
        })
    })
</script>