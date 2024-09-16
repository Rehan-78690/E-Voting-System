document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (email && password) {
        alert('Login successful!');
    } else {
        alert('Please enter both email and password.');
    }
});

document.getElementById('forgot-password').addEventListener('click', function() {
    alert('Forgot Password functionality not implemented.');
});
