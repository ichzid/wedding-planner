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
        <p class="summary-card__label">CPW Siap</p>
        <p class="summary-card__value">{{ doneCpw }}<span style="font-size:16px;font-weight:500;color:var(--ink-300)">/{{ totalDok }}</span></p>
        <div class="prog-track mt-2"><div class="prog-fill" :style="{ width: (totalDok ? Math.round(doneCpw/totalDok*100) : 0) + '%' }"></div></div>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">CPP Siap</p>
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
        <option value="done">Selesai</option>
        <option value="pending">Belum Lengkap</option>
      </select>
      <button v-if="searchQuery || filterStatus" class="btn btn--outline btn--sm" @click="resetFilters">
        <i class="fa-solid fa-xmark"></i> Reset
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
              <th class="text-center">CPW</th>
              <th class="text-center">CPP</th>
              <th>Catatan</th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="doc in filteredDocuments" :key="doc.id" :class="{ 'row--done': doc.cpw_status && doc.cpp_status }">
              <td class="mono-text">{{ doc.no }}</td>
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
                  <button class="btn btn--icon btn--ghost" @click="openEdit(doc)">
                    <i class="fa-solid fa-pen fa-xs"></i>
                  </button>
                  <button class="btn btn--icon btn--danger-ghost" @click="confirmDelete(doc)">
                    <i class="fa-solid fa-trash fa-xs"></i>
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
            <h3 class="modal-title">{{ editItem ? 'Edit Dokumen' : 'Tambah Dokumen' }}</h3>
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
import { ref, computed } from 'vue';
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

const searchQuery  = ref('');
const filterStatus = ref('');
const showModal = ref(false);
const editItem  = ref(null);
const saving    = ref(false);
const errors    = ref({});

const defaultForm = () => ({ nama_dokumen: '', biaya: '', catatan: '' });
const form = ref(defaultForm());

const doneCount = computed(() => props.documents.filter(d => d.cpw_status && d.cpp_status).length);

const filteredDocuments = computed(() => {
  let list = [...props.documents];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(d => d.nama_dokumen?.toLowerCase().includes(q));
  }
  if (filterStatus.value === 'done')    list = list.filter(d => d.cpw_status && d.cpp_status);
  if (filterStatus.value === 'pending') list = list.filter(d => !d.cpw_status || !d.cpp_status);
  return list;
});

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function resetFilters() {
  searchQuery.value  = '';
  filterStatus.value = '';
}

function openCreate() {
  editItem.value = null; form.value = defaultForm(); errors.value = {}; showModal.value = true;
}
function openEdit(doc) {
  editItem.value = doc;
  form.value = { nama_dokumen: doc.nama_dokumen, biaya: doc.biaya || '', catatan: doc.catatan || '' };
  errors.value = {}; showModal.value = true;
}
function closeModal() { showModal.value = false; }

function save() {
  saving.value = true; errors.value = {};
  const payload = { ...form.value, biaya: Number(form.value.biaya) || 0 };
  const url    = editItem.value ? route('dokumen-kua.update', editItem.value.id) : route('dokumen-kua.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, payload, {
    preserveScroll: true,
    onSuccess: () => { showToast(editItem.value ? 'Dokumen diupdate.' : 'Dokumen ditambahkan.'); closeModal(); saving.value = false; },
    onError: (errs) => { errors.value = errs; saving.value = false; },
  });
}

function toggleCpw(doc) {
  router.patch(route('kua.toggle-cpw', doc.id), {}, { preserveScroll: true, onSuccess: () => showToast('Status CPW diupdate.') });
}
function toggleCpp(doc) {
  router.patch(route('kua.toggle-cpp', doc.id), {}, { preserveScroll: true, onSuccess: () => showToast('Status CPP diupdate.') });
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
