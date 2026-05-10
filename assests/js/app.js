// /assets/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  // Get the role from PHP session (which was set dynamically in header.php)
  //const userRole = '<?php echo $_SESSION["role"]; ?>';

  // ===== Sidebar toggle with remember state =====
  const sidebar = document.querySelector('.sidebar');
  const toggle  = document.querySelector('[data-toggle="sidebar"]');

  if (sidebar && toggle) {
    const KEY = 'scs.sidebar.collapsed';
    
    // Function to set the initial state from storage
    const initialState = localStorage.getItem(KEY);
    if (initialState === '1') {
        sidebar.classList.add('collapsed');
    }

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        // Save state: 1 for collapsed, 0 for open
        localStorage.setItem(KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
    });
  }

  // ===== Active menu link highlight =====
  const path = window.location.pathname; 
  document.querySelectorAll('.sidebar a[href]').forEach(a => {
    const href = a.getAttribute('href').replace(/\/+$/, '');
    if (href && path.endsWith(href)) a.classList.add('active');
  });

  // ===== Register role redirect (register.php) =====
  const roleSelect = document.querySelector('select[name="role"]');
  const registerForm = roleSelect?.closest('form');
  if (roleSelect && registerForm) {
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const role = roleSelect.value;
      if (!role) return;
      const base = '';
      window.location.href = role === 'faculty'
        ? `${base}/faculty_register.php`
        : `${base}/student_register.php`;
    });
  }

  // ===== Prevent double form submit =====
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      const btn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (!btn || btn.disabled) return;
      btn.disabled = true;
      const t = btn.textContent;
      btn.textContent = 'Please wait...';
      // re-enable as a fallback in case the page doesn’t navigate
      setTimeout(() => { btn.disabled = false; btn.textContent = t; }, 8000);
    });
  });

  // ===== Attendance helpers (faculty-specific) =====
  if (userRole === 'faculty') {
    const table = document.querySelector('[data-table="attendance"]');
    const allP  = document.querySelector('[data-action="mark-all-present"]');
    const allA  = document.querySelector('[data-action="mark-all-absent"]');

    function setAll(status) {
      if (!table) return;
      table.querySelectorAll('select[name^="status["], input[name^="status["]').forEach(el => {
        if (el.tagName === 'SELECT') el.value = status;
        if (el.type === 'checkbox') el.checked = (status === 'P');
      });
    }
    allP?.addEventListener('click', () => setAll('P'));
    allA?.addEventListener('click', () => setAll('A'));
  }

  // ===== Student-specific helpers =====
  if (userRole === 'student') {
    console.log('Student dashboard loaded');
    //need to add functionalities for student
  }

  // ===== Admin-specific helpers =====
  if (userRole === 'admin') {
    console.log('Admin dashboard loaded');
    //need to add functionalities for admin
  }
});