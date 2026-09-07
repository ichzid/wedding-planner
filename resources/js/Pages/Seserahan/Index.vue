<template>
  <AppLayout>
    <div class="seserahan-page">
      <PlannerHero eyebrow="Hadiah penuh makna" title="Daftar Seserahan" description="Susun kebutuhan seserahan dan pantau pembeliannya dalam satu tempat."><template #action><button class="primary-action" @click="openCreate"><Plus aria-hidden="true" />Tambah Seserahan</button></template></PlannerHero>

      <PlannerSummaryGrid aria-label="Ringkasan seserahan">
        <article class="summary-card"><span class="summary-icon"><Gift /></span><div><p class="summary-label">Total Item</p><p class="summary-value">{{ totalItem }}</p><p class="summary-sub">{{ sudahBeli }} dibeli · {{ belumBeli }} belum</p></div></article>
        <article class="summary-card"><span class="summary-icon"><CircleCheckBig /></span><div><p class="summary-label">Progress</p><p class="summary-value">{{ progressPct }}%</p><div class="progress-track"><span :style="{ width: progressPct + '%' }"></span></div></div></article>
        <article class="summary-card"><span class="summary-icon summary-icon--money"><WalletCards /></span><div><p class="summary-label">Total Harga</p><p class="summary-value">{{ formatRp(totalHarga) }}</p><p class="summary-sub">Nilai seluruh kebutuhan</p></div></article>
      </PlannerSummaryGrid>

      <PlannerDataPanel>
        <section class="toolbar" aria-label="Pencarian dan filter">
          <div class="toolbar-top">
            <div class="search-wrap"><Search aria-hidden="true" /><input v-model="searchQuery" class="toolbar-control" placeholder="Cari item atau kategori..."></div>
            <button class="filter-toggle" :class="{ active: mobileFiltersOpen || filterKategori || filterUntuk || filterStatus }" :aria-expanded="mobileFiltersOpen" aria-controls="seserahan-filters" aria-label="Tampilkan filter seserahan" @click="mobileFiltersOpen = !mobileFiltersOpen"><ListFilter aria-hidden="true" /><span v-if="filterKategori || filterUntuk || filterStatus" /></button>
          </div>
          <div id="seserahan-filters" class="toolbar-filters" :class="{ open: mobileFiltersOpen }">
            <label class="select-wrap"><select v-model="filterKategori" class="toolbar-control" aria-label="Filter kategori"><option value="">Semua Kategori</option><option v-for="k in kategoriOptions" :key="k" :value="k">{{ k }}</option></select><ChevronDown aria-hidden="true" /></label>
            <label class="select-wrap"><select v-model="filterUntuk" class="toolbar-control" aria-label="Filter peruntukan"><option value="">Semua Peruntukan</option><option value="cpp">Calon Pengantin Pria</option><option value="cpw">Calon Pengantin Wanita</option><option value="kedua">Kedua Calon Pengantin</option></select><ChevronDown aria-hidden="true" /></label>
            <label class="select-wrap"><select v-model="filterStatus" class="toolbar-control" aria-label="Filter status"><option value="">Semua Status</option><option value="sudah_dibeli">Sudah Dibeli</option><option value="belum">Belum Dibeli</option></select><ChevronDown /></label>
            <button v-if="hasFilters" class="secondary-action" @click="resetFilters"><X />Reset</button>
            <button class="secondary-action export-action" @click="exportToExcel"><FileSpreadsheet />Ekspor Excel</button>
          </div>
        </section>

        <div class="desktop-table">
          <table class="tbl">
            <thead><tr><th>#</th><th>Item & Kategori</th><th>Untuk</th><th class="right">Qty</th><th class="right">Harga Satuan</th><th class="right">Total</th><th>Status</th><th class="center">Aksi</th></tr></thead>
            <tbody>
              <tr v-for="(s,index) in filteredItems" :key="s.id" class="draggable-row" :class="rowClasses(s)" :draggable="canDragRows" @dragstart="startDrag(s,index,$event)" @dragover.prevent="setDragOver(s,index)" @drop="dropRow(index)" @dragend="endDrag">
                <td class="drag-cell" @touchstart.stop="handleTouchStart($event,s,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical /> {{ s.no }}</td>
                <td><strong>{{ s.nama_item }}</strong><small>{{ s.kategori }}</small></td>
                <td><span class="chip source-chip" :class="untukChipClass(s.untuk)"><Mars v-if="s.untuk === 'cpp' || s.untuk === 'kedua'" aria-hidden="true" /><Venus v-if="s.untuk === 'cpw' || s.untuk === 'kedua'" aria-hidden="true" />{{ untukLabel(s.untuk) }}</span></td>
                <td class="right">{{ s.qty }} {{ s.satuan }}</td><td class="right">{{ formatRp(s.harga) }}</td><td class="right strong">{{ formatRp(s.qty*s.harga) }}</td>
                <td><span class="chip" :class="s.status === 'sudah_dibeli' ? 'chip--ok' : 'chip--danger'">{{ statusLabel(s.status) }}</span></td>
                <td><div class="actions"><button class="icon-action" data-tooltip="Salin" :aria-label="`Salin seserahan ${s.nama_item}`" @click="openCopy(s)"><Copy /></button><button class="icon-action" data-tooltip="Edit" :aria-label="`Edit seserahan ${s.nama_item}`" @click="openEdit(s)"><Pencil /></button><button class="icon-action danger" data-tooltip="Hapus" :aria-label="`Hapus seserahan ${s.nama_item}`" @click="confirmDelete(s)"><Trash2 /></button></div></td>
              </tr>
            </tbody>
            <tfoot v-if="filteredItems.length"><tr><td colspan="5">Total ({{ filteredItems.length }} item)</td><td class="right strong">{{ formatRp(filteredTotalHarga) }}</td><td colspan="2"></td></tr></tfoot>
          </table>
          <PlannerEmptyState v-if="!filteredItems.length" :title="hasFilters ? 'Seserahan tidak ditemukan' : 'Belum ada seserahan'" :description="hasFilters ? 'Tidak ada data yang sesuai dengan pencarian atau filter.' : 'Mulai catat kebutuhan seserahan untuk hari bahagiamu.'"><template #icon><Gift /></template><template #action><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Seserahan Pertama' }}</button></template></PlannerEmptyState>
        </div>

        <section class="mobile-records" aria-label="Daftar seserahan">
          <article v-for="(s,index) in filteredItems" :key="s.id" class="record-card" :class="s.status === 'sudah_dibeli' ? 'bought' : 'pending'">
            <div class="record-head"><div><span class="category">{{ s.kategori }}</span><h2>{{ s.nama_item }}</h2></div><span class="chip" :class="s.status === 'sudah_dibeli' ? 'chip--ok' : 'chip--danger'">{{ statusLabel(s.status) }}</span></div>
            <dl><div><dt>Peruntukan</dt><dd class="source-label"><Mars v-if="s.untuk === 'cpp' || s.untuk === 'kedua'" aria-hidden="true" /><Venus v-if="s.untuk === 'cpw' || s.untuk === 'kedua'" aria-hidden="true" />{{ untukLabel(s.untuk) }}</dd></div><div><dt>Jumlah</dt><dd>{{ s.qty }} {{ s.satuan }}</dd></div><div><dt>Total</dt><dd>{{ formatRp(s.qty*s.harga) }}</dd></div></dl>
            <div class="record-footer"><button class="drag-handle" :disabled="!canDragRows" aria-label="Geser urutan" @touchstart.stop="handleTouchStart($event,s,index)" @touchmove.stop="handleTouchMove" @touchend.stop="handleTouchEnd" @touchcancel.stop="endDrag"><GripVertical />Urutan {{ s.no }}</button><div class="actions"><button class="icon-action" aria-label="Salin seserahan" @click="openCopy(s)"><Copy /></button><button class="icon-action" aria-label="Edit seserahan" @click="openEdit(s)"><Pencil /></button><button class="icon-action danger" aria-label="Hapus seserahan" @click="confirmDelete(s)"><Trash2 /></button></div></div>
          </article>
          <PlannerEmptyState v-if="!filteredItems.length" :title="hasFilters ? 'Seserahan tidak ditemukan' : 'Belum ada seserahan'" :description="hasFilters ? 'Tidak ada data yang sesuai dengan pencarian atau filter.' : 'Mulai catat kebutuhan seserahan untuk hari bahagiamu.'"><template #icon><Gift /></template><template #action><button :class="hasFilters ? 'secondary-action' : 'primary-action'" @click="hasFilters ? resetFilters() : openCreate()"><component :is="hasFilters ? X : Plus" />{{ hasFilters ? 'Reset Filter' : 'Tambah Seserahan Pertama' }}</button></template></PlannerEmptyState>
        </section>
      </PlannerDataPanel>
    </div>

    <Teleport to="body"><div v-if="showModal" class="drawer-backdrop" @click.self="closeModal"><aside class="drawer" role="dialog" aria-modal="true" aria-labelledby="drawer-title"><div class="drawer-handle"></div><header class="drawer-header"><div><span class="eyebrow">Daftar seserahan</span><h3 id="drawer-title" class="drawer-title">{{ editItem ? 'Edit Seserahan' : copyItem ? 'Salin Seserahan' : 'Tambah Seserahan' }}</h3></div><button class="icon-action" aria-label="Tutup" @click="closeModal"><X /></button></header>
      <form class="drawer-body" @submit.prevent="save">
        <div class="field"><label for="item-name">Nama Item <span>*</span></label><div class="field-control"><TextCursorInput /><input id="item-name" v-model="form.nama_item" required placeholder="Contoh: Cincin emas"></div></div>
        <div class="form-row"><div class="field"><label for="category">Kategori <span>*</span></label><div class="field-control"><Tags /><select id="category" v-model="form.kategori" required><option value="">Pilih kategori</option><option v-for="k in kategoriOptions" :key="k">{{ k }}</option></select><ChevronDown /></div></div><div class="field"><label for="recipient">Peruntukan <span>*</span></label><div class="field-control"><Users /><select id="recipient" v-model="form.untuk" required><option value="">Pilih peruntukan</option><option value="cpp">Calon Pengantin Pria</option><option value="cpw">Calon Pengantin Wanita</option><option value="kedua">Kedua Calon Pengantin</option></select><ChevronDown /></div></div></div>
        <div class="form-row"><div class="field"><label for="qty">Qty <span>*</span></label><div class="field-control"><Hash /><input id="qty" v-model="form.qty" type="number" min="1" required></div></div><div class="field"><label for="unit">Satuan</label><div class="field-control"><Package /><input id="unit" v-model="form.satuan" placeholder="buah, set, dll"></div></div></div>
        <div class="form-row"><div class="field"><label for="price">Harga Satuan <span>*</span></label><div class="field-control"><BadgeDollarSign /><input id="price" v-model="form.harga" type="number" min="0" required></div></div><div class="field"><label for="status">Status <span>*</span></label><div class="field-control"><CircleGauge /><select id="status" v-model="form.status" required><option value="belum">Belum Dibeli</option><option value="sudah_dibeli">Sudah Dibeli</option></select><ChevronDown /></div></div></div>
        <footer class="drawer-footer"><button type="button" class="secondary-action" @click="closeModal">Batal</button><button type="submit" class="primary-action" :disabled="saving"><LoaderCircle v-if="saving" class="spinner" />{{ saving ? 'Menyimpan...' : 'Simpan Seserahan' }}</button></footer>
      </form></aside></div></Teleport>
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

import { BadgeDollarSign, ChevronDown, CircleCheckBig, CircleGauge, Copy, FileSpreadsheet, Gift, GripVertical, Hash, ListFilter, LoaderCircle, Mars, Package, Pencil, Plus, Search, Tags, TextCursorInput, Trash2, Users, Venus, WalletCards, X } from '@lucide/vue';
import { showToast, confirmDeleteDialog } from '@/utils.js';
import * as XLSX from 'xlsx';

const props=defineProps({items:Array,kategoriOptions:Array,totalItem:Number,sudahBeli:Number,belumBeli:Number,totalHarga:Number});
const localItems=ref([...props.items]), searchQuery=ref(''), filterKategori=ref(''), filterUntuk=ref(''), filterStatus=ref(''), mobileFiltersOpen=ref(false);
const showModal=ref(false), editItem=ref(null), copyItem=ref(null), saving=ref(false), draggedIndex=ref(null), draggedId=ref(null), dragOverIndex=ref(null), dragOverId=ref(null);
const defaultForm=()=>({kategori:'',nama_item:'',untuk:'',qty:1,satuan:'buah',harga:0,status:'belum'}), form=ref(defaultForm());
watch(()=>props.items,v=>localItems.value=[...v]); watch(showModal,v=>document.body.style.overflow=v?'hidden':'');
onMounted(()=>window.addEventListener('keydown',handleKey)); onBeforeUnmount(()=>{window.removeEventListener('keydown',handleKey);document.body.style.overflow=''}); function handleKey(e){if(e.key==='Escape'&&showModal.value)closeModal()}
const progressPct=computed(()=>props.totalItem?Math.round(props.sudahBeli/props.totalItem*100):0);
const hasFilters=computed(()=>Boolean(searchQuery.value||filterKategori.value||filterUntuk.value||filterStatus.value));
const filteredItems=computed(()=>localItems.value.filter(s=>(!searchQuery.value||s.nama_item?.toLowerCase().includes(searchQuery.value.toLowerCase())||s.kategori?.toLowerCase().includes(searchQuery.value.toLowerCase()))&&(!filterKategori.value||s.kategori===filterKategori.value)&&(!filterUntuk.value||s.untuk===filterUntuk.value)&&(!filterStatus.value||s.status===filterStatus.value)));
const canDragRows=computed(()=>!hasFilters.value), filteredTotalHarga=computed(()=>filteredItems.value.reduce((n,s)=>n+(s.qty*s.harga),0));
const formatRp=n=>'Rp'+Number(n||0).toLocaleString('id-ID'); const statusLabel=s=>s==='sudah_dibeli'?'Sudah Dibeli':'Belum Dibeli';
function untukLabel(v){return {cpw:'CPW',cpp:'CPP',kedua:'CPP & CPW'}[v]||'-'} function untukChipClass(v){return v==='cpw'?'chip--cpw':v==='cpp'?'chip--cpp':v==='kedua'?'chip--ok':'chip--neutral'}
function resetFilters(){searchQuery.value='';filterKategori.value='';filterUntuk.value='';filterStatus.value=''}
function itemForm(s){return {kategori:s.kategori,nama_item:s.nama_item,untuk:s.untuk,qty:s.qty,satuan:s.satuan||'',harga:s.harga,status:s.status}}
function openCreate(){editItem.value=null;copyItem.value=null;form.value=defaultForm();showModal.value=true} function openCopy(s){editItem.value=null;copyItem.value=s;form.value=itemForm(s);showModal.value=true} function openEdit(s){editItem.value=s;copyItem.value=null;form.value=itemForm(s);showModal.value=true} function closeModal(){showModal.value=false;editItem.value=null;copyItem.value=null}
function save(){saving.value=true;const editing=editItem.value;router[editing?'patch':'post'](editing?route('seserahan.update',editing.id):route('seserahan.store'),{...form.value,qty:Number(form.value.qty)||1,harga:Number(form.value.harga)||0},{preserveScroll:true,onSuccess:()=>{showToast(editing?'Seserahan berhasil diperbarui.':copyItem.value?'Salinan seserahan berhasil ditambahkan.':'Seserahan berhasil ditambahkan.');closeModal();saving.value=false},onError:()=>saving.value=false,onFinish:()=>saving.value=false})}
function confirmDelete(s){confirmDeleteDialog(()=>router.delete(route('seserahan.destroy',s.id),{preserveScroll:true,onSuccess:()=>showToast('Seserahan berhasil dihapus.')}),{title:'Hapus item seserahan ini?',description:`Item “${s.nama_item}” akan dihapus permanen dari daftar seserahan.`})}
function rowClasses(s){return {'is-dragging':draggedId.value===s.id,'is-drop-target':dragOverId.value===s.id,'is-drop-before':dropPlacement(s.id)==='before','is-drop-after':dropPlacement(s.id)==='after','is-drag-disabled':!canDragRows.value}}
function startDrag(s,index,e){if(!canDragRows.value)return;draggedIndex.value=index;draggedId.value=s.id;if(e?.dataTransfer)e.dataTransfer.effectAllowed='move'}
function handleTouchStart(_,s,index){if(!canDragRows.value)return;startDrag(s,index,null)} function handleTouchMove(e){if(draggedId.value===null)return;e.preventDefault();const row=document.elementFromPoint(e.touches[0].clientX,e.touches[0].clientY)?.closest('.record-card, tr.draggable-row');if(!row)return;const records=[...document.querySelectorAll('.mobile-records .record-card')];const index=records.includes(row)?records.indexOf(row):[...row.parentNode.children].indexOf(row);const item=filteredItems.value[index];if(item)setDragOver(item,index)} function handleTouchEnd(){dragOverIndex.value!==null?dropRow(dragOverIndex.value):endDrag()}
function setDragOver(s,index){if(canDragRows.value&&draggedId.value!==s.id){dragOverId.value=s.id;dragOverIndex.value=index}} function dropPlacement(id){if(dragOverId.value!==id)return null;return dragOverIndex.value>draggedIndex.value?'after':'before'}
function dropRow(index){if(!canDragRows.value||draggedIndex.value===null||index===draggedIndex.value){endDrag();return}const list=[...localItems.value],[moved]=list.splice(draggedIndex.value,1);list.splice(index,0,moved);localItems.value=list.map((s,i)=>({...s,no:i+1}));router.patch(route('seserahan.reorder'),{ids:localItems.value.map(s=>s.id)},{preserveScroll:true,onSuccess:()=>showToast('Urutan seserahan berhasil disimpan.'),onError:()=>{localItems.value=[...props.items];showToast('Urutan seserahan gagal disimpan.')}});endDrag()} function endDrag(){draggedIndex.value=draggedId.value=dragOverIndex.value=dragOverId.value=null}
function exportToExcel(){if(!filteredItems.value.length){showToast('Tidak ada data untuk diekspor');return}const now=new Date(), rows=filteredItems.value.map((s,i)=>[s.no||i+1,s.kategori||'',s.nama_item||'',untukLabel(s.untuk),s.qty||0,s.satuan||'',s.harga||0,(s.qty||0)*(s.harga||0),statusLabel(s.status)]);rows.push(['','TOTAL','','','','','',filteredTotalHarga.value,'']);const ws=XLSX.utils.aoa_to_sheet([['DAFTAR LIST SESERAHAN'],[`Dicetak pada: ${now.toLocaleString('id-ID')}`],[],['No','Kategori','Nama Item','Untuk','Qty','Satuan','Harga Satuan','Total','Status'],...rows]);ws['!merges']=[{s:{r:0,c:0},e:{r:0,c:8}},{s:{r:1,c:0},e:{r:1,c:8}}];ws['!cols']=[{wch:5},{wch:20},{wch:30},{wch:20},{wch:10},{wch:15},{wch:20},{wch:20},{wch:20}];const wb=XLSX.utils.book_new();XLSX.utils.book_append_sheet(wb,ws,'List Seserahan');XLSX.writeFile(wb,`List_Seserahan_${now.toISOString().split('T')[0]}.xlsx`);showToast('Data berhasil diekspor ke Excel')}
</script>

<style scoped>
.seserahan-page{display:grid;width:min(1200px,calc(100% - 48px));margin:0 auto;grid-template-columns:minmax(0,1fr);row-gap:18px;padding-bottom:24px;color:#1a3300}.seserahan-page>*{margin-block:0}.hero-card{display:flex;min-height:190px;padding:32px;align-items:center;justify-content:space-between;gap:28px;border:1px solid #1a3300;border-radius:12px;background:#fcfaf5}.eyebrow{display:inline-block;margin-bottom:8px;padding:4px 8px;border-radius:6px;background:#ffe95c;color:#1a3300;font-size:11px;font-weight:600;letter-spacing:0;text-transform:none}.hero-card h1{color:#1a3300;font-family:Inter,system-ui,sans-serif;font-size:clamp(32px,4vw,40px);font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{margin-top:12px;color:#1a3300;font-size:16px;line-height:1.5}.primary-action,.secondary-action,.icon-action,.filter-toggle,.drag-handle{display:inline-flex;align-items:center;justify-content:center;border:0;font:inherit;cursor:pointer}.primary-action{min-height:43px;padding:10px 16px;gap:8px;border-radius:6px;background:#1a3300;color:#fcfaf5;font-size:13px;font-weight:700;box-shadow:0 1px 2px rgba(0,0,0,.05)}.primary-action:hover{background:var(--accent-hover)}.primary-action:disabled{opacity:.65;cursor:wait}.primary-action svg,.secondary-action svg{width:16px}.summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.summary-card{display:flex;padding:18px 20px;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.summary-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:10px;background:var(--accent-soft);color:var(--accent-hover)}.summary-icon svg{width:20px}.summary-icon--money{background:var(--warn-bg);color:var(--warn-text)}.summary-label{font-size:12px;font-weight:600;color:var(--text-muted)}.summary-value{margin-top:5px;font-size:clamp(20px,2vw,26px);font-weight:800}.summary-sub{margin-top:4px;color:var(--text-muted);font-size:12px}.progress-track{height:6px;margin-top:7px;overflow:hidden;border-radius:99px;background:var(--surface-muted)}.progress-track span{display:block;height:100%;background:linear-gradient(90deg,var(--accent-hover),var(--accent-light))}.data-panel{overflow:hidden;border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:var(--shadow-sm)}.data-panel>.toolbar{display:flex;margin:0;padding:12px;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--border);border-radius:0;box-shadow:none}.toolbar-top{display:flex;flex:1}.search-wrap{position:relative;flex:1}.search-wrap>svg{position:absolute;left:13px;top:50%;width:16px;transform:translateY(-50%);color:var(--text-dim)}.toolbar-control,.secondary-action{box-sizing:border-box;height:40px;min-height:40px;padding:0 12px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted);font:500 13px var(--font);line-height:1}.search-wrap input{width:100%;padding-left:39px}.toolbar-filters{display:flex;gap:8px}.select-wrap{position:relative;display:flex}.select-wrap select{width:155px;padding-right:34px;appearance:none}.select-wrap>svg{position:absolute;right:11px;top:50%;width:15px;transform:translateY(-50%);pointer-events:none}.secondary-action{gap:7px}.filter-toggle{display:none;position:relative;width:44px;height:44px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text-muted)}.filter-toggle>svg{width:19px}.filter-toggle>span{position:absolute;top:7px;right:7px;width:6px;height:6px;border-radius:50%;background:var(--accent)}.filter-toggle.active{background:var(--accent-soft);color:var(--accent-hover)}.desktop-table{display:block;margin:0;padding:0;overflow-x:auto}.desktop-table:deep(> .empty-card){margin:0;border:0!important;border-radius:0!important;box-shadow:none!important}.desktop-table .tbl{width:100%;margin:0!important;border-spacing:0;border-collapse:collapse}.tbl thead tr{height:42px}.tbl thead th{height:42px;padding-block:0!important;vertical-align:middle}.tbl th{padding:11px 12px;background:var(--surface-muted);color:var(--text-muted);font-size:11px;font-weight:700;letter-spacing:.055em;text-transform:uppercase;vertical-align:middle}.tbl td{padding:12px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:middle}.tbl td small{display:block;margin-top:3px;color:var(--text-muted)}.right{text-align:right}.center{text-align:center}.strong{font-weight:700}.actions{display:flex;justify-content:center}.icon-action{width:36px;height:36px;border-radius:8px;background:transparent;color:var(--text-dim)}.icon-action:hover{background:var(--accent-soft);color:var(--accent-hover)}.icon-action.danger:hover{background:var(--danger-bg);color:var(--danger-text)}.icon-action svg{width:15px}.drag-cell{white-space:nowrap;touch-action:none;color:var(--text-dim);cursor:grab}.drag-cell svg{display:inline;width:16px;margin-right:4px;vertical-align:middle;color:var(--text-dim);opacity:.62;stroke-width:1.8}.is-drag-disabled .drag-cell svg{opacity:.28}.draggable-row.is-dragging{opacity:.45}.draggable-row.is-drop-before{box-shadow:inset 0 3px var(--accent)}.draggable-row.is-drop-after{box-shadow:inset 0 -3px var(--accent)}.is-drag-disabled .drag-cell{opacity:.4;cursor:default}.tbl tfoot td{padding:10px 14px;background:var(--rose-pale);border-top:2px solid var(--border);border-bottom:0;font-size:13px;line-height:normal;vertical-align:middle}.tbl tfoot td:first-child{font-size:11px;font-weight:700;text-transform:uppercase}.chip--cpw{background:#fdf0f8;color:#c4719e}.chip--cpp{background:#eef4ff;color:#5a82c4}.source-chip,.source-label{display:inline-flex;align-items:center;gap:4px}.source-chip svg,.source-label svg{width:13px;height:13px;flex:none}.mobile-records{display:none}.drawer-backdrop{position:fixed;inset:0;z-index:1000;display:flex;justify-content:flex-end;background:rgba(34,45,37,.28);backdrop-filter:blur(2px);animation:drawerFade .2s ease}.drawer{display:flex;width:min(440px,100vw);height:100dvh;flex-direction:column;background:var(--surface);box-shadow:-18px 0 48px rgba(40,54,44,.16);animation:drawerFromRight .25s ease}.drawer-handle{display:none}.drawer-header{display:flex;min-height:86px;padding:20px 22px;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid var(--border)}.drawer-header .eyebrow{display:block;margin:0 0 3px;font-size:9px;font-weight:700;line-height:1.2;letter-spacing:.1em;text-transform:uppercase}.drawer-header .drawer-title{margin:0;font-family:var(--font-display);font-size:18px;font-weight:700;line-height:1.25}.drawer-body{display:flex;min-height:0;padding:22px;gap:16px;flex:1;flex-direction:column;overflow:auto}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{display:grid;gap:7px}.field label{font-size:13px;font-weight:600}.field label span{color:var(--accent)}.field-control{position:relative;display:flex;align-items:center;overflow:hidden;border:1px solid var(--border);border-radius:10px;background:#fbfcfa;transition:border-color .16s ease,box-shadow .16s ease,background .16s ease}.field-control:focus-within{border-color:var(--accent);background:var(--surface);box-shadow:0 0 0 3px var(--accent-soft)}.field-control>svg{position:absolute;left:13px;width:17px;color:#7a8b7e}.field-control>svg:last-child:not(:first-child){left:auto;right:13px;width:15px}.field-control input,.field-control select{width:100%;min-height:46px;padding:10px 40px;border:0!important;border-radius:inherit;outline:0;background:transparent!important;box-shadow:none!important;font:500 13px var(--font)}.field-control input:focus,.field-control select:focus{border:0!important;outline:0;box-shadow:none!important}.field-control select{appearance:none}.drawer-footer{display:flex;justify-content:flex-end;gap:9px;margin:auto -22px -22px;padding:18px 22px 22px;border-top:1px solid var(--border)}.spinner{animation:spin .8s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@keyframes drawerFade{from{opacity:0}}@keyframes drawerFromRight{from{transform:translateX(100%)}}@keyframes drawerFromBottom{from{transform:translateY(100%)}}
.seserahan-page{font-family:Inter,system-ui,sans-serif;row-gap:16px}.hero-card{gap:24px}.hero-card h1{font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:40px;font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{font-size:16px}.primary-action{min-height:40px;padding:12px 20px;font-size:16px;font-weight:500}.secondary-action,.toolbar-control{border-color:#b6b6b6;border-radius:6px;background:#fcfaf5;color:#1a3300;font-family:Inter,system-ui,sans-serif;font-size:14px}.summary-grid{gap:16px}.summary-card{padding:24px;gap:16px;border-color:#b6b6b6;background:#fcfaf5;box-shadow:none}.summary-icon{border-radius:12px;background:#d5f5c2;color:#1a3300}.summary-icon--money{background:#a8e5e5;color:#1a3300}.summary-label,.summary-sub,.tbl th,.tbl td small,.drag-cell,.category,.record-card dt,.drag-handle{color:#1a3300}.summary-label{font-size:14px}.summary-value{font-size:28px}.summary-sub{font-size:11px}.progress-track{border-radius:9999px;background:#f1f1f1}.progress-track span{background:#1a3300}.data-panel{border-color:#b6b6b6;border-radius:12px;background:#fcfaf5;box-shadow:none}.data-panel>.toolbar{border-color:#b6b6b6}.filter-toggle,.icon-action{border-radius:6px;color:#1a3300}.filter-toggle{border-color:#b6b6b6;background:#fcfaf5}.filter-toggle.active,.icon-action:hover{background:#d5f5c2;color:#1a3300}.filter-toggle>span{background:#1a3300}.tbl th{font-size:11px}.tbl td{border-color:#b6b6b6;font-size:12px}.tbl tfoot td{background:#f6d0ff;border-color:#b6b6b6}.record-card{padding:24px;border-color:#b6b6b6;border-radius:12px;background:#fcfaf5;box-shadow:none}.record-head h2{font-size:16px}.drawer-backdrop{background:rgba(26,51,0,.28)}.drawer{width:min(440px,100vw);background:#fcfaf5;box-shadow:none}.drawer-header,.drawer-footer{border-color:#b6b6b6}.drawer-header .drawer-title{font-family:Inter,system-ui,sans-serif;font-size:20px}.drawer-body{padding:24px;gap:16px}.field label{font-size:14px;font-weight:600}.field-control{border-color:#b6b6b6;border-radius:6px;background:#fcfaf5}.field-control:focus-within{border-color:#1a3300;background:#fcfaf5;box-shadow:0 0 0 1px #1a3300}.field-control>svg{color:#1a3300}.field-control input,.field-control select{font:500 14px Inter,system-ui,sans-serif}.drawer-footer{margin:auto -24px -24px;padding:16px 24px 24px}.chip--cpw{background:#f6d0ff;color:#1a3300}.chip--cpp{background:#a8e5e5;color:#1a3300}
@media(max-width:900px){.toolbar{align-items:stretch;flex-direction:column}.toolbar-top{width:100%}.toolbar-filters{width:100%;flex-wrap:wrap}.select-wrap{flex:1}.select-wrap select{width:100%;min-width:145px}}
@media(prefers-reduced-motion:reduce){.drawer-backdrop,.drawer,.draggable-row,.primary-action,.icon-action,.progress-track span{animation:none!important;transition:none!important}}
@media(max-width:767px){.seserahan-page{width:calc(100% - 32px);gap:16px}.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px}.hero-card h1{font-size:25px}.hero-card p{font-size:13px}.hero-card .primary-action{width:100%}.summary-grid{grid-template-columns:1fr 1fr;gap:8px}.summary-card{padding:13px;gap:9px}.summary-card:last-child{grid-column:1/-1}.summary-icon{width:34px;height:34px}.summary-value{font-size:19px}.data-panel{overflow:visible;border:0;background:transparent;box-shadow:none}.toolbar{display:block;padding:12px;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.toolbar-top{gap:8px}.toolbar-control,.secondary-action{height:44px;min-height:44px}.filter-toggle{display:flex}.toolbar-filters{display:none;grid-template-columns:1fr 1fr;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}.toolbar-filters.open{display:grid}.toolbar-filters>*{width:100%}.select-wrap select{width:100%}.export-action{grid-column:1/-1}.desktop-table{display:none}.mobile-records{display:grid;gap:10px;margin-top:14px}.record-card{padding:14px;border:1px solid var(--border);border-left-width:4px;border-radius:12px;background:var(--surface);box-shadow:var(--shadow-sm)}.record-card.bought{border-left-color:var(--ok-text)}.record-card.pending{border-left-color:var(--danger-text)}.record-head{display:flex;justify-content:space-between;gap:8px}.record-head h2{margin-top:6px;font-size:14px}.category{color:var(--text-muted);font-size:11px}.record-card dl{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:13px;padding:11px 0;border-block:1px solid var(--border)}.record-card dt{color:var(--text-muted);font-size:10px}.record-card dd{margin-top:3px;font-size:12px;font-weight:700}.record-footer{display:flex;margin-top:8px;align-items:center;justify-content:space-between}.drag-handle{gap:5px;padding:8px;background:none;color:var(--text-muted);font-size:11px;touch-action:none}.drag-handle svg{width:15px}.drawer-backdrop{align-items:flex-end}.drawer{width:100%;height:auto;max-height:92dvh;border-radius:20px 20px 0 0;box-shadow:0 -18px 48px rgba(40,54,44,.16);animation:drawerFromBottom .25s ease}.drawer-handle{display:block;width:40px;height:4px;margin:9px auto 0;border-radius:99px;background:var(--border)}.drawer-header{min-height:68px;padding:10px 16px 13px}.drawer-header .icon-action{width:44px;height:44px}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.form-row{grid-template-columns:1fr}.field-control input,.field-control select{font-size:16px}.drawer-footer{position:sticky;bottom:calc(-16px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}.drawer-footer>*{flex:1}}
.hero-card h1{font-size:55px}.hero-card p{font-size:18px}.eyebrow{font-size:12px}.secondary-action,.toolbar-control,.tbl td,.field-control input,.field-control select{font-size:16px}.drawer-header .drawer-title,.record-head h2{font-size:28px}.summary-label,.field label,.tbl td small{font-size:14px}.summary-sub,.tbl th,.category,.record-card dt,.drag-handle{font-size:11px}
@media(max-width:767px){.hero-card{padding:16px;gap:16px}.hero-card h1{font-size:40px}.hero-card p{font-size:16px}.summary-grid{gap:8px}.summary-card{padding:16px;gap:8px}.summary-value{font-size:20px}.toolbar{border-color:#b6b6b6;background:#fcfaf5}.mobile-records{gap:16px;margin-top:16px}.record-card{padding:16px;border-color:#b6b6b6;background:#fcfaf5;box-shadow:none}.record-head h2{font-size:28px}.record-card dt{font-size:11px}.record-card dd{font-size:16px}.drawer{border-radius:16px 16px 0 0;box-shadow:none}.drawer-handle{border-radius:9999px;background:#b6b6b6}.drawer-footer{background:#fcfaf5}}
/* Sunset Marketplace — final scoped overrides */
.seserahan-page{color:#240029}.hero-card{border-color:#d4ccd4;border-radius:14px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f);box-shadow:0 2px 2px rgba(32,0,36,.05)}.hero-card h1,.hero-card p,.eyebrow{color:#240029}.eyebrow{background:transparent;font-family:'JetBrains Mono',monospace;letter-spacing:.1em;text-transform:uppercase}.primary-action{background:#df37a7;color:#fff}.primary-action:hover{background:#c92f96}.secondary-action,.toolbar-control,.field-control{border-color:#6d526d;color:#240029}.summary-card,.data-panel,.record-card{border-color:#d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.progress-track span{background:#df37a7}.tbl th,.tbl td,.summary-label,.summary-sub,.field label{color:#240029}.icon-action:hover{background:#fff1f8;color:#df37a7}
/* Visual parity with Guest/Index.vue */
.seserahan-page{row-gap:24px;padding-bottom:32px;font-family:var(--font-inter)}
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
.drawer-header .drawer-title{font-family:var(--font-inter);font-size:28px;font-weight:700}
.drawer-body{padding:22px;gap:16px}
.field-control{overflow:visible;transition:none}
.field-control:focus-within{border-color:#1a3300;background:#fcfaf5;box-shadow:0 0 0 2px #ffe95c}
.field-control>svg{color:#7a8b7e}
.field-control input,.field-control select{font-family:var(--font-inter);font-size:16px}
.drawer-footer{margin:auto -22px -22px;padding:18px 22px 22px}
@media(max-width:767px){.seserahan-page{gap:14px}.hero-card{padding:18px 16px;gap:14px}.summary-card{padding:13px;gap:9px}.mobile-records{gap:10px;margin-top:14px}.record-card{padding:14px;box-shadow:var(--shadow-sm)}.drawer{border-radius:20px 20px 0 0}.drawer-body{padding:16px 16px calc(16px + env(safe-area-inset-bottom))}.drawer-footer{margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(16px + env(safe-area-inset-bottom));background:var(--surface)}}

/* Mobile-only layout refinements, aligned with the approved Budget pattern. */
@media(max-width:767px){
  .seserahan-page{width:calc(100% - 24px);row-gap:12px;padding-bottom:16px}
  .hero-card{padding:18px 16px;gap:14px;border-radius:12px}
  .hero-card h1{font-size:clamp(28px,9vw,36px);line-height:1.05;letter-spacing:.01em}
  .hero-card p{margin-top:8px;font-size:14px;line-height:1.45}
  .hero-card .primary-action{min-height:44px;font-size:14px}
  .summary-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  .summary-card{min-width:0;padding:12px;gap:8px;align-items:flex-start}
  .summary-card:last-child{grid-column:1/-1}
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
  .record-card.bought{border-left-color:var(--ok-text)}
  .record-card.pending{border-left-color:var(--danger-text)}
  .record-head{align-items:flex-start}
  .record-head>div{min-width:0}
  .record-head h2{margin-top:5px;font-size:18px;line-height:1.25;overflow-wrap:anywhere}
  .record-head>.chip{max-width:42%;flex:none;text-align:center;white-space:normal}
  .record-card dl{grid-template-columns:repeat(3,minmax(0,1fr));gap:4px;margin-top:12px;padding:10px 0}
  .record-card dl>div{min-width:0}
  .record-card dt{font-size:10px}
  .record-card dd{font-size:clamp(11px,3.4vw,14px);line-height:1.3;overflow-wrap:anywhere}
  .source-label{min-width:0;flex-wrap:wrap}
  .record-footer{margin-top:6px;gap:8px}
  .record-footer .actions{flex:none;gap:2px}
  .record-footer .icon-action{width:40px;height:40px}
  .drag-handle{min-width:0;text-align:left}
  .drawer{max-height:94dvh;border-radius:16px 16px 0 0}
  .drawer-header{min-height:64px;padding:8px 14px 12px}
  .drawer-header .drawer-title{font-size:18px}
  .drawer-body{padding:14px 16px calc(16px + env(safe-area-inset-bottom));gap:14px}
  .field{gap:6px}
  .field label{font-size:13px}
  .field-control input,.field-control select{min-height:44px}
  .drawer-footer{bottom:calc(-14px - env(safe-area-inset-bottom));margin:auto -16px calc(-16px - env(safe-area-inset-bottom));padding:12px 16px calc(14px + env(safe-area-inset-bottom));gap:8px}
}

/* Drawer CRUD — selaras dengan Anggaran dan Persiapan. */
.drawer{
  width:min(760px,100vw);
  border-left:1px solid rgba(109,82,109,.28);
  background:#fff;
  box-shadow:-18px 0 48px rgba(36,0,41,.12);
}
.drawer-body{gap:20px;padding:24px 28px}
.form-row{grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
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
.drawer-body .field-control>select{
  min-height:48px;
  padding-left:44px!important;
  border-radius:12px;
  color:#240029;
  font-family:var(--font-inter);
  font-size:16px;
  font-weight:400;
}
.drawer-body .field-control>select{padding-right:44px!important}
.drawer-body .field-control>input[type='number']{padding-right:12px!important}
.field-control input::placeholder{color:#9b879b;font-weight:400}
.drawer-footer{margin:auto -28px -24px;padding:18px 28px 24px;gap:12px;background:#fff}
.drawer-footer .secondary-action,
.drawer-footer .primary-action{min-height:44px;padding-inline:20px}
.drawer-header .icon-action:focus-visible,
.drawer-footer button:focus-visible{outline:3px solid rgba(223,55,167,.22);outline-offset:2px}

@media(max-width:767px){
  .drawer{width:100%;border-left:0;box-shadow:0 -18px 48px rgba(36,0,41,.12)}
  .drawer-body{gap:16px;padding:16px 16px calc(16px + env(safe-area-inset-bottom))}
  .form-row{grid-template-columns:1fr;gap:16px}
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
