<template>
  <AppLayout>
    <Head title="Dokumen KUA" />

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="page-title">Dokumen KUA</h1>
        <p class="page-sub">{{ doneCount }}/{{ totalDok }} dokumen selesai · Total biaya {{ formatRp(totalBiaya) }}</p>
      </div>
      <button class="btn btn--dark" @click="openCreate">
        <i class="fa-solid fa-plus fa-xs"></i> Tambah
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="card summary-card">
        <p class="summary-card__label">Total Dokumen</p>
        <p class="summary-card__value">{{ totalDok }}</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Mempelai Wanita Siap</p>
        <p class="summary-card__value">{{ doneCpw }}<span style="font-size:16px;font-weight:500;color:var(--ink-300)">/{{ totalDok }}</span></p>
        <div class="prog-track mt-2"><div class="prog-fill" :style="{ width: (totalDok ? Math.round(doneCpw/totalDok*100) : 0) + '%' }"></div></div>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Mempelai Pria Siap</p>
        <p class="summary-card__value">{{ doneCpp }}<span style="font-size:16px;font-weight:500;color:var(--ink-300)">/{{ totalDok }}</span></p>
        <div class="prog-track mt-2"><div class="prog-fill" :style="{ width: (totalDok ? Math.round(doneCpp/totalDok*100) : 0) + '%' }"></div></div>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Total Biaya</p>
        <p class="summary-card__value" style="font-size:18px">{{ formatRp(totalBiaya) }}</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="searchQuery" type="text" placeholder="Cari nama dokumen..." class="form-input search-input">
      </div>
      <select v-model="filterStatus" class="form-input toolbar__select">
        <option value="">Semua Status</option>
        <option value="done">Selesai Kedua Pihak</option>
        <option value="pending">Belum Selesai</option>
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
              <th>Nama Dokumen</th>
              <th class="text-right">Biaya</th>
              <th class="text-center">Mempelai Wanita</th>
              <th class="text-center">Mempelai Pria</th>
              <th>Catatan</th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(doc, index) in filteredDocuments"
              :key="doc.id"
              class="draggable-row"
              :class="{ 'row--done': doc.cpw_status && doc.cpp_status, 'is-dragging': draggedId === doc.id, 'is-drop-target': dragOverId === doc.id, 'is-drop-before': dropPlacement(doc.id) === 'before', 'is-drop-after': dropPlacement(doc.id) === 'after', 'is-drag-disabled': !canDragRows }"
              :draggable="canDragRows"
              @dragstart="startDrag(doc, index, $event)"
              @dragover.prevent="setDragOver(doc, index)"
              @drop="dropRow(index)"
              @dragend="endDrag"
              @touchstart="handleTouchStart"
              @touchmove="handleTouchMove"
              @touchend="handleTouchEnd"
            >
              <td class="mono-text drag-cell"><i class="fa-solid fa-grip-vertical"></i> {{ doc.no }}</td>
              <td>
                <p class="item-name">{{ doc.nama_dokumen }}</p>
              </td>
              <td class="text-right">{{ doc.biaya > 0 ? formatRp(doc.biaya) : '–' }}</td>
              <td class="text-center">
                <button
                  class="check-btn"
                  @click="toggleCpw(doc)"
                  :title="doc.cpw_status ? 'Tandai belum' : 'Tandai selesai'"
                >
                  <i :class="doc.cpw_status ? 'fa-solid fa-circle-check check-done' : 'fa-regular fa-circle check-pending'"></i>
                </button>
              </td>
              <td class="text-center">
                <button
                  class="check-btn"
                  @click="toggleCpp(doc)"
                  :title="doc.cpp_status ? 'Tandai belum' : 'Tandai selesai'"
                >
                  <i :class="doc.cpp_status ? 'fa-solid fa-circle-check check-done' : 'fa-regular fa-circle check-pending'"></i>
                </button>
              </td>
              <td class="text-muted">{{ doc.catatan || '–' }}</td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="btn btn--ghost btn--icon" title="Copy" @click="openCopy(doc)" :id="'copy-kua-'+doc.id">
                    <i class="fa-solid fa-copy action-icon action-icon--copy"></i>
                  </button>
                  <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(doc)" :id="'edit-kua-'+doc.id">
                    <i class="fa-solid fa-pen-to-square action-icon action-icon--edit"></i>
                  </button>
                  <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(doc)" :id="'del-kua-'+doc.id">
                    <i class="fa-solid fa-trash action-icon action-icon--delete"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!filteredDocuments.length">
              <td colspan="7">
                <div class="empty-state">
                  <i class="fa-solid fa-file-contract empty-state__icon"></i>
                  <p class="empty-state__text">Belum ada dokumen KUA</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">{{ editItem ? 'Edit Dokumen' : (copyItem ? 'Copy Dokumen' : 'Tambah Dokumen') }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <form @submit.prevent="save" class="modal-body">
            <div>
              <label class="form-label">Nama Dokumen *</label>
              <input v-model="form.nama_dokumen" type="text" required class="form-input" placeholder="KTP, Akta Lahir, Surat N1...">
              <p v-if="errors.nama_dokumen" class="form-error">{{ errors.nama_dokumen }}</p>
            </div>
            <div style="margin-top:12px">
              <label class="form-label">Biaya (Rp)</label>
              <input v-model="form.biaya" type="number" min="0" class="form-input">
            </div>
            <div style="margin-top:12px">
              <label class="form-label">Catatan</label>
              <textarea v-model="form.catatan" rows="2" class="form-input"></textarea>
            </div>
            <div class="modal-footer" style="margin-top:16px;padding:0">
              <button type="button" class="btn btn--outline" style="flex:1;justify-content:center" @click="closeModal">Batal</button>
              <button type="submit" class="btn btn--dark" style="flex:1;justify-content:center" :disabled="saving">
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

const props = defineProps({
  documents: Array,
  totalBiaya: Number,
  totalDok: Number,
  doneCpw: Number,
  doneCpp: Number,
  allDone: Boolean,
});

const localDocuments = ref([...props.documents]);
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

const defaultForm = () => ({ nama_dokumen: '', biaya: '', catatan: '' });
const form = ref(defaultForm());

const doneCount = computed(() => props.documents.filter(d => d.cpw_status && d.cpp_status).length);

watch(() => props.documents, (documents) => {
  localDocuments.value = [...documents];
});

const filteredDocuments = computed(() => {
  let list = [...localDocuments.value];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(d => d.nama_dokumen?.toLowerCase().includes(q));
  }
  if (filterStatus.value === 'done')    list = list.filter(d => d.cpw_status && d.cpp_status);
  if (filterStatus.value === 'pending') list = list.filter(d => !d.cpw_status || !d.cpp_status);
  return list;
});

const canDragRows = computed(() => !searchQuery.value && !filterStatus.value);

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function exportToExcel() {
  if (filteredDocuments.value.length === 0) {
    showToast('Tidak ada data untuk diexport');
    return;
  }

  const headers = ['No', 'Nama Dokumen', 'Status CPW', 'Status CPP', 'Biaya', 'Catatan'];
  
  const rows = filteredDocuments.value.map((doc, index) => [
    doc.no || index + 1,
    `"${(doc.nama_dokumen || '').replace(/"/g, '""')}"`,
    `"${doc.cpw_status ? 'Selesai' : 'Belum'}"`,
    `"${doc.cpp_status ? 'Selesai' : 'Belum'}"`,
    doc.biaya || 0,
    `"${(doc.catatan || '').replace(/"/g, '""')}"`
  ]);
  
  const totalBiayaFiltered = filteredDocuments.value.reduce((sum, d) => sum + (Number(d.biaya) || 0), 0);
  rows.push([
    '"TOTAL"',
    '""',
    '""',
    '""',
    totalBiayaFiltered,
    '""'
  ]);
  
  const csvContent = [
    headers.join(','),
    ...rows.map(row => row.join(','))
  ].join('\n');
  
  const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  
  const date = new Date().toISOString().split('T')[0];
  link.setAttribute('href', url);
  link.setAttribute('download', `Dokumen_KUA_${date}.csv`);
  link.style.visibility = 'hidden';
  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  showToast('Data berhasil diexport ke Excel/CSV');
}

function resetFilters() {
  searchQuery.value  = '';
  filterStatus.value = '';
}

function openCreate() {
  editItem.value = null;
  copyItem.value = null;
  form.value = defaultForm();
  errors.value = {};
  showModal.value = true;
}
function openCopy(doc) {
  editItem.value = null;
  copyItem.value = doc;
  form.value = { nama_dokumen: doc.nama_dokumen, biaya: doc.biaya || '', catatan: doc.catatan || '' };
  errors.value = {};
  showModal.value = true;
}
function openEdit(doc) {
  editItem.value = doc;
  copyItem.value = null;
  form.value = { nama_dokumen: doc.nama_dokumen, biaya: doc.biaya || '', catatan: doc.catatan || '' };
  errors.value = {}; 
  showModal.value = true;
}
function closeModal() {
  showModal.value = false;
  editItem.value = null;
  copyItem.value = null;
}

function save() {
  saving.value = true; errors.value = {};
  const payload = { ...form.value, biaya: Number(form.value.biaya) || 0 };
  const url    = editItem.value ? route('dokumen-kua.update', editItem.value.id) : route('dokumen-kua.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, payload, {
    preserveScroll: true,
    onSuccess: () => { showToast(editItem.value ? 'Dokumen diupdate.' : (copyItem.value ? 'Copy dokumen ditambahkan.' : 'Dokumen ditambahkan.')); closeModal(); saving.value = false; },
    onError: (errs) => { errors.value = errs; saving.value = false; },
  });
}

function toggleCpw(doc) {
  router.patch(route('kua.toggle-cpw', doc.id), {}, { preserveScroll: true, onSuccess: () => showToast('Status Mempelai Wanita diupdate.') });
}
function toggleCpp(doc) {
  router.patch(route('kua.toggle-cpp', doc.id), {}, { preserveScroll: true, onSuccess: () => showToast('Status Mempelai Pria diupdate.') });
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
      startDrag(filteredDocuments.value[b_index], b_index, null);
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
        const item = filteredDocuments.value[index];
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

  const reordered = [...localDocuments.value];
  const [moved] = reordered.splice(draggedIndex.value, 1);
  reordered.splice(targetIndex, 0, moved);
  localDocuments.value = reordered.map((item, index) => ({ ...item, no: index + 1 }));

  router.patch(route('kua.reorder'), {
    ids: localDocuments.value.map((item) => item.id),
  }, {
    preserveScroll: true,
    onSuccess: () => showToast('Urutan dokumen berhasil disimpan.'),
    onError: () => {
      localDocuments.value = [...props.documents];
      showToast('Urutan dokumen gagal disimpan.');
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

function confirmDelete(doc) {
  confirmDeleteDialog(() => {
    router.delete(route('dokumen-kua.destroy', doc.id), { preserveScroll: true, onSuccess: () => showToast('Dokumen dihapus.') });
  });
}
</script>

<style scoped>
.summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 18px; }
@media (min-width: 640px) { .summary-grid { grid-template-columns: repeat(4, 1fr); } }
.summary-card { padding: 18px; }
.summary-card__label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--ink-400); }
.summary-card__value { font-size: 22px; font-weight: 800; color: var(--ink-900); margin-top: 6px; letter-spacing: -0.02em; }

.toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.toolbar__search { position: relative; flex: 1; min-width: 180px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--ink-400); font-size: 12px; pointer-events: none; }
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
.drag-cell i { color: var(--ink-300); margin-right: 6px; font-size: 16px; }
.is-drag-disabled .drag-cell i { opacity: 0.35; }

.mono-text { font-family: monospace; font-size: 11px; color: var(--ink-300); }
.item-name { font-size: 13.5px; font-weight: 500; color: var(--ink-800); }
.text-right { text-align: right; }
.text-center { text-align: center; }
.text-muted { font-size: 12.5px; color: var(--ink-400); }

.row--done td { background: var(--ink-50); }

.check-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  font-size: 20px;
  line-height: 1;
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.check-btn:hover { transform: scale(1.15); }
.check-done { color: var(--ok-text); }
.check-pending { color: var(--text-dim); }
</style>
