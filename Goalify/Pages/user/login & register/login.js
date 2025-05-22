const loginBox = document.querySelector('form.login');
const registerBox = document.querySelector('form.register');
const loginLink = document.querySelector('.link.login');
const registerLink = document.querySelector('.link.register');

registerLink.addEventListener('click', () => {
    loginBox.classList.add('active');
    registerBox.classList.add('active');
})

loginLink.addEventListener('click', () => {
    loginBox.classList.remove('active');
    registerBox.classList.remove('active');
})