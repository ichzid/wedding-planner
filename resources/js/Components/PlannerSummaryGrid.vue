<template><section class="summary-grid"><slot /></section></template>
<style scoped>
.summary-grid{display:grid;grid-template-columns:repeat(var(--summary-columns,3),minmax(0,1fr));gap:12px}
@media(max-width:900px){.summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){.summary-grid{gap:8px}}
</style>
