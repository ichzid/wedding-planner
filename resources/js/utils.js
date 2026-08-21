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
  const meta = {
    success: { icon: 'fa-check', title: 'Berhasil' },
    error: { icon: 'fa-xmark', title: 'Terjadi kesalahan' },
    info: { icon: 'fa-info', title: 'Informasi' },
    warning: { icon: 'fa-exclamation', title: 'Perhatian' },
  }[normalizedType];

  const el = document.createElement('div');
  el.className = `toast toast--${normalizedType}`;
  el.setAttribute('role', normalizedType === 'error' ? 'alert' : 'status');
  el.setAttribute('aria-live', normalizedType === 'error' ? 'assertive' : 'polite');
  el.innerHTML = `
    <span class="toast__icon" aria-hidden="true"><i class="fa-solid ${meta.icon}"></i></span>
    <span class="toast__content">
      <strong class="toast__title"></strong>
      <span class="toast__message"></span>
    </span>
    <button type="button" class="toast__close" aria-label="Tutup notifikasi"><i class="fa-solid fa-xmark"></i></button>
    <span class="toast__progress" aria-hidden="true"></span>
  `;
  el.querySelector('.toast__title').textContent = meta.title;
  el.querySelector('.toast__message').textContent = msg;
  container.appendChild(el);

  let removalTimer;
  const dismiss = () => {
    clearTimeout(removalTimer);
    el.classList.remove('toast--show');
    setTimeout(() => el.remove(), 240);
  };

  el.querySelector('.toast__close').addEventListener('click', dismiss);
  requestAnimationFrame(() => el.classList.add('toast--show'));
  removalTimer = setTimeout(dismiss, 4200);
}

/**
 * Show a confirmation dialog before deleting
 * @param {Function} onConfirm
 * @param {{ title?: string, description?: string }} options
 */
export function confirmDeleteDialog(onConfirm, options = {}) {
  const title = options.title || 'Hapus data ini?';
  const description = options.description || 'Data yang sudah dihapus tidak dapat dikembalikan. Pastikan kamu tidak membutuhkannya lagi.';
  const previousFocus = document.activeElement;
  const overlay = document.createElement('div');
  overlay.className = 'confirm-dialog-backdrop';
  overlay.innerHTML = `
    <section class="confirm-dialog" role="alertdialog" aria-modal="true" aria-labelledby="confirm-dialog-title" aria-describedby="confirm-dialog-description">
      <span class="confirm-dialog__handle" aria-hidden="true"></span>
      <div class="confirm-dialog__heading">
        <div class="confirm-dialog__icon" aria-hidden="true"><i class="fa-solid fa-trash-can"></i></div>
        <div>
          <p class="confirm-dialog__eyebrow">Konfirmasi tindakan</p>
          <h2 id="confirm-dialog-title" class="confirm-dialog__title"></h2>
        </div>
      </div>
      <p id="confirm-dialog-description" class="confirm-dialog__description"></p>
      <div class="confirm-dialog__actions">
        <button type="button" class="confirm-dialog__button confirm-dialog__button--cancel">Batalkan</button>
        <button type="button" class="confirm-dialog__button confirm-dialog__button--danger"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus</button>
      </div>
    </section>
  `;

  overlay.querySelector('.confirm-dialog__title').textContent = title;
  overlay.querySelector('.confirm-dialog__description').textContent = description;
  document.body.appendChild(overlay);
  document.body.style.overflow = 'hidden';

  const cancelButton = overlay.querySelector('.confirm-dialog__button--cancel');
  const confirmButton = overlay.querySelector('.confirm-dialog__button--danger');

  const close = () => {
    window.removeEventListener('keydown', handleKeydown);
    overlay.classList.add('confirm-dialog-backdrop--closing');
    document.body.style.overflow = '';
    setTimeout(() => {
      overlay.remove();
      previousFocus?.focus?.();
    }, 180);
  };

  const handleKeydown = (event) => {
    if (event.key === 'Escape') close();
  };

  overlay.addEventListener('click', (event) => { if (event.target === overlay) close(); });
  cancelButton.addEventListener('click', close);
  confirmButton.addEventListener('click', () => {
    close();
    onConfirm();
  });
  window.addEventListener('keydown', handleKeydown);
  requestAnimationFrame(() => {
    overlay.classList.add('confirm-dialog-backdrop--show');
    cancelButton.focus();
  });
}