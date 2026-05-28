const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    // Vérifier si dark mode était sauvegardé
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.removeAttribute('data-theme'); // mode clair par défaut
    }
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            // Passer en mode clair
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        } else {
            // Passer en mode dark
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });
}

const passwordInput = document.getElementById('password');
if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        updateStrengthMeter(strength);
    });
}

function checkPasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    return score;
}

function updateStrengthMeter(score) {
    const bars = document.querySelectorAll('.strength-bar');
    const text = document.querySelector('.strength-text');
    
    if (!bars.length) return;
    
    bars.forEach((bar, i) => {
        bar.classList.remove('active-1', 'active-2', 'active-3', 'active-4');
        if (i < score) {
            bar.classList.add(`active-${score}`);
        }
    });
    
    const messages = ['', 'Very weak', 'Weak', 'Medium', 'Strong'];
    if (text) text.textContent = messages[score] || 'Enter a password';
}

document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match');
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters');
        }
    });
}