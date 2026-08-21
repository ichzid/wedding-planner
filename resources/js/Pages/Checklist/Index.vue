<template>
  <AppLayout>
    <div class="checklist-page">
      <header class="hero-card">
        <div class="hero-card__content">
          <span class="eyebrow">Rencana hari bahagia</span>
          <h1>Daftar Persiapan</h1>
          <p>Susun dan pantau setiap detail menuju hari pernikahanmu.</p>
        </div>
        <button class="primary-action" @click="openCreate">
          <Plus aria-hidden="true" />
          <span>Tambah Persiapan</span>
        </button>
      </header>

      <section class="progress-card" aria-label="Progres persiapan">
        <span class="progress-card__icon"><ListChecks aria-hidden="true" /></span>
        <div class="progress-card__content">
          <div class="progress-meta">
            <div><strong>{{ doneCount }} dari {{ totalCount }}</strong><span> persiapan selesai</span></div>
            <strong>{{ progressPct }}%</strong>
          </div>
          <div class="progress-track"><span :style="{ width: progressPct + '%' }"></span></div>
        </div>
      </section>

      <section class="toolbar" aria-label="Pencarian dan filter">
        <div class="toolbar__top">
          <div class="toolbar__search">
            <Search aria-hidden="true" />
            <input v-model="searchQuery" type="text" placeholder="Cari persiapan..." class="toolbar-control search-input">
          </div>
          <button type="button" class="mobile-filter-toggle" :class="{ 'mobile-filter-toggle--active': mobileFiltersOpen || filterBulan || filterStatus }" :aria-expanded="mobileFiltersOpen" aria-controls="checklist-mobile-filters" aria-label="Tampilkan filter persiapan" @click="mobileFiltersOpen = !mobileFiltersOpen">
            <ListFilter aria-hidden="true" />
            <span v-if="filterBulan || filterStatus" class="mobile-filter-toggle__dot" aria-hidden="true"></span>
          </button>
        </div>
        <div id="checklist-mobile-filters" class="toolbar__filters" :class="{ 'toolbar__filters--open': mobileFiltersOpen }">
          <select v-model="filterBulan" class="toolbar-control toolbar__select" aria-label="Filter bulan">
            <option value="">Semua Bulan</option>
            <option v-for="b in bulanOptions" :key="b" :value="b">{{ b }}</option>
          </select>
          <select v-model="filterStatus" class="toolbar-control toolbar__select" aria-label="Filter status">
            <option value="">Semua Status</option>
            <option value="done">Selesai</option>
            <option value="pending">Belum Selesai</option>
          </select>
          <button v-if="filterBulan || filterStatus || searchQuery" class="secondary-action secondary-action--compact" @click="resetFilters">
            <X aria-hidden="true" /><span>Reset</span>
          </button>
          <button class="secondary-action" @click="exportToExcel">
            <FileSpreadsheet aria-hidden="true" /><span>Ekspor Excel</span>
          </button>
        </div>
      </section>

      <!-- Grouped list -->
    <div v-for="(items, bulan) in groupedChecklists" :key="bulan" class="group-section">
      <div class="group-header">
        <span class="group-label">{{ bulan }}</span>
        <span class="group-count">{{ items.filter(i => i.status).length }}/{{ items.length }}</span>
      </div>
      <div class="card checklist-dropzone" :class="{ 'checklist-dropzone--active': dragOverGroup === bulan }" @dragover.prevent="setGroupDragOver(bulan)" @drop="dropToGroup(bulan)">
        <div
          v-for="item in items"
          :key="item.id"
          :data-item-id="item.id"
          class="checklist-row draggable-row"
          :class="{ 'checklist-row--done': item.status, 'is-dragging': draggedId === item.id, 'is-drop-target': dragOverId === item.id, 'is-drop-before': dropPlacement(item.id) === 'before', 'is-drop-after': dropPlacement(item.id) === 'after', 'is-drag-disabled': !canDragRows }"
          :draggable="canDragRows && !isTouchMode"
          @dragstart="startDrag(item, $event)"
          @dragover.prevent="setDragOver(item)"
          @drop.stop="dropRow(item)"
          @dragend="endDrag"
        >
          <span
            class="drag-cell"
            @touchstart.stop="handleTouchStart"
            @touchmove.stop="handleTouchMove"
            @touchend.stop="handleTouchEnd"
            @touchcancel.stop="endDrag"
          ><GripVertical aria-hidden="true" /></span>
          <button class="check-btn" @click.stop="toggleItem(item)" :title="item.status ? 'Tandai belum selesai' : 'Tandai selesai'">
            <CircleCheckBig v-if="item.status" class="check-done" aria-hidden="true" />
            <Circle v-else class="check-pending" aria-hidden="true" />
          </button>
          <div class="checklist-content">
            <p class="checklist-task" :class="{ 'checklist-task--done': item.status }">{{ item.persiapan }}</p>
            <p v-if="item.detail" class="checklist-detail">{{ item.detail }}</p>
          </div>
          <div class="checklist-actions">
            <button class="icon-action" data-tooltip="Salin" :aria-label="`Salin persiapan ${item.persiapan}`" @click.stop="openCopy(item)" :id="'copy-checklist-'+item.id">
              <Copy class="action-icon action-icon--copy" aria-hidden="true" />
            </button>
            <button class="icon-action" data-tooltip="Edit" :aria-label="`Edit persiapan ${item.persiapan}`" @click.stop="openEdit(item)" :id="'edit-checklist-'+item.id">
              <Pencil class="action-icon action-icon--edit" aria-hidden="true" />
            </button>
            <button class="icon-action icon-action--danger" data-tooltip="Hapus" :aria-label="`Hapus persiapan ${item.persiapan}`" @click.stop="confirmDelete(item)" :id="'del-checklist-'+item.id">
              <Trash2 class="action-icon action-icon--delete" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
    </div>

      <!-- Empty state -->
      <div v-if="!Object.keys(groupedChecklists).length" class="empty-card">
        <span class="empty-card__icon"><ListChecks aria-hidden="true" /></span>
        <h2>Belum ada persiapan</h2>
        <p>{{ searchQuery || filterBulan || filterStatus ? 'Tidak ada persiapan yang sesuai dengan filter.' : 'Mulai susun daftar agar setiap detail hari bahagia tetap terpantau.' }}</p>
        <button v-if="searchQuery || filterBulan || filterStatus" class="secondary-action" @click="resetFilters"><X aria-hidden="true" />Reset Filter</button>
        <button v-else class="primary-action" @click="openCreate"><Plus aria-hidden="true" />Tambah Persiapan Pertama</button>
      </div>
    </div>

    <!-- Drawer form -->
    <Teleport to="body">
      <div v-if="showModal" class="drawer-backdrop" @click.self="closeModal">
        <aside class="checklist-drawer" role="dialog" aria-modal="true" :aria-labelledby="'checklist-drawer-title'">
          <div class="checklist-drawer__handle" aria-hidden="true"></div>
          <div class="checklist-drawer__header">
            <div>
              <span class="eyebrow">Daftar persiapan</span>
              <h3 id="checklist-drawer-title" class="drawer-title">{{ editItem ? 'Edit Persiapan' : (copyItem ? 'Salin Persiapan' : 'Tambah Persiapan') }}</h3>
            </div>
            <button class="icon-action" aria-label="Tutup" title="Tutup" @click="closeModal"><X aria-hidden="true" /></button>
          </div>
          <form @submit.prevent="save" class="checklist-drawer__body">

            <div class="form-field">
              <label class="form-label" for="prep-time">Target Waktu <span>*</span></label>
              <div class="field-control field-control--select">
                <CalendarClock aria-hidden="true" />
                <select id="prep-time" v-model="form.bulan_range" required class="form-input select-input">
                  <option value="">Pilih target waktu</option>
                  <option v-for="b in bulanOptions" :key="b" :value="b">{{ b }}</option>
                </select>
                <span class="select-chevron" aria-hidden="true"></span>
              </div>
              <p class="form-helper">Tentukan kapan persiapan ini sebaiknya diselesaikan.</p>
            </div>
            <div class="form-field">
              <label class="form-label" for="prep-name">Nama Persiapan <span>*</span></label>
              <div class="field-control">
                <TextCursorInput aria-hidden="true" />
                <input id="prep-name" v-model="form.persiapan" type="text" required class="form-input" placeholder="Contoh: Pesan lokasi resepsi">
              </div>
              <p class="form-helper">Gunakan nama singkat agar mudah ditemukan.</p>
            </div>
            <div class="form-field">
              <label class="form-label" for="prep-note">Catatan <small>Opsional</small></label>
              <div class="field-control field-control--textarea">
                <NotepadText aria-hidden="true" />
                <textarea id="prep-note" v-model="form.detail" rows="4" class="form-input" placeholder="Tambahkan detail, kontak, atau hal penting lainnya..."></textarea>
              </div>
            </div>
            <div class="checklist-drawer__footer">
              <button type="button" class="secondary-action" @click="closeModal">Batal</button>
              <button type="submit" class="primary-action" :disabled="saving">
                <LoaderCircle v-if="saving" class="spinner" aria-hidden="true" />
                {{ saving ? 'Menyimpan...' : 'Simpan Persiapan' }}
              </button>
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
import { CalendarClock, Circle, CircleCheckBig, Copy, FileSpreadsheet, GripVertical, ListChecks, ListFilter, LoaderCircle, NotepadText, Pencil, Plus, Search, TextCursorInput, Trash2, X } from '@lucide/vue';
import { showToast, confirmDeleteDialog } from '@/utils.js';
import * as XLSX from 'xlsx';

const props = defineProps({
  checklists: Object,
  bulanOptions: Array,
});

function flattenChecklists(checklists) {
  const items = [];
  for (const group of Object.values(checklists)) {
    items.push(...group);
  }
  return items;
}

const localItems = ref(flattenChecklists(props.checklists));

watch(() => props.checklists, (checklists) => {
  localItems.value = flattenChecklists(checklists);
}, { deep: true });

const allItems = computed(() => localItems.value);

const searchQuery  = ref('');
const mobileFiltersOpen = ref(false);
const filterBulan  = ref('');
const filterStatus = ref('');
const draggedIndex = ref(null);
const draggedId = ref(null);
const dragOverIndex = ref(null);
const dragOverId = ref(null);
const dragOverGroup = ref(null);
const isTouchMode = ref(false);
let touchModeQuery;

function syncTouchMode() {
  isTouchMode.value = touchModeQuery?.matches ?? false;
}

function handleDrawerKeydown(event) {
  if (event.key === 'Escape' && showModal.value) closeModal();
}

onMounted(() => {
  touchModeQuery = window.matchMedia('(hover: none), (pointer: coarse)');
  syncTouchMode();
  touchModeQuery.addEventListener?.('change', syncTouchMode);
  window.addEventListener('keydown', handleDrawerKeydown);
});

onBeforeUnmount(() => {
  touchModeQuery?.removeEventListener?.('change', syncTouchMode);
  window.removeEventListener('keydown', handleDrawerKeydown);
  document.body.style.overflow = '';
});

const showModal = ref(false);
watch(showModal, (isOpen) => {
  document.body.style.overflow = isOpen ? 'hidden' : '';
});

const editItem  = ref(null);
const copyItem  = ref(null);
const saving    = ref(false);

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

const canDragRows = computed(() => !searchQuery.value && !filterBulan.value && !filterStatus.value);

const groupOrder = computed(() => {
  const order = new Map();
  props.bulanOptions.forEach((bulan, index) => order.set(bulan, index));
  return order;
});

const orderedItems = computed(() => [...filteredItems.value].sort((a, b) => {
  const aOrder = groupOrder.value.get(a.bulan_range) ?? Number.MAX_SAFE_INTEGER;
  const bOrder = groupOrder.value.get(b.bulan_range) ?? Number.MAX_SAFE_INTEGER;
  if (aOrder !== bOrder) return aOrder - bOrder;
  return (a.no ?? 0) - (b.no ?? 0);
}));

const groupedChecklists = computed(() => {
  const groups = {};

  for (const item of orderedItems.value) {
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

function exportToExcel() {
  if (filteredItems.value.length === 0) {
    showToast('Tidak ada data untuk diexport');
    return;
  }

  const dateNow = new Date();
  const dateStr = dateNow.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

  const headers = ['No', 'Bulan/Target', 'Tugas', 'Detail', 'Status'];
  
  const dataRows = filteredItems.value.map((item, index) => [
    item.no || index + 1,
    item.bulan_range || '',
    item.persiapan || '',
    item.detail || '',
    item.status ? 'Selesai' : 'Belum'
  ]);
  
  const finalData = [
    ['DAFTAR PERSIAPAN PERNIKAHAN'],
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
    { wch: 20 }, // Bulan/Target
    { wch: 40 }, // Tugas
    { wch: 40 }, // Detail
    { wch: 15 }  // Status
  ];

  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Checklist");
  
  XLSX.writeFile(wb, `Checklist_Pernikahan_${dateNow.toISOString().split('T')[0]}.xlsx`);
  showToast('Data berhasil diexport ke Excel');
}

function openCreate() {
  editItem.value  = null;
  copyItem.value  = null;
  form.value      = defaultForm();
  showModal.value = true;
}

function openCopy(item) {
  editItem.value  = null;
  copyItem.value  = item;
  form.value      = { bulan_range: item.bulan_range, persiapan: item.persiapan, detail: item.detail || '' };
  showModal.value = true;
}

function openEdit(item) {
  editItem.value  = item;
  copyItem.value  = null;
  form.value      = { bulan_range: item.bulan_range, persiapan: item.persiapan, detail: item.detail || '' };
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editItem.value = null;
  copyItem.value = null;
}

function save() {
  saving.value = true;
  const url    = editItem.value ? route('checklist.update', editItem.value.id) : route('checklist.store');
  const method = editItem.value ? 'patch' : 'post';
  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(editItem.value ? 'Persiapan berhasil diperbarui.' : (copyItem.value ? 'Salinan persiapan berhasil ditambahkan.' : 'Persiapan berhasil ditambahkan.'));
      closeModal();
      saving.value = false;
    },
    onError: () => { saving.value = false; },
  });
}

function toggleItem(item) {
  const previousStatus = Boolean(item.status);
  const nextStatus = !previousStatus;
  const itemIndex = localItems.value.findIndex((row) => row.id === item.id);

  if (itemIndex !== -1) {
    localItems.value[itemIndex] = {
      ...localItems.value[itemIndex],
      status: nextStatus,
    };
    localItems.value = [...localItems.value];
  }

  router.patch(route('checklist.toggle', item.id), {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => showToast(nextStatus ? 'Persiapan berhasil diselesaikan.' : 'Persiapan ditandai belum selesai.'),
    onError: () => {
      if (itemIndex !== -1) {
        localItems.value[itemIndex] = {
          ...localItems.value[itemIndex],
          status: previousStatus,
        };
        localItems.value = [...localItems.value];
      }
      showToast('Status persiapan gagal diperbarui.');
    },
  });
}

function startDrag(item, event) {
  if (!canDragRows.value) return;
  draggedIndex.value = orderedItems.value.findIndex((row) => row.id === item.id);
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
    const itemId = Number(targetRow.dataset.itemId);
    const item = orderedItems.value.find(i => i.id === itemId);
    if (item) startDrag(item, null);
  }
}

function handleTouchMove(event) {
    if(!canDragRows.value || draggedId.value === null) return;
    event.preventDefault(); // Prevent scrolling while dragging
    const touch = event.touches[0];
    const targetEl = document.elementFromPoint(touch.clientX, touch.clientY);
    const targetRow = targetEl?.closest('.draggable-row');
    if (targetRow) {
        const itemId = Number(targetRow.dataset.itemId);
        const item = orderedItems.value.find(i => i.id === itemId);
        if (item) setDragOver(item);
    } else {
        const targetGroup = targetEl?.closest('.checklist-dropzone');
        if (targetGroup) {
             const groupLabel = targetGroup.previousElementSibling?.querySelector('.group-label')?.textContent;
             if(groupLabel) setGroupDragOver(groupLabel);
        }
    }
}

function setDragOver(item) {
  if (!canDragRows.value || draggedId.value === item.id) return;
  dragOverId.value = item.id;
  dragOverIndex.value = orderedItems.value.findIndex((row) => row.id === item.id);
  dragOverGroup.value = item.bulan_range;
}

function setGroupDragOver(bulan) {
  if (!canDragRows.value || draggedId.value === null) return;
  dragOverGroup.value = bulan;
}

function dropPlacement(id) {
  if (dragOverId.value !== id || draggedIndex.value === null || dragOverIndex.value === null) return null;
  return dragOverIndex.value > draggedIndex.value ? 'after' : 'before';
}

function persistReorder(reordered) {
  localItems.value = reordered.map((item, index) => ({ ...item, no: index + 1 }));

  router.patch(route('checklist.reorder'), {
    items: localItems.value.map((item) => ({ id: item.id, bulan_range: item.bulan_range })),
  }, {
    preserveScroll: true,
    onSuccess: () => showToast('Urutan checklist berhasil disimpan.'),
    onError: () => {
      localItems.value = flattenChecklists(props.checklists);
      showToast('Urutan checklist gagal disimpan.');
    },
  });
}

function dropRow(target) {
  const sourceIndex = orderedItems.value.findIndex((row) => row.id === draggedId.value);
  const targetIndex = orderedItems.value.findIndex((row) => row.id === target.id);
  if (!canDragRows.value || sourceIndex < 0 || targetIndex < 0 || sourceIndex === targetIndex) {
    endDrag();
    return;
  }

  const reordered = [...orderedItems.value];
  const [moved] = reordered.splice(sourceIndex, 1);
  moved.bulan_range = target.bulan_range;
  const insertIndex = sourceIndex < targetIndex ? targetIndex - 1 : targetIndex;
  reordered.splice(insertIndex, 0, moved);
  persistReorder(reordered);
  endDrag();
}

function dropToGroup(bulan) {
  const sourceIndex = orderedItems.value.findIndex((row) => row.id === draggedId.value);
  if (!canDragRows.value || sourceIndex < 0) {
    endDrag();
    return;
  }

  const reordered = [...orderedItems.value];
  const [moved] = reordered.splice(sourceIndex, 1);
  moved.bulan_range = bulan;
  const lastIndexInGroup = reordered.map((item) => item.bulan_range).lastIndexOf(bulan);
  reordered.splice(lastIndexInGroup + 1, 0, moved);
  persistReorder(reordered);
  endDrag();
}

function handleTouchEnd(event) {
    if (draggedId.value !== null) {
        if (dragOverId.value !== null) {
            const targetItem = orderedItems.value.find(i => i.id === dragOverId.value);
            if (targetItem) dropRow(targetItem);
        } else if (dragOverGroup.value !== null) {
            dropToGroup(dragOverGroup.value);
        } else {
            endDrag();
        }
    } else {
        endDrag();
    }
}

function endDrag() {
  draggedIndex.value = null;
  draggedId.value = null;
  dragOverIndex.value = null;
  dragOverId.value = null;
  dragOverGroup.value = null;
}

function confirmDelete(item) {
  confirmDeleteDialog(() => {
    router.delete(route('checklist.destroy', item.id), {
      preserveScroll: true,
      onSuccess: () => showToast('Item dihapus.'),
    });
  }, {
    title: 'Hapus persiapan ini?',
    description: `Persiapan “${item.persiapan}” akan dihapus permanen dari daftar persiapan.`,
  });
}
</script>

<style scoped>
.checklist-page { display: grid; gap: 22px; padding-bottom: 24px; }
.hero-card { display: flex; min-height: 190px; padding: 34px 38px; align-items: center; justify-content: space-between; gap: 28px; border: 1px solid var(--border); border-radius: 20px; background: radial-gradient(circle at 90% 20%, rgba(111,146,95,.24), transparent 34%), linear-gradient(135deg, #fff 0%, #eef4e8 100%); }
.hero-card__content { max-width: 620px; }
.eyebrow { display: block; margin-bottom: 7px; color: #66806b; font-size: 10px; font-weight: 750; letter-spacing: .1em; text-transform: uppercase; }
.hero-card h1 { color: var(--text); font-family: var(--font-display); font-size: clamp(28px, 3vw, 38px); line-height: 1.15; letter-spacing: -.025em; }
.hero-card p { margin-top: 9px; color: var(--text-muted); font-size: 14px; }
.primary-action, .secondary-action, .icon-action { display: inline-flex; align-items: center; justify-content: center; border: 0; font: inherit; cursor: pointer; transition: .2s ease; }
.primary-action { min-height: 43px; padding: 10px 16px; gap: 8px; border-radius: 10px; background: var(--accent); color: #fff; font-size: 12.5px; font-weight: 700; box-shadow: 0 6px 16px rgba(88,123,75,.2); }
.primary-action:hover { background: var(--accent-hover); transform: translateY(-1px); }.primary-action:disabled { opacity: .65; cursor: wait; transform: none; }
.primary-action svg, .secondary-action svg { width: 16px; height: 16px; }
.progress-card { display: flex; padding: 18px 20px; align-items: center; gap: 14px; border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow-sm); }
.progress-card__icon { display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center; border-radius: 10px; background: var(--accent-soft); color: var(--accent-hover); flex-shrink: 0; }.progress-card__icon svg { width: 19px; }
.progress-card__content { flex: 1; min-width: 0; }.progress-meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; color: var(--text-muted); font-size: 12px; }.progress-meta strong { color: var(--text); font-weight: 700; }
.progress-track { height: 7px; border-radius: 999px; background: var(--surface-muted); overflow: hidden; }.progress-track > span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--accent-hover), var(--accent-light)); transition: width .35s ease; }
.toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px; border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow-sm); }
.toolbar__top { display: flex; flex: 1; min-width: 0; }.toolbar__search { position: relative; flex: 1; min-width: 190px; }.toolbar__search > svg { position: absolute; top: 50%; left: 13px; width: 16px; color: var(--text-dim); transform: translateY(-50%); pointer-events: none; }.toolbar__filters { display: flex; align-items: center; gap: 8px; }
.mobile-filter-toggle { display: none; position: relative; width: 44px; height: 44px; flex: none; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 9px; background: var(--surface); color: var(--text-muted); cursor: pointer; }
.mobile-filter-toggle svg { width: 19px; height: 19px; }
.mobile-filter-toggle--active { border-color: var(--accent-light); background: var(--accent-soft); color: var(--accent-hover); }
.mobile-filter-toggle__dot { position: absolute; top: 7px; right: 7px; width: 6px; height: 6px; border: 1px solid var(--surface); border-radius: 999px; background: var(--accent-hover); }
.toolbar-control { min-height: 40px; padding: 9px 12px; border: 1px solid var(--border); border-radius: 9px; outline: 0; background: #fbfcfa; color: #526159; font-family: var(--font); font-size: 12.5px; font-weight: 500; line-height: 1.2; letter-spacing: 0; transition: .2s ease; }.toolbar-control:focus { border-color: #8ca18f; box-shadow: 0 0 0 3px rgba(102,126,105,.1); }.search-input { width: 100%; padding-left: 39px; }.toolbar__select { width: 145px; padding-right: 36px; appearance: none; -webkit-appearance: none; background-color: #fbfcfa; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2366786a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 11px center; background-size: 15px; }
.secondary-action { min-height: 40px; padding: 9px 13px; gap: 7px; border: 1px solid var(--border); border-radius: 9px; background: #fbfcfa; color: #526159; font-family: var(--font); font-size: 12.5px; font-weight: 500; line-height: 1.2; letter-spacing: 0; white-space: nowrap; }.secondary-action:hover { border-color: #aab9aa; color: #526c57; background: #f7f9f6; }.secondary-action--compact { padding-inline: 10px; }
.group-section { margin-top: 2px; }.group-header { display: flex; min-height: 36px; padding: 0 4px 8px; align-items: center; justify-content: space-between; }.group-label { color: #536e59; font-size: 11px; font-weight: 750; letter-spacing: .08em; text-transform: uppercase; }.group-count { padding: 4px 9px; border-radius: 999px; background: #edf2eb; color: #66786a; font-size: 10.5px; font-weight: 700; }
.checklist-dropzone { border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow-sm); transition: box-shadow .18s ease, background .18s ease; }.checklist-dropzone--active { background: #f2f6f0; box-shadow: inset 0 0 0 2px #77907b; }
.draggable-row { position: relative; cursor: grab; transition: background .18s ease, opacity .18s ease, transform .18s ease, box-shadow .18s ease; }.draggable-row.is-dragging { opacity: .45; transform: scale(.995); }.draggable-row.is-drop-target { background: #f2f6f0; }.draggable-row.is-drop-before { box-shadow: inset 0 3px 0 #718a75; }.draggable-row.is-drop-after { box-shadow: inset 0 -3px 0 #718a75; }.draggable-row.is-drag-disabled { cursor: default; }
.checklist-row { display: flex; min-height: 68px; padding: 13px 15px 13px 8px; align-items: center; gap: 11px; border-bottom: 1px solid #edf1eb; }.checklist-row:last-child { border-bottom: 0; }.checklist-row:hover { background: #fafcf9; }.checklist-row--done { background: #f5f8f3; }
.drag-cell { display: inline-flex; padding: 9px 7px; color: #a7b2a7; flex-shrink: 0; touch-action: none; cursor: grab; }.drag-cell svg { width: 16px; }.is-drag-disabled .drag-cell { opacity: .35; }
.check-btn { display: inline-flex; padding: 2px; border: 0; background: none; cursor: pointer; flex-shrink: 0; transition: transform .2s ease; }.check-btn:hover { transform: scale(1.1); }.check-btn svg { width: 21px; height: 21px; stroke-width: 1.8; }.check-done { color: #5d7863; }.check-pending { color: #aab4aa; }
.checklist-content { flex: 1; min-width: 0; }.checklist-task { color: var(--text); font-size: 13px; font-weight: 650; line-height: 1.45; }.checklist-task--done { color: var(--text-dim); text-decoration: line-through; }.checklist-detail { margin-top: 3px; color: var(--text-muted); font-size: 11.5px; line-height: 1.45; }
.checklist-actions { display: flex; gap: 3px; flex-shrink: 0; }.icon-action { width: 34px; height: 34px; border-radius: 8px; background: transparent; color: var(--text-dim); }.icon-action:hover { background: #edf2eb; color: #526c57; }.icon-action--danger:hover { background: #fff0ee; color: #a5544c; }.icon-action svg { width: 15px; height: 15px; }
.empty-card { display: flex; min-height: 270px; padding: 34px; flex-direction: column; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 14px; background: var(--surface); text-align: center; }.empty-card__icon { display: inline-flex; width: 48px; height: 48px; align-items: center; justify-content: center; border-radius: 13px; background: #e8eee5; color: #5d7863; }.empty-card__icon svg { width: 23px; }.empty-card h2 { margin-top: 14px; font-size: 16px; }.empty-card p { max-width: 410px; margin: 6px 0 18px; color: var(--text-muted); font-size: 12.5px; line-height: 1.6; }
.drawer-backdrop { position: fixed; inset: 0; z-index: 1000; display: flex; justify-content: flex-end; background: rgba(34, 45, 37, .28); backdrop-filter: blur(2px); animation: drawerFade .2s ease; }.checklist-drawer { width: min(440px, 100vw); height: 100dvh; display: flex; flex-direction: column; background: var(--surface); border-left: 1px solid var(--border); box-shadow: -18px 0 48px rgba(40, 54, 44, .16); animation: drawerFromRight .25s ease; }.checklist-drawer__handle { display: none; }.checklist-drawer__header { min-height: 86px; display: flex; align-items: center; justify-content: space-between; padding: 20px 22px; border-bottom: 1px solid var(--border); }.checklist-drawer__header .eyebrow { margin-bottom: 3px; font-size: 9px; }.drawer-title { color: var(--text); font-size: 18px; font-weight: 700; line-height: 1.25; }.checklist-drawer__body { flex: 1; display: flex; flex-direction: column; gap: 18px; min-height: 0; padding: 22px; overflow-y: auto; }.form-field { display: grid; gap: 7px; }.form-label { display: flex; align-items: center; gap: 5px; color: var(--text); font-size: 12.5px; font-weight: 650; }.form-label span { color: #708a74; }.form-label small { margin-left: auto; color: var(--text-dim); font-size: 10.5px; font-weight: 500; }.field-control { position: relative; display: flex; align-items: center; border: 1px solid var(--border); border-radius: 10px; background: #fbfcfa; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; }.field-control:focus-within { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px var(--accent-soft); }.field-control > svg { position: absolute; left: 13px; z-index: 1; width: 17px; height: 17px; color: #7a8b7e; pointer-events: none; }.checklist-drawer__body .field-control .form-input { min-height: 46px; width: 100%; padding: 10px 42px !important; border: 0 !important; border-radius: 10px; background: transparent !important; color: var(--text); font-family: var(--font); font-size: 13px; font-weight: 500; box-shadow: none !important; }.checklist-drawer__body .field-control .form-input:focus { border: 0 !important; outline: 0; box-shadow: none !important; }.checklist-drawer__body .field-control .form-input::placeholder { color: #9aa49d; font-size: 12.5px; font-weight: 400; }.select-input { padding-right: 44px !important; appearance: none !important; -webkit-appearance: none !important; background-image: none !important; cursor: pointer; }.select-chevron { position: absolute; right: 15px; width: 8px; height: 8px; border-right: 1.8px solid #66786a; border-bottom: 1.8px solid #66786a; transform: translateY(-2px) rotate(45deg); pointer-events: none; }.field-control--textarea { align-items: flex-start; }.field-control--textarea > svg { top: 14px; }.checklist-drawer__body .field-control textarea.form-input { min-height: 124px; padding-top: 12px !important; resize: vertical; line-height: 1.55; }.form-helper { margin: -1px 1px 0; color: var(--text-dim); font-size: 10.5px; line-height: 1.45; }.checklist-drawer__footer { display: flex; justify-content: flex-end; gap: 9px; margin: auto -22px -22px; padding: 18px 22px 22px; border-top: 1px solid var(--border); background: var(--surface); }.spinner { animation: spin .8s linear infinite; }@keyframes spin { to { transform: rotate(360deg); } }@keyframes drawerFade { from { opacity: 0; } }@keyframes drawerFromRight { from { transform: translateX(100%); } }
@media (max-width: 900px) { .toolbar { align-items: stretch; flex-direction: column; }.toolbar__filters { flex-wrap: wrap; }.toolbar__select { flex: 1; min-width: 150px; } }
@media (max-width: 767px) {
  .checklist-page { gap: 14px; padding-bottom: calc(18px + env(safe-area-inset-bottom)); }
  .hero-card { min-height: auto; padding: 18px 16px; align-items: stretch; flex-direction: column; gap: 14px; border-radius: 16px; }
  .hero-card h1 { font-size: 25px; }.hero-card p { margin-top: 5px; font-size: 12.5px; }.eyebrow { margin-bottom: 4px; }
  .hero-card .primary-action { width: 100%; min-height: 44px; }
  .progress-card { padding: 12px 14px; gap: 10px; }.progress-card__icon { width: 34px; height: 34px; }.progress-meta { margin-bottom: 6px; }.progress-track { height: 6px; }
  .toolbar { display: block !important; padding: 0; border: 0; background: transparent; box-shadow: none; }.toolbar__top { width: 100%; gap: 8px; }.toolbar__search { width: 100%; margin-bottom: 0; }.mobile-filter-toggle { display: flex; min-height: 44px; }.toolbar__filters { display: none; width: 100%; margin-top: 8px; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 8px; }.toolbar__filters--open { display: grid; }
  .toolbar__filters > * { width: 100% !important; max-width: none !important; min-width: 0 !important; min-height: 44px; }.toolbar__filters .secondary-action:last-child { grid-column: 1 / -1; }.toolbar-control { min-height: 44px; }.secondary-action--compact { grid-column: auto; }
  .group-header { padding-inline: 2px; }.card.checklist-dropzone { display: grid; gap: 14px; padding: 0; border: 0 !important; border-radius: 0; background: transparent !important; box-shadow: none !important; overflow: visible !important; }
  .checklist-row { min-height: 0; margin: 0 2px; padding: 10px 8px; align-items: flex-start; gap: 4px; border: 1px solid var(--border); border-radius: 12px; background: var(--surface); box-shadow: 0 3px 10px rgba(38, 51, 36, .08); }
  .checklist-row:last-child { border-bottom: 1px solid var(--border); }.checklist-row:hover { background: var(--surface); }.checklist-row--done, .checklist-row--done:hover { background: #f5f8f3; }
  .drag-cell, .check-btn, .icon-action { min-width: 40px; width: 40px; height: 40px; align-items: center; justify-content: center; padding: 0; }
  .drag-cell { margin-top: 0; }.drag-cell svg { width: 17px; }.check-btn { margin-top: 0; }.checklist-content { padding: 3px 4px 4px 0; }
  .checklist-actions { display: flex; flex: 0 0 100%; order: 5; justify-content: flex-end; gap: 2px; padding: 2px 0 0; border-top: 0; }
  .checklist-row { flex-wrap: wrap; }.checklist-content { flex-basis: calc(100% - 88px); }
  .drawer-backdrop { align-items: flex-end; justify-content: stretch; }.checklist-drawer { width: 100%; height: auto; max-height: 92dvh; border-left: 0; border-top: 1px solid var(--border); border-radius: 20px 20px 0 0; box-shadow: 0 -18px 48px rgba(40, 54, 44, .16); animation: drawerFromBottom .25s ease; }
  .checklist-drawer__handle { display: block; width: 40px; height: 4px; margin: 9px auto 0; border-radius: 999px; background: var(--border); }.checklist-drawer__header { min-height: 68px; padding: 10px 16px 13px; }
  .checklist-drawer__header .icon-action { width: 44px; height: 44px; }.checklist-drawer__body { gap: 14px; padding: 16px 16px calc(16px + env(safe-area-inset-bottom)); overflow-y: auto; }
  .checklist-drawer__body .form-input { min-height: 44px; font-size: 16px; }.checklist-drawer__footer { position: sticky; bottom: calc(-16px - env(safe-area-inset-bottom)); flex-direction: row; margin: auto -16px calc(-16px - env(safe-area-inset-bottom)); padding: 12px 16px calc(16px + env(safe-area-inset-bottom)); background: var(--surface); }.checklist-drawer__footer > button { flex: 1; min-height: 44px; }
  @keyframes drawerFromBottom { from { transform: translateY(100%); } }
}
@media (max-width: 520px) { .progress-card__icon { display: none; }.progress-meta span { display: none; } }
</style>