<template><article class="summary-card"><span class="summary-icon" :class="iconClass"><slot name="icon" /></span><div class="summary-content"><p class="summary-label">{{ label }}</p><p class="summary-value"><slot name="value">{{ value }}</slot></p><slot /></div></article></template>
<script setup>
defineProps({ label:{type:String,required:true}, value:{type:[String,Number],default:''}, iconClass:{type:String,default:''} });
</script>
<style scoped>
.summary-card{display:flex;min-width:0;padding:18px 20px;gap:14px;border:1px solid #d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}.summary-icon{display:flex;width:38px;height:38px;align-items:center;justify-content:center;flex:none;border-radius:6px;background:#fff1f8;color:#df37a7}.summary-icon:deep(svg){width:20px}.summary-content{min-width:0}.summary-label{color:#240029;font-size:14px;font-weight:600}.summary-value{margin-top:5px;color:#240029;font-size:clamp(19px,2vw,25px);font-weight:800}.summary-content:deep(.summary-sub){margin-top:4px;color:#240029;font-size:11px}.summary-content:deep(.progress-track){height:6px;margin-top:7px;overflow:hidden;border-radius:99px;background:#f1f1f1}.summary-content:deep(.progress-track span){display:block;height:100%;background:#df37a7}
@media(max-width:767px){.summary-card{padding:13px;gap:9px}.summary-icon{width:34px;height:34px}.summary-value{font-size:20px}}
</style>
