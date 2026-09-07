<template>
  <AppLayout>
    <main class="support-page">
      <header class="page-header">
        <h1>Kami siap membantumu</h1>
        <p>Hubungi tim dukungan jika kamu mengalami kendala saat menggunakan Ruang Pasangan.</p>
      </header>

      <section class="support-card">
        <div class="support-card__copy">
          <span class="status"><span /> Tim dukungan tersedia</span>
          <h2>Ada yang bisa kami bantu?</h2>
          <p>Sampaikan pertanyaan atau kendalamu melalui email. Sertakan detail masalah agar tim kami dapat membantu dengan lebih baik.</p>
        </div>
        <a href="mailto:dukungan@ruangpasangan.id" class="support-action"><Mail aria-hidden="true" /><span><small>Email dukungan</small>dukungan@ruangpasangan.id</span><ArrowRight aria-hidden="true" /></a>
      </section>
    </main>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowRight, Mail } from '@lucide/vue';
</script>

<style scoped>
.support-page{width:min(860px,calc(100% - 48px));margin:0 auto;padding:12px 0 64px;color:#240029;font-family:Inter,system-ui,sans-serif}.page-header{display:block!important;width:100%;padding:32px 24px 18px;text-align:center}.page-header h1{max-width:760px;margin:0 auto;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:clamp(38px,5vw,55px);font-weight:800;line-height:1.08;letter-spacing:.02em}.page-header p{max-width:680px;margin:14px auto 0;color:#240029;font-size:17px;line-height:1.55}.support-card{margin-top:28px;padding:30px;border:1px solid #d4ccd4;border-radius:16px;background:#fff;box-shadow:0 6px 24px rgba(32,0,36,.06)}.status{display:inline-flex;align-items:center;gap:7px;color:#6d526d;font-size:12px;font-weight:700}.status span{width:8px;height:8px;border-radius:50%;background:#df37a7;box-shadow:0 0 0 4px #fff1f8}.support-card h2{margin-top:15px;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:28px}.support-card__copy>p{max-width:620px;margin-top:9px;color:#6d526d;font-size:14px;line-height:1.6}.support-action{display:flex;min-height:66px;margin-top:24px;padding:12px 16px;align-items:center;gap:12px;border:1px solid rgba(223,55,167,.3);border-radius:12px;background:#fffafd;color:#240029;text-decoration:none}.support-action>svg:first-child{width:21px;color:#df37a7}.support-action>svg:last-child{width:17px;margin-left:auto}.support-action span{display:grid;gap:3px;font-size:14px;font-weight:700}.support-action small{color:#6d526d;font-size:11px;font-weight:500}.support-action:hover{border-color:#df37a7;background:#fff1f8;color:#df37a7}@media(max-width:767px){.support-page{width:calc(100% - 24px);padding:12px 0 40px}.page-header{padding:28px 18px}.page-header h1{font-size:38px}.page-header p{font-size:15px}.support-card{margin-top:22px;padding:22px 18px}.support-card h2{font-size:24px}.support-action{padding:11px 13px}.support-action span{min-width:0;overflow-wrap:anywhere}}
</style>
