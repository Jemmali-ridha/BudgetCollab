const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });
}

const modal = document.getElementById('budgetModal');
const newBudgetBtn = document.getElementById('newBudgetBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelModalBtn = document.getElementById('cancelModalBtn');

if (newBudgetBtn) {
    newBudgetBtn.addEventListener('click', () => {
        modal.classList.add('active');
    });
}

function closeModal() {
    modal.classList.remove('active');
}

if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

if (modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
}

const selectorBtns = document.querySelectorAll('.selector-btn');
selectorBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const parent = btn.parentElement;
        parent.querySelectorAll('.selector-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

const alertBtns = document.querySelectorAll('.alert-btn');
alertBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        alertBtns.forEach(b => {
            b.style.background = '';
            b.style.color = '';
        });
        btn.style.background = 'var(--green)';
        btn.style.color = 'white';
    });
});

const budgetForm = document.getElementById('budgetForm');
if (budgetForm) {
    budgetForm.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Budget created successfully!');
        closeModal();
        budgetForm.reset();
        
        document.querySelectorAll('.selector-btn').forEach((btn, i) => {
            if (i === 0) btn.classList.add('active');
            else btn.classList.remove('active');
        });
        
        alertBtns.forEach(btn => {
            btn.style.background = '';
            btn.style.color = '';
        });
    });
}

const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', (e) => {
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');
    });
});