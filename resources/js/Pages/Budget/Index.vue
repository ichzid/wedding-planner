<template>
  <AppLayout>
    <Head title="Budget Pernikahan" />

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="page-title">Wedding Budget</h1>
        <p class="page-sub">{{ formatRp(totalEstimasi) }} total estimasi · {{ progressPct }}% terbayar</p>
      </div>
      <button class="btn btn--primary" @click="openCreate">
        <i class="fa-solid fa-plus fa-xs"></i> Tambah
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="card summary-card">
        <p class="summary-card__label">Total Estimasi</p>
        <p class="summary-card__value">{{ formatRp(totalEstimasi) }}</p>
        <p class="summary-card__sub">{{ budgets.length }} item budget</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Sudah Dibayar</p>
        <p class="summary-card__value">{{ formatRp(totalAktual) }}</p>
        <div class="prog-track mt-2"><div class="prog-fill" :style="{ width: progressPct + '%' }"></div></div>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Sisa Tagihan</p>
        <p class="summary-card__value">{{ formatRp(totalSisa) }}</p>
        <p class="summary-card__sub">DP: {{ formatRp(totalDp) }} · Lunas: {{ formatRp(totalPelunasan) }}</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="searchQuery" type="text" placeholder="Cari item atau vendor..." class="form-input search-input" />
      </div>
      <select v-model="filterKategori" class="form-input toolbar__select">
        <option value="">Semua Kategori</option>
        <option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option>
      </select>
      <select v-model="filterStatus" class="form-input toolbar__select">
        <option value="">Semua Status</option>
        <option v-for="(label, key) in statusOptions" :key="key" :value="key">{{ label }}</option>
      </select>
      <button v-if="filterKategori || filterStatus || searchQuery" class="btn btn--outline btn--sm" @click="resetFilters">
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
              <th>
                <button class="sort-btn" @click="toggleSort('kategori')">
                  Kategori <i :class="sortIcon('kategori')"></i>
                </button>
              </th>
              <th>Item / Vendor</th>
              <th class="text-right">
                <button class="sort-btn sort-btn--right" @click="toggleSort('estimasi_budget')">
                  Est. Budget <i :class="sortIcon('estimasi_budget')"></i>
                </button>
              </th>
              <th class="text-right">DP</th>
              <th class="text-right">Pelunasan</th>
              <th class="text-right">Sisa</th>
              <th>
                <button class="sort-btn" @click="toggleSort('status')">
                  Status <i :class="sortIcon('status')"></i>
                </button>
              </th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="b in filteredBudgets" :key="b.id">
              <td class="mono-text">{{ b.no }}</td>
              <td><span class="chip chip--soft">{{ b.kategori }}</span></td>
              <td>
                <p class="item-name">{{ b.item }}</p>
                <p v-if="b.vendor" class="item-sub">{{ b.vendor }}</p>
              </td>
              <td class="text-right fw-600">{{ formatRp(b.estimasi_budget) }}</td>
              <td class="text-right" :class="b.dp > 0 ? 'text-dark' : 'text-dim'">
                {{ b.dp > 0 ? formatRp(b.dp) : '–' }}
              </td>
              <td class="text-right" :class="b.pelunasan > 0 ? 'text-dark' : 'text-dim'">
                {{ b.pelunasan > 0 ? formatRp(b.pelunasan) : '–' }}
              </td>
              <td class="text-right fw-600" :class="(b.estimasi_budget - b.dp - b.pelunasan) > 0 ? 'text-danger' : 'text-ok'">
                {{ formatRp(b.estimasi_budget - b.dp - b.pelunasan) }}
              </td>
              <td>
                <span class="chip" :class="statusChip(b.status)">{{ statusOptions[b.status] || b.status }}</span>
              </td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(b)" :id="'edit-budget-'+b.id">
                    <i class="fa-solid fa-pen-to-square" style="font-size:13px;"></i>
                  </button>
                  <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(b)" :id="'del-budget-'+b.id">
                    <i class="fa-solid fa-trash" style="font-size:13px;"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!filteredBudgets.length">
              <td colspan="9">
                <div class="empty-state">
                  <i class="fa-solid fa-wallet empty-state__icon"></i>
                  <p class="empty-state__text">Belum ada data budget. Mulai catat pengeluaran pernikahanmu!</p>
                  <button class="btn btn--primary" @click="openCreate">
                    <i class="fa-solid fa-plus fa-xs"></i> Tambah Budget Pertama
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredBudgets.length">
            <tr class="tfoot-row">
              <td colspan="3" class="tfoot-label">Total</td>
              <td class="text-right fw-700">{{ formatRp(filteredTotalEstimasi) }}</td>
              <td class="text-right fw-600">{{ formatRp(filteredTotalDp) }}</td>
              <td class="text-right fw-600">{{ formatRp(filteredTotalPelunasan) }}</td>
              <td class="text-right fw-700 text-danger">{{ formatRp(filteredTotalSisa) }}</td>
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
            <h3 class="modal-title">{{ editItem ? 'Edit Budget' : 'Tambah Budget' }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <form @submit.prevent="save" class="modal-body">
            <div class="form-row-2">
              <div>
                <label class="form-label">Kategori *</label>
                <select v-model="form.kategori" required class="form-input">
                  <option value="">Pilih</option>
                  <option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option>
                </select>
                <p v-if="errors.kategori" class="form-error">{{ errors.kategori }}</p>
              </div>
              <div>
                <label class="form-label">Status *</label>
                <select v-model="form.status" required class="form-input">
                  <option v-for="(label, key) in statusOptions" :key="key" :value="key">{{ label }}</option>
                </select>
              </div>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Nama Item *</label>
              <input v-model="form.item" type="text" required class="form-input" placeholder="Sewa Gedung Resepsi">
              <p v-if="errors.item" class="form-error">{{ errors.item }}</p>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Vendor</label>
              <input v-model="form.vendor" type="text" class="form-input" placeholder="Nama vendor...">
            </div>
            <div class="form-row-3" style="margin-top:var(--space-md)">
              <div>
                <label class="form-label">Est. Budget (Rp) *</label>
                <input v-model="form.estimasi_budget" type="number" required min="0" class="form-input">
              </div>
              <div>
                <label class="form-label">DP (Rp)</label>
                <input v-model="form.dp" type="number" min="0" class="form-input">
              </div>
              <div>
                <label class="form-label">Pelunasan (Rp)</label>
                <input v-model="form.pelunasan" type="number" min="0" class="form-input">
              </div>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Catatan</label>
              <textarea v-model="form.catatan" rows="2" class="form-input"></textarea>
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
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import { showToast, confirmDeleteDialog } from '@/utils.js';

const props = defineProps({
  budgets: Array,
  kategoriOptions: Array,
  statusOptions: Object,
  totalEstimasi: Number,
  totalDp: Number,
  totalPelunasan: Number,
  totalAktual: Number,
  totalSisa: Number,
});

const searchQuery  = ref('');
const filterKategori = ref('');
const filterStatus   = ref('');
const sortBy    = ref('');
const sortDir   = ref('asc');
const showModal = ref(false);
const editItem  = ref(null);
const saving    = ref(false);
const errors    = ref({});

const defaultForm = () => ({
  kategori: '',
  status: 'belum',
  item: '',
  vendor: '',
  estimasi_budget: '',
  dp: '',
  pelunasan: '',
  catatan: '',
});
const form = ref(defaultForm());

const filteredBudgets = computed(() => {
  let list = [...props.budgets];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(b =>
      b.item?.toLowerCase().includes(q) ||
      b.vendor?.toLowerCase().includes(q)
    );
  }
  if (filterKategori.value) list = list.filter(b => b.kategori === filterKategori.value);
  if (filterStatus.value)   list = list.filter(b => b.status === filterStatus.value);
  if (sortBy.value) {
    list.sort((a, b) => {
      let av = a[sortBy.value], bv = b[sortBy.value];
      if (typeof av === 'string') av = av.toLowerCase();
      if (typeof bv === 'string') bv = bv.toLowerCase();
      if (av < bv) return sortDir.value === 'asc' ? -1 : 1;
      if (av > bv) return sortDir.value === 'asc' ? 1 : -1;
      return 0;
    });
  }
  return list;
});

const filteredTotalEstimasi  = computed(() => filteredBudgets.value.reduce((s, b) => s + (b.estimasi_budget || 0), 0));
const filteredTotalDp        = computed(() => filteredBudgets.value.reduce((s, b) => s + (b.dp || 0), 0));
const filteredTotalPelunasan = computed(() => filteredBudgets.value.reduce((s, b) => s + (b.pelunasan || 0), 0));
const filteredTotalSisa      = computed(() => filteredTotalEstimasi.value - filteredTotalDp.value - filteredTotalPelunasan.value);

const progressPct = computed(() => {
  if (!props.totalEstimasi) return 0;
  return Math.min(100, Math.round(props.totalAktual / props.totalEstimasi * 100));
});

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function statusChip(s) {
  const map = { belum: 'chip--danger', dp_terbayar: 'chip--warn', lunas: 'chip--ok' };
  return map[s] || 'chip--soft';
}

function resetFilters() {
  searchQuery.value   = '';
  filterKategori.value = '';
  filterStatus.value  = '';
}

function toggleSort(field) {
  if (sortBy.value === field) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value  = field;
    sortDir.value = 'asc';
  }
}

function sortIcon(field) {
  if (sortBy.value !== field) return 'fa-solid fa-sort text-dim fa-xs';
  return sortDir.value === 'asc'
    ? 'fa-solid fa-sort-up fa-xs'
    : 'fa-solid fa-sort-down fa-xs';
}

function openCreate() {
  editItem.value = null;
  form.value     = defaultForm();
  errors.value   = {};
  showModal.value = true;
}

function openEdit(b) {
  editItem.value = b;
  form.value = {
    kategori:        b.kategori,
    status:          b.status,
    item:            b.item,
    vendor:          b.vendor || '',
    estimasi_budget: b.estimasi_budget,
    dp:              b.dp,
    pelunasan:       b.pelunasan,
    catatan:         b.catatan || '',
  };
  errors.value   = {};
  showModal.value = true;
}

function closeModal() { showModal.value = false; }

async function save() {
  saving.value = true;
  errors.value = {};
  const payload = {
    ...form.value,
    estimasi_budget: Number(form.value.estimasi_budget) || 0,
    dp:              Number(form.value.dp) || 0,
    pelunasan:       Number(form.value.pelunasan) || 0,
  };
  const url = editItem.value
    ? route('budget.update', editItem.value.id)
    : route('budget.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, payload, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(editItem.value ? 'Budget berhasil diupdate.' : 'Budget berhasil ditambahkan.');
      closeModal();
      saving.value = false;
    },
    onError: (errs) => {
      errors.value = errs;
      saving.value = false;
    },
  });
}

function confirmDelete(b) {
  confirmDeleteDialog(() => {
    router.delete(route('budget.destroy', b.id), {
      preserveScroll: true,
      onSuccess: () => showToast('Budget berhasil dihapus.'),
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

.sort-btn {
  background: none; border: none; cursor: pointer;
  font-size: inherit; font-weight: inherit; color: inherit;
  display: inline-flex; align-items: center; gap: 5px;
  padding: 0; letter-spacing: inherit; text-transform: inherit;
}
.sort-btn--right { margin-left: auto; display: flex; }

.mono-text { font-family: monospace; font-size: 11px; color: var(--text-dim); }
.item-name { font-size: 13.5px; font-weight: 500; color: var(--text); }
.item-sub  { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

.text-right { text-align: right; }
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.text-dark  { color: var(--text); }
.text-dim   { color: var(--text-dim); }
.text-danger { color: var(--danger-text); }
.text-ok     { color: var(--ok-text); }

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

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-md); }
@media (max-width: 480px) {
  .form-row-2 { grid-template-columns: 1fr; }
  .form-row-3 { grid-template-columns: 1fr; }
}
</style>