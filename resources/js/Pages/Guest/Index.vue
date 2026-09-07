<template>
  <AppLayout>
    <div class="guest-page">
      <PlannerHero eyebrow="Orang-orang terkasih" title="Daftar Undangan" description="Kelola daftar tamu dan pantau konfirmasi kehadiran dalam satu tempat."><template #action><button class="primary-action" type="button" @click="openCreate"><Plus aria-hidden="true" />Tambah Tamu</button></template></PlannerHero>

      <PlannerSummaryGrid aria-label="Ringkasan daftar tamu" style="--summary-columns:4">
        <article class="summary-card"><span class="summary-icon"><UsersRound aria-hidden="true" /></span><div><p class="summary-label">Total Tamu</p><p class="summary-value">{{ totalTamu }}</p><p class="summary-sub">{{ hadir }} konfirmasi hadir</p></div></article>
        <article class="summary-card"><span class="summary-icon summary-icon--cpw"><UserRound aria-hidden="true" /></span><div><p class="summary-label">Calon Pengantin Wanita</p><p class="summary-value">{{ tamuCpw }}</p><p class="summary-sub">{{ namaCpw || 'Pihak wanita' }}</p></div></article>
        <article class="summary-card"><span class="summary-icon summary-icon--cpp"><UserRound aria-hidden="true" /></span><div><p class="summary-label">Calon Pengantin Pria</p><p class="summary-value">{{ tamuCpp }}</p><p class="summary-sub">{{ namaCpp || 'Pihak pria' }}</p></div></article>
        <article class="summary-card"><span class="summary-icon summary-icon--ok"><UserRoundCheck aria-hidden="true" /></span><div><p class="summary-label">Konfirmasi Hadir</p><p class="summary-value">{{ hadir }}</p><div class="progress-track"><span :style="{ width: attendancePct + '%' }"></span></div></div></article>
      </PlannerSummaryGrid>

      <PlannerDataPanel>
        <section class="toolbar" aria-label="Pencarian dan filter tamu">
          <div class="toolbar-top"><div class="search-wrap"><Search aria-hidden="true" /><input v-model="search" class="toolbar-control" type="search" placeholder="Cari nama tamu atau catatan..." aria-label="Cari tamu"></div><button class="filter-toggle" :class="{ active: mobileFiltersOpen || filterPihak || filterStatus }" type="button" :aria-expanded="mobileFiltersOpen" aria-controls="guest-filters" aria-label="Tampilkan filter tamu" @click="mobileFiltersOpen = !mobileFiltersOpen"><ListFilter aria-hidden="true" /><span v-if="filterPihak || filterStatus" aria-hidden="true" /></button></div>
          <div id="guest-filters" class="toolbar-filters" :class="{ open: mobileFiltersOpen }">
            <label class="select-wrap"><select v-model="filterPihak" class="toolbar-control" aria-label="Filter pihak tamu"><option value="">Semua Pihak</option><option value="cpw">Calon Pengantin Wanita</option><option value="cpp">Calon Pengantin Pria</option><option value="umum">Umum</option></select><ChevronDown aria-hidden="true" /></label>
            <label class="select-wrap"><select v-model="filterStatus" class="toolbar-control" aria-label="Filter status undangan"><option value="">Semua Status</option><option value="belum_dikirim">Belum Dikirim</option><option value="sudah_dikirim">Sudah Dikirim</option><option value="hadir">Konfirmasi Hadir</option><option value="tidak_hadir">Tidak Hadir</option></select><ChevronDown aria-hidden="true" /></label>
            <button v-if="hasFilters" class="secondary-action" type="button" @click="resetFilters"><X aria-hidden="true" />Reset</button>
            <button class="secondary-action export-action" type="button" @click="exportToExcel"><FileSpreadsheet aria-hidden="true" />Ekspor Excel</button>
          </div>
        </section>

        <div class="desktop-table">
          <table class="tbl"><thead><tr><th>#</th><th>Nama Tamu</th><th>Pihak</th><th>Status Undangan</th><th>Catatan</th><th class="center">Aksi</th></tr></thead><tbody>
            <tr v-for="(guest,index) in filteredGuests" :key="guest.id" class="draggable-row" :class="rowClasses(guest)" :draggable="canDragRows" @dragstart="startDrag(guest,index,$event)" @dragover.prevent="setDragOver(guest,index)" @drop.prevent="dropRow(index)" @dragend="endDrag">
              <td class="drag-cell" @touchstart.stop="handleTouchStart($event,guest,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical aria-hidden="true" /> {{ guest.no || index + 1 }}</td><td><strong>{{ guest.nama_tamu }}</strong></td><td><span class="chip party-chip" :class="pihakChipClass(guest.pihak)"><Venus v-if="guest.pihak === 'cpw'" aria-hidden="true" /><Mars v-if="guest.pihak === 'cpp'" aria-hidden="true" />{{ pihakLabel(guest.pihak) }}</span></td><td><span class="chip" :class="statusChipClass(guest.status)">{{ statusLabel(guest.status) }}</span></td><td class="note">{{ guest.catatan || '–' }}</td><td><div class="actions"><button class="icon-action" type="button" data-tooltip="Salin" :aria-label="`Salin tamu ${guest.nama_tamu}`" @click="openCopy(guest)"><Copy aria-hidden="true" /></button><button class="icon-action" type="button" data-tooltip="Edit" :aria-label="`Edit tamu ${guest.nama_tamu}`" @click="openEdit(guest)"><Pencil aria-hidden="true" /></button><button class="icon-action danger" type="button" data-tooltip="Hapus" :aria-label="`Hapus tamu ${guest.nama_tamu}`" @click="confirmDelete(guest)"><Trash2 aria-hidden="true" /></button></div></td>
            </tr></tbody><tfoot v-if="filteredGuests.length"><tr><td colspan="4">{{ filteredGuests.length }} tamu ditampilkan</td><td class="strong">{{ filteredHadir }} hadir</td><td></td></tr></tfoot></table>
          <PlannerEmptyState v-if="!filteredGuests.length" :title="hasFilters ? 'Tamu tidak ditemukan' : 'Belum ada tamu undangan'" :description="hasFilters ? 'Tidak ada tamu yang sesuai dengan pencarian atau filter.' : 'Mulai susun daftar orang terkasih yang akan diundang.'"><template #icon><UsersRound /></template><template #action><button type="button" :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Tamu Pertama' }}</button></template></PlannerEmptyState>
        </div>

        <section class="mobile-records" aria-label="Daftar tamu undangan">
          <article v-for="(guest,index) in filteredGuests" :key="guest.id" class="record-card" :class="statusChipClass(guest.status)"><div class="record-head"><div><span class="category">Tamu #{{ guest.no || index + 1 }}</span><h2>{{ guest.nama_tamu }}</h2></div><span class="chip" :class="statusChipClass(guest.status)">{{ statusLabel(guest.status) }}</span></div><dl><div><dt>Pihak</dt><dd class="party-label"><Venus v-if="guest.pihak === 'cpw'" aria-hidden="true" /><Mars v-if="guest.pihak === 'cpp'" aria-hidden="true" />{{ pihakLabel(guest.pihak) }}</dd></div><div><dt>Kehadiran</dt><dd>{{ statusLabel(guest.status) }}</dd></div></dl><div v-if="guest.catatan" class="record-contact"><span>Kontak / catatan</span><p>{{ guest.catatan }}</p></div><div class="record-footer"><button class="drag-handle" type="button" :disabled="!canDragRows" :aria-label="`Geser urutan tamu ${guest.nama_tamu}`" @touchstart.stop="handleTouchStart($event,guest,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical aria-hidden="true" />Urutan {{ guest.no || index + 1 }}</button><div class="actions"><button class="icon-action" type="button" :aria-label="`Salin tamu ${guest.nama_tamu}`" @click="openCopy(guest)"><Copy /></button><button class="icon-action" type="button" :aria-label="`Edit tamu ${guest.nama_tamu}`" @click="openEdit(guest)"><Pencil /></button><button class="icon-action danger" type="button" :aria-label="`Hapus tamu ${guest.nama_tamu}`" @click="confirmDelete(guest)"><Trash2 /></button></div></div></article>
          <PlannerEmptyState v-if="!filteredGuests.length" :title="hasFilters ? 'Tamu tidak ditemukan' : 'Belum ada tamu undangan'" :description="hasFilters ? 'Tidak ada tamu yang sesuai dengan pencarian atau filter.' : 'Mulai susun daftar orang terkasih yang akan diundang.'"><template #icon><UsersRound /></template><template #action><button type="button" :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Tamu Pertama' }}</button></template></PlannerEmptyState>
        </section>
      </PlannerDataPanel>
    </div>

    <Teleport to="body"><div v-if="showDrawer" class="drawer-backdrop" @click.self="closeDrawer"><aside class="drawer" role="dialog" aria-modal="true" aria-labelledby="guest-drawer-title"><div class="drawer-handle" aria-hidden="true"></div><header class="drawer-header"><div><span class="eyebrow">Daftar undangan</span><h3 id="guest-drawer-title">{{ editGuest ? 'Edit Tamu' : copyGuest ? 'Salin Tamu' : 'Tambah Tamu' }}</h3></div><button class="icon-action" type="button" aria-label="Tutup form tamu" @click="closeDrawer"><X /></button></header>
      <form class="drawer-body" @submit.prevent="save"><div class="field"><label for="guest-name">Nama Tamu <span>*</span></label><div class="field-control"><ContactRound aria-hidden="true" /><input id="guest-name" v-model="form.nama_tamu" required maxlength="255" placeholder="Contoh: Budi Santoso"></div></div><div class="form-row"><div class="field"><label for="guest-side">Dari Pihak <span>*</span></label><div class="field-control"><UsersRound aria-hidden="true" /><select id="guest-side" v-model="form.pihak" required><option value="cpw">Calon Pengantin Wanita</option><option value="cpp">Calon Pengantin Pria</option><option value="umum">Umum / Keduanya</option></select><ChevronDown aria-hidden="true" /></div></div><div class="field"><label for="guest-status">Status Undangan <span>*</span></label><div class="field-control"><CircleGauge aria-hidden="true" /><select id="guest-status" v-model="form.status" required><option value="belum_dikirim">Belum Dikirim</option><option value="sudah_dikirim">Sudah Dikirim</option><option value="hadir">Konfirmasi Hadir</option><option value="tidak_hadir">Tidak Hadir</option></select><ChevronDown aria-hidden="true" /></div></div></div><div class="field"><label for="guest-note">Catatan</label><div class="field-control textarea"><NotepadText aria-hidden="true" /><textarea id="guest-note" v-model="form.catatan" maxlength="500" rows="4" placeholder="Contoh: Teman kuliah atau keluarga besar"></textarea></div></div><footer class="drawer-footer"><button class="secondary-action" type="button" @click="closeDrawer">Batal</button><button class="primary-action" type="submit" :disabled="saving"><LoaderCircle v-if="saving" class="spinner" aria-hidden="true" />{{ saving ? 'Menyimpan...' : 'Simpan Tamu' }}</button></footer></form></aside></div></Teleport>
  </AppLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import PlannerHero from '@/Components/PlannerHero.vue';
import PlannerSummaryGrid from '@/Components/PlannerSummaryGrid.vue';
import PlannerDataPanel from '@/Components/PlannerDataPanel.vue';
import PlannerEmptyState from '@/Components/PlannerEmptyState.vue';
import { ChevronDown, CircleGauge, ContactRound, Copy, FileSpreadsheet, GripVertical, ListFilter, LoaderCircle, Mars, NotepadText, Pencil, Plus, Search, Trash2, UserRound, UserRoundCheck, UsersRound, Venus, X } from '@lucide/vue';
import { confirmDeleteDialog, showToast } from '@/utils.js';
import * as XLSX from 'xlsx';

const props = defineProps({ guests:Array, totalTamu:Number, tamuCpw:Number, tamuCpp:Number, tamuUmum:Number, hadir:Number, namaCpw:String, namaCpp:String });
const localGuests=ref([...props.guests]),search=ref(''),filterPihak=ref(''),filterStatus=ref(''),mobileFiltersOpen=ref(false),showDrawer=ref(false),editGuest=ref(null),copyGuest=ref(null),saving=ref(false),draggedIndex=ref(null),draggedId=ref(null),dragOverIndex=ref(null),dragOverId=ref(null);
const defaultForm=()=>({nama_tamu:'',pihak:'cpw',status:'belum_dikirim',catatan:''}),form=ref(defaultForm());
watch(()=>props.guests,v=>localGuests.value=[...v]);watch(showDrawer,v=>document.body.style.overflow=v?'hidden':'');
onMounted(()=>window.addEventListener('keydown',handleKey));onBeforeUnmount(()=>{window.removeEventListener('keydown',handleKey);document.body.style.overflow=''});function handleKey(e){if(e.key==='Escape'&&showDrawer.value)closeDrawer()}
const attendancePct=computed(()=>props.totalTamu?Math.round(props.hadir/props.totalTamu*100):0),hasFilters=computed(()=>Boolean(search.value||filterPihak.value||filterStatus.value));
const filteredGuests=computed(()=>{const q=search.value.trim().toLowerCase();return localGuests.value.filter(g=>(!q||g.nama_tamu?.toLowerCase().includes(q)||g.catatan?.toLowerCase().includes(q))&&(!filterPihak.value||g.pihak===filterPihak.value)&&(!filterStatus.value||g.status===filterStatus.value))});
const filteredHadir=computed(()=>filteredGuests.value.filter(g=>g.status==='hadir').length);
const canDragRows=computed(()=>!hasFilters.value);const pihakLabel=v=>({cpw:'CPW',cpp:'CPP',umum:'Umum'}[v]||'Umum');const pihakChipClass=v=>v==='cpw'?'chip--cpw':v==='cpp'?'chip--cpp':'chip--neutral';const statusLabel=v=>({belum_dikirim:'Belum Dikirim',sudah_dikirim:'Sudah Dikirim',hadir:'Konfirmasi Hadir',tidak_hadir:'Tidak Hadir'}[v]||v);const statusChipClass=v=>v==='hadir'?'chip--ok':v==='tidak_hadir'?'chip--danger':v==='sudah_dikirim'?'chip--warn':'chip--soft';
function resetFilters(){search.value='';filterPihak.value='';filterStatus.value=''}function guestForm(g){return{nama_tamu:g.nama_tamu,pihak:g.pihak,status:g.status,catatan:g.catatan||''}}function openCreate(){editGuest.value=null;copyGuest.value=null;form.value=defaultForm();showDrawer.value=true}function openCopy(g){editGuest.value=null;copyGuest.value=g;form.value=guestForm(g);showDrawer.value=true}function openEdit(g){editGuest.value=g;copyGuest.value=null;form.value=guestForm(g);showDrawer.value=true}function closeDrawer(){showDrawer.value=false;editGuest.value=null;copyGuest.value=null}
function save(){if(saving.value)return;saving.value=true;const editing=editGuest.value;router[editing?'patch':'post'](editing?route('tamu.update',editing.id):route('tamu.store'),form.value,{preserveScroll:true,onSuccess:()=>{showToast(editing?'Tamu berhasil diperbarui.':copyGuest.value?'Salinan tamu berhasil ditambahkan.':'Tamu berhasil ditambahkan.');closeDrawer()},onFinish:()=>saving.value=false})}
function confirmDelete(g){confirmDeleteDialog(()=>router.delete(route('tamu.destroy',g.id),{preserveScroll:true,onSuccess:()=>showToast('Tamu berhasil dihapus.')}),{title:'Hapus undangan ini?',description:`Undangan untuk “${g.nama_tamu}” akan dihapus permanen dari daftar undangan.`})}
function rowClasses(g){return{'is-dragging':draggedId.value===g.id,'is-drop-before':dropPlacement(g.id)==='before','is-drop-after':dropPlacement(g.id)==='after','is-drag-disabled':!canDragRows.value}}function startDrag(g,index,e){if(!canDragRows.value)return;draggedIndex.value=index;draggedId.value=g.id;if(e?.dataTransfer)e.dataTransfer.effectAllowed='move'}function handleTouchStart(_,g,index){if(canDragRows.value)startDrag(g,index)}function handleTouchMove(e){if(draggedId.value===null)return;e.preventDefault();const row=document.elementFromPoint(e.touches[0].clientX,e.touches[0].clientY)?.closest('.record-card,tr.draggable-row');if(!row)return;const cards=[...document.querySelectorAll('.mobile-records .record-card')],index=cards.includes(row)?cards.indexOf(row):[...row.parentNode.children].indexOf(row),item=filteredGuests.value[index];if(item)setDragOver(item,index)}function handleTouchEnd(){dragOverIndex.value!==null?dropRow(dragOverIndex.value):endDrag()}function setDragOver(g,index){if(canDragRows.value&&draggedId.value!==g.id){dragOverId.value=g.id;dragOverIndex.value=index}}function dropPlacement(id){if(dragOverId.value!==id)return null;return dragOverIndex.value>draggedIndex.value?'after':'before'}
function dropRow(index){if(!canDragRows.value||draggedIndex.value===null||index===draggedIndex.value){endDrag();return}const previous=[...localGuests.value],list=[...localGuests.value],[moved]=list.splice(draggedIndex.value,1);list.splice(index,0,moved);localGuests.value=list.map((g,i)=>({...g,no:i+1}));router.patch(route('tamu.reorder'),{ids:localGuests.value.map(g=>g.id)},{preserveScroll:true,onSuccess:()=>showToast('Urutan tamu berhasil disimpan.'),onError:()=>{localGuests.value=previous;showToast('Urutan tamu gagal disimpan.')}});endDrag()}function endDrag(){draggedIndex.value=draggedId.value=dragOverIndex.value=dragOverId.value=null}
function exportToExcel(){if(!filteredGuests.value.length){showToast('Tidak ada data untuk diekspor.');return}const now=new Date(),rows=filteredGuests.value.map((g,i)=>[g.no||i+1,g.nama_tamu||'',pihakLabel(g.pihak),statusLabel(g.status),g.catatan||'']),ws=XLSX.utils.aoa_to_sheet([['DAFTAR TAMU UNDANGAN'],[`Dicetak pada: ${now.toLocaleString('id-ID')}`],[],['No','Nama Tamu','Pihak','Status Undangan','Catatan'],...rows]);ws['!merges']=[{s:{r:0,c:0},e:{r:0,c:4}},{s:{r:1,c:0},e:{r:1,c:4}}];ws['!cols']=[{wch:5},{wch:30},{wch:24},{wch:22},{wch:40}];const wb=XLSX.utils.book_new();XLSX.utils.book_append_sheet(wb,ws,'Daftar Undangan');XLSX.writeFile(wb,`Daftar_Undangan_${now.toISOString().split('T')[0]}.xlsx`);showToast('Data berhasil diekspor ke Excel.')}
</script>

<style scoped>
.guest-page{display:grid;width:min(1200px,calc(100% - 48px));margin:0 auto;gap:24px;padding-bottom:32px;color:#1a3300}.hero-card{display:flex;min-height:190px;padding:34px 38px;align-items:center;justify-content:space-between;gap:28px;border:1px solid #b6b6b6;border-radius:12px;background:#fcfaf5}.eyebrow{display:block;margin-bottom:7px;color:var(--accent-hover);font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}.hero-card h1{font-family:var(--font-display);font-size:clamp(28px,3vw,38px);line-height:1.15}.hero-card p{margin-top:9px;color:var(--text-muted);font-size:14px}.primary-action,.secondary-action,.icon-action,.filter-toggle,.drag-handle{display:inline-flex;align-items:center;justify-content:center;border:0;font:inherit;cursor:pointer}.primary-action{min-height:43px;padding:10px 16px;gap:8px;border-radius:10px;background:var(--accent);color:#fff;font-size:13px;font-weight:700}.primary-action:hover{background:var(--accent-hover)}.primary-action:disabled{opacity:.65;cursor:wait}.primary-action svg,.secondary-action svg{width:16px}.summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.summary-card{display:flex;min-width:0;padding:18px 20px;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.summary-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:10px;background:var(--accent-soft);color:var(--accent-hover)}.summary-icon svg{width:20px}.summary-icon--cpw{background:#fdf0f8;color:#c4719e}.summary-icon--cpp{background:#eef4ff;color:#5a82c4}.summary-icon--ok{background:var(--ok-bg);color:var(--ok-text)}.summary-label{font-size:12px;font-weight:600;color:var(--text-muted)}.summary-value{margin-top:5px;font-size:clamp(19px,2vw,25px);font-weight:800}.summary-sub{margin-top:4px;color:var(--text-muted);font-size:12px}.progress-track{height:6px;margin-top:7px;overflow:hidden;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;height:100%;background:linear-gradient(90deg,var(--accent-hover),var(--accent-light))}.data-panel{overflow:hidden;border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:var(--shadow-sm)}.toolbar{display:flex;padding:12px;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border)}.toolbar-top{display:flex;flex:1}.search-wrap{position:relative;flex:1}.search-wrap>svg{position:absolute;left:13px;top:50%;width:16px;transform:translateY(-50%);color:var(--text-dim)}.toolbar-control,.secondary-action{box-sizing:border-box;height:40px;min-height:40px;padding:0 12px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted);font:500 13px var(--font);line-height:1}.search-wrap input{width:100%;padding-left:39px}.toolbar-filters{display:flex;gap:8px}.select-wrap{position:relative;display:flex}.select-wrap select{width:180px;padding-right:34px;appearance:none}.select-wrap>svg{position:absolute;right:11px;top:50%;width:15px;transform:translateY(-50%);pointer-events:none}.secondary-action{gap:7px}.filter-toggle{display:none;position:relative;width:44px;height:44px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted)}.filter-toggle svg{width:19px}.filter-toggle span{position:absolute;top:7px;right:7px;width:6px;height:6px;border-radius:50%;background:var(--accent)}.filter-toggle.active{background:var(--accent-soft);color:var(--accent-hover)}.desktop-table{overflow-x:auto}.desktop-table:deep(> .empty-card){margin:0;border:0!important;border-radius:0!important;box-shadow:none!important}.tbl{width:100%;border-collapse:collapse}.tbl th{height:42px;padding:0 12px;background:var(--surface-muted);color:var(--text-muted);font-size:11px;font-weight:700;letter-spacing:.055em;text-transform:uppercase}.tbl td{padding:12px;border-bottom:1px solid var(--border);font-size:12px}.tbl tfoot td{background:var(--surface-muted);color:var(--text-muted);font-weight:700}.center{text-align:center}.note{max-width:240px;color:var(--text-muted)}.chip--cpw{background:#fdf0f8;color:#c4719e}.chip--cpp{background:#eef4ff;color:#5a82c4}.party-chip,.party-label{display:inline-flex;align-items:center;gap:4px}.party-chip svg,.party-label svg{width:13px;height:13px;flex:none}.actions{display:flex;justify-content:center}.icon-action{width:36px;height:36px;border-radius:8px;background:transparent;color:var(--text-dim)}.icon-action:hover{background:var(--accent-soft);color:var(--accent-hover)}.icon-action.danger:hover{background:var(--danger-bg);color:var(--danger-text)}.icon-action svg{width:15px}.drag-cell{white-space:nowrap;touch-action:none;color:var(--text-dim);cursor:grab}.drag-cell svg{display:inline;width:16px;margin-right:4px;vertical-align:middle;color:var(--text-dim);opacity:.62;stroke-width:1.8}.is-drag-disabled .drag-cell svg{opacity:.28}.draggable-row.is-dragging{opacity:.45}.draggable-row.is-drop-before{box-shadow:inset 0 3px var(--accent)}.draggable-row.is-drop-after{box-shadow:inset 0 -3px var(--accent)}.is-drag-disabled .drag-cell{opacity:.4;cursor:default}.mobile-records{display:none}.drawer-backdrop{position:fixed;inset:0;z-index:1000;display:flex;justify-content:flex-end;background:rgba(34,45,37,.28);backdrop-filter:blur(2px);animation:drawerFade .2s ease}.drawer{display:flex;width:min(440px,100vw);height:100dvh;flex-direction:column;background:var(--surface);box-shadow:-18px 0 48px rgba(40,54,44,.16);animation:drawerFromRight .25s ease}.drawer-handle{display:none}.drawer-header{display:flex;min-height:86px;padding:20px 22px;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.drawer-header h3{font-family:var(--font-display);font-size:18px}.drawer-body{display:flex;min-height:0;padding:22px;gap:16px;flex:1;flex-direction:column;overflow:auto}.field{display:grid;gap:7px}.field label{font-size:13px;font-weight:600}.field label span{color:var(--accent)}.field-control{position:relative;display:flex;align-items:center;border:1px solid var(--border);border-radius:10px;background:#fbfcfa}.field-control:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.field-control>svg{position:absolute;left:13px;width:17px;color:#7a8b7e}.field-control>svg:last-child:not(:first-child){left:auto;right:13px;width:15px}.field-control input,.field-control select,.field-control textarea{width:100%;padding:10px 40px;border:0!important;background:transparent!important;box-shadow:none!important;font:500 13px var(--font)}.field-control input,.field-control select{min-height:46px}.field-control select{appearance:none}.field-control.textarea{align-items:flex-start}.field-control.textarea>svg{top:11px;width:17px;height:17px}.field-control.textarea textarea{line-height:19px}.drawer-footer{display:flex;justify-content:flex-end;gap:9px;margin:auto -22px -22px;padding:18px 22px 22px;border-top:1px solid var(--border)}.spinner{animation:spin .8s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@keyframes drawerFromRight{from{transform:translateX(100%)}}@keyframes drawerFromBottom{from{transform:translateY(100%)}}
.guest-page{gap:24px;font-family:var(--font-inter)}.eyebrow{color:#1a3300;font:500 12px/1.5 var(--font-roboto-mono)}.hero-card h1{color:#1a3300;font-family:var(--font-bricolage-grotesque);font-size:40px;font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{color:#1a3300;font-size:16px;line-height:1.5}.primary-action{border-radius:6px;background:#1a3300;color:#fcfaf5;font-size:14px;font-weight:500;box-shadow:0 1px 2px rgba(0,0,0,.05)}.primary-action:hover{background:#1a3300}.summary-card,.data-panel{border-color:#b6b6b6;border-radius:12px;background:#fcfaf5;box-shadow:none}.summary-icon{border-radius:6px;background:#ffe95c;color:#1a3300}.summary-icon--cpw{background:#f6d0ff;color:#1a3300}.summary-icon--cpp{background:#a8e5e5;color:#1a3300}.summary-icon--ok{background:#d5f5c2;color:#1a3300}.summary-label,.tbl td{font-size:14px}.summary-sub{font-size:12px}.progress-track span{background:#1a3300}.toolbar-control,.secondary-action,.filter-toggle{border-radius:6px;background:#fcfaf5;font-size:14px}.icon-action{border-radius:6px}.drawer{background:#fcfaf5;box-shadow:none}.drawer-header h3{font-family:var(--font-inter);font-size:24px;font-weight:700}.field label{font-size:14px}.field-control{border-radius:6px;background:#fcfaf5}.field-control:focus-within{border-color:#1a3300;box-shadow:0 0 0 2px #ffe95c}.field-control input,.field-control select,.field-control textarea{font-family:var(--font-inter);font-size:16px}
@media(max-width:900px){.summary-grid{grid-template-columns:1fr 1fr}.toolbar{align-items:stretch;flex-direction:column}.toolbar-filters{width:100%}}
@media(max-width:767px){.guest-page{gap:14px}.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px}.hero-card h1{font-size:25px}.hero-card p{font-size:13px}.hero-card .primary-action{width:100%}.summary-grid{gap:8px}.summary-card{padding:13px;gap:9px}.summary-icon{width:34px;height:34px}.summary-value{font-size:18px}.data-panel{overflow:visible;border:0;background:transparent;box-shadow:none}.toolbar{display:block;padding:12px;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.toolbar-top{gap:8px}.toolbar-control,.secondary-action{height:44px;min-height:44px}.filter-toggle{display:flex}.toolbar-filters{display:none;grid-template-columns:1fr 1fr;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}.toolbar-filters.open{display:grid}.toolbar-filters>*{width:100%}.select-wrap select{width:100%}.export-action{grid-column:1/-1}.desktop-table{display:none}.mobile-records{display:grid;gap:10px;margin-top:14px}.record-card{padding:14px;border:1px solid var(--border);border-left:4px solid var(--text-dim);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.record-card.chip--ok{border-left-color:var(--ok-text)}.record-card.chip--danger{border-left-color:var(--danger-text)}.record-card.chip--warn{border-left-color:var(--warn-text)}.record-head{display:flex;justify-content:space-between;gap:8px}.record-head h2{margin-top:6px;font-size:14px}.category{color:var(--text-muted);font-size:11px}.record-card dl{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:13px;padding:11px 0;border-block:1px solid var(--border)}.record-card dt{color:var(--text-muted);font-size:10px}.record-card dd{margin-top:3px;font-size:12px;font-weight:700}.record-note{margin-top:10px;color:var(--text-muted);font-size:12px}.record-footer{display:flex;margin-top:8px;align-items:center;justify-content:space-between}.drag-handle{gap:5px;padding:8px;background:none;color:var(--text-muted);font-size:11px;touch-action:none}.drag-handle svg{width:15px}.drawer-backdrop{align-items:flex-end}.drawer{width:100%;height:auto;max-height:92dvh;border-radius:20px 20px 0 0;animation:drawerFromBottom .25s ease}.drawer-handle{display:block;width:40px;height:4px;margin:9px auto 0;border-radius:99px;background:var(--border)}.drawer-header{min-height:68px;padding:10px 16px 13px}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.field-control input,.field-control select,.field-control textarea{font-size:16px}.drawer-footer{position:sticky;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}.drawer-footer>*{flex:1}}
.hero-card h1{font-size:55px}.hero-card p{font-size:18px}.eyebrow{font-size:12px}.primary-action,.secondary-action,.toolbar-control,.tbl td,.field-control input,.field-control select,.field-control textarea{font-size:16px}.drawer-header h3,.record-head h2{font-size:28px}.summary-label,.field label,.note{font-size:14px}.summary-sub,.tbl th,.category,.record-card dt,.record-note,.drag-handle{font-size:11px}
@media(max-width:767px){.guest-page{width:calc(100% - 32px)}.hero-card,.toolbar,.record-card{border-radius:12px;background:#fcfaf5;box-shadow:none}.hero-card h1{font-size:40px}.hero-card p{font-size:16px}.record-head h2{font-size:28px}.record-card dd{font-size:16px}}
@media(prefers-reduced-motion:reduce){.drawer,.draggable-row,.primary-action,.icon-action,.progress-track span{animation:none!important;transition:none!important}}.primary-action:focus-visible,.secondary-action:focus-visible,.icon-action:focus-visible,.filter-toggle:focus-visible,.drag-handle:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
/* Sunset Marketplace — final scoped overrides */
.guest-page{color:#240029}.hero-card{border-color:#d4ccd4;border-radius:14px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f);box-shadow:0 2px 2px rgba(32,0,36,.05)}.hero-card h1,.hero-card p,.eyebrow{color:#240029}.eyebrow{font-family:'JetBrains Mono',monospace;letter-spacing:.1em;text-transform:uppercase}.primary-action{background:#df37a7;color:#fff}.primary-action:hover{background:#c92f96}.secondary-action,.toolbar-control,.field-control{border-color:#6d526d;color:#240029}.summary-card,.data-panel,.record-card{border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.progress-track span{background:#df37a7}.tbl th,.tbl td,.summary-label,.summary-sub,.field label{color:#240029}.icon-action:hover{background:#fff1f8;color:#df37a7}
/* Visual parity with Seserahan/Index.vue */
.guest-page{row-gap:24px;padding-bottom:32px;font-family:var(--font-inter)}
.hero-card{padding:34px 38px;gap:28px}
.eyebrow{display:block;margin-bottom:7px;padding:0;border-radius:0;font-size:12px;font-weight:500;line-height:1.5}
.primary-action{min-height:43px;padding:10px 16px;font-size:16px;font-weight:500}
.summary-grid{gap:12px}
.summary-card{min-width:0;padding:18px 20px;gap:14px}
.summary-icon{border-radius:6px}
.summary-label{font-size:14px}
.summary-value{font-size:clamp(19px,2vw,25px)}
.summary-sub{font-size:11px}
.toolbar-control,.secondary-action{font-family:var(--font);font-size:16px}
.select-wrap select{width:180px}
.tbl td{font-size:16px}
.tbl tfoot td{padding:12px;background:var(--surface-muted);border-top:0;border-bottom:1px solid var(--border);font-size:16px}
.tbl tfoot td:first-child{font-size:16px;text-transform:none}
.record-card{padding:14px}
.drawer-header{gap:normal}
.drawer-header .eyebrow{margin-bottom:7px;font-size:12px;font-weight:500;line-height:1.5}
.drawer-header h3{font-family:var(--font-inter);font-size:28px;font-weight:700}
.drawer-body{padding:22px;gap:16px}
.field-control{overflow:visible;transition:none}
.field-control:focus-within{border-color:#1a3300;background:#fcfaf5;box-shadow:0 0 0 2px #ffe95c}
.field-control>svg{color:#7a8b7e}
.field-control input,.field-control select,.field-control textarea{font-family:var(--font-inter);font-size:16px}
.drawer-footer{margin:auto -22px -22px;padding:18px 22px 22px}
@media(max-width:767px){.guest-page{gap:14px}.hero-card{padding:18px 16px;gap:14px}.summary-card{padding:13px;gap:9px}.mobile-records{gap:10px;margin-top:14px}.record-card{padding:14px;box-shadow:var(--shadow-sm)}.drawer{border-radius:20px 20px 0 0}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.drawer-footer{margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}}
/* Mobile-only layout refinements, aligned with Budget/Seserahan. */
@media(max-width:767px){
  .guest-page{width:calc(100% - 24px);row-gap:12px;padding-bottom:16px}
  .hero-card{padding:18px 16px;gap:14px;border-radius:12px}
  .hero-card h1{font-size:clamp(28px,9vw,36px);line-height:1.05;letter-spacing:.01em}
  .hero-card p{margin-top:8px;font-size:14px;line-height:1.45}
  .hero-card .primary-action{min-height:44px;font-size:14px}
  .summary-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  .summary-card{min-width:0;padding:12px;gap:8px;align-items:flex-start}
  .summary-card>div{min-width:0}
  .summary-icon{width:32px;height:32px;border-radius:9px}
  .summary-icon svg{width:17px}
  .summary-label{font-size:11px;line-height:1.25}
  .summary-value{margin-top:4px;font-size:clamp(15px,4.2vw,18px);line-height:1.2;overflow-wrap:anywhere}
  .summary-sub{font-size:10px;line-height:1.4;overflow-wrap:anywhere}
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
  .record-head{align-items:flex-start}
  .record-head>div{min-width:0}
  .record-head h2{margin-top:5px;font-size:18px;line-height:1.25;overflow-wrap:anywhere}
  .record-head>.chip{max-width:42%;flex:none;text-align:center;white-space:normal}
  .record-card dl{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:12px;padding:10px 0}
  .record-card dl>div{min-width:0}
  .record-card dt{font-size:10px}
  .record-card dd{font-size:clamp(11px,3.4vw,14px);line-height:1.3;overflow-wrap:anywhere}
  .party-label{min-width:0;flex-wrap:wrap}
  .record-contact{margin-top:10px;padding:10px;border-radius:8px;background:#fff8fc}
  .record-contact span{display:block;color:#6d526d;font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
  .record-contact p{margin-top:4px;color:#240029;font-size:13px;line-height:1.45;overflow-wrap:anywhere;white-space:pre-wrap}
  .record-footer{margin-top:8px;gap:8px;flex-wrap:wrap}
  .record-footer .actions{margin-left:auto;flex:none;gap:2px}
  .record-footer .icon-action{width:40px;height:40px}
  .drag-handle{min-width:0;text-align:left}
  .drawer{max-height:94dvh;border-radius:16px 16px 0 0}
  .drawer-header{min-height:64px;padding:8px 14px 12px}
  .drawer-header h3{font-size:18px}
  .drawer-header .icon-action{width:44px;height:44px}
  .drawer-body{padding:14px 16px calc(16px + env(safe-area-inset-bottom));gap:14px}
  .field{gap:6px}
  .field label{font-size:13px}
  .field-control input,.field-control select{min-height:44px}
  .field-control textarea{min-height:96px}
  .drawer-footer{bottom:calc(-14px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(14px + env(safe-area-inset-bottom));gap:8px}
}

/* Drawer CRUD — selaras dengan halaman planner lainnya. */
.drawer{width:min(760px,100vw);border-left:1px solid rgba(109,82,109,.28);background:#fff;box-shadow:-18px 0 48px rgba(36,0,41,.12)}
.drawer-body{gap:20px;padding:24px 28px}
.form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.field{min-width:0;gap:8px}
.field label{color:#240029;font-size:14px;line-height:1.4}
.field label span{color:#df37a7}
.field-control{
  isolation:isolate;
  overflow:hidden;
  border-color:rgba(109,82,109,.38);
  border-radius:12px;
  background:#fffafd;
  box-shadow:0 1px 2px rgba(32,0,36,.035);
  transition:border-color .16s ease,background-color .16s ease,box-shadow .16s ease;
}
.field-control:hover{border-color:rgba(109,82,109,.62);background:#fff}
.field-control:focus-within{border-color:#df37a7;background:#fff;box-shadow:0 0 0 3px rgba(223,55,167,.12)}
.field-control>svg{z-index:1;color:#6d526d;pointer-events:none;transition:color .16s ease}
.field-control:focus-within>svg{color:#df37a7}
.drawer-body .field-control>input,
.drawer-body .field-control>select,
.drawer-body .field-control>textarea{
  min-height:48px;
  padding-left:44px!important;
  border-radius:12px;
  color:#240029;
  font-family:var(--font-inter);
  font-size:16px;
  font-weight:400;
}
.drawer-body .field-control>select{padding-right:44px!important}
.field-control input::placeholder,
.field-control textarea::placeholder{color:#9b879b;font-weight:400}
.field-control.textarea{align-items:flex-start;overflow:hidden}
.field-control.textarea>svg{top:14px}
.drawer-body .field-control>textarea{min-height:144px;padding:13px 40px 13px 44px!important;line-height:1.55;resize:vertical}
.drawer-footer{margin:auto -28px -24px;padding:18px 28px 24px;gap:12px;background:#fff}
.drawer-footer .secondary-action,
.drawer-footer .primary-action{min-height:44px;padding-inline:20px}
.drawer-header .icon-action:focus-visible,
.drawer-footer button:focus-visible{outline:3px solid rgba(223,55,167,.22);outline-offset:2px}

@media(max-width:767px){
  .drawer{width:100%;border-left:0;box-shadow:0 -18px 48px rgba(36,0,41,.12)}
  .drawer-body{gap:16px;padding:16px 16px calc(16px + env(safe-area-inset-bottom))}
  .form-row{grid-template-columns:1fr;gap:16px}
  .drawer-body .field-control>textarea{min-height:128px}
  .drawer-footer{
    position:sticky;
    z-index:10;
    bottom:calc(-16px - env(safe-area-inset-bottom));
    margin:auto -16px calc(-16px - env(safe-area-inset-bottom));
    padding:12px 16px calc(16px + env(safe-area-inset-bottom));
    align-items:stretch;
    background:#fff;
    box-shadow:0 -8px 18px rgba(32,0,36,.06);
  }
  .drawer-footer .secondary-action,
  .drawer-footer .primary-action{
    height:46px;
    min-width:0;
    min-height:46px;
    max-height:46px;
    padding:0 12px;
    flex:1;
    font-size:14px;
    line-height:1;
    white-space:nowrap;
  }
}
</style>
