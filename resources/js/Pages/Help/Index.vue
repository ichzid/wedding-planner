<template>
  <AppLayout>
    <main class="info-page">
      <header class="page-header">
        <h1>Ada yang bisa kami bantu?</h1>
        <p>Temukan jawaban atas pertanyaan yang sering ditanyakan tentang Ruang Pasangan.</p>
      </header>

      <section class="faq-list" aria-label="Pertanyaan yang sering ditanyakan">
        <article v-for="(item, index) in faqItems" :key="item.question" class="faq-item" :class="{ 'faq-item--open': openItem === index }">
          <button type="button" class="faq-question" :aria-expanded="openItem === index" :aria-controls="`faq-answer-${index}`" @click="toggleItem(index)">
            <span>{{ item.question }}</span>
            <ChevronDown aria-hidden="true" />
          </button>
          <div v-if="openItem === index" :id="`faq-answer-${index}`" class="faq-answer">
            <p>{{ item.answer }}</p>
          </div>
        </article>
      </section>
    </main>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ChevronDown } from '@lucide/vue';

const openItem = ref(0);
const faqItems = [
  { question: 'Bagaimana cara mulai menggunakan Ruang Pasangan?', answer: 'Setelah membuat akun, lengkapi informasi pernikahan pada proses awal. Setelah itu kamu dapat langsung menyusun persiapan, anggaran, seserahan, dokumen KUA, dan daftar undangan.' },
  { question: 'Apakah Ruang Pasangan bisa digunakan gratis?', answer: 'Ya. Paket Ruang Awal dapat digunakan gratis untuk kebutuhan perencanaan dasar. Kamu dapat beralih ke Paket Ruang Pasangan jika membutuhkan fitur yang lebih lengkap.' },
  { question: 'Bagaimana cara mencatat anggaran pernikahan?', answer: 'Buka menu Anggaran, lalu pilih Tambah Anggaran. Isi kategori, nama kebutuhan, estimasi biaya, DP, pelunasan, dan status pembayarannya.' },
  { question: 'Apakah data persiapan bisa diubah atau dihapus?', answer: 'Bisa. Gunakan tombol aksi pada setiap item untuk mengubah informasi atau menghapus data yang sudah tidak diperlukan.' },
  { question: 'Bagaimana cara mengelola daftar undangan?', answer: 'Buka menu Daftar Undangan untuk menambahkan nama tamu, pihak pengundang, status undangan, kehadiran, dan catatan tambahan.' },
  { question: 'Apakah Paket Ruang Pasangan dibayar setiap bulan?', answer: 'Tidak. Paket Ruang Pasangan direncanakan sebagai pembelian satu kali seharga Rp59.000 untuk penggunaan selamanya.' },
  { question: 'Apa yang harus dilakukan jika mengalami kendala?', answer: 'Buka menu Hubungi Dukungan pada menu akun, lalu kirimkan detail kendala melalui alamat email dukungan yang tersedia.' },
];

function toggleItem(index) {
  openItem.value = openItem.value === index ? null : index;
}
</script>

<style scoped>
.info-page{width:min(960px,calc(100% - 48px));margin:0 auto;padding:12px 0 64px;color:#240029;font-family:Inter,system-ui,sans-serif}.page-header{display:block!important;width:100%;padding:32px 24px 18px;text-align:center}.page-header h1{max-width:760px;margin:0 auto;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:clamp(38px,5vw,55px);font-weight:800;line-height:1.08;letter-spacing:.02em}.page-header p{max-width:680px;margin:14px auto 0;color:#240029;font-size:17px;line-height:1.55}.faq-list{display:grid;max-width:820px;margin:24px auto 0;gap:10px}.faq-item{overflow:hidden;border:1px solid #d4ccd4;border-radius:14px;background:#fff;box-shadow:0 3px 12px rgba(32,0,36,.045);transition:border-color .16s ease,box-shadow .16s ease}.faq-item--open{border-color:rgba(223,55,167,.42);box-shadow:0 6px 20px rgba(223,55,167,.08)}.faq-question{display:flex;width:100%;min-height:64px;padding:17px 20px;align-items:center;justify-content:space-between;gap:16px;border:0;background:transparent;color:#240029;font:700 15px/1.4 Inter,sans-serif;text-align:left;cursor:pointer}.faq-question svg{width:18px;min-width:18px;color:#6d526d;transition:transform .18s ease,color .18s ease}.faq-item--open .faq-question svg{color:#df37a7;transform:rotate(180deg)}.faq-question:hover{color:#df37a7}.faq-answer{padding:0 52px 19px 20px}.faq-answer p{color:#6d526d;font-size:14px;line-height:1.65}@media(max-width:767px){.info-page{width:calc(100% - 24px);padding:12px 0 40px}.page-header{padding:28px 18px}.page-header h1{font-size:38px}.page-header p{font-size:15px}.faq-list{margin-top:14px}.faq-question{min-height:60px;padding:15px 16px;font-size:14px}.faq-answer{padding:0 42px 17px 16px}.faq-answer p{font-size:13px}}
</style>
