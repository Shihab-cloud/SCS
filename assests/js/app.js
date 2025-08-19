// /assets/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  // ===== Sidebar toggle with remember state =====
  const sidebar = document.querySelector('.sidebar');
  const toggle  = document.querySelector('[data-toggle="sidebar"]');
  if (sidebar && toggle) {
    const KEY = 'scs.sidebar.collapsed';
    const apply = () => sidebar.classList.toggle('collapsed', localStorage.getItem(KEY) === '1');
    toggle.addEventListener('click', () => {
      localStorage.setItem(KEY, localStorage.getItem(KEY) === '1' ? '0' : '1');
      apply();
    });
    apply();
  }

  // ===== Active menu link highlight =====
  const path = location.pathname.replace(/\/+$/, '');
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
      // Adjust base path if your folder name/case differs
      const base = '/smart_cloud_system';
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

  // ===== Attendance helpers (optional) =====
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
});