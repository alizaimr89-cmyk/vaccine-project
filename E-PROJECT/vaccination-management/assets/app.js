// NurtureDose premium interactions: safe table search, dismissible alerts, and confirmation prompts.
document.querySelectorAll('.alert').forEach((alert) => {
  alert.setAttribute('role', 'status');
  alert.setAttribute('tabindex', '0');
  window.setTimeout(() => { alert.classList.add('alert-dismissed'); }, 6500);
});
document.querySelectorAll('[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (event) => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
// Add a client-side search field to each dashboard table. It filters only rows already rendered.
document.querySelectorAll('.table-wrap table').forEach((table, index) => {
  const wrap = table.closest('.table-wrap');
  if (!wrap || table.querySelector('thead') === null) return;
  const toolbar = document.createElement('div');
  toolbar.className = 'table-tools';
  const label = document.createElement('label');
  label.className = 'table-search-label';
  label.textContent = 'Quick search';
  const input = document.createElement('input');
  input.className = 'search-input table-search';
  input.type = 'search';
  input.placeholder = 'Search this table…';
  input.setAttribute('aria-label', 'Search table ' + (index + 1));
  label.appendChild(input);
  const count = document.createElement('span');
  count.className = 'table-result-count';
  toolbar.append(label, count);
  wrap.parentNode.insertBefore(toolbar, wrap);
  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const update = () => {
    const term = input.value.trim().toLocaleLowerCase();
    let visible = 0;
    rows.forEach((row) => {
      const match = row.textContent.toLocaleLowerCase().includes(term);
      row.hidden = !match;
      if (match) visible++;
    });
    count.textContent = term ? `${visible} matching row${visible === 1 ? '' : 's'}` : `${rows.length} row${rows.length === 1 ? '' : 's'}`;
  };
  input.addEventListener('input', update);
  update();
});
// Highlight the active section when a management shortcut is selected.
if (location.hash) {
  const target = document.querySelector(location.hash);
  if (target) target.classList.add('section-target');
}
