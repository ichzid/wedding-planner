<template>
  <section class="toolbar" :aria-label="ariaLabel">
    <div class="toolbar-top"><slot name="search" /><button type="button" class="filter-toggle" :class="{active:open||active}" :aria-expanded="open" :aria-controls="controls" :aria-label="toggleLabel" @click="$emit('toggle')"><ListFilter aria-hidden="true" /><span v-if="active" aria-hidden="true" /></button></div>
    <div :id="controls" class="toolbar-filters" :class="{open}"><slot name="filters" /></div>
  </section>
</template>
<script setup>
import { ListFilter } from '@lucide/vue';
defineProps({open:Boolean,active:Boolean,controls:{type:String,required:true},ariaLabel:{type:String,default:'Pencarian dan filter'},toggleLabel:{type:String,default:'Tampilkan filter'}});defineEmits(['toggle']);
</script>
<style scoped>
.toolbar{display:flex;margin:0;padding:12px;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #d4ccd4}.toolbar-top{display:flex;flex:1;min-width:0}.filter-toggle{display:none;position:relative;width:44px;height:44px;flex:none;align-items:center;justify-content:center;border:1px solid #6d526d;border-radius:6px;background:#fff;color:#240029;cursor:pointer}.filter-toggle svg{width:19px}.filter-toggle span{position:absolute;top:7px;right:7px;width:6px;height:6px;border-radius:50%;background:#df37a7}.filter-toggle.active{background:#fff1f8;color:#df37a7}.toolbar-filters{display:flex;gap:8px}
@media(max-width:900px){.toolbar{align-items:stretch;flex-direction:column}.toolbar-filters{width:100%;flex-wrap:wrap}}
@media(max-width:767px){.toolbar{display:block;padding:12px;border:1px solid #d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.toolbar-top{width:100%;gap:8px}.filter-toggle{display:flex}.toolbar-filters{display:none;grid-template-columns:1fr 1fr;margin-top:10px;padding-top:10px;border-top:1px solid #d4ccd4}.toolbar-filters.open{display:grid}.toolbar-filters:deep(>*){width:100%!important}.toolbar-filters:deep(.export-action){grid-column:1/-1}}
</style>
