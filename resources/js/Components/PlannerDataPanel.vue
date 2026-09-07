<template><section class="data-panel"><slot /></section></template>
<style scoped>
.data-panel{overflow:hidden;border:1px solid #d4ccd4;border-radius:14px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.11),0 2px 2px rgba(32,0,36,.05)}
@media(max-width:767px){.data-panel{overflow:visible;border:0;background:transparent;box-shadow:none}}
</style>
