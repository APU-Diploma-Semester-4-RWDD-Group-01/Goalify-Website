const mediaQuery = window.matchMedia("(min-width: 1181px)");

const showHome = (screenSize) => {
    const root = document.documentElement;
    if (!screenSize.matches) {
        window.location.href = "../login & register/login.php";
    }
}


showHome(mediaQuery);

mediaQuery.addEventListener('change', (e) => {
    if (!e.matches) {
        window.location.href = "../login & register/login.php";
    }
});

const phrases = ["How can I help you?", "Make your To-Do list with Goalify!", "Goalify is here to assist you."];
let index = 0;
let charIndex = 0;
let typingText = document.getElementById("typing-text");

function typeText() {
    if (charIndex < phrases[index].length) {
        typingText.textContent += phrases[index].charAt(charIndex);
        charIndex++;
        setTimeout(typeText, 100);
    } else {
        setTimeout(eraseText, 2000);
    }
}

function eraseText() {
    if (charIndex > 0) {
        typingText.textContent = phrases[index].substring(0, charIndex - 1);
        charIndex--;
        setTimeout(eraseText, 50);
    } else {
        index = (index + 1) % phrases.length;
        setTimeout(typeText, 500);
    }
}

typeText();