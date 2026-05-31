<template>
  <AppLayout>
    <Head title="Daftar Undangan" />
    <div>
    <!-- Page Header -->
    <div class="page-header">
      <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
          <h1 class="page-title">Daftar Undangan</h1>
          <p class="page-sub">Kelola undangan tamu pernikahan Anda</p>
        </div>
        <button class="btn btn--primary" @click="openAdd">
          <i class="fa-solid fa-plus"></i>
          Tambah Tamu
        </button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="card summary-card">
        <p class="summary-card__label">Total Tamu</p>
        <p class="summary-card__value">{{ totalTamu }}</p>
        <p class="summary-card__sub">{{ hadir }} konfirmasi hadir</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Mempelai Wanita</p>
        <p class="summary-card__value">{{ tamuCpw }}</p>
        <p class="summary-card__sub">Tamu dari pihak mempelai wanita</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Mempelai Pria</p>
        <p class="summary-card__value">{{ tamuCpp }}</p>
        <p class="summary-card__sub">Tamu dari pihak mempelai pria</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Konfirmasi Hadir</p>
        <p class="summary-card__value">{{ hadir }}</p>
        <p class="summary-card__sub">Dari {{ totalTamu }} total tamu</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="search" type="text" placeholder="Cari nama tamu..." class="form-input search-input" id="search-tamu">
      </div>
      <select v-model="filterPihak" class="form-input toolbar__select" id="filter-pihak">
        <option value="">Semua Pihak</option>
        <option value="cpw">Pihak Mempelai Wanita</option>
        <option value="cpp">Pihak Mempelai Pria</option>
        <option value="umum">Umum</option>
      </select>
      <select v-model="filterStatus" class="form-input toolbar__select" id="filter-status">
        <option value="">Semua Status</option>
        <option value="belum_dikirim">Belum Dikirim</option>
        <option value="sudah_dikirim">Sudah Dikirim</option>
        <option value="hadir">Konfirmasi Hadir</option>
        <option value="tidak_hadir">Tidak Hadir</option>
      </select>
      <button class="btn btn--outline" @click="exportToExcel">
        <i class="fa-solid fa-file-excel" style="color: #2e7d32;"></i>
        Export Excel
      </button>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="section-header">
        <span class="section-title">
          <i class="fa-solid fa-list" style="color:var(--rose); margin-right:8px;"></i>
          Daftar Undangan ({{ filteredGuests.length }})
        </span>
      </div>
      <div style="overflow-x:auto;">
        <table class="tbl">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th>Nama Tamu</th>
              <th>Pihak</th>
              <th>Status Undangan</th>
              <th>Catatan</th>
              <th style="width:90px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredGuests.length === 0">
              <td colspan="6">
                <div class="empty-state">
                  <div class="empty-state__icon"><i class="fa-solid fa-user-slash"></i></div>
                  <div class="empty-state__text">Belum ada tamu yang dicatat.</div>
                  <button class="btn btn--primary btn--sm" @click="openAdd">
                    <i class="fa-solid fa-plus"></i> Tambah Tamu Sekarang
                  </button>
                </div>
              </td>
            </tr>
            <tr
              v-for="(guest, index) in filteredGuests"
              :key="guest.id"
              class="draggable-row"
              :class="{ 'is-dragging': draggedId === guest.id, 'is-drop-target': dragOverId === guest.id, 'is-drop-before': dropPlacement(guest.id) === 'before', 'is-drop-after': dropPlacement(guest.id) === 'after', 'is-drag-disabled': !canDragRows }"
              :draggable="canDragRows"
              @dragstart="startDrag(guest, index, $event)"
              @dragover.prevent="setDragOver(guest, index)"
              @drop="dropRow(index)"
              @dragend="endDrag"
              @touchstart="handleTouchStart"
              @touchmove="handleTouchMove"
              @touchend="handleTouchEnd"
            >
              <td class="mono-text drag-cell"><i class="fa-solid fa-grip-vertical"></i> {{ guest.no || index + 1 }}</td>
              <td>
                <span class="guest-name">{{ guest.nama_tamu }}</span>
              </td>
              <td>
                <span class="chip" :class="pihakChipClass(guest.pihak)">
                  {{ pihakLabel(guest.pihak) }}
                </span>
              </td>
              <td>
                <span class="chip" :class="statusChipClass(guest.status)">
                  {{ statusLabel(guest.status) }}
                </span>
              </td>
              <td style="font-size:12.5px; color:var(--text-muted); max-width:200px;">
                {{ guest.catatan || '—' }}
              </td>
              <td style="text-align:center;">
                <div style="display:flex; gap:4px; justify-content:center;">
                  <button class="btn btn--ghost btn--icon" title="Copy" @click="openCopy(guest)" :id="'copy-tamu-'+guest.id">
                    <i class="fa-solid fa-copy action-icon action-icon--copy"></i>
                  </button>
                  <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(guest)" :id="'edit-tamu-'+guest.id">
                    <i class="fa-solid fa-pen-to-square action-icon action-icon--edit"></i>
                  </button>
                  <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(guest)" :id="'del-tamu-'+guest.id">
                    <i class="fa-solid fa-trash action-icon action-icon--delete"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">{{ isEditing ? 'Edit Tamu' : (isCopying ? 'Copy Tamu' : 'Tambah Tamu') }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <form @submit.prevent="submitForm" class="modal-body">
            <div>
              <label class="form-label">Nama Tamu *</label>
              <input v-model="form.nama_tamu" type="text" class="form-input" placeholder="Contoh: Budi Santoso" id="input-nama-tamu" required>
              <p v-if="errors.nama_tamu" class="form-error">{{ errors.nama_tamu }}</p>
            </div>
            <div class="form-row-2" style="margin-top:var(--space-md)">
              <div>
                <label class="form-label">Dari Pihak *</label>
                <select v-model="form.pihak" class="form-input" id="input-pihak" required>
                  <option value="cpw">Pihak Mempelai Wanita</option>
                  <option value="cpp">Pihak Mempelai Pria</option>
                  <option value="umum">Umum / Keduanya</option>
                </select>
                <p v-if="errors.pihak" class="form-error">{{ errors.pihak }}</p>
              </div>
              <div>
                <label class="form-label">Status Undangan *</label>
                <select v-model="form.status" class="form-input" id="input-status" required>
                  <option value="belum_dikirim">Belum Dikirim</option>
                  <option value="sudah_dikirim">Sudah Dikirim</option>
                  <option value="hadir">Konfirmasi Hadir</option>
                  <option value="tidak_hadir">Tidak Hadir</option>
                </select>
                <p v-if="errors.status" class="form-error">{{ errors.status }}</p>
              </div>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Catatan</label>
              <input v-model="form.catatan" type="text" class="form-input" placeholder="Misal: Teman kuliah, Keluarga besar..." id="input-catatan">
            </div>
            <div class="modal-footer" style="margin-top:var(--space-lg);padding:0">
              <button type="button" class="btn btn--outline" style="flex:1;justify-content:center" @click="closeModal">Batal</button>
              <button type="submit" class="btn btn--primary" style="flex:1;justify-content:center" :disabled="submitting">
                <i v-if="submitting" class="fa-solid fa-spinner fa-spin fa-xs"></i>
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirm Modal -->
      <div v-if="showDeleteModal" class="modal-backdrop" @click.self="showDeleteModal=false">
        <div class="modal-box" style="max-width:380px;">
          <div class="modal-header">
            <span class="modal-title" style="color:var(--danger-text);">
              <i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>
              Hapus Tamu
            </span>
            <button class="btn btn--ghost btn--icon" @click="showDeleteModal=false">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="modal-body" style="padding-top:12px;">
            <p style="font-size:14px; color:var(--text-muted); line-height:1.6;">
              Hapus <strong style="color:var(--text);">{{ deleteTarget?.nama_tamu }}</strong> dari daftar tamu? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="modal-footer" style="padding:16px 0 0; border:none;">
              <button class="btn btn--outline" @click="showDeleteModal=false">Batal</button>
              <button class="btn btn--primary" style="background:var(--danger-text); margin-left:auto;" @click="doDelete">
                <i class="fa-solid fa-trash"></i> Ya, Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import { showToast } from '@/utils.js';
import * as XLSX from 'xlsx';

const props = defineProps({
  guests:    Array,
  totalTamu: Number,
  tamuCpw:   Number,
  tamuCpp:   Number,
  tamuUmum:  Number,
  hadir:     Number,
  namaCpw:   String,
  namaCpp:   String,
});

const localGuests = ref([...props.guests]);
const search       = ref('');
const filterPihak  = ref('');
const filterStatus = ref('');
const draggedIndex = ref(null);
const draggedId = ref(null);
const dragOverIndex = ref(null);
const dragOverId = ref(null);
const showModal    = ref(false);
const showDeleteModal = ref(false);
const deleteTarget    = ref(null);
const isEditing    = ref(false);
const isCopying    = ref(false);
const editId       = ref(null);
const submitting   = ref(false);
const errors       = ref({});

const form = ref({ nama_tamu: '', pihak: 'cpw', status: 'belum_dikirim', catatan: '' });

watch(() => props.guests, (guests) => {
  localGuests.value = [...guests];
});

const filteredGuests = computed(() => {
  return localGuests.value.filter(g => {
    const matchSearch = g.nama_tamu.toLowerCase().includes(search.value.toLowerCase());
    const matchPihak  = filterPihak.value ? g.pihak === filterPihak.value : true;
    const matchStatus = filterStatus.value ? g.status === filterStatus.value : true;
    return matchSearch && matchPihak && matchStatus;
  });
});

const canDragRows = computed(() => !search.value && !filterPihak.value && !filterStatus.value);

function resetFilters() {
  search.value = '';
  filterPihak.value = '';
  filterStatus.value = '';
}

function openAdd() {
  isEditing.value = false;
  isCopying.value = false;
  editId.value    = null;
  form.value      = { nama_tamu: '', pihak: 'cpw', status: 'belum_dikirim', catatan: '' };
  errors.value    = {};
  showModal.value = true;
}

function openCopy(guest) {
  isEditing.value = false;
  isCopying.value = true;
  editId.value    = null;
  form.value      = { nama_tamu: guest.nama_tamu, pihak: guest.pihak, status: guest.status, catatan: guest.catatan || '' };
  errors.value    = {};
  showModal.value = true;
}

function openEdit(guest) {
  isEditing.value = true;
  isCopying.value = false;
  editId.value    = guest.id;
  form.value      = { nama_tamu: guest.nama_tamu, pihak: guest.pihak, status: guest.status, catatan: guest.catatan || '' };
  errors.value    = {};
  showModal.value = true;
}

function closeModal() { showModal.value = false; }

function submitForm() {
  submitting.value = true;
  errors.value     = {};

  const url    = isEditing.value ? route('tamu.update', editId.value) : route('tamu.store');
  const method = isEditing.value ? 'patch' : 'post';

  router[method](url, form.value, {
    onSuccess: () => { showModal.value = false; submitting.value = false; showToast(isEditing.value ? 'Tamu berhasil diperbarui' : (isCopying.value ? 'Copy tamu berhasil ditambahkan' : 'Tamu berhasil ditambahkan')); },
    onError:   (e) => { errors.value = e; submitting.value = false; },
    preserveScroll: true,
  });
}

function confirmDelete(guest) {
  deleteTarget.value  = guest;
  showDeleteModal.value = true;
}

function doDelete() {
  router.delete(route('tamu.destroy', deleteTarget.value.id), {
    onSuccess: () => { showDeleteModal.value = false; showToast('Tamu berhasil dihapus'); },
    preserveScroll: true,
  });
}

function pihakLabel(pihak) {
  if (pihak === 'cpw') return 'Mempelai Wanita';
  if (pihak === 'cpp') return 'Mempelai Pria';
  return 'Umum';
}

function pihakChipClass(pihak) {
  if (pihak === 'cpw') return 'chip--cpw';
  if (pihak === 'cpp') return 'chip--cpp';
  return 'chip--neutral';
}

function statusLabel(status) {
  const map = { belum_dikirim: 'Belum Dikirim', sudah_dikirim: 'Sudah Dikirim', hadir: 'Konfirmasi Hadir', tidak_hadir: 'Tidak Hadir' };
  return map[status] || status;
}

function statusChipClass(status) {
  if (status === 'hadir')        return 'chip--ok';
  if (status === 'tidak_hadir')  return 'chip--danger';
  if (status === 'sudah_dikirim') return 'chip--warn';
  return 'chip--soft';
}

function exportToExcel() {
  if (filteredGuests.value.length === 0) {
    showToast('Tidak ada data untuk diexport');
    return;
  }

  const dateNow = new Date();
  const dateStr = dateNow.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

  const headers = ['No', 'Nama Tamu', 'Pihak', 'Status Undangan', 'Catatan'];
  
  const dataRows = filteredGuests.value.map((guest, index) => [
    guest.no || index + 1,
    guest.nama_tamu || '',
    pihakLabel(guest.pihak),
    statusLabel(guest.status),
    guest.catatan || ''
  ]);
  
  const finalData = [
    ['DAFTAR TAMU UNDANGAN PERNIKAHAN'],
    [`Dicetak pada: ${dateStr}`],
    [], // empty row
    headers,
    ...dataRows
  ];

  const ws = XLSX.utils.aoa_to_sheet(finalData);
  
  // Merge cells for title
  ws['!merges'] = [
    { s: { r: 0, c: 0 }, e: { r: 0, c: 4 } }, // Merge A1:E1
    { s: { r: 1, c: 0 }, e: { r: 1, c: 4 } }  // Merge A2:E2
  ];

  // Set column widths
  ws['!cols'] = [
    { wch: 5 },  // No
    { wch: 30 }, // Nama Tamu
    { wch: 25 }, // Pihak
    { wch: 20 }, // Status
    { wch: 40 }  // Catatan
  ];

  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Daftar Tamu");
  
  XLSX.writeFile(wb, `Daftar_Tamu_${dateNow.toISOString().split('T')[0]}.xlsx`);
  showToast('Data berhasil diexport ke Excel');
}

function startDrag(item, index, event) {
  if (!canDragRows.value) return;
  draggedIndex.value = index;
  draggedId.value = item.id;
  if(event && event.dataTransfer) {
      event.dataTransfer.effectAllowed = 'move';
  }
}

function handleTouchStart(event) {
  if (!canDragRows.value) return;
  const targetEl = event.target;
  if (!targetEl?.closest('.drag-cell')) return;

  const targetRow = targetEl.closest('.draggable-row');
  if (targetRow) {
      const b_index = Array.from(targetRow.parentNode.children).indexOf(targetRow);
      startDrag(filteredGuests.value[b_index], b_index, null);
  }
}

function handleTouchMove(event) {
    if(!canDragRows.value || draggedId.value === null) return;
    event.preventDefault(); // Prevent scrolling while dragging
    const touch = event.touches[0];
    const targetEl = document.elementFromPoint(touch.clientX, touch.clientY);
    const targetRow = targetEl?.closest('.draggable-row');
    if (targetRow) {
        const index = Array.from(targetRow.parentNode.children).indexOf(targetRow);
        const item = filteredGuests.value[index];
        setDragOver(item, index);
    }
}

function setDragOver(item, index) {
  if (!canDragRows.value || draggedId.value === item.id) return;
  dragOverId.value = item.id;
  dragOverIndex.value = index;
}

function dropPlacement(id) {
  if (dragOverId.value !== id || draggedIndex.value === null || dragOverIndex.value === null) return null;
  return dragOverIndex.value > draggedIndex.value ? 'after' : 'before';
}

function dropRow(targetIndex) {
  if (!canDragRows.value || draggedIndex.value === null || draggedIndex.value === targetIndex) {
    endDrag();
    return;
  }

  const reordered = [...localGuests.value];
  const [moved] = reordered.splice(draggedIndex.value, 1);
  reordered.splice(targetIndex, 0, moved);
  localGuests.value = reordered.map((item, index) => ({ ...item, no: index + 1 }));

  router.patch(route('tamu.reorder'), {
    ids: localGuests.value.map((item) => item.id),
  }, {
    preserveScroll: true,
    onSuccess: () => showToast('Urutan tamu berhasil disimpan.'),
    onError: () => {
      localGuests.value = [...props.guests];
      showToast('Urutan tamu gagal disimpan.');
    },
  });

  endDrag();
}

function handleTouchEnd(event) {
    if (draggedId.value !== null && dragOverIndex.value !== null) {
        dropRow(dragOverIndex.value);
    } else {
        endDrag();
    }
}

function endDrag() {
  draggedIndex.value = null;
  draggedId.value = null;
  dragOverIndex.value = null;
  dragOverId.value = null;
}

</script>

<style scoped>
.summary-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
}
@media (min-width: 640px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .summary-grid { grid-template-columns: repeat(4, 1fr); } }

.summary-card { padding: var(--space-xl); }
.summary-card__label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: none;
  letter-spacing: 0.02em;
}
.summary-card__value {
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  margin-top: 6px;
  letter-spacing: -0.02em;
}
.summary-card__sub { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }

.toolbar {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  flex-wrap: wrap;
  margin-bottom: var(--space-lg);
}
.toolbar__search {
  position: relative;
  flex: 1;
  min-width: 180px;
}
.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-dim);
  font-size: 12px;
  pointer-events: none;
}
.search-input { padding-left: 30px; }
.toolbar__select { max-width: 180px; }
@media (max-width: 640px) { .toolbar__select { max-width: none; width: 100%; } }

.draggable-row { cursor: grab; position: relative; transition: background 0.18s ease, opacity 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease; }
.draggable-row.is-dragging { opacity: 0.45; transform: scale(0.995); }
.draggable-row.is-drop-target { background: var(--rose-pale); box-shadow: inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-before { box-shadow: inset 0 3px 0 var(--rose), inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-after { box-shadow: inset 0 -3px 0 var(--rose), inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-before td:first-child::before,
.draggable-row.is-drop-after td:first-child::after { content: ''; position: absolute; left: 10px; width: 8px; height: 8px; border-radius: 999px; background: var(--rose); box-shadow: 0 0 0 3px var(--rose-pale); }
.draggable-row.is-drop-before td:first-child::before { top: -4px; }
.draggable-row.is-drop-after td:first-child::after { bottom: -4px; }
.draggable-row.is-drag-disabled { cursor: default; }
.drag-cell { white-space: nowrap; touch-action: none; padding: 12px; cursor: grab; }
.drag-cell i { color: var(--text-dim); margin-right: 6px; font-size: 16px; }
.is-drag-disabled .drag-cell i { opacity: 0.35; }
.mono-text { font-family: monospace; font-size: 11px; color: var(--text-dim); }

.guest-name { font-weight: 500; color: var(--text); }

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); }
@media (max-width: 480px) { .form-row-2 { grid-template-columns: 1fr; } }

.chip--cpw   { background: #fdf0f8; color: #c4719e; border: 1px solid #f0c8e4; }
.chip--cpp   { background: #eef4ff; color: #5a82c4; border: 1px solid #c8d8f0; }
</style>
