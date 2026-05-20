<template>
  <AppLayout>
    <Head title="Daftar Tamu" />
    <div>
    <!-- Page Header -->
    <div class="page-header">
      <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
          <h1 class="page-title">Daftar Tamu</h1>
          <p class="page-sub">Kelola undangan tamu pernikahan Anda</p>
        </div>
        <button class="btn btn--primary" @click="openAdd">
          <i class="fa-solid fa-plus"></i>
          Tambah Tamu
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card stat-card--total">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-body">
          <div class="stat-val">{{ totalTamu }}</div>
          <div class="stat-label">Total Tamu</div>
        </div>
      </div>
      <div class="stat-card stat-card--cpw">
        <div class="stat-icon"><i class="fa-solid fa-venus"></i></div>
        <div class="stat-body">
          <div class="stat-val">{{ tamuCpw }}</div>
          <div class="stat-label">Pihak {{ namaCpw }}</div>
        </div>
      </div>
      <div class="stat-card stat-card--cpp">
        <div class="stat-icon"><i class="fa-solid fa-mars"></i></div>
        <div class="stat-body">
          <div class="stat-val">{{ tamuCpp }}</div>
          <div class="stat-label">Pihak {{ namaCpp }}</div>
        </div>
      </div>
      <div class="stat-card stat-card--hadir">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-body">
          <div class="stat-val">{{ hadir }}</div>
          <div class="stat-label">Konfirmasi Hadir</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom:16px;">
      <div class="filter-bar">
        <div class="search-wrap">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
          <input v-model="search" type="text" placeholder="Cari nama tamu..." class="form-input search-input" id="search-tamu">
        </div>
        <select v-model="filterPihak" class="form-input filter-select" id="filter-pihak">
          <option value="">Semua Pihak</option>
          <option value="cpw">Pihak {{ namaCpw }}</option>
          <option value="cpp">Pihak {{ namaCpp }}</option>
          <option value="umum">Umum</option>
        </select>
        <select v-model="filterStatus" class="form-input filter-select" id="filter-status">
          <option value="">Semua Status</option>
          <option value="belum_dikirim">Belum Dikirim</option>
          <option value="sudah_dikirim">Sudah Dikirim</option>
          <option value="hadir">Konfirmasi Hadir</option>
          <option value="tidak_hadir">Tidak Hadir</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="section-header">
        <span class="section-title">
          <i class="fa-solid fa-list" style="color:var(--rose); margin-right:8px;"></i>
          Daftar Tamu ({{ filteredGuests.length }})
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
            <tr v-for="(guest, idx) in filteredGuests" :key="guest.id">
              <td style="color:var(--text-dim); font-size:12px;">{{ idx + 1 }}</td>
              <td>
                <span class="guest-name">{{ guest.nama_tamu }}</span>
              </td>
              <td>
                <span class="chip" :class="pihakChipClass(guest.pihak)">
                  <i :class="pihakIcon(guest.pihak)"></i>
                  {{ pihakLabel(guest.pihak) }}
                </span>
              </td>
              <td>
                <span class="chip" :class="statusChipClass(guest.status)">
                  <i :class="statusIcon(guest.status)"></i>
                  {{ statusLabel(guest.status) }}
                </span>
              </td>
              <td style="font-size:12.5px; color:var(--text-muted); max-width:200px;">
                {{ guest.catatan || '—' }}
              </td>
              <td style="text-align:center;">
                <div style="display:flex; gap:4px; justify-content:center;">
                  <button class="btn btn--ghost btn--icon" title="Edit" @click="openEdit(guest)" :id="'edit-tamu-'+guest.id">
                    <i class="fa-solid fa-pen-to-square" style="font-size:13px;"></i>
                  </button>
                  <button class="btn btn--danger-ghost btn--icon" title="Hapus" @click="confirmDelete(guest)" :id="'del-tamu-'+guest.id">
                    <i class="fa-solid fa-trash" style="font-size:13px;"></i>
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
      <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header">
            <span class="modal-title">
              <i class="fa-solid fa-user-plus" style="color:var(--rose); margin-right:8px;"></i>
              {{ isEditing ? 'Edit Tamu' : 'Tambah Tamu' }}
            </span>
            <button class="btn btn--ghost btn--icon" @click="closeModal">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm" style="display:flex; flex-direction:column; gap:16px;">
              <div>
                <label class="form-label">Nama Tamu <span style="color:var(--danger-text)">*</span></label>
                <input v-model="form.nama_tamu" type="text" class="form-input" placeholder="Contoh: Budi Santoso" id="input-nama-tamu" required>
                <p v-if="errors.nama_tamu" class="form-error">{{ errors.nama_tamu }}</p>
              </div>
              <div>
                <label class="form-label">Dari Pihak <span style="color:var(--danger-text)">*</span></label>
                <select v-model="form.pihak" class="form-input" id="input-pihak" required>
                  <option value="cpw">Pihak {{ namaCpw }} (CPW)</option>
                  <option value="cpp">Pihak {{ namaCpp }} (CPP)</option>
                  <option value="umum">Umum / Keduanya</option>
                </select>
                <p v-if="errors.pihak" class="form-error">{{ errors.pihak }}</p>
              </div>
              <div>
                <label class="form-label">Status Undangan <span style="color:var(--danger-text)">*</span></label>
                <select v-model="form.status" class="form-input" id="input-status" required>
                  <option value="belum_dikirim">Belum Dikirim</option>
                  <option value="sudah_dikirim">Sudah Dikirim</option>
                  <option value="hadir">Konfirmasi Hadir</option>
                  <option value="tidak_hadir">Tidak Hadir</option>
                </select>
                <p v-if="errors.status" class="form-error">{{ errors.status }}</p>
              </div>
              <div>
                <label class="form-label">Catatan</label>
                <input v-model="form.catatan" type="text" class="form-input" placeholder="Misal: Teman kuliah, Keluarga besar..." id="input-catatan">
              </div>
              <div class="modal-footer" style="padding:0; border:none; margin-top:4px;">
                <button type="button" class="btn btn--outline" @click="closeModal">Batal</button>
                <button type="submit" class="btn btn--primary" :disabled="submitting" style="margin-left:auto;">
                  <i class="fa-solid fa-floppy-disk"></i>
                  {{ submitting ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Tambah Tamu') }}
                </button>
              </div>
            </form>
          </div>
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
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';

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

const search       = ref('');
const filterPihak  = ref('');
const filterStatus = ref('');
const showModal    = ref(false);
const showDeleteModal = ref(false);
const deleteTarget    = ref(null);
const isEditing    = ref(false);
const editId       = ref(null);
const submitting   = ref(false);
const errors       = ref({});

const form = ref({ nama_tamu: '', pihak: 'cpw', status: 'belum_dikirim', catatan: '' });

const filteredGuests = computed(() => {
  return props.guests.filter(g => {
    const matchSearch = g.nama_tamu.toLowerCase().includes(search.value.toLowerCase());
    const matchPihak  = filterPihak.value ? g.pihak === filterPihak.value : true;
    const matchStatus = filterStatus.value ? g.status === filterStatus.value : true;
    return matchSearch && matchPihak && matchStatus;
  });
});

function openAdd() {
  isEditing.value = false;
  editId.value    = null;
  form.value      = { nama_tamu: '', pihak: 'cpw', status: 'belum_dikirim', catatan: '' };
  errors.value    = {};
  showModal.value = true;
}

function openEdit(guest) {
  isEditing.value = true;
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
    onSuccess: () => { showModal.value = false; submitting.value = false; showToast(isEditing.value ? 'Tamu berhasil diperbarui' : 'Tamu berhasil ditambahkan'); },
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
  if (pihak === 'cpw') return 'Pihak ' + props.namaCpw;
  if (pihak === 'cpp') return 'Pihak ' + props.namaCpp;
  return 'Umum';
}

function pihakIcon(pihak) {
  if (pihak === 'cpw') return 'fa-solid fa-venus';
  if (pihak === 'cpp') return 'fa-solid fa-mars';
  return 'fa-solid fa-users';
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

function statusIcon(status) {
  const map = { belum_dikirim: 'fa-solid fa-envelope', sudah_dikirim: 'fa-solid fa-paper-plane', hadir: 'fa-solid fa-circle-check', tidak_hadir: 'fa-solid fa-circle-xmark' };
  return map[status] || '';
}

function statusChipClass(status) {
  if (status === 'hadir')        return 'chip--ok';
  if (status === 'tidak_hadir')  return 'chip--danger';
  if (status === 'sudah_dikirim') return 'chip--warn';
  return 'chip--soft';
}

function showToast(msg) {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const el = document.createElement('div');
  el.textContent = msg;
  el.style.cssText = 'background:#1a1a18;color:#fff;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.2);pointer-events:auto;opacity:0;transition:opacity 0.2s;';
  container.appendChild(el);
  requestAnimationFrame(() => el.style.opacity = '1');
  setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }, 2500);
}
</script>

<style scoped>
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}
@media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .stats-row { grid-template-columns: 1fr 1fr; } }

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 18px;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: var(--surface);
  box-shadow: var(--shadow-sm);
}
.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}
.stat-card--total .stat-icon  { background: var(--rose-pale); color: var(--rose); }
.stat-card--cpw   .stat-icon  { background: #fdf0f8; color: #c4719e; }
.stat-card--cpp   .stat-icon  { background: #eef4ff; color: #5a82c4; }
.stat-card--hadir .stat-icon  { background: var(--ok-bg); color: var(--ok-text); }

.stat-val   { font-size: 22px; font-weight: 700; color: var(--text); line-height: 1; }
.stat-label { font-size: 12px; color: var(--text-muted); margin-top: 3px; }

.filter-bar {
  display: flex;
  gap: 10px;
  padding: 14px 16px;
  flex-wrap: wrap;
}
.search-wrap {
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
.filter-select { width: 160px; flex-shrink: 0; }
@media (max-width: 640px) { .filter-select { width: 100%; } }

.guest-name { font-weight: 500; color: var(--text); }

.chip--cpw   { background: #fdf0f8; color: #c4719e; border: 1px solid #f0c8e4; }
.chip--cpp   { background: #eef4ff; color: #5a82c4; border: 1px solid #c8d8f0; }
</style>
