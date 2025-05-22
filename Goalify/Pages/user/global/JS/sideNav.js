/*
    Author: Phang Shea Wen
    Date: 2025/01/12
    Filename: sideNav.js
    Description: To handle the appearance of the side-navigational structure in desktop view and mobile view, and the size of the main content
                1) By default:
                    - Desktop: show side-navigational bar
                    - Mobile: hide side-navigational bar
                2) Button to expand and minimize side-navigational bar
                    - For desktop, click on arrow button
                    - For mobile, click on menu button
                3) Size of main content
                    - For desktop, changes with the width of the side-navigational bar
                    - For mobile, does not change, the side-navigational bar appears over the main content
*/
document.addEventListener('DOMContentLoaded', () => {
    const toggleClose = document.querySelector('#toggle-side-nav span.material-symbols-rounded.close');
    const toggleExpand = document.querySelector('nav.side-nav span.material-symbols-rounded.expand');
    const sideNav = document.querySelector('nav.side-nav');
    const menuClose = document.querySelector('#menu');

    const mediaQuery = window.matchMedia("(min-width: 1181px)");

    // Set Initial State for Side Navigational Bar
    const setSideNav = (screenSize) => {
        const root = document.documentElement;
        if (screenSize.matches) {
            if (sideNav.classList.contains('close')) {
                sideNav.classList.remove('close');
                root.style.setProperty('--side-nav-width', '22%');
            } else {
                root.style.setProperty('--side-nav-width', '22%');
            }
        } else {
            if (!sideNav.classList.contains('close')) {
                sideNav.classList.add('close');
                root.style.setProperty('--side-nav-width', '2%');
            } else {
                root.style.setProperty('--side-nav-width', '2%');
            }
        }
    }

    // Toggle Side Navigational Bar
    if (toggleClose) {
        toggleClose.addEventListener('click', () => {
            const root = document.documentElement;
            if (!sideNav.classList.contains('close')) {
                sideNav.classList.add('close');
                root.style.setProperty('--side-nav-width', '2%');
            }
        })
    }

    if (toggleExpand) {
        toggleExpand.addEventListener('click', () => {
            const root = document.documentElement;
            if (sideNav.classList.contains('close')) {
                sideNav.classList.remove('close');
                root.style.setProperty('--side-nav-width', '22%');
            }
        })
    }

    if (menuClose) {
        menuClose.addEventListener('click', () => {
            if (!sideNav.classList.contains('close')) {
                sideNav.classList.add('close');
            } else {
                sideNav.classList.remove('close');
            }
        })
    }

    setSideNav(mediaQuery);

    mediaQuery.addEventListener('change', (e) => { // e is event object
        setSideNav(mediaQuery); // update the class list of the side-nav
    });
})