/**
 * Show a toast notification
 * @param {string} msg
 * @param {'success'|'error'|'info'|'warning'} type
 */
export function showToast(msg, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 8px;
      pointer-events: none;
    `;
    document.body.appendChild(container);
  }

  const icons = {
    success: 'fa-circle-check',
    error:   'fa-circle-xmark',
    info:    'fa-circle-info',
    warning: 'fa-triangle-exclamation',
  };
  const bgColors = {
    success: '#1a1a18',
    error:   '#8a3535',
    info:    '#2a4a7a',
    warning: '#7a5a28',
  };

  const el = document.createElement('div');
  el.style.cssText = `
    pointer-events: all;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    color: #fff;
    font-size: 13.5px;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 8px 24px rgba(0,0,0,0.18);
    background: ${bgColors[type] || bgColors.success};
    transform: translateX(20px);
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
    max-width: 320px;
  `;
  el.innerHTML = `<i class="fa-solid ${icons[type] || icons.success}" style="font-size:14px;flex-shrink:0"></i><span>${msg}</span>`;
  container.appendChild(el);

  requestAnimationFrame(() => {
    el.style.transform = 'translateX(0)';
    el.style.opacity   = '1';
  });

  setTimeout(() => {
    el.style.transform = 'translateX(20px)';
    el.style.opacity   = '0';
    setTimeout(() => el.remove(), 300);
  }, 3200);
}

/**
 * Show a confirmation dialog before deleting
 * @param {Function} onConfirm
 */
export function confirmDeleteDialog(onConfirm) {
  // Create overlay
  const overlay = document.createElement('div');
  overlay.style.cssText = `
    position: fixed; inset: 0; z-index: 10000;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    font-family: 'Inter', sans-serif;
  `;

  const box = document.createElement('div');
  box.style.cssText = `
    background: #fff;
    border-radius: 14px;
    padding: 28px 24px 24px;
    width: 100%;
    max-width: 360px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  `;

  box.innerHTML = `
    <div style="width:44px;height:44px;background:#f5efef;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
      <i class="fa-solid fa-trash" style="color:#8a3535;font-size:16px"></i>
    </div>
    <p style="font-size:15px;font-weight:700;color:#1a1a18;margin-bottom:6px">Hapus item ini?</p>
    <p style="font-size:13px;color:#747470;line-height:1.5;margin-bottom:20px">Data yang dihapus tidak bisa dikembalikan.</p>
    <div style="display:flex;gap:8px">
      <button id="dlg-cancel" style="flex:1;padding:9px;background:#f0f0ef;border:none;border-radius:8px;font-size:13px;font-weight:600;color:#555552;cursor:pointer;font-family:inherit">Batal</button>
      <button id="dlg-confirm" style="flex:1;padding:9px;background:#1a1a18;border:none;border-radius:8px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;font-family:inherit">Ya, Hapus</button>
    </div>
  `;

  overlay.appendChild(box);
  document.body.appendChild(overlay);

  const close = () => overlay.remove();

  // overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
  box.querySelector('#dlg-cancel').addEventListener('click', close);
  box.querySelector('#dlg-confirm').addEventListener('click', () => {
    close();
    onConfirm();
  });
}
