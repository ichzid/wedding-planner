<template>
  <AppLayout>
    <Head title="Checklist Pernikahan" />

    <!-- Header -->
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="page-title">Checklist Pernikahan</h1>
        <p class="page-sub">{{ doneCount }}/{{ totalCount }} tugas selesai · {{ progressPct }}%</p>
      </div>
      <button class="btn btn--primary" @click="openCreate">
        <i class="fa-solid fa-plus fa-xs"></i> Tambah
      </button>
    </div>

    <!-- Progress bar -->
    <div class="card" style="padding:16px var(--space-xl);margin-bottom:var(--space-xl);display:flex;align-items:center;gap:14px">
      <div style="flex:1">
        <div class="prog-track"><div class="prog-fill" :style="{ width: progressPct + '%' }"></div></div>
      </div>
      <span style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap">{{ progressPct }}%</span>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar__search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input v-model="searchQuery" type="text" placeholder="Cari persiapan..." class="form-input search-input">
      </div>
      <select v-model="filterBulan" class="form-input toolbar__select">
        <option value="">Semua Bulan</option>
        <option v-for="b in bulanOptions" :key="b" :value="b">{{ b }}</option>
      </select>
      <select v-model="filterStatus" class="form-input toolbar__select">
        <option value="">Semua Status</option>
        <option value="done">Selesai</option>
        <option value="pending">Belum</option>
      </select>
      <button v-if="filterBulan || filterStatus || searchQuery" class="btn btn--outline btn--sm" @click="resetFilters">
        <i class="fa-solid fa-xmark"></i> Reset
      </button>
    </div>

    <!-- Grouped list -->
    <div v-for="(items, bulan) in groupedChecklists" :key="bulan" class="group-section">
      <div class="group-header">
        <span class="group-label">{{ bulan }}</span>
        <span class="group-count">{{ items.filter(i => i.status).length }}/{{ items.length }}</span>
      </div>
      <div class="card" style="overflow:hidden">
        <div
          v-for="item in items"
          :key="item.id"
          class="checklist-row"
          :class="{ 'checklist-row--done': item.status }"
        >
          <button class="check-btn" @click="toggleItem(item)" :title="item.status ? 'Tandai belum' : 'Tandai selesai'">
            <i :class="item.status ? 'fa-solid fa-circle-check check-done' : 'fa-regular fa-circle check-pending'"></i>
          </button>
          <div class="checklist-content">
            <p class="checklist-task" :class="{ 'checklist-task--done': item.status }">{{ item.persiapan }}</p>
            <p v-if="item.detail" class="checklist-detail">{{ item.detail }}</p>
          </div>
          <div class="checklist-actions">
            <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(item)" :id="'edit-checklist-'+item.id">
              <i class="fa-solid fa-pen-to-square" style="font-size:13px;"></i>
            </button>
            <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(item)" :id="'del-checklist-'+item.id">
              <i class="fa-solid fa-trash" style="font-size:13px;"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="!Object.keys(groupedChecklists).length" class="card">
      <div class="empty-state">
        <i class="fa-solid fa-list-check empty-state__icon"></i>
        <p class="empty-state__text">Belum ada checklist pernikahan. Yuk, mulai rencanakan persiapanmu!</p>
        <button class="btn btn--primary" @click="openCreate">
          <i class="fa-solid fa-plus fa-xs"></i> Tambah Checklist Pertama
        </button>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">{{ editItem ? 'Edit Checklist' : 'Tambah Checklist' }}</h3>
            <button class="btn btn--icon btn--ghost" @click="closeModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <form @submit.prevent="save" class="modal-body">
            <div>
              <label class="form-label">Bulan *</label>
              <select v-model="form.bulan_range" required class="form-input">
                <option value="">Pilih bulan</option>
                <option v-for="b in bulanOptions" :key="b" :value="b">{{ b }}</option>
              </select>
              <p v-if="errors.bulan_range" class="form-error">{{ errors.bulan_range }}</p>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Persiapan *</label>
              <input v-model="form.persiapan" type="text" required class="form-input" placeholder="Booking venue...">
              <p v-if="errors.persiapan" class="form-error">{{ errors.persiapan }}</p>
            </div>
            <div style="margin-top:var(--space-md)">
              <label class="form-label">Detail</label>
              <textarea v-model="form.detail" rows="2" class="form-input" placeholder="Detail tambahan..."></textarea>
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
  checklists: Object,
  bulanOptions: Array,
});

const allItems = computed(() => {
  const items = [];
  for (const group of Object.values(props.checklists)) {
    items.push(...group);
  }
  return items;
});

const searchQuery  = ref('');
const filterBulan  = ref('');
const filterStatus = ref('');
const showModal = ref(false);
const editItem  = ref(null);
const saving    = ref(false);
const errors    = ref({});

const defaultForm = () => ({ bulan_range: '', persiapan: '', detail: '' });
const form = ref(defaultForm());

const doneCount  = computed(() => allItems.value.filter(i => i.status).length);
const totalCount = computed(() => allItems.value.length);
const progressPct = computed(() => {
  if (!totalCount.value) return 0;
  return Math.round(doneCount.value / totalCount.value * 100);
});

const filteredItems = computed(() => {
  let list = [...allItems.value];
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(i => i.persiapan?.toLowerCase().includes(q) || i.detail?.toLowerCase().includes(q));
  }
  if (filterBulan.value) list = list.filter(i => i.bulan_range === filterBulan.value);
  if (filterStatus.value === 'done')    list = list.filter(i => i.status);
  if (filterStatus.value === 'pending') list = list.filter(i => !i.status);
  return list;
});

const groupedChecklists = computed(() => {
  const groups = {};
  for (const item of filteredItems.value) {
    const key = item.bulan_range || 'Lainnya';
    if (!groups[key]) groups[key] = [];
    groups[key].push(item);
  }
  return groups;
});

function resetFilters() {
  searchQuery.value  = '';
  filterBulan.value  = '';
  filterStatus.value = '';
}

function openCreate() {
  editItem.value  = null;
  form.value      = defaultForm();
  errors.value    = {};
  showModal.value = true;
}

function openEdit(item) {
  editItem.value  = item;
  form.value      = { bulan_range: item.bulan_range, persiapan: item.persiapan, detail: item.detail || '' };
  errors.value    = {};
  showModal.value = true;
}

function closeModal() { showModal.value = false; }

function save() {
  saving.value = true;
  errors.value = {};
  const url    = editItem.value ? route('checklist.update', editItem.value.id) : route('checklist.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => { showToast(editItem.value ? 'Checklist diupdate.' : 'Checklist ditambahkan.'); closeModal(); saving.value = false; },
    onError: (errs) => { errors.value = errs; saving.value = false; },
  });
}

function toggleItem(item) {
  router.patch(route('checklist.toggle', item.id), {}, {
    preserveScroll: true,
    onSuccess: () => showToast(item.status ? 'Ditandai belum.' : 'Selesai! ✓'),
  });
}

function confirmDelete(item) {
  confirmDeleteDialog(() => {
    router.delete(route('checklist.destroy', item.id), {
      preserveScroll: true,
      onSuccess: () => showToast('Item dihapus.'),
    });
  });
}
</script>

<style scoped>
.toolbar {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  flex-wrap: wrap;
  margin-bottom: var(--space-xl);
}
.toolbar__search { position: relative; flex: 1; min-width: 180px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 12px; pointer-events: none; }
.search-input { padding-left: 30px; }
.toolbar__select { max-width: 160px; }

.group-section { margin-bottom: var(--space-lg); }
.group-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 2px var(--space-sm);
}
.group-label {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
}
.group-count {
  font-size: 11.5px;
  color: var(--text-dim);
  font-weight: 600;
}

.checklist-row {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  padding: 14px 18px;
  border-bottom: 1px solid #f5ece8;
  transition: background 0.15s;
}
.checklist-row:last-child { border-bottom: none; }
.checklist-row:hover { background: #fdf9f6; }
.checklist-row--done { background: var(--rose-pale); }

.check-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  flex-shrink: 0;
  font-size: 20px;
  line-height: 1;
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.check-btn:hover { transform: scale(1.15); }
.check-done { color: var(--ok-text); }
.check-pending { color: var(--text-dim); }

.checklist-content { flex: 1; min-width: 0; }
.checklist-task {
  font-size: 13.5px;
  font-weight: 500;
  color: var(--text);
}
.checklist-task--done {
  text-decoration: line-through;
  color: var(--text-dim);
}
.checklist-detail {
  font-size: 12px;
  color: var(--text-muted);
  margin-top: 2px;
}

.checklist-actions {
  display: flex;
  gap: 2px;
  flex-shrink: 0;
}
</style>