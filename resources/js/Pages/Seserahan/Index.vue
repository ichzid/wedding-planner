<template>
  <AppLayout>
    <Head title="Seserahan" />

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="page-title">List Seserahan</h1>
        <p class="page-sub">{{ sudahBeli }}/{{ totalItem }} item sudah dibeli · {{ progressPct }}%</p>
      </div>
      <button class="btn btn--primary" @click="openCreate">
        <i class="fa-solid fa-plus fa-xs"></i> Tambah
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="card summary-card">
        <p class="summary-card__label">Total Item</p>
        <p class="summary-card__value">{{ totalItem }}</p>
        <p class="summary-card__sub">{{ sudahBeli }} sudah dibeli · {{ belumBeli }} belum</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Progress</p>
        <div class="prog-track mt-2">
          <div class="prog-fill" :style="{ width: progressPct + '%' }"></div>
        </div>
        <p class="summary-card__sub mt-2">{{ progressPct }}% selesai</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Total Harga</p>
        <p class="summary-card__value">{{ formatRp(totalHarga) }}</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="searchQuery" type="text" placeholder="Cari item atau kategori..." class="form-input search-input" />
      </div>
      <select v-model="filterStatus" class="form-input toolbar__select">
        <option value="">Semua Status</option>
        <option value="sudah_dibeli">Sudah Dibeli</option>
        <option value="belum">Belum Dibeli</option>
      </select>
      <button v-if="filterStatus || searchQuery" class="btn btn--outline btn--sm" @click="resetFilters">
        <i class="fa-solid fa-xmark"></i> Reset
      </button>
      <button class="btn btn--outline" @click="exportToExcel">
        <i class="fa-solid fa-file-excel" style="color: #2e7d32;"></i>
        Export Excel
      </button>
    </div>

    <!-- Table -->
    <div class="card" style="overflow:hidden">
      <div style="overflow-x:auto">
        <table class="tbl">
          <thead>
            <tr>
              <th style="width:36px">#</th>
              <th>Nama Item & Kategori</th>
              <th>Untuk</th>
              <th class="text-right">Qty</th>
              <th class="text-right">Harga Satuan</th>
              <th class="text-right">Total</th>
              <th>Status</th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(s, index) in filteredItems"
              :key="s.id"
              class="draggable-row"
              :class="{ 'is-dragging': draggedId === s.id, 'is-drop-target': dragOverId === s.id, 'is-drop-before': dropPlacement(s.id) === 'before', 'is-drop-after': dropPlacement(s.id) === 'after', 'is-drag-disabled': !canDragRows }"
              :draggable="canDragRows"
              @dragstart="startDrag(s, index, $event)"
              @dragover.prevent="setDragOver(s, index)"
              @drop="dropRow(index)"
              @dragend="endDrag"
              @touchstart="handleTouchStart"
              @touchmove="handleTouchMove"
              @touchend="handleTouchEnd"
            >
              <td class="mono-text drag-cell"><i class="fa-solid fa-grip-vertical"></i> {{ s.no }}</td>
              <td>
                <p class="item-name">{{ s.nama_item }}</p>
                <p class="item-sub">{{ s.kategori }}</p>
              </td>
              <td>
                <span class="chip" :class="untukChipClass(s.untuk)">
                  {{ untukLabel(s.untuk) }}
                </span>
              </td>
              <td class="text-right fw-600">{{ s.qty }} {{ s.satuan }}</td>
              <td class="text-right">{{ formatRp(s.harga) }}</td>
              <td class="text-right fw-600 text-dark">{{ formatRp(s.qty * s.harga) }}</td>
              <td>
                <span class="chip" :class="s.status === 'sudah_dibeli' ? 'chip--ok' : 'chip--danger'">
                  {{ s.status === 'sudah_dibeli' ? 'Sudah Dibeli' : 'Belum Dibeli' }}
                </span>
              </td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="btn btn--ghost btn--icon" title="Copy" @click="openCopy(s)" :id="'copy-seserahan-'+s.id">
                    <i class="fa-solid fa-copy action-icon action-icon--copy"></i>
                  </button>
                  <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(s)" :id="'edit-seserahan-'+s.id">
                    <i class="fa-solid fa-pen-to-square action-icon action-icon--edit"></i>
                  </button>
                  <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(s)" :id="'del-seserahan-'+s.id">
                    <i class="fa-solid fa-trash action-icon action-icon--delete"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!filteredItems.length">
              <td colspan="8">
                <div class="empty-state">
                  <i class="fa-solid fa-gift empty-state__icon"></i>
                  <p class="empty-state__text">Belum ada data seserahan. Yuk, mulai catat seserahan pernikahanmu!</p>
                  <button class="btn btn--primary" @click="openCreate">
                    <i class="fa-solid fa-plus fa-xs"></i> Tambah Seserahan Pertama
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredItems.length">
            <tr class="tfoot-row">
              <td colspan="5" class="tfoot-label">Total ({{ filteredItems.length }} item)</td>
              <td class="text-right fw-700 text-dark">{{ formatRp(filteredTotalHarga) }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">{{ editItem ? 'Edit Seserahan' : (copyItem ? 'Copy Seserahan' : 'Tambah Seserahan') }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <form @submit.prevent="save" class="modal-body">
            <div>
              <label class="form-label">Nama Item *</label>
              <input v-model="form.nama_item" type="text" required class="form-input" placeholder="Cincin Emas 24K">
              <p v-if="errors.nama_item" class="form-error">{{ errors.nama_item }}</p>
            </div>
            <div class="form-row-2" style="margin-top:var(--space-md)">
              <div>
                <label class="form-label">Kategori *</label>
                <select v-model="form.kategori" required class="form-input">
                  <option v-for="kat in kategoriOptions" :key="kat" :value="kat">{{ kat }}</option>
                </select>
                <p v-if="errors.kategori" class="form-error">{{ errors.kategori }}</p>
              </div>
              <div>
                <label class="form-label">Untuk *</label>
                <select v-model="form.untuk" required class="form-input">
                  <option value="cpp">Mempelai Pria</option>
                  <option value="cpw">Mempelai Wanita</option>
                </select>
                <p v-if="errors.untuk" class="form-error">{{ errors.untuk }}</p>
              </div>
            </div>
            <div class="form-row-2" style="margin-top:var(--space-md)">
              <div>
                <label class="form-label">Qty *</label>
                <input v-model="form.qty" type="number" min="1" required class="form-input">
                <p v-if="errors.qty" class="form-error">{{ errors.qty }}</p>
              </div>
              <div>
                <label class="form-label">Satuan</label>
                <input v-model="form.satuan" type="text" class="form-input" placeholder="set, buah, dll">
                <p v-if="errors.satuan" class="form-error">{{ errors.satuan }}</p>
              </div>
            </div>
            <div class="form-row-2" style="margin-top:var(--space-md)">
              <div>
                <label class="form-label">Harga Satuan (Rp) *</label>
                <input v-model="form.harga" type="number" min="0" required class="form-input">
                <p v-if="errors.harga" class="form-error">{{ errors.harga }}</p>
              </div>
              <div>
                <label class="form-label">Status *</label>
                <select v-model="form.status" required class="form-input">
                  <option value="belum">Belum Dibeli</option>
                  <option value="sudah_dibeli">Sudah Dibeli</option>
                </select>
                <p v-if="errors.status" class="form-error">{{ errors.status }}</p>
              </div>
            </div>
            <div class="modal-footer" style="margin-top:var(--space-lg);padding:0">
              <button type="button" class="btn btn--outline" style="flex:1;justify-content:center" @click="closeModal">Batal</button>
              <button type="submit" class="btn btn--primary" style="flex:1;justify-content:center" :disabled="saving">
                <i v-if="saving" class="fa-solid fa-spinner fa-spin fa-xs"></i>
                {{ saving ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import { showToast, confirmDeleteDialog } from '@/utils.js';
import * as XLSX from 'xlsx';

const props = defineProps({
  items: Array,
  kategoriOptions: Array,
  totalItem: Number,
  sudahBeli: Number,
  belumBeli: Number,
  totalHarga: Number,
});

const localItems = ref([...props.items]);
const searchQuery  = ref('');
const filterStatus = ref('');
const draggedIndex = ref(null);
const draggedId = ref(null);
const dragOverIndex = ref(null);
const dragOverId = ref(null);
const showModal = ref(false);
const editItem  = ref(null);
const copyItem  = ref(null);
const saving    = ref(false);
const errors    = ref({});

const defaultForm = () => ({
  kategori:       'Umum',
  nama_item:      '',
  untuk:          'cpp',
  qty:            1,
  satuan:         'buah',
  harga:          0,
  status:         'belum',
});
const form = ref(defaultForm());

const progressPct = computed(() => {
  if (!props.totalItem) return 0;
  return Math.round(props.sudahBeli / props.totalItem * 100);
});

watch(() => props.items, (items) => {
  localItems.value = [...items];
});

const filteredItems = computed(() => {
  let list = [...localItems.value];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(s =>
      s.nama_item?.toLowerCase().includes(q) ||
      s.kategori?.toLowerCase().includes(q)
    );
  }
  if (filterStatus.value) {
    list = list.filter(s => s.status === filterStatus.value);
  }
  return list;
});

const canDragRows = computed(() => !searchQuery.value && !filterStatus.value);
const filteredTotalHarga = computed(() => filteredItems.value.reduce((sum, i) => sum + (i.qty * i.harga), 0));

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function exportToExcel() {
  if (filteredItems.value.length === 0) {
    showToast('Tidak ada data untuk diexport');
    return;
  }

  const dateNow = new Date();
  const dateStr = dateNow.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

  const headers = ['No', 'Kategori', 'Nama Item', 'Untuk', 'Qty', 'Satuan', 'Harga Satuan', 'Total', 'Status'];
  
  const dataRows = filteredItems.value.map((i, index) => [
    i.no || index + 1,
    i.kategori || '',
    i.nama_item || '',
    untukLabel(i.untuk),
    i.qty || 0,
    i.satuan || '',
    i.harga || 0,
    (i.qty || 0) * (i.harga || 0),
    i.status === 'sudah_dibeli' ? 'Sudah Dibeli' : 'Belum Dibeli'
  ]);
  
  dataRows.push([
    '',
    'TOTAL',
    '',
    '',
    '',
    '',
    '',
    filteredTotalHarga.value,
    ''
  ]);

  const finalData = [
    ['DAFTAR LIST SESERAHAN'],
    [`Dicetak pada: ${dateStr}`],
    [], // empty row
    headers,
    ...dataRows
  ];

  const ws = XLSX.utils.aoa_to_sheet(finalData);
  
  // Merge cells for title
  ws['!merges'] = [
    { s: { r: 0, c: 0 }, e: { r: 0, c: 8 } }, // Merge A1:I1
    { s: { r: 1, c: 0 }, e: { r: 1, c: 8 } }  // Merge A2:I2
  ];

  // Set column widths
  ws['!cols'] = [
    { wch: 5 },  // No
    { wch: 20 }, // Kategori
    { wch: 30 }, // Nama Item
    { wch: 20 }, // Untuk
    { wch: 10 }, // Qty
    { wch: 15 }, // Satuan
    { wch: 20 }, // Harga Satuan
    { wch: 20 }, // Total
    { wch: 20 }  // Status
  ];

  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "List Seserahan");
  
  XLSX.writeFile(wb, `List_Seserahan_${dateNow.toISOString().split('T')[0]}.xlsx`);
  showToast('Data berhasil diexport ke Excel');
}

function resetFilters() {
  searchQuery.value  = '';
  filterStatus.value = '';
}

function untukLabel(untuk) {
  if (untuk === 'cpw') return 'Mempelai Wanita';
  if (untuk === 'cpp') return 'Mempelai Pria';
  return '-';
}

function untukChipClass(untuk) {
  if (untuk === 'cpw') return 'chip--cpw';
  if (untuk === 'cpp') return 'chip--cpp';
  return 'chip--neutral';
}

function seserahanToForm(s) {
  return {
    kategori:  s.kategori,
    nama_item: s.nama_item,
    untuk:     s.untuk,
    qty:       s.qty,
    satuan:    s.satuan || '',
    harga:     s.harga,
    status:    s.status,
  };
}

function openCreate() {
  editItem.value   = null;
  copyItem.value   = null;
  form.value       = defaultForm();
  errors.value     = {};
  showModal.value  = true;
}

function openCopy(s) {
  editItem.value   = null;
  copyItem.value   = s;
  form.value       = {
    kategori:  s.kategori,
    nama_item: s.nama_item,
    untuk:     s.untuk,
    qty:       s.qty,
    satuan:    s.satuan || '',
    harga:     s.harga,
    status:    s.status,
  };
  errors.value     = {};
  showModal.value  = true;
}

function openEdit(s) {
  editItem.value = s;
  copyItem.value = null;
  form.value = {
    kategori:  s.kategori,
    nama_item: s.nama_item,
    untuk:     s.untuk,
    qty:       s.qty,
    satuan:    s.satuan || '',
    harga:     s.harga,
    status:    s.status,
  };
  errors.value   = {};
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editItem.value = null;
  copyItem.value = null;
}

async function save() {
  saving.value = true;
  errors.value = {};
  const payload = {
    ...form.value,
    qty:   Number(form.value.qty) || 1,
    harga: Number(form.value.harga) || 0,
  };
  const url = editItem.value
    ? route('seserahan.update', editItem.value.id)
    : route('seserahan.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, payload, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(editItem.value ? 'Seserahan berhasil diupdate.' : (copyItem.value ? 'Copy seserahan berhasil ditambahkan.' : 'Seserahan berhasil ditambahkan.'));
      closeModal();
      saving.value = false;
    },
    onError: (errs) => {
      errors.value = errs;
      saving.value = false;
    },
  });
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
      startDrag(filteredItems.value[b_index], b_index, null);
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
        const item = filteredItems.value[index];
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

  const reordered = [...localItems.value];
  const [moved] = reordered.splice(draggedIndex.value, 1);
  reordered.splice(targetIndex, 0, moved);
  localItems.value = reordered.map((item, index) => ({ ...item, no: index + 1 }));

  router.patch(route('seserahan.reorder'), {
    ids: localItems.value.map((item) => item.id),
  }, {
    preserveScroll: true,
    onSuccess: () => showToast('Urutan seserahan berhasil disimpan.'),
    onError: () => {
      localItems.value = [...props.items];
      showToast('Urutan seserahan gagal disimpan.');
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

function confirmDelete(s) {
  confirmDeleteDialog(() => {
    router.delete(route('seserahan.destroy', s.id), {
      preserveScroll: true,
      onSuccess: () => showToast('Seserahan berhasil dihapus.'),
    });
  });
}
</script>

<style scoped>
.summary-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
}
@media (min-width: 640px) { .summary-grid { grid-template-columns: repeat(3, 1fr); } }

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
.toolbar__search { position: relative; flex: 1; min-width: 180px; }
.search-icon {
  position: absolute; left: 10px; top: 50%;
  transform: translateY(-50%); color: var(--text-dim);
  font-size: 12px; pointer-events: none;
}
.search-input { padding-left: 30px; }
.toolbar__select { max-width: 160px; }

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
.item-name { font-size: 13.5px; font-weight: 500; color: var(--text); }
.item-sub  { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

.text-right { text-align: right; }
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.text-dark  { color: var(--text); }
.text-dim   { color: var(--text-dim); }

.tfoot-row td {
  padding: 10px 14px;
  background: var(--rose-pale);
  border-top: 2px solid var(--border);
  font-size: 13px;
}
.tfoot-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--text-muted);
}

.chip--cpw { background: #fdf0f8; color: #c4719e; border: 1px solid #f0c8e4; }
.chip--cpp { background: #eef4ff; color: #5a82c4; border: 1px solid #c8d8f0; }
.chip--neutral { background: var(--bg-soft); color: var(--text-muted); border: 1px solid var(--border); }

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); }
@media (max-width: 480px) { .form-row-2 { grid-template-columns: 1fr; } }
</style>