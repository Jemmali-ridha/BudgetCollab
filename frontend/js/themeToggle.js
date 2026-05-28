const toggle = document.getElementById('themeToggle');
  const root = document.documentElement;
 
  const darkVars = {
    '--bg': '#0f1710',
    '--white': '#1a2e1e',
    '--text-dark': '#e8f5ea',
    '--text-muted': '#8aab90',
    '--border': '#2a3f2e',
    '--green-icon-bg': '#1a3d28',
    '--purple-icon-bg': '#2a2550',
    '--amber-icon-bg': '#3a2a0a',
    '--green-light': '#1a3d28',
  };
 
  const lightVars = {
    '--bg': '#eef2ee',
    '--white': '#ffffff',
    '--text-dark': '#111c14',
    '--text-muted': '#6b7280',
    '--border': '#dde4dd',
    '--green-icon-bg': '#d4f0e4',
    '--purple-icon-bg': '#ede9fb',
    '--amber-icon-bg': '#fef3e2',
    '--green-light': '#e6f7f0',
  };
 
  let isDark = false;
 
  toggle.addEventListener('click', () => {
    isDark = !isDark;
    toggle.classList.toggle('dark', isDark);
    const vars = isDark ? darkVars : lightVars;
    for (const [key, val] of Object.entries(vars)) {
      root.style.setProperty(key, val);
    }
  });