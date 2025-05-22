document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('menu');
    const sideNav = document.querySelector('nav.side-nav')
    const mediaQuery = window.matchMedia("(min-width: 1181px)");
    const searchBar = document.getElementById("search-bar");
    const bottomNav = document.getElementById('bottom-nav-mobile');
    const logoutMobile = document.getElementById('logout-mobile');

    menu.addEventListener('click', () => {
        const root = document.documentElement;
        if (sideNav.classList.contains('close')) {
            sideNav.classList.remove('close');
            root.style.setProperty('--side-nav-width', '22%');
            // root.style.setProperty('--top-nav-width', 'calc(100% - 22%)');
        } else {
            sideNav.classList.add('close');
            root.style.setProperty('--side-nav-width', '0%');
            // root.style.setProperty('--side-nav-width', '100%');
        }
    })

    const setSideNav = (screenSize) => {
        const root = document.documentElement;
        if (screenSize.matches) {
            menu.style.display = 'inline';
            // searchBar.style.display = 'flex';
            bottomNav.style.display = 'none';
            logoutMobile.style.display = 'none';
            if (sideNav.classList.contains('close')) {
                sideNav.classList.remove('close');
                root.style.setProperty('--side-nav-width', '22%');
            } else {
                root.style.setProperty('--side-nav-width', '22%');
            }
        } else {
            // searchBar.style.display = 'none';
            menu.style.display = 'none';
            bottomNav.style.display = 'block';
            logoutMobile.style.display = 'inline';
            if (!sideNav.classList.contains('close')) {
                sideNav.classList.add('close');
                root.style.setProperty('--side-nav-width', '0%');
            } else {
                root.style.setProperty('--side-nav-width', '0%');
            }
        }
    }

    setSideNav(mediaQuery);

    mediaQuery.addEventListener('change', (e) => { // e is event object
        setSideNav(mediaQuery); // update the class list of the side-nav
    });
})