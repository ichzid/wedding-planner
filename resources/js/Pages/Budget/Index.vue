<template>
  <AppLayout>

    <div class="budget-page">
    <header class="hero-card">
      <div>
        <span class="eyebrow">Rencana keuangan pernikahan</span>
        <h1>Anggaran Pernikahan</h1>
        <p>Catat estimasi, pembayaran, dan sisa tagihan dalam satu tempat.</p>
      </div>
      <button class="primary-action" @click="openCreate"><Plus aria-hidden="true" />Tambah Anggaran</button>
    </header>

    <section class="summary-grid" aria-label="Ringkasan anggaran">
      <article class="summary-card"><span class="summary-icon"><WalletCards /></span><div><p class="summary-card__label">Total Estimasi</p><p class="summary-card__value">{{ formatRp(totalEstimasi) }}</p><p class="summary-card__sub">{{ budgets.length }} item anggaran</p></div></article>
      <article class="summary-card"><span class="summary-icon"><Banknote /></span><div><p class="summary-card__label">Sudah Dibayar</p><p class="summary-card__value">{{ formatRp(totalAktual) }}</p><div class="progress-meta"><span>{{ progressPct }}% dari estimasi</span></div><div class="progress-track"><span :style="{ width: progressPct + '%' }"></span></div></div></article>
      <article class="summary-card"><span class="summary-icon summary-icon--warning"><ReceiptText /></span><div><p class="summary-card__label">Sisa Tagihan</p><p class="summary-card__value">{{ formatRp(totalSisa) }}</p><p class="summary-card__sub">DP {{ formatRp(totalDp) }} · Pelunasan {{ formatRp(totalPelunasan) }}</p></div></article>
    </section>

    <section class="budget-panel">
    <section class="toolbar" aria-label="Pencarian dan filter">
      <div class="toolbar__top">
        <div class="toolbar__search"><Search aria-hidden="true" /><input v-model="searchQuery" type="text" placeholder="Cari item atau vendor..." class="toolbar-control search-input" /></div>
        <button type="button" class="mobile-filter-toggle" :class="{ 'mobile-filter-toggle--active': mobileFiltersOpen || filterKategori || filterStatus }" :aria-expanded="mobileFiltersOpen" aria-controls="budget-mobile-filters" aria-label="Tampilkan filter anggaran" @click="mobileFiltersOpen = !mobileFiltersOpen">
          <ListFilter aria-hidden="true" />
          <span v-if="filterKategori || filterStatus" class="mobile-filter-toggle__dot" aria-hidden="true"></span>
        </button>
      </div>
      <div id="budget-mobile-filters" class="toolbar__filters" :class="{ 'toolbar__filters--open': mobileFiltersOpen }">
        <label class="toolbar-select-wrap"><select v-model="filterKategori" class="toolbar-control toolbar__select" aria-label="Filter kategori"><option value="">Semua Kategori</option><option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option></select><ChevronDown aria-hidden="true" /></label>
        <label class="toolbar-select-wrap"><select v-model="filterStatus" class="toolbar-control toolbar__select" aria-label="Filter status"><option value="">Semua Status</option><option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option></select><ChevronDown aria-hidden="true" /></label>
        <button v-if="filterKategori || filterStatus || searchQuery || sortBy" class="secondary-action" @click="resetFilters"><X aria-hidden="true" />Reset</button>
        <button class="secondary-action toolbar__export" @click="exportToExcel"><FileSpreadsheet aria-hidden="true" />Ekspor Excel</button>
      </div>
    </section>

    <!-- Table desktop -->
    <div class="budget-table">
      <div style="overflow-x:auto">
        <table class="tbl">
          <thead>
            <tr>
              <th style="width:36px">#</th>
              <th>
                <button class="sort-btn" @click="toggleSort('kategori')">
                  Kategori <component :is="sortIcon('kategori')" aria-hidden="true" />
                </button>
              </th>
              <th>Item / Vendor</th>
              <th>Sumber Dana</th>
              <th class="text-right">
                <button class="sort-btn sort-btn--right" @click="toggleSort('estimasi_budget')">
                  Estimasi <component :is="sortIcon('estimasi_budget')" aria-hidden="true" />
                </button>
              </th>
              <th class="text-right">DP</th>
              <th class="text-right">Pelunasan</th>
              <th class="text-right">Sisa</th>
              <th>
                <button class="sort-btn" @click="toggleSort('status')">
                  Status <component :is="sortIcon('status')" aria-hidden="true" />
                </button>
              </th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(b, index) in filteredBudgets"
              :key="b.id"
              class="draggable-row"
              :class="{ 'budget-row--paid': b.status === 'lunas', 'budget-row--partial': b.status === 'dp_terbayar', 'budget-row--unpaid': b.status === 'belum', 'is-dragging': draggedId === b.id, 'is-drop-target': dragOverId === b.id, 'is-drop-before': dropPlacement(b.id) === 'before', 'is-drop-after': dropPlacement(b.id) === 'after', 'is-drag-disabled': !canDragRows }"
              :draggable="canDragRows"
              @dragstart="startDrag(b, index, $event)"
              @dragover.prevent="setDragOver(b, index)"
              @drop="dropRow(index)"
              @dragend="endDrag"
              @touchstart="handleTouchStart"
              @touchmove="handleTouchMove"
              @touchend="handleTouchEnd"
            >
              <td class="drag-cell"><GripVertical aria-hidden="true" /> {{ b.no }}</td>
              <td><span class="category-label"><span class="category-dot" :class="categoryDotClass(b.kategori)" aria-hidden="true"></span>{{ b.kategori }}</span></td>
              <td>
                <p class="item-name">{{ b.item }}</p>
                <p v-if="b.vendor" class="item-sub">{{ b.vendor }}</p>
              </td>
              <td>
                <span class="chip source-chip" :class="sumberDanaChipClass(b.sumber_dana)">
                  <Mars v-if="b.sumber_dana === 'cpp' || b.sumber_dana === 'bersama'" aria-hidden="true" />
                  <Venus v-if="b.sumber_dana === 'cpw' || b.sumber_dana === 'bersama'" aria-hidden="true" />
                  {{ sumberDanaOptions[b.sumber_dana] || 'CPP' }}
                </span>
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
                <span class="chip" :class="statusChip(b.status)">{{ statusLabel(b.status) }}</span>
              </td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="icon-action" data-tooltip="Salin" :aria-label="`Salin anggaran ${b.item}`" @click="openCopy(b)" :id="'copy-budget-'+b.id"><Copy aria-hidden="true" /></button>
                  <button class="icon-action" data-tooltip="Edit" :aria-label="`Edit anggaran ${b.item}`" @click="openEdit(b)" :id="'edit-budget-'+b.id"><Pencil aria-hidden="true" /></button>
                  <button class="icon-action icon-action--danger" data-tooltip="Hapus" :aria-label="`Hapus anggaran ${b.item}`" @click="confirmDelete(b)" :id="'del-budget-'+b.id"><Trash2 aria-hidden="true" /></button>
                </div>
              </td>
            </tr>
            <tr v-if="!filteredBudgets.length">
              <td colspan="10">
                <div class="empty-state">
                  <WalletCards class="empty-state__icon" aria-hidden="true" />
                  <p class="empty-state__text">Belum ada anggaran. Mulai catat kebutuhan dan perkiraan biayanya.</p>
                  <button class="btn btn--primary" @click="openCreate">
                    <Plus aria-hidden="true" /> Tambah anggaran pertama
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredBudgets.length">
            <tr class="tfoot-row">
              <td colspan="4" class="tfoot-label">Total</td>
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

    <section class="mobile-records" aria-label="Daftar anggaran">
      <article v-for="b in filteredBudgets" :key="b.id" class="record-card" :class="`record-card--${b.status}`">
        <div class="record-card__header"><div><span class="category-label"><span class="category-dot" :class="categoryDotClass(b.kategori)" aria-hidden="true"></span>{{ b.kategori }}</span><h2>{{ b.item }}</h2><p>{{ b.vendor || 'Vendor belum ditentukan' }}</p></div><span class="chip" :class="statusChip(b.status)">{{ statusLabel(b.status) }}</span></div>
        <dl class="record-card__amounts"><div><dt>Estimasi</dt><dd>{{ formatRp(b.estimasi_budget) }}</dd></div><div><dt>Terbayar</dt><dd>{{ formatRp((b.dp || 0) + (b.pelunasan || 0)) }}</dd></div><div><dt>Sisa</dt><dd :class="(b.estimasi_budget - b.dp - b.pelunasan) > 0 ? 'text-danger' : 'text-ok'">{{ formatRp(b.estimasi_budget - b.dp - b.pelunasan) }}</dd></div></dl>
        <div class="record-card__footer"><span class="source-label"><Mars v-if="b.sumber_dana === 'cpp' || b.sumber_dana === 'bersama'" aria-hidden="true" /><Venus v-if="b.sumber_dana === 'cpw' || b.sumber_dana === 'bersama'" aria-hidden="true" />{{ sumberDanaOptions[b.sumber_dana] || 'CPP' }}</span><div><button class="icon-action" aria-label="Salin anggaran" @click="openCopy(b)"><Copy /></button><button class="icon-action" aria-label="Edit anggaran" @click="openEdit(b)"><Pencil /></button><button class="icon-action icon-action--danger" aria-label="Hapus anggaran" @click="confirmDelete(b)"><Trash2 /></button></div></div>
      </article>
      <div v-if="!filteredBudgets.length" class="empty-card"><WalletCards /><h2>Belum ada anggaran</h2><p>Tidak ada data yang sesuai. Tambahkan anggaran atau atur ulang filter.</p><button class="primary-action" @click="openCreate"><Plus />Tambah Anggaran</button></div>
    </section>
    </section>
    </div>

    <!-- Drawer form -->
    <Teleport to="body">
      <div v-if="showModal" class="drawer-backdrop" @click.self="closeModal">
        <aside class="budget-drawer" role="dialog" aria-modal="true" aria-labelledby="budget-drawer-title">
          <div class="drawer-handle" aria-hidden="true"></div>
          <div class="drawer-header">
            <div><span class="eyebrow">Anggaran pernikahan</span><h3 id="budget-drawer-title" class="drawer-title">{{ editItem ? 'Edit Anggaran' : (copyItem ? 'Salin Anggaran' : 'Tambah Anggaran') }}</h3></div>
            <button class="icon-action" aria-label="Tutup" title="Tutup" @click="closeModal"><X aria-hidden="true" /></button>
          </div>
          <form @submit.prevent="save" class="drawer-body">

            <div class="form-row-2">
              <div class="form-field">
                <label class="form-label" for="budget-category">Kategori <span>*</span></label>
                <div class="field-control field-control--select">
                  <Tags aria-hidden="true" />
                  <select id="budget-category" v-model="form.kategori" required class="form-input select-input">
                    <option value="">Pilih kategori</option>
                    <option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option>
                  </select>
                  <span class="select-chevron" aria-hidden="true"></span>
                </div>
              </div>
              <div class="form-field">
                <label class="form-label" for="budget-source">Dana Dari <span>*</span></label>
                <div class="field-control field-control--select">
                  <HandCoins aria-hidden="true" />
                  <select id="budget-source" v-model="form.sumber_dana" required class="form-input select-input">
                    <option value="" disabled>Pilih sumber dana</option>
                    <option v-for="(label, key) in sumberDanaFormOptions" :key="key" :value="key">{{ label }}</option>
                  </select>
                  <span class="select-chevron" aria-hidden="true"></span>
                </div>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label" for="budget-status">Status <span>*</span></label>
              <div class="field-control field-control--select">
                <CircleGauge aria-hidden="true" />
                <select id="budget-status" v-model="form.status" required class="form-input select-input">
                  <option value="" disabled>Pilih status</option>
                  <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                </select>
                <span class="select-chevron" aria-hidden="true"></span>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label" for="budget-item">Nama Item <span>*</span></label>
              <div class="field-control">
                <TextCursorInput aria-hidden="true" />
                <input id="budget-item" v-model="form.item" type="text" required class="form-input" placeholder="Contoh: Sewa gedung resepsi">
              </div>
              <p class="form-helper">Gunakan nama singkat agar anggaran mudah ditemukan.</p>
            </div>
            <div class="form-field">
              <label class="form-label" for="budget-vendor">Vendor <small>Opsional</small></label>
              <div class="field-control">
                <Store aria-hidden="true" />
                <input id="budget-vendor" v-model="form.vendor" type="text" class="form-input" placeholder="Nama vendor...">
              </div>
            </div>
            <div class="form-row-3">
              <div class="form-field">
                <label class="form-label" for="budget-estimate">Estimasi (Rp) <span>*</span></label>
                <div class="field-control">
                  <BadgeDollarSign aria-hidden="true" />
                  <input id="budget-estimate" v-model="form.estimasi_budget" type="number" required min="0" class="form-input" placeholder="0">
                </div>
              </div>
              <div class="form-field">
                <label class="form-label" for="budget-dp">DP (Rp)</label>
                <div class="field-control">
                  <Coins aria-hidden="true" />
                  <input id="budget-dp" v-model="form.dp" type="number" min="0" class="form-input" placeholder="0">
                </div>
              </div>
              <div class="form-field">
                <label class="form-label" for="budget-payment">Pelunasan (Rp)</label>
                <div class="field-control">
                  <CircleDollarSign aria-hidden="true" />
                  <input id="budget-payment" v-model="form.pelunasan" type="number" min="0" class="form-input" placeholder="0">
                </div>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label" for="budget-note">Catatan <small>Opsional</small></label>
              <div class="field-control field-control--textarea">
                <NotepadText aria-hidden="true" />
                <textarea id="budget-note" v-model="form.catatan" rows="3" class="form-input" placeholder="Tambahkan detail pembayaran atau catatan penting..."></textarea>
              </div>
            </div>
            <div class="drawer-footer">
              <button type="button" class="secondary-action" @click="closeModal">Batal</button>
              <button type="submit" class="primary-action" :disabled="saving"><LoaderCircle v-if="saving" class="spinner" aria-hidden="true" />{{ saving ? 'Menyimpan...' : 'Simpan Anggaran' }}</button>
            </div>
          </form>
        </aside>
      </div>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowDown, ArrowUp, BadgeDollarSign, Banknote, ChevronDown, ChevronsUpDown, CircleDollarSign, CircleGauge, Coins, Copy, FileSpreadsheet, GripVertical, HandCoins, ListFilter, LoaderCircle, Mars, NotepadText, Pencil, Plus, ReceiptText, Search, Store, Tags, TextCursorInput, Trash2, Venus, WalletCards, X } from '@lucide/vue';
import { showToast, confirmDeleteDialog } from '@/utils.js';
import * as XLSX from 'xlsx';

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

const localBudgets = ref([...props.budgets]);
const searchQuery  = ref('');
const mobileFiltersOpen = ref(false);
const filterKategori = ref('');
const filterStatus   = ref('');
const sortBy    = ref('');
const sortDir   = ref('asc');
const showModal = ref(false);
const editItem  = ref(null);
const copyItem  = ref(null);
const saving    = ref(false);
const draggedIndex = ref(null);
const draggedId = ref(null);
const dragOverIndex = ref(null);
const dragOverId = ref(null);

const sumberDanaOptions = {
  cpp: 'CPP',
  cpw: 'CPW',
  bersama: 'CPP & CPW',
};

const sumberDanaFormOptions = {
  cpp: 'Calon Pengantin Pria',
  cpw: 'Calon Pengantin Wanita',
  bersama: 'Kedua Calon Pengantin',
};

const statusLabels = {
  belum: 'Pending',
  dp_terbayar: 'DP Paid',
  lunas: 'Paid',
};

function statusLabel(status) {
  return statusLabels[status] || status;
}

function categoryDotClass(category) {
  const index = props.kategoriOptions.indexOf(category);
  return `category-dot--${Math.max(index, 0) % 6}`;
}

const defaultForm = () => ({
  kategori: '',
  sumber_dana: '',
  status: '',
  item: '',
  vendor: '',
  estimasi_budget: '',
  dp: '',
  pelunasan: '',
  catatan: '',
});
const form = ref(defaultForm());

watch(() => props.budgets, (budgets) => {
  localBudgets.value = [...budgets];
});

watch(showModal, (isOpen) => { document.body.style.overflow = isOpen ? 'hidden' : ''; });
function handleDrawerKeydown(event) { if (event.key === 'Escape' && showModal.value) closeModal(); }
onMounted(() => window.addEventListener('keydown', handleDrawerKeydown));
onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleDrawerKeydown);
  document.body.style.overflow = '';
});

const filteredBudgets = computed(() => {
  let list = [...localBudgets.value];
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

const canDragRows = computed(() => !searchQuery.value && !filterKategori.value && !filterStatus.value && !sortBy.value);

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

function exportToExcel() {
  if (filteredBudgets.value.length === 0) {
    showToast('Tidak ada data untuk diexport');
    return;
  }

  const dateNow = new Date();
  const dateStr = dateNow.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

  const headers = ['No', 'Kategori', 'Item / Vendor', 'Sumber Dana', 'Est. Budget', 'DP', 'Pelunasan', 'Sisa', 'Status', 'Catatan'];
  
  const dataRows = filteredBudgets.value.map((b, index) => [
    b.no || index + 1,
    b.kategori || '',
    (b.item || '') + (b.vendor ? ' - ' + b.vendor : ''),
    sumberDanaOptions[b.sumber_dana] || 'CPP',
    b.estimasi_budget || 0,
    b.dp || 0,
    b.pelunasan || 0,
    (b.estimasi_budget || 0) - (b.dp || 0) - (b.pelunasan || 0),
    statusLabel(b.status),
    b.catatan || ''
  ]);
  
  // Add total row
  dataRows.push([
    '',
    'TOTAL',
    '',
    '',
    filteredTotalEstimasi.value,
    filteredTotalDp.value,
    filteredTotalPelunasan.value,
    filteredTotalSisa.value,
    '',
    ''
  ]);
  
  const finalData = [
    ['DAFTAR WEDDING BUDGET'],
    [`Dicetak pada: ${dateStr}`],
    [], // empty row
    headers,
    ...dataRows
  ];

  const ws = XLSX.utils.aoa_to_sheet(finalData);
  
  // Merge cells for title
  ws['!merges'] = [
    { s: { r: 0, c: 0 }, e: { r: 0, c: 9 } }, // Merge A1:J1
    { s: { r: 1, c: 0 }, e: { r: 1, c: 9 } }  // Merge A2:J2
  ];

  // Set column widths
  ws['!cols'] = [
    { wch: 5 },  // No
    { wch: 20 }, // Kategori
    { wch: 30 }, // Item/Vendor
    { wch: 20 }, // Sumber Dana
    { wch: 15 }, // Est Budget
    { wch: 15 }, // DP
    { wch: 15 }, // Pelunasan
    { wch: 15 }, // Sisa
    { wch: 15 }, // Status
    { wch: 30 }  // Catatan
  ];

  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Wedding Budget");
  
  XLSX.writeFile(wb, `Wedding_Budget_${dateNow.toISOString().split('T')[0]}.xlsx`);
  showToast('Data berhasil diexport ke Excel');
}

function statusChip(s) {
  const map = { belum: 'chip--danger', dp_terbayar: 'chip--warn', lunas: 'chip--ok' };
  return map[s] || 'chip--soft';
}

function sumberDanaChipClass(sumberDana) {
  if (sumberDana === 'cpw') return 'chip--cpw';
  if (sumberDana === 'bersama') return 'chip--bersama';
  return 'chip--cpp';
}

function resetFilters() {
  searchQuery.value   = '';
  filterKategori.value = '';
  filterStatus.value  = '';
  sortBy.value = '';
  sortDir.value = 'asc';
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
  if (sortBy.value !== field) return ChevronsUpDown;
  return sortDir.value === 'asc' ? ArrowUp : ArrowDown;
}

function budgetToForm(b) {
  return {
    kategori:        b.kategori,
    sumber_dana:     b.sumber_dana || 'cpp',
    status:          b.status,
    item:            b.item,
    vendor:          b.vendor || '',
    estimasi_budget: b.estimasi_budget,
    dp:              b.dp,
    pelunasan:       b.pelunasan,
    catatan:         b.catatan || '',
  };
}

function openCreate() {
  editItem.value = null;
  copyItem.value = null;
  form.value     = defaultForm();
  showModal.value = true;
}

function openCopy(b) {
  editItem.value = null;
  copyItem.value = b;
  form.value     = {
    kategori:        b.kategori,
    sumber_dana:     b.sumber_dana || 'cpp',
    status:          b.status,
    item:            b.item,
    vendor:          b.vendor || '',
    estimasi_budget: b.estimasi_budget,
    dp:              b.dp,
    pelunasan:       b.pelunasan,
    catatan:         b.catatan || '',
  };
  showModal.value = true;
}

function openEdit(b) {
  editItem.value = b;
  copyItem.value = null;
  form.value = {
    kategori:        b.kategori,
    sumber_dana:     b.sumber_dana || 'cpp',
    status:          b.status,
    item:            b.item,
    vendor:          b.vendor || '',
    estimasi_budget: b.estimasi_budget,
    dp:              b.dp,
    pelunasan:       b.pelunasan,
    catatan:         b.catatan || '',
  };
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editItem.value = null;
  copyItem.value = null;
}

async function save() {
  saving.value = true;
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
      showToast(editItem.value ? 'Anggaran berhasil diperbarui.' : (copyItem.value ? 'Salinan anggaran berhasil ditambahkan.' : 'Anggaran berhasil ditambahkan.'));
      closeModal();
      saving.value = false;
    },
    onError: () => {
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
      startDrag(filteredBudgets.value[b_index], b_index, null);
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
        const item = filteredBudgets.value[index];
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

  const reordered = [...localBudgets.value];
  const [moved] = reordered.splice(draggedIndex.value, 1);
  reordered.splice(targetIndex, 0, moved);
  localBudgets.value = reordered.map((item, index) => ({ ...item, no: index + 1 }));

  router.patch(route('budget.reorder'), {
    ids: localBudgets.value.map((item) => item.id),
  }, {
    preserveScroll: true,
    onSuccess: () => showToast('Urutan budget berhasil disimpan.'),
    onError: () => {
      localBudgets.value = [...props.budgets];
      showToast('Urutan budget gagal disimpan.');
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

function confirmDelete(b) {
  confirmDeleteDialog(() => {
    router.delete(route('budget.destroy', b.id), {
      preserveScroll: true,
      onSuccess: () => showToast('Budget berhasil dihapus.'),
    });
  }, {
    title: 'Hapus anggaran ini?',
    description: `Anggaran “${b.item}” akan dihapus permanen beserta rincian pembayarannya.`,
  });
}
</script>

<style scoped>
.summary-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: var(--space-md);
  margin-bottom: 0;
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
.tbl thead tr { height:42px; }
.tbl thead th { height:42px; padding-block:0; vertical-align:middle; }
.grouped-th {
  background: var(--rose-pale);
  border-left: 1px solid var(--border);
  border-right: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  color: var(--text) !important;
  font-weight: 700 !important;
  text-align: center !important;
}
.sub-th {
  background: var(--rose-pale);
  padding-top: 8px !important;
  padding-bottom: 8px !important;
  border-bottom: 1px solid var(--border);
}
.sub-th:first-child { border-left: 1px solid var(--border); }
.sub-th:last-child { border-right: 1px solid var(--border); }

.draggable-row { cursor: grab; position: relative; transition: background 0.18s ease, opacity 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease; }
.draggable-row.is-dragging { opacity: 0.45; transform: scale(0.995); }
.draggable-row.is-drop-target { background: var(--rose-pale); box-shadow: inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-before { box-shadow: inset 0 3px 0 var(--rose), inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-after { box-shadow: inset 0 -3px 0 var(--rose), inset 0 0 0 1px rgba(199, 121, 141, 0.18); }
.draggable-row.is-drop-before td:first-child::before,
.draggable-row.is-drop-after td:first-child::after {
  content: '';
  position: absolute;
  left: 10px;
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: var(--rose);
  box-shadow: 0 0 0 3px var(--rose-pale);
}
.draggable-row.is-drop-before td:first-child::before { top: -4px; }
.draggable-row.is-drop-after td:first-child::after { bottom: -4px; }
.draggable-row.is-drag-disabled { cursor: default; }
.drag-cell { white-space: nowrap; touch-action: none; padding: 12px; cursor: grab; }
.drag-cell i { color: var(--text-dim); font-size: 16px; }
.is-drag-disabled .drag-cell i { opacity: 0.35; }

.mono-text { font-family: monospace; font-size: 11px; color: var(--text-dim); }
.item-name { font-size: 13.5px; font-weight: 500; color: var(--text); }
.item-sub  { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
.category-label { display:inline-flex; align-items:center; gap:7px; color:var(--text-muted); font-size:12px; font-weight:600; }
.category-dot { width:8px; height:8px; flex:none; border-radius:999px; background:#86b68a; box-shadow:0 0 0 2px rgba(134,182,138,.12); }
.category-dot--1 { background:#d8ad6d; box-shadow:0 0 0 2px rgba(216,173,109,.12); }
.category-dot--2 { background:#d78f8f; box-shadow:0 0 0 2px rgba(215,143,143,.12); }
.category-dot--3 { background:#86a9cf; box-shadow:0 0 0 2px rgba(134,169,207,.12); }
.category-dot--4 { background:#ae96c9; box-shadow:0 0 0 2px rgba(174,150,201,.12); }
.category-dot--5 { background:#d89caf; box-shadow:0 0 0 2px rgba(216,156,175,.12); }

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

.chip--cpw { background: #fdf0f8; color: #c4719e; border: 1px solid #f0c8e4; }
.chip--cpp { background: #eef4ff; color: #5a82c4; border: 1px solid #c8d8f0; }
.chip--bersama { background: #f3effb; color: #8067a8; border: 1px solid #d9ccec; }
.source-chip,.source-label{display:inline-flex;align-items:center;gap:4px}.source-chip svg,.source-label svg{width:13px;height:13px;flex:none}

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-md); }
@media (max-width: 480px) {
  .form-row-2 { grid-template-columns: 1fr; }
  .form-row-3 { grid-template-columns: 1fr; }
}

.budget-page { display:grid; row-gap:18px; padding-bottom:24px; }
.budget-page > .hero-card,
.budget-page > .summary-grid,
.budget-page > .budget-panel { margin:0; }
.hero-card { display:flex; min-height:190px; padding:34px 38px; align-items:center; justify-content:space-between; gap:28px; border:1px solid var(--border); border-radius:20px; background:radial-gradient(circle at 90% 20%, rgba(111,146,95,.24), transparent 34%), linear-gradient(135deg, #fff 0%, #eef4e8 100%); }
.eyebrow { display:block; margin-bottom:7px; color:var(--accent-hover); font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
.hero-card h1 { font-family:var(--font-display); font-size:clamp(28px,3vw,38px); font-weight:600; line-height:1.15; letter-spacing:-.025em; }
.hero-card p { margin-top:9px; color:var(--text-muted); font-size:14px; }
.primary-action,.secondary-action,.icon-action { display:inline-flex; align-items:center; justify-content:center; border:0; font:inherit; cursor:pointer; transition:.2s ease; }
.primary-action { min-height:43px; padding:10px 16px; gap:8px; border-radius:10px; background:var(--accent); color:white; font-size:12.5px; font-weight:700; }.primary-action:hover{background:var(--accent-hover)}.primary-action:disabled{opacity:.65;cursor:wait}.primary-action svg,.secondary-action svg{width:16px;height:16px}
.summary-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin:0; }.summary-card{display:flex;min-width:0;padding:18px 20px;gap:14px;align-items:flex-start;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.summary-card>div{min-width:0}.summary-icon{display:inline-flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:10px;background:var(--accent-soft);color:var(--accent-hover)}.summary-icon svg{width:20px}.summary-icon--warning{background:var(--warn-bg);color:var(--warn-text)}.summary-card__value{font-size:clamp(20px,2vw,26px)}.progress-meta{margin-top:4px;color:var(--text-muted);font-size:11px}.progress-track{height:6px;margin-top:6px;overflow:hidden;border-radius:999px;background:var(--surface-muted)}.progress-track span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--accent-hover),var(--accent-light))}
.budget-panel { overflow:hidden; border:1px solid var(--border); border-radius:14px; background:var(--surface); box-shadow:var(--shadow-sm); }
.toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px; margin:0; border:0; border-bottom:1px solid var(--border); border-radius:0; background:var(--surface); box-shadow:none }.toolbar__top{display:flex;flex:1;min-width:0}.toolbar__search{position:relative;flex:1;min-width:190px}.toolbar__search>svg{position:absolute;left:13px;top:50%;width:16px;color:var(--text-dim);transform:translateY(-50%)}.toolbar__filters{display:flex;gap:8px}.toolbar-control,.secondary-action{min-height:40px;padding:9px 12px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted);font-family:var(--font);font-size:12.5px;font-weight:500}.search-input{width:100%;padding-left:39px}.toolbar__select{width:150px}.secondary-action{gap:7px;white-space:nowrap}.secondary-action:hover{background:var(--accent-soft);color:var(--accent-hover)}
.budget-table{overflow:hidden;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.tbl thead th,.grouped-th,.sub-th{background:var(--surface-muted)!important}.sort-btn svg{width:13px}.icon-action{width:34px;height:34px;border-radius:8px;background:transparent;color:var(--text-dim)}.icon-action:hover{background:var(--accent-soft);color:var(--accent-hover)}.icon-action--danger:hover{background:var(--danger-bg);color:var(--danger-text)}.icon-action svg{width:15px;height:15px}.drag-cell svg{display:inline;width:16px;vertical-align:middle}.mobile-records{display:none}
.drawer-backdrop{position:fixed;inset:0;z-index:1000;display:flex;justify-content:flex-end;background:rgba(34,45,37,.28);backdrop-filter:blur(2px)}.budget-drawer{display:flex;width:min(440px,100vw);height:100dvh;flex-direction:column;background:var(--surface);border-left:1px solid var(--border);box-shadow:var(--shadow-lg)}.drawer-handle{display:none}.drawer-header{display:flex;min-height:86px;padding:20px 22px;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.drawer-title{font-size:18px;font-weight:700;line-height:1.25}.drawer-body{display:flex;min-height:0;padding:22px;flex:1;flex-direction:column;overflow-y:auto}.drawer-body .form-input{min-height:44px}.drawer-footer{display:flex;justify-content:flex-end;gap:9px;margin:auto -22px -22px;padding:18px 22px 22px;border-top:1px solid var(--border);background:var(--surface)}.spinner{animation:spin .8s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:900px){.summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.summary-card:last-child{grid-column:1/-1}.toolbar{align-items:stretch;flex-direction:column}.toolbar__filters{flex-wrap:wrap}.toolbar__select{flex:1}}
@media(max-width:767px){.budget-page{gap:14px}.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px;border-radius:16px}.hero-card h1{font-size:25px}.hero-card p{font-size:12.5px}.hero-card .primary-action{width:100%;min-height:44px}.summary-grid{grid-template-columns:1fr 1fr;gap:8px}.summary-card{padding:13px;gap:9px}.summary-card:last-child{grid-column:1/-1}.summary-icon{width:34px;height:34px}.summary-card__value{font-size:19px}.toolbar{display:block;padding:0;border:0;background:transparent;box-shadow:none}.toolbar__search{margin-bottom:8px}.toolbar__filters{display:grid;grid-template-columns:1fr 1fr}.toolbar__filters>*{width:100%;min-width:0;min-height:44px}.toolbar__export{grid-column:1/-1}.budget-table{display:none}.mobile-records{display:grid;gap:10px}.record-card{padding:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.record-card__header{display:flex;align-items:flex-start;justify-content:space-between;gap:8px}.record-card__header h2{margin-top:8px;font-size:14px}.record-card__header p{margin-top:3px;color:var(--text-muted);font-size:11.5px}.record-card__amounts{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:14px;padding:12px 0;border-block:1px solid var(--border)}.record-card dt{color:var(--text-muted);font-size:10.5px}.record-card dd{margin-top:3px;font-size:12px;font-weight:700}.record-card__footer{display:flex;align-items:center;justify-content:space-between;margin-top:8px}.record-card__footer>span{display:flex;align-items:center;gap:6px;color:var(--text-muted);font-size:11px}.record-card__footer>span svg{width:14px}.record-card__footer>div{display:flex}.icon-action{min-width:40px;height:40px}.empty-card{padding:30px;text-align:center;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.empty-card>svg{width:32px;color:var(--accent)}.empty-card h2{margin-top:10px;font-size:16px}.empty-card p{margin:6px 0 16px;color:var(--text-muted);font-size:12px}.drawer-backdrop{align-items:flex-end}.budget-drawer{width:100%;height:auto;max-height:92dvh;border:0;border-top:1px solid var(--border);border-radius:20px 20px 0 0}.drawer-handle{display:block;width:40px;height:4px;margin:9px auto 0;border-radius:999px;background:var(--border)}.drawer-header{min-height:68px;padding:10px 16px 13px}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.drawer-body .form-input{font-size:16px}.drawer-footer{position:sticky;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}.drawer-footer>*{flex:1;min-height:44px}}
@media(max-width:430px){.summary-icon{display:none}.summary-card{display:block}.record-card__amounts{gap:4px}.record-card dd{font-size:11px}}

/* Polished table and interaction states */
.budget-table { border:0; border-radius:0; box-shadow:none; }
.toolbar-select-wrap { position:relative; display:flex; min-width:0; }
.toolbar-select-wrap .toolbar__select { padding-right:34px; appearance:none; -webkit-appearance:none; background-image:none; }
.toolbar-select-wrap > svg { position:absolute; top:50%; right:11px; width:15px; height:15px; color:var(--text-dim); transform:translateY(-50%); pointer-events:none; }
.mobile-filter-toggle { display:none; position:relative; width:44px; height:44px; flex:none; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:9px; background:var(--surface); color:var(--text-muted); cursor:pointer; }
.mobile-filter-toggle svg { width:19px; height:19px; }
.mobile-filter-toggle--active { border-color:var(--accent-light); background:var(--accent-soft); color:var(--accent-hover); }
.mobile-filter-toggle__dot { position:absolute; top:7px; right:7px; width:6px; height:6px; border:1px solid var(--surface); border-radius:999px; background:var(--accent-hover); }
.tbl { width:100%; border-collapse:collapse; }
.tbl th { padding:11px 12px; color:var(--text-muted); font-size:10.5px; font-weight:700; letter-spacing:.055em; text-transform:uppercase; }
.tbl td { padding:12px; border-bottom:1px solid var(--border); font-size:12px; vertical-align:middle; }
.tbl tbody tr:last-child td { border-bottom:0; }
.tbl tbody .draggable-row:hover { background:var(--surface-soft); }
.tbl tbody .budget-row--paid { background:var(--ok-bg); }
.tbl tbody .budget-row--partial { background:var(--warn-bg); }
.tbl tbody .budget-row--unpaid { background:var(--surface); }
.tbl tbody .draggable-row.is-drop-target { background:var(--accent-soft); }
.drag-cell { color:var(--text-dim); }
.drag-cell svg { display:inline; width:16px; margin-right:4px; vertical-align:middle; color:var(--text-dim); opacity:.62; stroke-width:1.8; }
.is-drag-disabled .drag-cell svg { opacity:.28; }
.sort-btn:focus-visible,.icon-action:focus-visible,.primary-action:focus-visible,.secondary-action:focus-visible { outline:2px solid var(--accent); outline-offset:2px; }
.primary-action:hover { transform:translateY(-1px); }
.icon-action:disabled,.secondary-action:disabled { opacity:.55; cursor:not-allowed; }

/* Drawer and form parity with Checklist */
.drawer-backdrop { animation:drawerFade .2s ease; }
.budget-drawer { box-shadow:-18px 0 48px rgba(40,54,44,.16); animation:drawerFromRight .25s ease; }
.drawer-header .eyebrow { margin-bottom:3px; font-size:9px; }
.drawer-body { gap:16px; }
.form-field { display:grid; gap:7px; min-width:0; }
.form-label { display:flex; align-items:center; gap:5px; color:var(--text); font-size:12.5px; font-weight:650; }
.form-label span { color:var(--accent); }
.form-label small { margin-left:auto; color:var(--text-dim); font-size:10.5px; font-weight:500; }
.field-control { position:relative; display:flex; align-items:center; border:1px solid var(--border); border-radius:10px; background:#fbfcfa; transition:border-color .16s ease,box-shadow .16s ease,background .16s ease; }
.field-control:focus-within { border-color:var(--accent); background:var(--surface); box-shadow:0 0 0 3px var(--accent-soft); }
.field-control > svg { position:absolute; left:13px; z-index:1; width:17px; height:17px; color:#7a8b7e; pointer-events:none; }
.drawer-body .field-control .form-input { min-height:46px; width:100%; padding:10px 42px !important; border:0 !important; border-radius:10px; background:transparent !important; color:var(--text); font-family:var(--font); font-size:13px; font-weight:500; box-shadow:none !important; }
.drawer-body .field-control .form-input:focus { border:0 !important; outline:0; box-shadow:none !important; }
.drawer-body .field-control .form-input::placeholder { color:#9aa49d; font-size:12.5px; font-weight:400; }
.drawer-body .field-control input[type='number'] { padding-right:8px !important; }
.drawer-body .field-control input[type='number']::-webkit-inner-spin-button,
.drawer-body .field-control input[type='number']::-webkit-outer-spin-button { margin-left:auto; }
.select-input { padding-right:44px !important; appearance:none !important; -webkit-appearance:none !important; background-image:none !important; cursor:pointer; }
.select-chevron { position:absolute; right:15px; width:8px; height:8px; border-right:1.8px solid #66786a; border-bottom:1.8px solid #66786a; transform:translateY(-2px) rotate(45deg); pointer-events:none; }
.field-control--textarea { align-items:flex-start; }
.field-control--textarea > svg { top:14px; }
.drawer-body .field-control textarea.form-input { min-height:124px; padding-top:12px !important; resize:vertical; line-height:1.55; }
.form-helper { color:var(--text-dim); font-size:10.5px; line-height:1.45; }
@keyframes drawerFade { from { opacity:0; } }
@keyframes drawerFromRight { from { transform:translateX(100%); } }
@media (prefers-reduced-motion:reduce) { .drawer-backdrop,.budget-drawer,.primary-action,.icon-action,.draggable-row { animation:none!important; transition:none!important; } }
@media(max-width:767px){
  .budget-panel { overflow:visible; border:0; border-radius:0; background:transparent; box-shadow:none; }
  .budget-panel .toolbar { display:block; padding:12px; border:1px solid var(--border); border-radius:12px; background:var(--surface); box-shadow:var(--shadow-sm); }
  .toolbar__top { width:100%; gap:8px; }
  .toolbar__search { min-width:0; margin-bottom:0; }
  .toolbar__search .toolbar-control { height:44px; min-height:44px; }
  .mobile-filter-toggle { display:flex; height:44px; min-height:44px; }
  .toolbar__filters { display:none; grid-template-columns:1fr 1fr; margin-top:10px; padding-top:10px; border-top:1px solid var(--border); }
  .toolbar__filters--open { display:grid; }
  .toolbar__filters .toolbar__export { grid-column:1 / -1; }
  .budget-panel .mobile-records { margin-top:14px; padding:0; background:transparent; }
  .toolbar-select-wrap { width:100%; }
  .toolbar-select-wrap .toolbar__select { width:100%; }
  .record-card { position:relative; overflow:hidden; border-left-width:4px; transition:border-color .18s ease,background .18s ease; }
  .record-card--belum { border-left-color:var(--danger-text); background:var(--surface); }
  .record-card--dp_terbayar { border-left-color:var(--warn-text); background:var(--surface); }
  .record-card--lunas { border-left-color:var(--ok-text); background:var(--surface); }
  .budget-drawer { animation:drawerFromBottom .25s ease; box-shadow:0 -18px 48px rgba(40,54,44,.16); }
  .drawer-body { gap:14px; }
  .form-row-2,.form-row-3 { grid-template-columns:1fr; gap:14px; }
  .drawer-header .icon-action { width:44px;height:44px; }
  .drawer-footer { padding-top:12px; border-top:1px solid var(--border); }
  @keyframes drawerFromBottom { from { transform:translateY(100%); } }
}
</style>