document.addEventListener('DOMContentLoaded', () => {
  const text = document.getElementById('doc-content');
  const count = document.getElementById('char-count');
  const update = () => { if (text && count) count.textContent = `${text.value.length.toLocaleString('tr-TR')} / 50.000`; };
  text?.addEventListener('input', update); update();
  document.querySelectorAll('[data-sample]').forEach(button => button.addEventListener('click', () => {
    const sample = document.getElementById(`sample-${button.dataset.sample}`);
    text.value = sample.content.textContent.trim();
    document.getElementById('doc-title').value = sample.dataset.title;
    document.getElementById('doc-sector').value = sample.dataset.sector;
    update(); text.focus();
  }));
  document.getElementById('text-file')?.addEventListener('change', async event => {
    const file = event.target.files[0]; if (!file) return;
    const feedback = document.getElementById('file-feedback');
    if (!file.name.toLowerCase().endsWith('.txt') || file.size > 200000) {
      feedback.textContent = 'En fazla 200 KB boyutunda bir .txt dosyası seçin.'; event.target.value = ''; return;
    }
    try {
      const content = await file.text();
      if (content.length > 50000) { feedback.textContent = 'Metin 50.000 karakteri aşmamalıdır.'; return; }
      text.value = content;
      if (!document.getElementById('doc-title').value) document.getElementById('doc-title').value = file.name.replace(/\.txt$/i, '').slice(0,150);
      feedback.textContent = `${file.name} metin alanına aktarıldı.`; update(); text.focus();
    } catch { feedback.textContent = 'Dosya okunamadı. Metni yapıştırarak deneyin.'; }
  });
  document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  }));
  document.querySelectorAll('[data-loading]').forEach(button => button.form.addEventListener('submit', () => {
    button.disabled = true; button.textContent = button.dataset.loading;
  }));
  document.getElementById('print-report')?.addEventListener('click', () => {
    const details = Array.from(document.querySelectorAll('details'));
    const states = details.map(d => d.open);
    details.forEach(d => d.open = true);
    window.addEventListener('afterprint', () => details.forEach((d,i) => d.open = states[i]), { once: true });
    window.print();
  });
  document.querySelectorAll('.password-toggle').forEach(button => button.addEventListener('click', () => {
    const input = document.getElementById(button.getAttribute('aria-controls'));
    const show = input.type === 'password'; input.type = show ? 'text' : 'password';
    button.setAttribute('aria-pressed',String(show)); button.setAttribute('aria-label',show?'Parolayı gizle':'Parolayı göster');
  }));
  const menu = document.querySelector('.mobile-toggle');
  menu?.addEventListener('click', () => {
    const isOpen = document.body.classList.toggle('menu-open');
    menu.setAttribute('aria-expanded', String(isOpen));
    menu.setAttribute('aria-label', isOpen ? 'Menüyü kapat' : 'Menüyü aç');
  });
  const closeMenu = () => { document.body.classList.remove('menu-open'); menu?.setAttribute('aria-expanded','false'); menu?.setAttribute('aria-label','Menüyü aç'); };
  document.querySelectorAll('.menu-backdrop,.sidebar-close').forEach(button => button.addEventListener('click',closeMenu));
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
});
