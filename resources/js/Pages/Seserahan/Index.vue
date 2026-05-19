<template>
  <AppLayout>
    <Head title="List Seserahan" />

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="page-title">List Seserahan</h1>
        <p class="page-sub">{{ sudahBeli }}/{{ totalItem }} item sudah dibeli · Total {{ formatRp(totalHarga) }}</p>
      </div>
      <button class="btn btn--dark" @click="openCreate">
        <i class="fa-solid fa-plus fa-xs"></i> Tambah
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="card summary-card">
        <p class="summary-card__label">Total Item</p>
        <p class="summary-card__value">{{ totalItem }}</p>
        <p class="summary-card__sub">item seserahan</p>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Sudah Dibeli</p>
        <p class="summary-card__value">{{ sudahBeli }}</p>
        <div class="prog-track mt-2">
          <div class="prog-fill" :style="{ width: (totalItem ? Math.round(sudahBeli/totalItem*100) : 0) + '%' }"></div>
        </div>
      </div>
      <div class="card summary-card">
        <p class="summary-card__label">Total Harga</p>
        <p class="summary-card__value" style="font-size:18px">{{ formatRp(totalHarga) }}</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="searchQuery" type="text" placeholder="Cari nama item..." class="form-input search-input">
      </div>
      <select v-model="filterKategori" class="form-input toolbar__select">
        <option value="">Semua Kategori</option>
        <option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option>
      </select>
      <select v-model="filterUntuk" class="form-input toolbar__select">
        <option value="">Semua Mempelai</option>
        <option value="groom">Mempelai Pria</option>
        <option value="bride">Mempelai Wanita</option>
      </select>
      <select v-model="filterStatus" class="form-input toolbar__select">
        <option value="">Semua Status</option>
        <option value="sudah_dibeli">Sudah Dibeli</option>
        <option value="belum">Belum</option>
      </select>
      <button v-if="filterKategori || filterUntuk || filterStatus || searchQuery" class="btn btn--outline btn--sm" @click="resetFilters">
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
                <button class="sort-btn" @click="toggleSort('kategori')">Kategori <i :class="sortIcon('kategori')"></i></button>
              </th>
              <th>
                <button class="sort-btn" @click="toggleSort('nama_item')">Nama Item <i :class="sortIcon('nama_item')"></i></button>
              </th>
              <th>Untuk</th>
              <th class="text-right">Qty</th>
              <th class="text-right">
                <button class="sort-btn sort-btn--right" @click="toggleSort('harga')">Harga <i :class="sortIcon('harga')"></i></button>
              </th>
              <th class="text-right">Subtotal</th>
              <th>Status</th>
              <th class="text-center" style="width:80px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredItems" :key="item.id">
              <td class="mono-text">{{ item.no }}</td>
              <td><span class="chip chip--neutral">{{ item.kategori }}</span></td>
              <td>
                <p class="item-name">{{ item.nama_item }}</p>
                <p v-if="item.satuan" class="item-sub">{{ item.satuan }}</p>
              </td>
              <td>
                <span class="chip" :class="item.untuk === 'bride' ? 'chip--outline' : 'chip--neutral'">
                  {{ item.untuk === 'bride' ? 'Mempelai Wanita' : 'Mempelai Pria' }}
                </span>
              </td>
              <td class="text-right">{{ item.qty }}</td>
              <td class="text-right">{{ formatRp(item.harga) }}</td>
              <td class="text-right fw-600">{{ formatRp(item.qty * item.harga) }}</td>
              <td>
                <button class="chip" :class="item.status === 'sudah_dibeli' ? 'chip--ok' : 'chip--danger'" @click="toggleItem(item)" style="cursor:pointer;border:none">
                  {{ item.status === 'sudah_dibeli' ? 'Sudah Dibeli' : 'Belum' }}
                </button>
              </td>
              <td>
                <div style="display:flex;align-items:center;justify-content:center;gap:2px">
                  <button class="btn btn--icon btn--ghost" @click="openEdit(item)">
                    <i class="fa-solid fa-pen fa-xs"></i>
                  </button>
                  <button class="btn btn--icon btn--danger-ghost" @click="confirmDelete(item)">
                    <i class="fa-solid fa-trash fa-xs"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!filteredItems.length">
              <td colspan="9">
                <div class="empty-state">
                  <i class="fa-solid fa-gift empty-state__icon"></i>
                  <p class="empty-state__text">Belum ada item seserahan</p>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredItems.length">
            <tr class="tfoot-row">
              <td colspan="6" class="tfoot-label">Total Harga</td>
              <td class="text-right fw-700">{{ formatRp(filteredTotalHarga) }}</td>
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
            <h3 class="modal-title">{{ editItem ? 'Edit Seserahan' : 'Tambah Seserahan' }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
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
                <label class="form-label">Untuk *</label>
                <select v-model="form.untuk" required class="form-input">
                  <option value="groom">Mempelai Pria</option>
                  <option value="bride">Mempelai Wanita</option>
                </select>
              </div>
            </div>
            <div style="margin-top:12px">
              <label class="form-label">Nama Item *</label>
              <input v-model="form.nama_item" type="text" required class="form-input" placeholder="Cincin, Kain Batik...">
              <p v-if="errors.nama_item" class="form-error">{{ errors.nama_item }}</p>
            </div>
            <div class="form-row-3" style="margin-top:12px">
              <div>
                <label class="form-label">Qty *</label>
                <input v-model="form.qty" type="number" required min="1" class="form-input">
              </div>
              <div>
                <label class="form-label">Satuan</label>
                <input v-model="form.satuan" type="text" class="form-input" placeholder="pcs, set...">
              </div>
              <div>
                <label class="form-label">Harga (Rp) *</label>
                <input v-model="form.harga" type="number" required min="0" class="form-input">
              </div>
            </div>
            <div style="margin-top:12px">
              <label class="form-label">Status *</label>
              <select v-model="form.status" required class="form-input">
                <option value="belum">Belum</option>
                <option value="sudah_dibeli">Sudah Dibeli</option>
              </select>
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
  items: Array,
  kategoriOptions: Array,
  totalItem: Number,
  totalHarga: Number,
  sudahBeli: Number,
  belumBeli: Number,
});

const searchQuery   = ref('');
const filterKategori = ref('');
const filterUntuk   = ref('');
const filterStatus  = ref('');
const sortBy  = ref('');
const sortDir = ref('asc');
const showModal = ref(false);
const editItem  = ref(null);
const saving    = ref(false);
const errors    = ref({});

// Kategori from items
const kategoriOptions = computed(() => props.kategoriOptions || []);

const defaultForm = () => ({ kategori: '', nama_item: '', untuk: 'groom', qty: 1, satuan: '', harga: '', status: 'belum' });
const form = ref(defaultForm());

const filteredItems = computed(() => {
  let list = [...props.items];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(i => i.nama_item?.toLowerCase().includes(q));
  }
  if (filterKategori.value) list = list.filter(i => i.kategori === filterKategori.value);
  if (filterUntuk.value)    list = list.filter(i => i.untuk === filterUntuk.value);
  if (filterStatus.value)   list = list.filter(i => i.status === filterStatus.value);
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

const filteredTotalHarga = computed(() =>
  filteredItems.value.reduce((s, i) => s + (i.qty * i.harga), 0)
);

function formatRp(n) {
  return 'Rp' + Number(n || 0).toLocaleString('id-ID');
}

function resetFilters() {
  searchQuery.value    = '';
  filterKategori.value = '';
  filterUntuk.value    = '';
  filterStatus.value   = '';
}

function toggleSort(field) {
  if (sortBy.value === field) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  else { sortBy.value = field; sortDir.value = 'asc'; }
}

function sortIcon(field) {
  if (sortBy.value !== field) return 'fa-solid fa-sort text-dim fa-xs';
  return sortDir.value === 'asc' ? 'fa-solid fa-sort-up fa-xs' : 'fa-solid fa-sort-down fa-xs';
}

function openCreate() {
  editItem.value = null; form.value = defaultForm(); errors.value = {}; showModal.value = true;
}
function openEdit(item) {
  editItem.value = item;
  form.value = { kategori: item.kategori, nama_item: item.nama_item, untuk: item.untuk, qty: item.qty, satuan: item.satuan || '', harga: item.harga, status: item.status };
  errors.value = {}; showModal.value = true;
}
function closeModal() { showModal.value = false; }

function save() {
  saving.value = true; errors.value = {};
  const payload = { ...form.value, qty: Number(form.value.qty) || 1, harga: Number(form.value.harga) || 0 };
  const url    = editItem.value ? route('seserahan.update', editItem.value.id) : route('seserahan.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, payload, {
    preserveScroll: true,
    onSuccess: () => { showToast(editItem.value ? 'Item diupdate.' : 'Item ditambahkan.'); closeModal(); saving.value = false; },
    onError: (errs) => { errors.value = errs; saving.value = false; },
  });
}

function toggleItem(item) {
  router.patch(route('seserahan.toggle', item.id), {}, { preserveScroll: true, onSuccess: () => showToast('Status diupdate.') });
}

function confirmDelete(item) {
  confirmDeleteDialog(() => {
    router.delete(route('seserahan.destroy', item.id), { preserveScroll: true, onSuccess: () => showToast('Item dihapus.') });
  });
}
</script>

<style scoped>
.summary-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 12px; margin-bottom: 18px; }
@media (min-width: 640px) { .summary-grid { grid-template-columns: repeat(3, 1fr); } }
.summary-card { padding: 18px; }
.summary-card__label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--ink-400); }
.summary-card__value { font-size: 22px; font-weight: 800; color: var(--ink-900); margin-top: 6px; letter-spacing: -0.02em; }
.summary-card__sub { font-size: 11.5px; color: var(--ink-400); margin-top: 4px; }

.toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.toolbar__search { position: relative; flex: 1; min-width: 180px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--ink-400); font-size: 12px; pointer-events: none; }
.search-input { padding-left: 30px; }
.toolbar__select { max-width: 150px; }

.sort-btn { background: none; border: none; cursor: pointer; font-size: inherit; font-weight: inherit; color: inherit; display: inline-flex; align-items: center; gap: 5px; padding: 0; letter-spacing: inherit; text-transform: inherit; }
.sort-btn--right { margin-left: auto; display: flex; }

.mono-text { font-family: monospace; font-size: 11px; color: var(--ink-300); }
.item-name { font-size: 13.5px; font-weight: 500; color: var(--ink-800); }
.item-sub  { font-size: 11.5px; color: var(--ink-400); margin-top: 2px; }
.text-right { text-align: right; }
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.text-dim { color: var(--ink-300); }

.tfoot-row td { padding: 10px 14px; background: var(--ink-50); border-top: 2px solid var(--border); font-size: 13px; }
.tfoot-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink-500); }

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
@media (max-width: 480px) { .form-row-2, .form-row-3 { grid-template-columns: 1fr; } }
</style>
