<template>
  <AppLayout>

    <div class="budget-page">
    <PlannerHero eyebrow="Rencana keuangan pernikahan" title="Anggaran Pernikahan" description="Catat estimasi, pembayaran, dan sisa tagihan dalam satu tempat.">
      <template #action><button class="primary-action" @click="openCreate"><Plus aria-hidden="true" />Tambah Anggaran</button></template>
    </PlannerHero>

    <PlannerSummaryGrid aria-label="Ringkasan anggaran">
      <article class="summary-card"><span class="summary-icon"><WalletCards /></span><div><p class="summary-label">Total Estimasi</p><p class="summary-value">{{ formatRp(totalEstimasi) }}</p><p class="summary-sub">{{ budgets.length }} item anggaran</p></div></article>
      <article class="summary-card"><span class="summary-icon"><Banknote /></span><div><p class="summary-label">Sudah Dibayar</p><p class="summary-value">{{ formatRp(totalAktual) }}</p><div class="progress-meta"><span>{{ progressPct }}% dari estimasi</span></div><div class="progress-track"><span :style="{ width: progressPct + '%' }"></span></div></div></article>
      <article class="summary-card"><span class="summary-icon summary-icon--warning"><ReceiptText /></span><div><p class="summary-label">Sisa Tagihan</p><p class="summary-value">{{ formatRp(totalSisa) }}</p><p class="summary-sub">DP {{ formatRp(totalDp) }} · Pelunasan {{ formatRp(totalPelunasan) }}</p></div></article>
    </PlannerSummaryGrid>

    <PlannerDataPanel>
    <section class="toolbar" aria-label="Pencarian dan filter">
      <div class="toolbar-top">
        <div class="search-wrap"><Search aria-hidden="true" /><input v-model="searchQuery" type="text" placeholder="Cari item atau vendor..." class="toolbar-control search-input" /></div>
        <button type="button" class="filter-toggle" :class="{ 'active': mobileFiltersOpen || filterKategori || filterStatus }" :aria-expanded="mobileFiltersOpen" aria-controls="budget-mobile-filters" aria-label="Tampilkan filter anggaran" @click="mobileFiltersOpen = !mobileFiltersOpen">
          <ListFilter aria-hidden="true" />
          <span v-if="filterKategori || filterStatus" class="filter-dot" aria-hidden="true"></span>
        </button>
      </div>
      <div id="budget-mobile-filters" class="toolbar-filters" :class="{ 'open': mobileFiltersOpen }">
        <label class="select-wrap"><select v-model="filterKategori" class="toolbar-control toolbar-select" aria-label="Filter kategori"><option value="">Semua Kategori</option><option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option></select><ChevronDown aria-hidden="true" /></label>
        <label class="select-wrap"><select v-model="filterStatus" class="toolbar-control toolbar-select" aria-label="Filter status"><option value="">Semua Status</option><option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option></select><ChevronDown aria-hidden="true" /></label>
        <button v-if="filterKategori || filterStatus || searchQuery || sortBy" class="secondary-action" @click="resetFilters"><X aria-hidden="true" />Reset</button>
        <button class="secondary-action export-action" @click="exportToExcel"><FileSpreadsheet aria-hidden="true" />Ekspor Excel</button>
      </div>
    </section>

    <div class="desktop-table">
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
              <th class="right">
                <button class="sort-btn sort-btn--right" @click="toggleSort('estimasi_budget')">
                  Estimasi <component :is="sortIcon('estimasi_budget')" aria-hidden="true" />
                </button>
              </th>
              <th class="right">DP</th>
              <th class="right">Pelunasan</th>
              <th class="right">Sisa</th>
              <th>
                <button class="sort-btn" @click="toggleSort('status')">
                  Status <component :is="sortIcon('status')" aria-hidden="true" />
                </button>
              </th>
              <th class="center" style="width:80px">Aksi</th>
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
              <td class="right strong">{{ formatRp(b.estimasi_budget) }}</td>
              <td class="right" :class="b.dp > 0 ? 'text-dark' : 'text-dim'">
                {{ b.dp > 0 ? formatRp(b.dp) : '–' }}
              </td>
              <td class="right" :class="b.pelunasan > 0 ? 'text-dark' : 'text-dim'">
                {{ b.pelunasan > 0 ? formatRp(b.pelunasan) : '–' }}
              </td>
              <td class="right strong" :class="(b.estimasi_budget - b.dp - b.pelunasan) > 0 ? 'text-danger' : 'text-ok'">
                {{ formatRp(b.estimasi_budget - b.dp - b.pelunasan) }}
              </td>
              <td>
                <span class="chip" :class="statusChip(b.status)">{{ statusLabel(b.status) }}</span>
              </td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="icon-action" data-tooltip="Salin" :aria-label="`Salin anggaran ${b.item}`" @click="openCopy(b)" :id="'copy-budget-'+b.id"><Copy aria-hidden="true" /></button>
                  <button class="icon-action" data-tooltip="Edit" :aria-label="`Edit anggaran ${b.item}`" @click="openEdit(b)" :id="'edit-budget-'+b.id"><Pencil aria-hidden="true" /></button>
                  <button class="icon-action danger" data-tooltip="Hapus" :aria-label="`Hapus anggaran ${b.item}`" @click="confirmDelete(b)" :id="'del-budget-'+b.id"><Trash2 aria-hidden="true" /></button>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredBudgets.length">
            <tr class="tfoot-row">
              <td colspan="4" class="tfoot-label">Total</td>
              <td class="right strong">{{ formatRp(filteredTotalEstimasi) }}</td>
              <td class="right strong">{{ formatRp(filteredTotalDp) }}</td>
              <td class="right strong">{{ formatRp(filteredTotalPelunasan) }}</td>
              <td class="right strong text-danger">{{ formatRp(filteredTotalSisa) }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
        <div v-if="!filteredBudgets.length" class="empty-card"><WalletCards /><h2>{{ hasFilters ? 'Anggaran tidak ditemukan' : 'Belum ada anggaran' }}</h2><p>{{ hasFilters ? 'Tidak ada data yang sesuai dengan pencarian atau filter.' : 'Mulai catat kebutuhan dan perkiraan biaya pernikahan.' }}</p><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Anggaran Pertama' }}</button></div>
    </div>

    <section class="mobile-records" aria-label="Daftar anggaran">
      <article v-for="b in filteredBudgets" :key="b.id" class="record-card" :class="`record-card--${b.status}`">
        <div class="record-head"><div><span class="category-label"><span class="category-dot" :class="categoryDotClass(b.kategori)" aria-hidden="true"></span>{{ b.kategori }}</span><h2>{{ b.item }}</h2><p>{{ b.vendor || 'Vendor belum ditentukan' }}</p></div><span class="chip" :class="statusChip(b.status)">{{ statusLabel(b.status) }}</span></div>
        <dl class="record-amounts"><div><dt>Estimasi</dt><dd>{{ formatRp(b.estimasi_budget) }}</dd></div><div><dt>Terbayar</dt><dd>{{ formatRp((b.dp || 0) + (b.pelunasan || 0)) }}</dd></div><div><dt>Sisa</dt><dd :class="(b.estimasi_budget - b.dp - b.pelunasan) > 0 ? 'text-danger' : 'text-ok'">{{ formatRp(b.estimasi_budget - b.dp - b.pelunasan) }}</dd></div></dl>
        <div class="record-footer"><span class="source-label"><Mars v-if="b.sumber_dana === 'cpp' || b.sumber_dana === 'bersama'" aria-hidden="true" /><Venus v-if="b.sumber_dana === 'cpw' || b.sumber_dana === 'bersama'" aria-hidden="true" />{{ sumberDanaOptions[b.sumber_dana] || 'CPP' }}</span><div><button class="icon-action" aria-label="Salin anggaran" @click="openCopy(b)"><Copy /></button><button class="icon-action" aria-label="Edit anggaran" @click="openEdit(b)"><Pencil /></button><button class="icon-action danger" aria-label="Hapus anggaran" @click="confirmDelete(b)"><Trash2 /></button></div></div>
      </article>
      <div v-if="!filteredBudgets.length" class="empty-card"><WalletCards /><h2>{{ hasFilters ? 'Anggaran tidak ditemukan' : 'Belum ada anggaran' }}</h2><p>{{ hasFilters ? 'Tidak ada data yang sesuai dengan pencarian atau filter.' : 'Mulai catat kebutuhan dan perkiraan biaya pernikahan.' }}</p><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Anggaran Pertama' }}</button></div>
    </section>
    </PlannerDataPanel>
    </div>

    <!-- Drawer form -->
    <Teleport to="body">
      <div v-if="showModal" class="drawer-backdrop" @click.self="closeModal">
        <aside class="drawer" role="dialog" aria-modal="true" aria-labelledby="drawer-title">
          <div class="drawer-handle" aria-hidden="true"></div>
          <div class="drawer-header">
            <div><span class="eyebrow">Anggaran pernikahan</span><h3 id="drawer-title" class="drawer-title">{{ editItem ? 'Edit Anggaran' : (copyItem ? 'Salin Anggaran' : 'Tambah Anggaran') }}</h3></div>
            <button class="icon-action" aria-label="Tutup" title="Tutup" @click="closeModal"><X aria-hidden="true" /></button>
          </div>
          <form @submit.prevent="save" class="drawer-body">

            <div class="form-row form-row--details">
              <div class="field">
                <label class="field-label" for="budget-category">Kategori <span>*</span></label>
                <div class="field-control field-control--select">
                  <Tags aria-hidden="true" />
                  <select id="budget-category" v-model="form.kategori" required class="form-input select-input">
                    <option value="">Pilih kategori</option>
                    <option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option>
                  </select>
                  <span class="select-chevron" aria-hidden="true"></span>
                </div>
              </div>
              <div class="field">
                <label class="field-label" for="budget-source">Dana Dari <span>*</span></label>
                <div class="field-control field-control--select">
                  <HandCoins aria-hidden="true" />
                  <select id="budget-source" v-model="form.sumber_dana" required class="form-input select-input">
                    <option value="" disabled>Pilih sumber dana</option>
                    <option v-for="(label, key) in sumberDanaFormOptions" :key="key" :value="key">{{ label }}</option>
                  </select>
                  <span class="select-chevron" aria-hidden="true"></span>
                </div>
              </div>
              <div class="field">
                <label class="field-label" for="budget-status">Status <span>*</span></label>
                <div class="field-control field-control--select">
                  <CircleGauge aria-hidden="true" />
                  <select id="budget-status" v-model="form.status" required class="form-input select-input">
                    <option value="" disabled>Pilih status</option>
                    <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                  </select>
                  <span class="select-chevron" aria-hidden="true"></span>
                </div>
              </div>
            </div>
            <div class="field">
              <label class="field-label" for="budget-item">Nama Item <span>*</span></label>
              <div class="field-control">
                <TextCursorInput aria-hidden="true" />
                <input id="budget-item" v-model="form.item" type="text" required class="form-input" placeholder="Contoh: Sewa gedung resepsi">
              </div>
              <p class="form-helper">Gunakan nama singkat agar anggaran mudah ditemukan.</p>
            </div>
            <div class="field">
              <label class="field-label" for="budget-vendor">Vendor <small>Opsional</small></label>
              <div class="field-control">
                <Store aria-hidden="true" />
                <input id="budget-vendor" v-model="form.vendor" type="text" class="form-input" placeholder="Nama vendor...">
              </div>
            </div>
            <div class="form-row form-row--three">
              <div class="field">
                <label class="field-label" for="budget-estimate">Estimasi (Rp) <span>*</span></label>
                <div class="field-control">
                  <BadgeDollarSign aria-hidden="true" />
                  <input id="budget-estimate" v-model="form.estimasi_budget" type="number" required min="0" class="form-input" placeholder="0">
                </div>
              </div>
              <div class="field">
                <label class="field-label" for="budget-dp">DP (Rp)</label>
                <div class="field-control">
                  <Coins aria-hidden="true" />
                  <input id="budget-dp" v-model="form.dp" type="number" min="0" class="form-input" placeholder="0">
                </div>
              </div>
              <div class="field">
                <label class="field-label" for="budget-payment">Pelunasan (Rp)</label>
                <div class="field-control">
                  <CircleDollarSign aria-hidden="true" />
                  <input id="budget-payment" v-model="form.pelunasan" type="number" min="0" class="form-input" placeholder="0">
                </div>
              </div>
            </div>
            <div class="field">
              <label class="field-label" for="budget-note">Catatan <small>Opsional</small></label>
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
import PlannerHero from '@/Components/PlannerHero.vue';
import PlannerSummaryGrid from '@/Components/PlannerSummaryGrid.vue';
import PlannerDataPanel from '@/Components/PlannerDataPanel.vue';
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
  belum: 'Belum Dibayar',
  dp_terbayar: 'DP Dibayar',
  lunas: 'Dibayar',
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

const hasFilters = computed(() => Boolean(searchQuery.value || filterKategori.value || filterStatus.value || sortBy.value));
const canDragRows = computed(() => !hasFilters.value);

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

function handleTouchEnd() {
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
.budget-page{display:grid;width:min(1200px,calc(100% - 48px));margin:0 auto;grid-template-columns:minmax(0,1fr);row-gap:18px;padding-bottom:24px;color:#240029}.budget-page>*{margin-block:0}.hero-card{display:flex;min-height:190px;padding:32px;align-items:center;justify-content:space-between;gap:28px;border:1px solid #240029;border-radius:12px;background:#fff}.eyebrow{display:inline-block;margin-bottom:8px;padding:4px 8px;border-radius:6px;background:#fff1bd;color:#240029;font-size:11px;font-weight:600;letter-spacing:0;text-transform:none}.hero-card h1{color:#240029;font-family:Inter,system-ui,sans-serif;font-size:clamp(32px,4vw,40px);font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{margin-top:12px;color:#240029;font-size:16px;line-height:1.5}.primary-action,.secondary-action,.icon-action,.filter-toggle,.drag-handle{display:inline-flex;align-items:center;justify-content:center;border:0;font:inherit;cursor:pointer}.primary-action{min-height:43px;padding:10px 16px;gap:8px;border-radius:6px;background:#240029;color:#fff;font-size:13px;font-weight:700;box-shadow:0 1px 2px rgba(0,0,0,.05)}.primary-action:hover{background:#c92f96}.primary-action:disabled{opacity:.65;cursor:wait}.primary-action svg,.secondary-action svg{width:16px}.summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.summary-card{display:flex;padding:18px 20px;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.summary-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:10px;background:#fff1f8;color:#c92f96}.summary-icon svg{width:20px}.summary-icon--warning{background:var(--warn-bg);color:var(--warn-text)}.summary-label{font-size:12px;font-weight:600;color:var(--text-muted)}.summary-value{margin-top:5px;font-size:clamp(20px,2vw,26px);font-weight:800}.summary-sub{margin-top:4px;color:var(--text-muted);font-size:12px}.progress-track{height:6px;margin-top:7px;overflow:hidden;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;height:100%;background:linear-gradient(90deg,#c92f96,#f28acb)}.data-panel{overflow:hidden;border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:var(--shadow-sm)}.data-panel>.toolbar{display:flex;margin:0;padding:12px;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border);border-radius:0;box-shadow:none}.toolbar-top{display:flex;flex:1}.search-wrap{position:relative;flex:1}.search-wrap>svg{position:absolute;left:13px;top:50%;width:16px;transform:translateY(-50%);color:var(--text-dim)}.toolbar-control,.secondary-action{box-sizing:border-box;height:40px;min-height:40px;padding:0 12px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted);font:500 13px var(--font);line-height:1}.search-wrap input{width:100%;padding-left:39px}.toolbar-filters{display:flex;gap:8px}.select-wrap{position:relative;display:flex}.select-wrap select{width:155px;padding-right:34px;appearance:none}.select-wrap>svg{position:absolute;right:11px;top:50%;width:15px;transform:translateY(-50%);pointer-events:none}.secondary-action{gap:7px}.filter-toggle{display:none;position:relative;width:44px;height:44px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted)}.filter-toggle>svg{width:19px}.filter-toggle>span{position:absolute;top:7px;right:7px;width:6px;height:6px;border-radius:50%;background:#df37a7}.filter-toggle.active{background:#fff1f8;color:#c92f96}.desktop-table{display:block;margin:0;padding:0;overflow-x:auto}.desktop-table .tbl{width:100%;margin:0!important;border-spacing:0;border-collapse:collapse}.tbl thead tr{height:42px}.tbl thead th{height:42px;padding-block:0!important;vertical-align:middle}.tbl th{padding:11px 12px;background:var(--surface-muted);color:var(--text-muted);font-size:11px;font-weight:700;letter-spacing:.055em;text-transform:uppercase;vertical-align:middle}.tbl td{padding:12px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:middle}.tbl td small{display:block;margin-top:3px;color:var(--text-muted)}.right{text-align:right}.center{text-align:center}.strong{font-weight:700}.actions{display:flex;justify-content:center}.icon-action{width:36px;height:36px;border-radius:8px;background:transparent;color:var(--text-dim)}.icon-action:hover{background:#fff1f8;color:#c92f96}.icon-action.danger:hover{background:var(--danger-bg);color:var(--danger-text)}.icon-action svg{width:15px}.drag-cell{white-space:nowrap;touch-action:none;color:var(--text-dim);cursor:grab}.drag-cell svg{display:inline;width:16px;margin-right:4px;vertical-align:middle;color:var(--text-dim);opacity:.62;stroke-width:1.8}.is-drag-disabled .drag-cell svg{opacity:.28}.draggable-row.is-dragging{opacity:.45}.draggable-row.is-drop-before{box-shadow:inset 0 3px #df37a7}.draggable-row.is-drop-after{box-shadow:inset 0 -3px #df37a7}.is-drag-disabled .drag-cell{opacity:.4;cursor:default}.tbl tfoot td{padding:10px 14px;background:var(--rose-pale);border-top:2px solid var(--border);border-bottom:0;font-size:13px;line-height:normal;vertical-align:middle}.tbl tfoot td:first-child{font-size:11px;font-weight:700;text-transform:uppercase}.chip--cpw{background:#fdf0f8;color:#c4719e}.chip--cpp{background:#eef4ff;color:#5a82c4}.source-chip,.source-label{display:inline-flex;align-items:center;gap:4px}.source-chip svg,.source-label svg{width:13px;height:13px;flex:none}.mobile-records{display:none}.empty-card{display:flex;min-height:250px;padding:32px;align-items:center;justify-content:center;flex-direction:column;text-align:center}.empty-card>svg{width:42px;color:#df37a7}.empty-card h2{margin-top:12px;font-size:16px}.empty-card p{margin:6px 0 17px;color:var(--text-muted);font-size:12px}.drawer-backdrop{position:fixed;inset:0;z-index:1000;display:flex;justify-content:flex-end;background:rgba(34,45,37,.28);backdrop-filter:blur(2px);animation:drawerFade .2s ease}.drawer{display:flex;width:min(440px,100vw);height:100dvh;flex-direction:column;background:var(--surface);box-shadow:-18px 0 48px rgba(40,54,44,.16);animation:drawerFromRight .25s ease}.drawer-handle{display:none}.drawer-header{display:flex;min-height:86px;padding:20px 22px;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid var(--border)}.drawer-header .eyebrow{display:block;margin:0 0 3px;font-size:9px;font-weight:700;line-height:1.2;letter-spacing:.1em;text-transform:uppercase}.drawer-header .drawer-title{margin:0;font-family:var(--font-display);font-size:18px;font-weight:700;line-height:1.25}.drawer-body{display:flex;min-height:0;padding:22px;gap:16px;flex:1;flex-direction:column;overflow:auto}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{display:grid;gap:7px}.field label{font-size:13px;font-weight:600}.field label span{color:#df37a7}.field-control{position:relative;display:flex;align-items:center;overflow:hidden;border:1px solid var(--border);border-radius:10px;background:#fbfcfa;transition:border-color .16s ease,box-shadow .16s ease,background .16s ease}.field-control:focus-within{border-color:#df37a7;background:var(--surface);box-shadow:0 0 0 3px #fff1f8}.field-control>svg{position:absolute;left:13px;width:17px;color:#7a8b7e}.field-control>svg:last-child:not(:first-child){left:auto;right:13px;width:15px}.field-control input,.field-control select{width:100%;min-height:46px;padding:10px 40px;border:0!important;border-radius:inherit;outline:0;background:transparent!important;box-shadow:none!important;font:500 13px var(--font)}.field-control input:focus,.field-control select:focus{border:0!important;outline:0;box-shadow:none!important}.field-control select{appearance:none}.drawer-footer{display:flex;justify-content:flex-end;gap:9px;margin:auto -22px -22px;padding:18px 22px 22px;border-top:1px solid var(--border)}.spinner{animation:spin .8s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@keyframes drawerFade{from{opacity:0}}@keyframes drawerFromRight{from{transform:translateX(100%)}}@keyframes drawerFromBottom{from{transform:translateY(100%)}}
.budget-page{font-family:Inter,system-ui,sans-serif;row-gap:16px}.hero-card{gap:24px}.hero-card h1{font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:40px;font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{font-size:16px}.primary-action{min-height:40px;padding:12px 20px;font-size:16px;font-weight:500}.secondary-action,.toolbar-control{border-color:#d4ccd4;border-radius:6px;background:#fff;color:#240029;font-family:Inter,system-ui,sans-serif;font-size:14px}.summary-grid{gap:16px}.summary-card{padding:24px;gap:16px;border-color:#d4ccd4;background:#fff;box-shadow:none}.summary-icon{border-radius:12px;background:#fff1f8;color:#240029}.summary-icon--warning{background:#cdeeee;color:#240029}.summary-label,.summary-sub,.tbl th,.tbl td small,.drag-cell,.category,.record-card dt,.drag-handle{color:#240029}.summary-label{font-size:14px}.summary-value{font-size:28px}.summary-sub{font-size:11px}.progress-track{border-radius:9999px;background:#f1f1f1}.progress-track span{background:#240029}.data-panel{border-color:#d4ccd4;border-radius:12px;background:#fff;box-shadow:none}.data-panel>.toolbar{border-color:#d4ccd4}.filter-toggle,.icon-action{border-radius:6px;color:#240029}.filter-toggle{border-color:#d4ccd4;background:#fff}.filter-toggle.active,.icon-action:hover{background:#fff1f8;color:#240029}.filter-toggle>span{background:#240029}.tbl th{font-size:11px}.tbl td{border-color:#d4ccd4;font-size:12px}.tbl tfoot td{background:#f6d0ff;border-color:#d4ccd4}.record-card{padding:24px;border-color:#d4ccd4;border-radius:12px;background:#fff;box-shadow:none}.record-head h2,.empty-card h2{font-size:16px}.empty-card p{font-size:14px}.drawer-backdrop{background:rgba(26,51,0,.28)}.drawer{width:min(440px,100vw);background:#fff;box-shadow:none}.drawer-header,.drawer-footer{border-color:#d4ccd4}.drawer-header .drawer-title{font-family:Inter,system-ui,sans-serif;font-size:20px}.drawer-body{padding:24px;gap:16px}.field label{font-size:14px;font-weight:600}.field-control{border-color:#d4ccd4;border-radius:6px;background:#fff}.field-control:focus-within{border-color:#240029;background:#fff;box-shadow:0 0 0 1px #240029}.field-control>svg{color:#240029}.field-control input,.field-control select{font:500 14px Inter,system-ui,sans-serif}.drawer-footer{margin:auto -24px -24px;padding:16px 24px 24px}.chip--cpw{background:#f6d0ff;color:#240029}.chip--cpp{background:#cdeeee;color:#240029}
@media(max-width:900px){.toolbar{align-items:stretch;flex-direction:column}.toolbar-top{width:100%}.toolbar-filters{width:100%;flex-wrap:wrap}.select-wrap{flex:1}.select-wrap select{width:100%;min-width:145px}}
@media(prefers-reduced-motion:reduce){.drawer-backdrop,.drawer,.draggable-row,.primary-action,.icon-action,.progress-track span{animation:none!important;transition:none!important}}
@media(max-width:767px){.budget-page{width:calc(100% - 32px);gap:16px}.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px}.hero-card h1{font-size:25px}.hero-card p{font-size:13px}.hero-card .primary-action{width:100%}.summary-grid{grid-template-columns:1fr 1fr;gap:8px}.summary-card{padding:13px;gap:9px}.summary-card:last-child{grid-column:1/-1}.summary-icon{width:34px;height:34px}.summary-value{font-size:19px}.data-panel{overflow:visible;border:0;background:transparent;box-shadow:none}.toolbar{display:block;padding:12px;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.toolbar-top{gap:8px}.toolbar-control,.secondary-action{height:44px;min-height:44px}.filter-toggle{display:flex}.toolbar-filters{display:none;grid-template-columns:1fr 1fr;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}.toolbar-filters.open{display:grid}.toolbar-filters>*{width:100%}.select-wrap select{width:100%}.export-action{grid-column:1/-1}.desktop-table{display:none}.mobile-records{display:grid;gap:10px;margin-top:14px}.record-card{padding:14px;border:1px solid var(--border);border-left-width:4px;border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.record-card.bought{border-left-color:var(--ok-text)}.record-card.pending{border-left-color:var(--danger-text)}.record-head{display:flex;justify-content:space-between;gap:8px}.record-head h2{margin-top:6px;font-size:14px}.category{color:var(--text-muted);font-size:11px}.record-card dl{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:13px;padding:11px 0;border-block:1px solid var(--border)}.record-card dt{color:var(--text-muted);font-size:10px}.record-card dd{margin-top:3px;font-size:12px;font-weight:700}.record-footer{display:flex;margin-top:8px;align-items:center;justify-content:space-between}.drag-handle{gap:5px;padding:8px;background:none;color:var(--text-muted);font-size:11px;touch-action:none}.drag-handle svg{width:15px}.drawer-backdrop{align-items:flex-end}.drawer{width:100%;height:auto;max-height:92dvh;border-radius:20px 20px 0 0;box-shadow:0 -18px 48px rgba(40,54,44,.16);animation:drawerFromBottom .25s ease}.drawer-handle{display:block;width:40px;height:4px;margin:9px auto 0;border-radius:99px;background:var(--border)}.drawer-header{min-height:68px;padding:10px 16px 13px}.drawer-header .icon-action{width:44px;height:44px}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.form-row{grid-template-columns:1fr}.field-control input,.field-control select{font-size:16px}.drawer-footer{position:sticky;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}.drawer-footer>*{flex:1}}
.hero-card h1{font-size:55px}.hero-card p{font-size:18px}.eyebrow{font-size:12px}.secondary-action,.toolbar-control,.tbl td,.field-control input,.field-control select{font-size:16px}.empty-card h2,.drawer-header .drawer-title,.record-head h2{font-size:28px}.summary-label,.empty-card p,.field label,.tbl td small{font-size:14px}.summary-sub,.tbl th,.category,.record-card dt,.drag-handle{font-size:11px}
@media(max-width:767px){.hero-card{padding:16px;gap:16px}.hero-card h1{font-size:40px}.hero-card p{font-size:16px}.summary-grid{gap:8px}.summary-card{padding:16px;gap:8px}.summary-value{font-size:20px}.toolbar{border-color:#d4ccd4;background:#fff}.mobile-records{gap:16px;margin-top:16px}.record-card{padding:16px;border-color:#d4ccd4;background:#fff;box-shadow:none}.record-head h2{font-size:28px}.record-card dt{font-size:11px}.record-card dd{font-size:16px}.drawer{border-radius:16px 16px 0 0;box-shadow:none}.drawer-handle{border-radius:9999px;background:#d4ccd4}.drawer-footer{background:#fff}}
.budget-page{color:#240029}.hero-card{border-color:#d4ccd4;border-radius:14px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f);box-shadow:0 2px 2px rgba(32,0,36,.05)}.hero-card h1,.hero-card p,.eyebrow{color:#240029}.eyebrow{background:transparent;font-family:'JetBrains Mono',monospace;letter-spacing:.1em;text-transform:uppercase}.primary-action{background:#df37a7;color:#fff}.primary-action:hover{background:#c92f96}.secondary-action,.toolbar-control,.field-control{border-color:#6d526d;color:#240029}.summary-card,.data-panel,.record-card{border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.progress-track span{background:#df37a7}.tbl th,.tbl td,.summary-label,.summary-sub,.field label{color:#240029}.icon-action:hover{background:#fff1f8;color:#df37a7}

.summary-card>div{min-width:0}.progress-meta{margin-top:4px;color:#6d526d;font-size:11px}.sort-btn{display:inline-flex;padding:0;align-items:center;gap:5px;border:0;background:none;color:inherit;font:inherit;text-transform:inherit;cursor:pointer}.sort-btn--right{margin-left:auto}.sort-btn svg{width:13px}.item-name{font-weight:700}.item-sub{margin-top:2px;color:#6d526d;font-size:11px}.category-label{display:inline-flex;align-items:center;gap:7px}.category-dot{width:8px;height:8px;border-radius:50%;background:#df37a7}.category-dot--1{background:#ffcb0f}.category-dot--2{background:#ff8fb7}.category-dot--3{background:#62b7b7}.category-dot--4{background:#9b78c4}.category-dot--5{background:#f07d58}.text-dark{color:#240029}.text-dim{color:#9b879b}.text-danger{color:var(--danger-text)}.text-ok{color:var(--ok-text)}.chip--bersama{background:#fff1bd;color:#240029}.form-row--three{grid-template-columns:repeat(3,1fr)}.field-label{font-size:14px;font-weight:600}.field-label span{color:#df37a7}.field-label small{float:right;color:#6d526d;font-size:12px}.form-helper{color:#6d526d;font-size:11px}.field-control textarea{width:100%;min-height:124px;padding:12px 40px;border:0!important;outline:0;background:transparent!important;resize:vertical;font:500 14px Inter,sans-serif}.textarea-control{align-items:flex-start}.textarea-control>svg{top:14px}.record-amounts{display:grid;grid-template-columns:repeat(3,1fr)}@media(max-width:767px){.form-row--three{grid-template-columns:1fr}.record-head{display:flex;justify-content:space-between;gap:8px}.record-head h2{margin-top:6px;font-size:28px}.record-head p{margin-top:3px;color:#6d526d;font-size:11px}.record-amounts{gap:6px;margin-top:13px;padding:11px 0;border-block:1px solid #d4ccd4}.record-amounts dt{font-size:11px}.record-amounts dd{margin-top:3px;font-size:16px;font-weight:700}.record-footer{display:flex;margin-top:8px;align-items:center;justify-content:space-between}.record-footer>div{display:flex}}

/* Mobile-only layout refinements. */
@media(max-width:767px){
  .budget-page{width:calc(100% - 24px);row-gap:12px;padding-bottom:16px}
  .hero-card{padding:18px 16px;gap:14px;border-radius:12px}
  .hero-card h1{font-size:clamp(28px,9vw,36px);line-height:1.05;letter-spacing:.01em}
  .hero-card p{margin-top:8px;font-size:14px;line-height:1.45}
  .hero-card .primary-action{min-height:44px;font-size:14px}
  .summary-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  .summary-card{min-width:0;padding:12px;gap:8px;align-items:flex-start}
  .summary-card:last-child{grid-column:1/-1}
  .summary-icon{width:32px;height:32px;border-radius:9px}
  .summary-icon svg{width:17px}
  .summary-label{font-size:11px;line-height:1.25}
  .summary-value{margin-top:4px;font-size:clamp(15px,4.2vw,18px);line-height:1.2;overflow-wrap:anywhere}
  .summary-sub,.progress-meta{font-size:10px;line-height:1.4}
  .data-panel,.data-panel:hover{overflow:visible;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;transform:none!important}
  .data-panel>.toolbar{display:block;padding:10px;border:1px solid #d4ccd4;border-radius:12px;background:#fff;box-shadow:0 2px 8px rgba(32,0,36,.045)}
  .toolbar-top{gap:8px}
  .search-wrap{min-width:0}
  .search-wrap input{font-size:16px}
  .filter-toggle{width:44px;height:44px;flex:none}
  .toolbar-filters{grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:8px;margin-top:10px;padding-top:10px}
  .toolbar-filters .secondary-action{padding-inline:8px;font-size:13px}
  .mobile-records{display:grid;gap:12px;margin-top:14px;padding:0;background:transparent}
  .record-card{min-width:0;margin:0;padding:14px;border:1px solid #d4ccd4;border-left-width:4px;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(32,0,36,.065)}
  .record-card:first-child,.record-card:last-child,.record-card:only-child{border-radius:12px}
  .record-card--belum{border-left-color:var(--danger-text)}
  .record-card--dp_terbayar{border-left-color:#d28a00}
  .record-card--lunas{border-left-color:var(--ok-text)}
  .record-head{align-items:flex-start}
  .record-head>div{min-width:0}
  .record-head h2{margin-top:5px;font-size:18px;line-height:1.25;overflow-wrap:anywhere}
  .record-head p{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .record-head>.chip{max-width:42%;flex:none;text-align:center;white-space:normal}
  .record-amounts{grid-template-columns:repeat(3,minmax(0,1fr));gap:4px;margin-top:12px;padding:10px 0}
  .record-amounts>div{min-width:0}
  .record-amounts dt{font-size:10px}
  .record-amounts dd{font-size:clamp(11px,3.4vw,14px);line-height:1.3;overflow-wrap:anywhere}
  .record-footer{margin-top:6px;gap:8px}
  .source-label{min-width:0;font-size:11px}
  .record-footer>div{flex:none;gap:2px}
  .record-footer .icon-action{width:40px;height:40px}
  .empty-card{min-height:220px;padding:28px 18px;border:1px solid #d4ccd4;border-radius:12px;background:#fff}
  .empty-card>svg{width:38px}
  .empty-card h2{font-size:18px}
  .empty-card p{max-width:280px;font-size:13px;line-height:1.5}
  .empty-card .primary-action,.empty-card .secondary-action{width:100%;max-width:280px}
  .drawer{max-height:94dvh;border-radius:16px 16px 0 0}
  .drawer-header{min-height:64px;padding:8px 14px 12px}
  .drawer-header .drawer-title{font-size:18px}
  .drawer-body{padding:14px 16px calc(16px + env(safe-area-inset-bottom));gap:14px}
  .field{gap:6px}
  .field label{font-size:13px}
  .field-control input,.field-control select{min-height:44px}
  .field-control textarea{min-height:96px;font-size:16px}
  .drawer-footer{bottom:calc(-14px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(14px + env(safe-area-inset-bottom));gap:8px}
}

/* Drawer CRUD — selaras dengan form Checklist dan Profile. */
.drawer{width:min(760px,100vw);border-left:1px solid rgba(109,82,109,.28);box-shadow:-18px 0 48px rgba(36,0,41,.12)}
.drawer-body{gap:20px;padding:24px 28px}
.form-row--details{grid-template-columns:repeat(3,minmax(0,1fr))}
.form-row--three{grid-template-columns:repeat(3,minmax(0,1fr))}
.field{min-width:0;gap:8px}
.field-label{display:flex;align-items:center;gap:5px;line-height:1.4}
.field-label small{margin-left:auto;float:none;color:#6d526d;font-size:12px;font-weight:500}
.field-control{isolation:isolate;overflow:hidden;border-color:rgba(109,82,109,.38);border-radius:12px;background:#fffafd;box-shadow:0 1px 2px rgba(32,0,36,.035);transition:border-color .16s ease,background-color .16s ease,box-shadow .16s ease}
.field-control:hover{border-color:rgba(109,82,109,.62);background:#fff}
.field-control:focus-within{border-color:#df37a7;background:#fff;box-shadow:0 0 0 3px rgba(223,55,167,.12)}
.field-control>svg{z-index:1;color:#6d526d;pointer-events:none;transition:color .16s ease}
.field-control:focus-within>svg{color:#df37a7}
.drawer-body .field-control>.form-input{min-height:48px;padding-left:44px!important;border-radius:12px;color:#240029}
.drawer-body .field-control>select.form-input{padding-right:44px!important}
.drawer-body .field-control>input[type='number'].form-input{padding-left:44px!important;padding-right:12px!important}
.field-control input::placeholder,.field-control textarea::placeholder{color:#9b879b;font-weight:400}
.select-input{padding-right:44px!important;cursor:pointer}
.select-chevron{position:absolute;right:16px;width:8px;height:8px;border-right:1.8px solid #6d526d;border-bottom:1.8px solid #6d526d;transform:translateY(-2px) rotate(45deg);pointer-events:none}
.field-control--textarea{align-items:flex-start;overflow:hidden}
.field-control--textarea>svg{top:14px}
.drawer-body .field-control>textarea.form-input{min-height:144px;padding:13px 40px 13px 44px!important;border-radius:12px;color:#240029;line-height:1.55}
.form-helper{margin:0 2px;line-height:1.5}
.drawer-footer{margin:auto -28px -24px;padding:18px 28px 24px;gap:12px;background:#fff}
.drawer-footer .secondary-action,.drawer-footer .primary-action{min-height:44px;padding-inline:20px}
.drawer-header .icon-action:focus-visible,.drawer-footer button:focus-visible{outline:3px solid rgba(223,55,167,.22);outline-offset:2px}

@media(max-width:767px){
  .drawer{width:100%;border-left:0;box-shadow:0 -18px 48px rgba(36,0,41,.12)}
  .drawer-body{gap:16px;padding:16px 16px calc(16px + env(safe-area-inset-bottom))}
  .form-row--details,.form-row--three{grid-template-columns:1fr;gap:16px}
  .field-control textarea{min-height:128px}
  .drawer-footer{position:sticky;z-index:10;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));align-items:stretch;background:#fff;box-shadow:0 -8px 18px rgba(32,0,36,.06)}
  .drawer-footer .secondary-action,.drawer-footer .primary-action{height:46px;min-height:46px;max-height:46px;padding:0 12px;flex:1;line-height:1;white-space:nowrap}
  .drawer-footer .primary-action{font-size:14px}
}
</style>
