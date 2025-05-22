/*
    Author: Phang Shea Wen
    Date: 2025/01/13
    Filename: theme.js
    Description: To alter theme mode of the website (default light theme/ dark theme)
*/

document.addEventListener('DOMContentLoaded', () => {
    const mediaQuery = window.matchMedia("(min-width: 1180px)");
    const themeMode = document.querySelector('.material-symbols-rounded.theme_mode');

    // Dark and Light Mode
    // local storage -> local browser storage, specific to the same domain, tied to browser profile
    function themeModeListener() {
        if (themeMode) {
            themeMode.addEventListener('click', () => {
                const updateTheme = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.body.setAttribute('data-theme', updateTheme);
                const updateIcon = updateTheme === 'dark'? 'light_mode' : 'dark_mode';
                themeMode.textContent = updateIcon;
                if (updateTheme === 'dark') {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light')
                }
                // dispatch custom event notifying theme change
                // const themeChangedEvent = new Event('themeChanged');
                // document.body.dispatchEvent(themeChangedEvent);
            })
        }
    }

    function setTheme () {
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme) {
            document.body.setAttribute('data-theme', storedTheme);
            const updateIcon = storedTheme === 'dark' ? 'light_mode' : 'dark_mode';
            themeMode.textContent = updateIcon;
        } else {
            document.body.setAttribute('data-theme', 'light');
            themeMode.textContent = 'dark_mode';
        }
    }

    // Initialize on Page Load
    setTheme();
    themeModeListener();

    // Listen to Screen Size Changes
    mediaQuery.addEventListener('change', (e) => { // e is event object
        setTheme();
    });
})
