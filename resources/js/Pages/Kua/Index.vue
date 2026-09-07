<template>
  <AppLayout>
    <div class="kua-page">
      <PlannerHero eyebrow="Persiapan administrasi pernikahan" title="Dokumen KUA" description="Lengkapi persyaratan kedua calon pengantin dan pantau biaya pengurusannya."><template #action><button class="primary-action" @click="openCreate"><Plus aria-hidden="true" />Tambah Dokumen</button></template></PlannerHero>

      <PlannerSummaryGrid aria-label="Ringkasan dokumen KUA">
        <article class="summary-card"><span class="summary-icon"><Files /></span><div><p class="summary-label">Total Dokumen</p><p class="summary-value">{{ totalDok }}</p><p class="summary-sub">{{ doneCount }} lengkap kedua pihak</p></div></article>
        <article class="summary-card"><span class="summary-icon"><UserRoundCheck /></span><div><p class="summary-label">Calon Pengantin Wanita</p><p class="summary-value">{{ doneCpw }}/{{ totalDok }}</p><div class="progress-track"><span :style="{width: cpwProgress+'%'}"></span></div></div></article>
        <article class="summary-card"><span class="summary-icon"><UserRoundCheck /></span><div><p class="summary-label">Calon Pengantin Pria</p><p class="summary-value">{{ doneCpp }}/{{ totalDok }}</p><div class="progress-track"><span :style="{width: cppProgress+'%'}"></span></div></div></article>
        <article class="summary-card"><span class="summary-icon summary-icon--money"><WalletCards /></span><div><p class="summary-label">Total Biaya</p><p class="summary-value">{{ formatRp(totalBiaya) }}</p><p class="summary-sub">Estimasi pengurusan dokumen</p></div></article>
      </PlannerSummaryGrid>

      <PlannerDataPanel>
        <section class="toolbar" aria-label="Pencarian dan filter dokumen">
          <div class="toolbar-top"><div class="search-wrap"><Search aria-hidden="true" /><input v-model="searchQuery" class="toolbar-control" placeholder="Cari nama dokumen atau catatan..."></div><button class="filter-toggle" :class="{active:mobileFiltersOpen||filterStatus}" :aria-expanded="mobileFiltersOpen" aria-controls="kua-filters" aria-label="Tampilkan filter dokumen" @click="mobileFiltersOpen=!mobileFiltersOpen"><ListFilter aria-hidden="true" /><span v-if="filterStatus" /></button></div>
          <div id="kua-filters" class="toolbar-filters" :class="{open:mobileFiltersOpen}"><label class="select-wrap"><select v-model="filterStatus" class="toolbar-control" aria-label="Filter status kelengkapan"><option value="">Semua Status</option><option value="done">Lengkap Kedua Pihak</option><option value="pending">Belum Lengkap</option></select><ChevronDown aria-hidden="true" /></label><button v-if="hasFilters" class="secondary-action" @click="resetFilters"><X aria-hidden="true" />Reset</button><button class="secondary-action export-action" @click="exportToExcel"><FileSpreadsheet aria-hidden="true" />Ekspor Excel</button></div>
        </section>

        <div class="desktop-table">
          <table class="tbl"><thead><tr><th>#</th><th>Nama Dokumen</th><th class="right">Biaya</th><th class="center">Calon Pengantin Wanita</th><th class="center">Calon Pengantin Pria</th><th>Catatan</th><th class="center">Aksi</th></tr></thead><tbody>
            <tr v-for="(doc,index) in filteredDocuments" :key="doc.id" class="draggable-row" :class="rowClasses(doc)" :draggable="canDragRows" @dragstart="startDrag(doc,index,$event)" @dragover.prevent="setDragOver(doc,index)" @drop="dropRow(index)" @dragend="endDrag">
              <td class="drag-cell" @touchstart.stop="handleTouchStart($event,doc,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical aria-hidden="true" /> {{ doc.no }}</td><td><strong>{{ doc.nama_dokumen }}</strong></td><td class="right strong">{{ doc.biaya>0?formatRp(doc.biaya):'–' }}</td>
              <td class="center"><button class="status-button" :class="{done:doc.cpw_status}" :aria-label="`${doc.cpw_status?'Batalkan':'Tandai'} dokumen ${doc.nama_dokumen} untuk calon pengantin wanita`" :data-tooltip="doc.cpw_status?'Tandai belum lengkap':'Tandai lengkap'" @click="toggleCpw(doc)"><component :is="doc.cpw_status?CircleCheckBig:Circle" aria-hidden="true" /><span>{{ doc.cpw_status?'Lengkap':'Belum' }}</span></button></td>
              <td class="center"><button class="status-button" :class="{done:doc.cpp_status}" :aria-label="`${doc.cpp_status?'Batalkan':'Tandai'} dokumen ${doc.nama_dokumen} untuk calon pengantin pria`" :data-tooltip="doc.cpp_status?'Tandai belum lengkap':'Tandai lengkap'" :aria-pressed="doc.cpp_status" @click="toggleCpp(doc)"><component :is="doc.cpp_status?CircleCheckBig:Circle" aria-hidden="true" /><span>{{ doc.cpp_status?'Lengkap':'Belum' }}</span></button></td>
              <td class="note">{{ doc.catatan||'–' }}</td><td><div class="actions"><button class="icon-action" data-tooltip="Salin" :aria-label="`Salin dokumen ${doc.nama_dokumen}`" @click="openCopy(doc)"><Copy /></button><button class="icon-action" data-tooltip="Edit" :aria-label="`Edit dokumen ${doc.nama_dokumen}`" @click="openEdit(doc)"><Pencil /></button><button class="icon-action danger" data-tooltip="Hapus" :aria-label="`Hapus dokumen ${doc.nama_dokumen}`" @click="confirmDelete(doc)"><Trash2 /></button></div></td>
            </tr></tbody><tfoot v-if="filteredDocuments.length"><tr><td colspan="2">{{ filteredDocuments.length }} dokumen ditampilkan</td><td class="right strong">{{ formatRp(filteredTotalBiaya) }}</td><td colspan="4"></td></tr></tfoot></table><PlannerEmptyState v-if="!filteredDocuments.length" :title="hasFilters ? 'Dokumen tidak ditemukan' : 'Belum ada dokumen KUA'" :description="hasFilters ? 'Tidak ada dokumen yang sesuai dengan pencarian atau filter.' : 'Mulai susun persyaratan administrasi untuk kedua calon pengantin.'"><template #icon><FileText /></template><template #action><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Dokumen Pertama' }}</button></template></PlannerEmptyState>
        </div>

        <section class="mobile-records" aria-label="Daftar dokumen KUA"><article v-for="(doc,index) in filteredDocuments" :key="doc.id" class="record-card" :class="{complete:doc.cpw_status&&doc.cpp_status}"><div class="record-head"><div><span class="category">Dokumen #{{ doc.no }}</span><h2>{{ doc.nama_dokumen }}</h2></div><strong>{{ doc.biaya>0?formatRp(doc.biaya):'Tanpa biaya' }}</strong></div><div class="mobile-status"><button type="button" class="status-button" :class="{done:doc.cpw_status}" :aria-label="`${doc.cpw_status?'Batalkan':'Tandai'} dokumen ${doc.nama_dokumen} untuk calon pengantin wanita`" :aria-pressed="doc.cpw_status" @click="toggleCpw(doc)"><component :is="doc.cpw_status?CircleCheckBig:Circle" aria-hidden="true" /><span>CPW · {{ doc.cpw_status?'Lengkap':'Belum' }}</span></button><button type="button" class="status-button" :class="{done:doc.cpp_status}" :aria-label="`${doc.cpp_status?'Batalkan':'Tandai'} dokumen ${doc.nama_dokumen} untuk calon pengantin pria`" :aria-pressed="doc.cpp_status" @click="toggleCpp(doc)"><component :is="doc.cpp_status?CircleCheckBig:Circle" aria-hidden="true" /><span>CPP · {{ doc.cpp_status?'Lengkap':'Belum' }}</span></button></div><p v-if="doc.catatan" class="record-note">{{ doc.catatan }}</p><div class="record-footer"><button class="drag-handle" :disabled="!canDragRows" aria-label="Geser urutan dokumen" @touchstart.stop="handleTouchStart($event,doc,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical />Urutan {{ doc.no }}</button><div class="actions"><button class="icon-action" aria-label="Salin dokumen" @click="openCopy(doc)"><Copy /></button><button class="icon-action" aria-label="Edit dokumen" @click="openEdit(doc)"><Pencil /></button><button class="icon-action danger" aria-label="Hapus dokumen" @click="confirmDelete(doc)"><Trash2 /></button></div></div></article><PlannerEmptyState v-if="!filteredDocuments.length" :title="hasFilters ? 'Dokumen tidak ditemukan' : 'Belum ada dokumen KUA'" :description="hasFilters ? 'Tidak ada dokumen yang sesuai dengan pencarian atau filter.' : 'Mulai susun persyaratan administrasi untuk kedua calon pengantin.'"><template #icon><FileText /></template><template #action><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Dokumen Pertama' }}</button></template></PlannerEmptyState></section>
      </PlannerDataPanel>
    </div>

    <Teleport to="body"><div v-if="showModal" class="drawer-backdrop" @click.self="closeModal"><aside class="drawer" role="dialog" aria-modal="true" aria-labelledby="kua-drawer-title"><div class="drawer-handle" aria-hidden="true"></div><header class="drawer-header"><div><span class="eyebrow">Dokumen KUA</span><h3 id="kua-drawer-title" class="drawer-title">{{ editItem?'Edit Dokumen':copyItem?'Salin Dokumen':'Tambah Dokumen' }}</h3></div><button class="icon-action" aria-label="Tutup" @click="closeModal"><X /></button></header><form class="drawer-body" @submit.prevent="save"><div class="form-row"><div class="field"><label for="document-name">Nama Dokumen <span>*</span></label><div class="field-control"><FileText /><input id="document-name" v-model="form.nama_dokumen" required placeholder="Contoh: KTP, akta lahir, surat N1"></div></div><div class="field"><label for="document-cost">Biaya (Rp)</label><div class="field-control"><BadgeDollarSign /><input id="document-cost" v-model="form.biaya" type="number" min="0" placeholder="0"></div></div></div><div class="field"><label for="document-note">Catatan</label><div class="field-control textarea"><NotepadText /><textarea id="document-note" v-model="form.catatan" rows="4" placeholder="Tambahkan informasi penting..."></textarea></div></div><footer class="drawer-footer"><button type="button" class="secondary-action" @click="closeModal">Batal</button><button type="submit" class="primary-action" :disabled="saving"><LoaderCircle v-if="saving" class="spinner" />{{ saving?'Menyimpan...':'Simpan Dokumen' }}</button></footer></form></aside></div></Teleport>
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

import { BadgeDollarSign, ChevronDown, Circle, CircleCheckBig, Copy, Files, FileSpreadsheet, FileText, GripVertical, ListFilter, LoaderCircle, NotepadText, Pencil, Plus, Search, Trash2, UserRoundCheck, WalletCards, X } from '@lucide/vue';
import { confirmDeleteDialog, showToast } from '@/utils.js';
import * as XLSX from 'xlsx';

const props=defineProps({documents:Array,totalBiaya:Number,totalDok:Number,doneCpw:Number,doneCpp:Number,allDone:Boolean});
const localDocuments=ref([...props.documents]),searchQuery=ref(''),filterStatus=ref(''),mobileFiltersOpen=ref(false),showModal=ref(false),editItem=ref(null),copyItem=ref(null),saving=ref(false),draggedIndex=ref(null),draggedId=ref(null),dragOverIndex=ref(null),dragOverId=ref(null);
const defaultForm=()=>({nama_dokumen:'',biaya:'',catatan:''}),form=ref(defaultForm());
watch(()=>props.documents,v=>localDocuments.value=[...v]);watch(showModal,v=>document.body.style.overflow=v?'hidden':'');
onMounted(()=>window.addEventListener('keydown',handleKey));onBeforeUnmount(()=>{window.removeEventListener('keydown',handleKey);document.body.style.overflow=''});function handleKey(e){if(e.key==='Escape'&&showModal.value)closeModal()}
const doneCount=computed(()=>localDocuments.value.filter(d=>d.cpw_status&&d.cpp_status).length),cpwProgress=computed(()=>props.totalDok?Math.round(props.doneCpw/props.totalDok*100):0),cppProgress=computed(()=>props.totalDok?Math.round(props.doneCpp/props.totalDok*100):0),hasFilters=computed(()=>Boolean(searchQuery.value||filterStatus.value));
const filteredDocuments=computed(()=>{const q=searchQuery.value.toLowerCase();return localDocuments.value.filter(d=>(!q||d.nama_dokumen?.toLowerCase().includes(q)||d.catatan?.toLowerCase().includes(q))&&(!filterStatus.value||(filterStatus.value==='done'?(d.cpw_status&&d.cpp_status):(!d.cpw_status||!d.cpp_status))))});
const filteredTotalBiaya=computed(()=>filteredDocuments.value.reduce((total,doc)=>total+Number(doc.biaya||0),0));
const canDragRows=computed(()=>!hasFilters.value);const formatRp=n=>'Rp'+Number(n||0).toLocaleString('id-ID');
function resetFilters(){searchQuery.value='';filterStatus.value=''}function docForm(d){return{nama_dokumen:d.nama_dokumen,biaya:d.biaya||'',catatan:d.catatan||''}}function openCreate(){editItem.value=null;copyItem.value=null;form.value=defaultForm();showModal.value=true}function openCopy(d){editItem.value=null;copyItem.value=d;form.value=docForm(d);showModal.value=true}function openEdit(d){editItem.value=d;copyItem.value=null;form.value=docForm(d);showModal.value=true}function closeModal(){showModal.value=false;editItem.value=null;copyItem.value=null}
function save(){saving.value=true;const editing=editItem.value;router[editing?'patch':'post'](editing?route('dokumen-kua.update',editing.id):route('dokumen-kua.store'),{...form.value,biaya:Number(form.value.biaya)||0},{preserveScroll:true,onSuccess:()=>{showToast(editing?'Dokumen berhasil diperbarui.':copyItem.value?'Salinan dokumen berhasil ditambahkan.':'Dokumen berhasil ditambahkan.');closeModal()},onFinish:()=>saving.value=false})}
function toggleCpw(d){d.cpw_status=!d.cpw_status;router.patch(route('kua.toggle-cpw',d.id),{},{preserveScroll:true,onSuccess:()=>showToast('Status calon pengantin wanita diperbarui.'),onError:()=>{d.cpw_status=!d.cpw_status;showToast('Status calon pengantin wanita gagal diperbarui.')}})}function toggleCpp(d){d.cpp_status=!d.cpp_status;router.patch(route('kua.toggle-cpp',d.id),{},{preserveScroll:true,onSuccess:()=>showToast('Status calon pengantin pria diperbarui.'),onError:()=>{d.cpp_status=!d.cpp_status;showToast('Status calon pengantin pria gagal diperbarui.')}})}function confirmDelete(d){confirmDeleteDialog(()=>router.delete(route('dokumen-kua.destroy',d.id),{preserveScroll:true,onSuccess:()=>showToast('Dokumen berhasil dihapus.')}),{title:'Hapus dokumen ini?',description:`Dokumen “${d.nama_dokumen}” akan dihapus permanen beserta status kelengkapannya.`})}
function rowClasses(d){return{complete:d.cpw_status&&d.cpp_status,'is-dragging':draggedId.value===d.id,'is-drop-before':dropPlacement(d.id)==='before','is-drop-after':dropPlacement(d.id)==='after','is-drag-disabled':!canDragRows.value}}function startDrag(d,index,e){if(!canDragRows.value)return;draggedIndex.value=index;draggedId.value=d.id;if(e?.dataTransfer)e.dataTransfer.effectAllowed='move'}function handleTouchStart(_,d,index){if(canDragRows.value)startDrag(d,index)}function handleTouchMove(e){if(draggedId.value===null)return;e.preventDefault();const row=document.elementFromPoint(e.touches[0].clientX,e.touches[0].clientY)?.closest('.record-card,tr.draggable-row');if(!row)return;const cards=[...document.querySelectorAll('.mobile-records .record-card')],index=cards.includes(row)?cards.indexOf(row):[...row.parentNode.children].indexOf(row),item=filteredDocuments.value[index];if(item)setDragOver(item,index)}function handleTouchEnd(){dragOverIndex.value!==null?dropRow(dragOverIndex.value):endDrag()}function setDragOver(d,index){if(canDragRows.value&&draggedId.value!==d.id){dragOverId.value=d.id;dragOverIndex.value=index}}function dropPlacement(id){if(dragOverId.value!==id)return null;return dragOverIndex.value>draggedIndex.value?'after':'before'}function dropRow(index){if(!canDragRows.value||draggedIndex.value===null||index===draggedIndex.value){endDrag();return}const previous=[...localDocuments.value],list=[...localDocuments.value],[moved]=list.splice(draggedIndex.value,1);list.splice(index,0,moved);localDocuments.value=list.map((d,i)=>({...d,no:i+1}));router.patch(route('dokumen-kua.reorder'),{ids:localDocuments.value.map(d=>d.id)},{preserveScroll:true,onSuccess:()=>showToast('Urutan dokumen berhasil disimpan.'),onError:()=>{localDocuments.value=previous;showToast('Urutan dokumen gagal disimpan.')}});endDrag()}function endDrag(){draggedIndex.value=draggedId.value=dragOverIndex.value=dragOverId.value=null}
function exportToExcel(){if(!filteredDocuments.value.length){showToast('Tidak ada data untuk diekspor');return}const now=new Date(),rows=filteredDocuments.value.map((d,i)=>[d.no||i+1,d.nama_dokumen,d.cpw_status?'Lengkap':'Belum',d.cpp_status?'Lengkap':'Belum',d.biaya||0,d.catatan||'']),total=filteredDocuments.value.reduce((n,d)=>n+Number(d.biaya||0),0);rows.push(['','TOTAL','','',total,'']);const ws=XLSX.utils.aoa_to_sheet([['DAFTAR DOKUMEN KUA'],[`Dicetak pada: ${now.toLocaleString('id-ID')}`],[],['No','Nama Dokumen','Status CPW','Status CPP','Biaya','Catatan'],...rows]);ws['!merges']=[{s:{r:0,c:0},e:{r:0,c:5}},{s:{r:1,c:0},e:{r:1,c:5}}];ws['!cols']=[{wch:5},{wch:40},{wch:15},{wch:15},{wch:18},{wch:40}];const wb=XLSX.utils.book_new();XLSX.utils.book_append_sheet(wb,ws,'Dokumen KUA');XLSX.writeFile(wb,`Dokumen_KUA_${now.toISOString().split('T')[0]}.xlsx`);showToast('Data berhasil diekspor ke Excel')}
</script>

<style scoped>
.kua-page{display:grid;width:min(1200px,calc(100% - 48px));margin:0 auto;gap:18px;padding-bottom:24px;color:#1a3300}.hero-card{display:flex;min-height:190px;padding:32px;align-items:center;justify-content:space-between;gap:28px;border:1px solid #1a3300;border-radius:12px;background:#fcfaf5}.eyebrow{display:inline-block;margin-bottom:8px;padding:4px 8px;border-radius:6px;background:#ffe95c;color:#1a3300;font-size:11px;font-weight:600;letter-spacing:0;text-transform:none}.hero-card h1{color:#1a3300;font-family:Inter,system-ui,sans-serif;font-size:clamp(32px,4vw,40px);font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{margin-top:12px;color:#1a3300;font-size:16px;line-height:1.5}.primary-action,.secondary-action,.icon-action,.filter-toggle,.drag-handle,.status-button,.mobile-status button{display:inline-flex;align-items:center;justify-content:center;border:0;font:inherit;cursor:pointer}.primary-action{min-height:43px;padding:10px 16px;gap:8px;border-radius:6px;background:#1a3300;color:#fcfaf5;font-size:13px;font-weight:700;box-shadow:0 1px 2px rgba(0,0,0,.05)}.primary-action:hover{background:var(--accent-hover)}.primary-action:disabled{opacity:.65;cursor:wait}.primary-action svg,.secondary-action svg{width:16px}.summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.summary-card{display:flex;min-width:0;padding:18px 20px;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.summary-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:10px;background:var(--accent-soft);color:var(--accent-hover)}.summary-icon svg{width:20px}.summary-icon--money{background:var(--warn-bg);color:var(--warn-text)}.summary-label{font-size:12px;font-weight:600;color:var(--text-muted)}.summary-value{margin-top:5px;font-size:clamp(19px,2vw,25px);font-weight:800}.summary-sub{margin-top:4px;color:var(--text-muted);font-size:12px}.progress-track{height:6px;margin-top:7px;overflow:hidden;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;height:100%;background:linear-gradient(90deg,var(--accent-hover),var(--accent-light))}.data-panel{overflow:hidden;border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:var(--shadow-sm)}.toolbar{display:flex;padding:12px;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border)}.toolbar-top{display:flex;flex:1}.search-wrap{position:relative;flex:1}.search-wrap>svg{position:absolute;left:13px;top:50%;width:16px;transform:translateY(-50%);color:var(--text-dim)}.toolbar-control,.secondary-action{box-sizing:border-box;height:40px;min-height:40px;padding:0 12px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted);font:500 13px var(--font);line-height:1}.search-wrap input{width:100%;padding-left:39px}.toolbar-filters{display:flex;gap:8px}.select-wrap{position:relative;display:flex}.select-wrap select{width:190px;padding-right:34px;appearance:none}.select-wrap>svg{position:absolute;right:11px;top:50%;width:15px;transform:translateY(-50%);pointer-events:none}.secondary-action{gap:7px}.filter-toggle{display:none;position:relative;width:44px;height:44px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted)}.filter-toggle svg{width:19px}.filter-toggle span{position:absolute;top:7px;right:7px;width:6px;height:6px;border-radius:50%;background:var(--accent)}.filter-toggle.active{background:var(--accent-soft);color:var(--accent-hover)}.desktop-table{display:block;width:100%;margin:0;padding:0;overflow-x:auto}.desktop-table .tbl{width:100%;margin:0!important;border-spacing:0;border-collapse:collapse}.desktop-table:deep(> .empty-card){width:100%;box-sizing:border-box;margin:0;border:0!important;border-radius:0!important;box-shadow:none!important}.tbl th{height:42px;padding:0 12px;background:var(--surface-muted);color:var(--text-muted);font-size:11px;font-weight:700;letter-spacing:.055em;text-transform:uppercase}.tbl td{padding:12px;border-bottom:1px solid var(--border);font-size:12px}.tbl tbody tr.complete{background:var(--ok-bg)}.right{text-align:right}.center{text-align:center}.strong{font-weight:700}.note{max-width:220px;color:var(--text-muted)}.actions{display:flex;justify-content:center}.icon-action{width:36px;height:36px;border-radius:8px;background:transparent;color:var(--text-dim)}.icon-action:hover{background:var(--accent-soft);color:var(--accent-hover)}.icon-action.danger:hover{background:var(--danger-bg);color:var(--danger-text)}.icon-action svg{width:15px}.status-button{gap:6px;padding:6px 9px;border-radius:99px;background:var(--surface-muted);color:var(--text-muted);font-size:11px}.status-button.done{background:var(--ok-bg);color:var(--ok-text)}.status-button svg{width:16px}.drag-cell{white-space:nowrap;touch-action:none;color:var(--text-dim);cursor:grab}.drag-cell svg{display:inline;width:16px;margin-right:4px;vertical-align:middle;color:var(--text-dim);opacity:.62;stroke-width:1.8}.is-drag-disabled .drag-cell svg{opacity:.28}.draggable-row.is-dragging{opacity:.45}.draggable-row.is-drop-before{box-shadow:inset 0 3px var(--accent)}.draggable-row.is-drop-after{box-shadow:inset 0 -3px var(--accent)}.is-drag-disabled .drag-cell{opacity:.4;cursor:default}.mobile-records{display:none}.drawer-backdrop{position:fixed;inset:0;z-index:1000;display:flex;justify-content:flex-end;background:rgba(34,45,37,.28);backdrop-filter:blur(2px);animation:drawerFade .2s ease}.drawer{display:flex;width:min(440px,100vw);height:100dvh;flex-direction:column;background:var(--surface);box-shadow:-18px 0 48px rgba(40,54,44,.16);animation:drawerFromRight .25s ease}.drawer-handle{display:none}.drawer-header{display:flex;min-height:86px;padding:20px 22px;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.drawer-header h3{font-family:var(--font-display);font-size:18px}.drawer-body{display:flex;min-height:0;padding:22px;gap:16px;flex:1;flex-direction:column;overflow:auto}.field{display:grid;gap:7px}.field label{font-size:13px;font-weight:600}.field label span{color:var(--accent)}.field-control{position:relative;display:flex;align-items:center;border:1px solid var(--border);border-radius:10px;background:#fbfcfa}.field-control:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.field-control>svg{position:absolute;left:13px;width:17px;color:#7a8b7e}.field-control input,.field-control textarea{width:100%;padding:10px 12px 10px 40px;border:0!important;background:transparent!important;box-shadow:none!important;font:500 13px var(--font)}.field-control input{min-height:46px}.field-control.textarea{align-items:flex-start}.field-control.textarea>svg{top:7px}.drawer-footer{display:flex;justify-content:flex-end;gap:9px;margin:auto -22px -22px;padding:18px 22px 22px;border-top:1px solid var(--border)}.spinner{animation:spin .8s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@keyframes drawerFade{from{opacity:0}}@keyframes drawerFromRight{from{transform:translateX(100%)}}@keyframes drawerFromBottom{from{transform:translateY(100%)}}
.kua-page{font-family:Inter,system-ui,sans-serif;gap:16px}.hero-card{gap:24px}.hero-card h1{font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:40px;font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{font-size:16px}.primary-action{min-height:40px;padding:12px 20px;font-size:16px;font-weight:500}.secondary-action,.toolbar-control{border-color:#b6b6b6;border-radius:6px;background:#fcfaf5;color:#1a3300;font-family:Inter,system-ui,sans-serif;font-size:14px}.summary-grid{gap:16px}.summary-card{padding:24px;gap:16px;border-color:#b6b6b6;background:#fcfaf5;box-shadow:none}.summary-icon{border-radius:12px;background:#d5f5c2;color:#1a3300}.summary-icon--money{background:#a8e5e5;color:#1a3300}.summary-label,.summary-sub,.tbl th,.note,.drag-cell,.category,.record-note,.drag-handle{color:#1a3300}.summary-label{font-size:14px}.summary-value{font-size:28px}.summary-sub{font-size:11px}.progress-track{border-radius:9999px;background:#f1f1f1}.progress-track span{background:#1a3300}.data-panel{border-color:#b6b6b6;border-radius:12px;background:#fcfaf5;box-shadow:none}.toolbar{border-color:#b6b6b6}.filter-toggle,.icon-action{border-radius:6px;color:#1a3300}.filter-toggle{border-color:#b6b6b6;background:#fcfaf5}.filter-toggle.active,.icon-action:hover{background:#d5f5c2;color:#1a3300}.filter-toggle span{background:#1a3300}.tbl th{font-size:11px}.tbl td{border-color:#b6b6b6;font-size:12px}.tbl tbody tr.complete,.status-button.done{background:#d5f5c2;color:#1a3300}.status-button{border-radius:9999px;background:#f1f1f1;color:#1a3300;font-size:11px}.record-card{padding:24px;border-color:#b6b6b6;border-radius:12px;background:#fcfaf5;box-shadow:none}.record-head h2{font-size:16px}.drawer-backdrop{background:rgba(26,51,0,.28)}.drawer{width:min(440px,100vw);background:#fcfaf5;box-shadow:none}.drawer-header,.drawer-footer{border-color:#b6b6b6}.drawer-header h3{font-family:Inter,system-ui,sans-serif;font-size:20px}.drawer-body{padding:24px;gap:16px}.field label{font-size:14px;font-weight:600}.field-control{border-color:#b6b6b6;border-radius:6px;background:#fcfaf5}.field-control:focus-within{border-color:#1a3300;box-shadow:0 0 0 1px #1a3300}.field-control>svg{color:#1a3300}.field-control input,.field-control textarea{font:500 14px Inter,system-ui,sans-serif}.drawer-footer{margin:auto -24px -24px;padding:16px 24px 24px}
@media(max-width:900px){.summary-grid{grid-template-columns:1fr 1fr}.toolbar{align-items:stretch;flex-direction:column}.toolbar-filters{width:100%}}
@media(max-width:767px){.kua-page{width:calc(100% - 32px);gap:16px}.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px}.hero-card h1{font-size:25px}.hero-card p{font-size:13px}.hero-card .primary-action{width:100%}.summary-grid{gap:8px}.summary-card{padding:13px;gap:9px}.summary-icon{width:34px;height:34px}.summary-value{font-size:18px}.data-panel{overflow:visible;border:0;background:transparent;box-shadow:none}.toolbar{display:block;padding:12px;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.toolbar-top{gap:8px}.toolbar-control,.secondary-action{height:44px;min-height:44px}.filter-toggle{display:flex}.toolbar-filters{display:none;grid-template-columns:1fr 1fr;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}.toolbar-filters.open{display:grid}.toolbar-filters>*{width:100%}.select-wrap select{width:100%}.export-action{grid-column:1/-1}.desktop-table{display:none}.mobile-records{display:grid;gap:10px;margin-top:14px}.record-card{padding:14px;border:1px solid var(--border);border-left:4px solid var(--warn-text);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.record-card.complete{border-left-color:var(--ok-text)}.record-head{display:flex;justify-content:space-between;gap:8px}.record-head h2{margin-top:6px;font-size:14px}.record-head>strong{font-size:12px}.category{color:var(--text-muted);font-size:11px}.mobile-status{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:13px}.mobile-status .status-button{width:100%;min-height:40px;justify-content:flex-start;padding:9px}.mobile-status .status-button.done{background:var(--ok-bg);color:var(--ok-text)}.mobile-status svg{width:15px}.record-note{margin-top:10px;color:var(--text-muted);font-size:12px}.record-footer{display:flex;margin-top:8px;align-items:center;justify-content:space-between}.drag-handle{gap:5px;padding:8px;background:none;color:var(--text-muted);font-size:11px;touch-action:none}.drag-handle svg{width:15px}.drawer-backdrop{align-items:flex-end}.drawer{width:100%;height:auto;max-height:92dvh;border-radius:20px 20px 0 0;box-shadow:0 -18px 48px rgba(40,54,44,.16);animation:drawerFromBottom .25s ease}.drawer-handle{display:block;width:40px;height:4px;margin:9px auto 0;border-radius:99px;background:var(--border)}.drawer-header{min-height:68px;padding:10px 16px 13px}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.field-control input,.field-control textarea{font-size:16px}.drawer-footer{position:sticky;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}.drawer-footer>*{flex:1}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}.primary-action:focus-visible,.secondary-action:focus-visible,.icon-action:focus-visible,.filter-toggle:focus-visible,.status-button:focus-visible,.drag-handle:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
.hero-card h1{font-size:55px}.hero-card p{font-size:18px}.eyebrow{font-size:12px}.secondary-action,.toolbar-control,.tbl td,.field-control input,.field-control textarea{font-size:16px}.drawer-header h3,.record-head h2{font-size:28px}.summary-label,.field label,.note{font-size:14px}.summary-sub,.tbl th,.status-button,.category,.record-note,.drag-handle{font-size:11px}
@media(max-width:767px){.hero-card{padding:16px;gap:16px}.hero-card h1{font-size:40px}.hero-card p{font-size:16px}.summary-grid{gap:8px}.summary-card{padding:16px;gap:8px}.summary-value{font-size:20px}.toolbar{border-color:#b6b6b6;background:#fcfaf5}.mobile-records{gap:16px;margin-top:16px}.record-card{padding:16px;border-color:#b6b6b6;background:#fcfaf5;box-shadow:none}.record-head h2{font-size:28px}.category{font-size:11px}.record-note{font-size:11px}.mobile-status{gap:8px}.mobile-status .status-button.done{background:#d5f5c2;color:#1a3300}.drawer{border-radius:16px 16px 0 0;box-shadow:none}.drawer-handle{border-radius:9999px;background:#b6b6b6}.drawer-footer{background:#fcfaf5}}
/* Sunset Marketplace — final scoped overrides */
.kua-page{color:#240029}.hero-card{border-color:#d4ccd4;border-radius:14px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f);box-shadow:0 2px 2px rgba(32,0,36,.05)}.hero-card h1,.hero-card p,.eyebrow{color:#240029}.eyebrow{background:transparent;font-family:'JetBrains Mono',monospace;letter-spacing:.1em;text-transform:uppercase}.primary-action{background:#df37a7;color:#fff}.primary-action:hover{background:#c92f96}.secondary-action,.toolbar-control,.field-control{border-color:#6d526d;color:#240029}.summary-card,.data-panel,.record-card{border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.progress-track span{background:#df37a7}.tbl th,.tbl td,.summary-label,.summary-sub,.field label{color:#240029}.status-button.done{background:#dcfce7;color:#15803d}.icon-action:hover{background:#fff1f8;color:#df37a7}

/* Mobile-only layout refinements, aligned with Budget/Seserahan. */
@media(max-width:767px){
  .kua-page{width:calc(100% - 24px);gap:12px;padding-bottom:16px}
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
  .summary-sub{font-size:10px;line-height:1.4}
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
  .record-card:not(.complete){border-left-color:#d28a00}
  .record-card.complete{border-left-color:var(--ok-text)}
  .record-head{align-items:flex-start}
  .record-head>div{min-width:0}
  .record-head h2{margin-top:5px;font-size:18px;line-height:1.25;overflow-wrap:anywhere}
  .record-head>strong{max-width:42%;flex:none;font-size:12px;line-height:1.35;text-align:right;overflow-wrap:anywhere}
  .mobile-status{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:12px}
  .mobile-status .status-button{min-width:0;min-height:42px;line-height:1.25;white-space:normal}
  .mobile-status .status-button span{overflow-wrap:anywhere}
  .record-note{margin-top:10px;font-size:12px;line-height:1.5;overflow-wrap:anywhere}
  .record-footer{margin-top:6px;gap:8px}
  .record-footer .actions{flex:none;gap:2px}
  .record-footer .icon-action{width:40px;height:40px}
  .drag-handle{min-width:0;text-align:left;white-space:normal}
  .drawer{max-height:94dvh;border-radius:16px 16px 0 0}
  .drawer-header{min-height:64px;padding:8px 14px 12px}
  .drawer-header .icon-action{width:44px;height:44px}
  .drawer-header h3{font-size:18px}
  .drawer-body{padding:14px 16px calc(16px + env(safe-area-inset-bottom));gap:14px}
  .field{gap:6px}
  .field label{font-size:13px}
  .field-control input{min-height:44px}
  .field-control textarea{min-height:96px}
  .drawer-footer{bottom:calc(-14px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(14px + env(safe-area-inset-bottom));gap:8px}
}

/* Drawer CRUD — selaras dengan Anggaran, Persiapan, dan Seserahan. */
.drawer{width:min(760px,100vw);border-left:1px solid rgba(109,82,109,.28);background:#fff;box-shadow:-18px 0 48px rgba(36,0,41,.12)}
.drawer-body{gap:20px;padding:24px 28px}
.form-row{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,.6fr);gap:18px}
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
.drawer-body .field-control>textarea{
  min-height:48px;
  padding-left:44px!important;
  border-radius:12px;
  color:#240029;
  font-family:var(--font-inter);
  font-size:16px;
  font-weight:400;
}
.drawer-body .field-control>input[type='number']{padding-right:12px!important}
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
