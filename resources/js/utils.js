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
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const normalizedType = ['success', 'error', 'info', 'warning'].includes(type) ? type : 'success';
  const icons = {
    success: 'fa-circle-check',
    error: 'fa-circle-xmark',
    info: 'fa-circle-info',
    warning: 'fa-triangle-exclamation',
  };

  const el = document.createElement('div');
  el.className = `toast toast--${normalizedType}`;
  el.innerHTML = `<span class="toast__icon"><i class="fa-solid ${icons[normalizedType]}"></i></span><span class="toast__message"></span>`;
  el.querySelector('.toast__message').textContent = msg;
  container.appendChild(el);

  requestAnimationFrame(() => el.classList.add('toast--show'));

  setTimeout(() => {
    el.classList.remove('toast--show');
    setTimeout(() => el.remove(), 240);
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
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    font-family: 'Inter', sans-serif;
    animation: fadeIn 0.15s ease-out;
  `;

  const box = document.createElement('div');
  box.style.cssText = `
    background: #fffdfb;
    border: 1px solid #e8d5ce;
    border-radius: 14px;
    padding: 28px 24px 24px;
    width: 100%;
    max-width: 380px;
    box-shadow: 0 20px 60px rgba(74,44,42,0.15);
    animation: modalEnter 0.2s ease-out;
  `;

  box.innerHTML = `
    <div style="width:44px;height:44px;background:#fdf0f0;border:1px solid #f0c8c8;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
      <i class="fa-solid fa-trash" style="color:#c75b5b;font-size:16px"></i>
    </div>
    <p style="font-size:15px;font-weight:700;color:#4a2c2a;font-family:'Playfair Display',serif;margin-bottom:6px">Hapus item ini?</p>
    <p style="font-size:13px;color:#8b6f6a;line-height:1.5;margin-bottom:20px">Data yang dihapus tidak bisa dikembalikan.</p>
    <div style="display:flex;gap:10px">
      <button id="dlg-cancel" style="flex:1;padding:9px 12px;background:#faf0e8;border:1px solid #e8d5ce;border-radius:8px;font-size:13px;font-weight:600;color:#8b6f6a;cursor:pointer;font-family:inherit;transition:all 0.2s">Batal</button>
      <button id="dlg-confirm" style="flex:1;padding:9px 12px;background:linear-gradient(135deg, #c75b5b, #b04a4a);border:none;border-radius:8px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;font-family:inherit;transition:all 0.2s">Ya, Hapus</button>
    </div>
    <style>
      @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
      @keyframes modalEnter { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
      #dlg-cancel:hover { background: #e8d5ce; }
      #dlg-confirm:hover { opacity: 0.9; transform: scale(1.02); }
    </style>
  `;

  overlay.appendChild(box);
  document.body.appendChild(overlay);

  const close = () => overlay.remove();

  overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
  box.querySelector('#dlg-cancel').addEventListener('click', close);
  box.querySelector('#dlg-confirm').addEventListener('click', () => {
    close();
    onConfirm();
  });
}