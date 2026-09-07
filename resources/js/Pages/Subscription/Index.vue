<template>
  <AppLayout>
    <main class="subscription-page">
      <header class="subscription-hero">
        <h1>Pilih paket yang sesuai dengan rencanamu</h1>
        <p>Mulai dengan fitur dasar secara gratis, atau nikmati kebebasan merencanakan tanpa batas bersama Ruang Pasangan.</p>
      </header>

      <section class="plans" aria-label="Pilihan paket langganan">
        <article v-for="plan in plans" :key="plan.name" class="plan-card" :class="{ 'plan-card--featured': plan.featured }">
          <div class="plan-card__top">
            <span v-if="plan.featured" class="popular-badge">Paling populer</span>
            <span class="plan-icon" :class="{ 'plan-icon--featured': plan.featured }"><component :is="plan.icon" aria-hidden="true" /></span>
            <p class="plan-kicker">{{ plan.kicker }}</p>
            <h2>{{ plan.name }}</h2>
            <p class="plan-description">{{ plan.description }}</p>
          </div>

          <div class="price">
            <strong>{{ plan.price }}</strong>
            <span>{{ plan.period }}</span>
          </div>

          <button type="button" class="plan-action" :class="{ 'plan-action--featured': plan.featured }" :disabled="!plan.featured" @click="plan.featured && selectPlan()">
            {{ plan.action }}
            <ArrowRight aria-hidden="true" />
          </button>

          <div class="divider" />
          <p class="features-title">Yang kamu dapatkan</p>
          <ul class="features">
            <li v-for="feature in plan.features" :key="feature"><CircleCheck aria-hidden="true" /><span>{{ feature }}</span></li>
          </ul>
        </article>
      </section>

      <p class="footnote">Bayar sekali dan gunakan Paket Ruang Pasangan selamanya. Harga dan fitur dapat berubah sebelum peluncuran resmi.</p>

      <Modal :show="showOrderModal" max-width="md" @close="closeOrderModal">
        <section class="payment-modal" role="document">
          <header class="payment-modal__header">
            <span class="payment-modal__icon"><ShoppingBag aria-hidden="true" /></span>
            <div>
              <p>Konfirmasi pesanan</p>
              <h2>Paket Ruang Pasangan</h2>
            </div>
            <button type="button" class="modal-close" aria-label="Tutup modal" @click="closeOrderModal"><X aria-hidden="true" /></button>
          </header>
          <div class="order-summary">
            <div><span>Paket</span><strong>Ruang Pasangan</strong></div>
            <div><span>Masa aktif</span><strong>Selamanya</strong></div>
            <div class="order-total"><span>Total pembayaran</span><strong>Rp59.000</strong></div>
          </div>
          <p class="payment-note">Pembayaran dilakukan satu kali untuk menikmati seluruh fitur tanpa batas.</p>
          <footer class="payment-modal__footer">
            <button type="button" class="modal-button modal-button--secondary" @click="closeOrderModal">Batal</button>
            <button type="button" class="modal-button modal-button--primary" @click="openQrisModal">Bayar</button>
          </footer>
        </section>
      </Modal>

      <Modal :show="showQrisModal" max-width="sm" @close="closeQrisModal">
        <section class="payment-modal payment-modal--qris" role="document">
          <header class="payment-modal__header">
            <span class="payment-modal__icon"><QrCode aria-hidden="true" /></span>
            <div>
              <p>Pembayaran QRIS</p>
              <h2>Selesaikan pembayaran</h2>
            </div>
            <button type="button" class="modal-close" aria-label="Tutup modal" @click="closeQrisModal"><X aria-hidden="true" /></button>
          </header>
          <div class="qris-box">
            <QrCode aria-hidden="true" />
            <strong>QRIS segera tersedia</strong>
            <span>Kode pembayaran akan tampil di sini setelah layanan pembayaran diaktifkan.</span>
          </div>
          <div class="qris-total"><span>Total pembayaran</span><strong>Rp59.000</strong></div>
          <footer class="payment-modal__footer payment-modal__footer--single">
            <button type="button" class="modal-button modal-button--secondary" @click="closeQrisModal">Tutup</button>
          </footer>
        </section>
      </Modal>
    </main>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { ArrowRight, CircleCheck, HeartHandshake, QrCode, ShoppingBag, Sparkles, X } from '@lucide/vue';

const plans = [
  {
    name: 'Paket Ruang Awal',
    kicker: 'Untuk mulai merencanakan',
    description: 'Semua kebutuhan dasar untuk menyusun rencana pernikahan bersama pasangan.',
    price: 'Gratis',
    period: 'selamanya',
    action: 'Paket saat ini',
    icon: HeartHandshake,
    featured: false,
    features: ['Dashboard ringkasan rencana pernikahan', 'Maksimal menambahkan 5 data persiapan', 'Maksimal menambahkan 5 data anggaran', 'Maksimal menambahkan 5 data seserahan', 'Maksimal menambahkan 5 dokumen KUA', 'Maksimal menambahkan 50 tamu undangan'],
  },
  {
    name: 'Paket Ruang Pasangan',
    kicker: 'Untuk rencana yang lebih lengkap',
    description: 'Fitur lanjutan untuk pasangan yang ingin mengelola setiap detail dengan lebih leluasa.',
    price: 'Rp59.000',
    period: 'untuk selamanya',
    action: 'Pilih Paket Ruang Pasangan',
    icon: Sparkles,
    featured: true,
    features: ['Nikmati seluruh fitur Paket Ruang Awal', 'Tambahkan data persiapan tanpa batas', 'Kelola anggaran tanpa batas', 'Catat seserahan tanpa batas', 'Kelola dokumen KUA tanpa batas', 'Tambahkan tamu undangan tanpa batas', 'Dapatkan pengingat persiapan otomatis', 'Unduh laporan lengkap dalam format Excel', 'Dapatkan dukungan pelanggan prioritas'],
  },
];

const showOrderModal = ref(false);
const showQrisModal = ref(false);

function selectPlan() {
  showOrderModal.value = true;
}

function closeOrderModal() {
  showOrderModal.value = false;
}

function openQrisModal() {
  showOrderModal.value = false;
  showQrisModal.value = true;
}

function closeQrisModal() {
  showQrisModal.value = false;
}
</script>

<style scoped>
.subscription-page{width:min(1040px,calc(100% - 48px));margin:0 auto;padding:12px 0 64px;color:#240029;font-family:Inter,system-ui,sans-serif}
.subscription-hero{padding:32px 24px 18px;text-align:center}
.eyebrow{display:block;margin-bottom:8px;font:500 12px/1.5 'JetBrains Mono',monospace;letter-spacing:.1em;text-transform:uppercase}
.subscription-hero h1{max-width:760px;margin:0 auto;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:clamp(38px,5vw,55px);font-weight:800;line-height:1.08;letter-spacing:.02em}
.subscription-hero p{max-width:680px;margin:14px auto 0;font-size:17px;line-height:1.55}
.plans{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:24px;align-items:stretch}
.plan-card{position:relative;display:flex;min-width:0;padding:28px;flex-direction:column;border:1px solid #d4ccd4;border-radius:16px;background:#fff;box-shadow:0 0 0 1px rgba(41,0,41,.07),0 8px 24px rgba(32,0,36,.06)}
.plan-card--featured{border-color:rgba(223,55,167,.5);box-shadow:0 0 0 1px rgba(223,55,167,.14),0 14px 34px rgba(223,55,167,.1)}
.popular-badge{position:absolute;top:18px;right:18px;padding:6px 9px;border-radius:999px;background:#fff1f8;color:#df37a7;font-size:11px;font-weight:700}
.plan-icon{display:flex;width:44px;height:44px;align-items:center;justify-content:center;border-radius:12px;background:#fff1bd;color:#240029}
.plan-icon--featured{background:#fff1f8;color:#df37a7}.plan-icon svg{width:21px}
.plan-kicker{margin-top:20px;color:#6d526d;font:600 11px/1.4 'JetBrains Mono',monospace;letter-spacing:.06em;text-transform:uppercase}
.plan-card h2{margin-top:6px;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:30px;line-height:1.15}
.plan-description{min-height:68px;margin-top:9px;color:#6d526d;font-size:14px;line-height:1.55}
.price{display:flex;margin-top:22px;align-items:baseline;gap:7px}.price strong{font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:34px}.price span{color:#6d526d;font-size:13px}
.plan-action{display:flex;width:100%;min-height:48px;margin-top:20px;padding:0 16px;align-items:center;justify-content:center;gap:8px;border:1px solid rgba(109,82,109,.45);border-radius:12px;background:#fff;color:#240029;font:700 14px Inter,sans-serif;cursor:pointer}.plan-action:disabled{border-color:#d4ccd4;background:#f7f4f7;color:#8b7b8b;cursor:default}.plan-action:disabled:hover{border-color:#d4ccd4;background:#f7f4f7;color:#8b7b8b}.plan-action:hover{border-color:#df37a7;color:#df37a7}.plan-action--featured{border-color:#df37a7;background:#df37a7;color:#fff}.plan-action--featured:hover{background:#c92f96;color:#fff}.plan-action svg{width:16px}
.divider{height:1px;margin:24px 0;background:#eee8ee}.features-title{font-size:13px;font-weight:700}.features{display:grid;margin-top:14px;gap:12px}.features li{display:flex;align-items:flex-start;gap:9px;color:#6d526d;font-size:13px;line-height:1.45}.features svg{width:17px;min-width:17px;color:#df37a7}
.footnote{max-width:680px;margin:20px auto 0;color:#6d526d;font-size:12px;line-height:1.5;text-align:center}
.payment-modal{overflow:hidden;color:#240029;background:#fff;font-family:Inter,system-ui,sans-serif}.payment-modal__header{display:flex;padding:22px 22px 18px;align-items:center;gap:12px;border-bottom:1px solid #eee8ee}.payment-modal__icon{display:flex;width:42px;height:42px;flex:none;align-items:center;justify-content:center;border-radius:12px;background:#fff1f8;color:#df37a7}.payment-modal__icon svg{width:20px}.payment-modal__header div{min-width:0;flex:1}.payment-modal__header p{color:#6d526d;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase}.payment-modal__header h2{margin-top:2px;font-family:'Bricolage Grotesque',Inter,sans-serif;font-size:23px;line-height:1.2}.modal-close{display:flex;width:38px;height:38px;flex:none;align-items:center;justify-content:center;border:0;border-radius:11px;background:#f7f4f7;color:#6d526d;cursor:pointer}.modal-close:hover{background:#fff1f8;color:#df37a7}.modal-close svg{width:18px}.order-summary{display:grid;margin:22px 22px 0;padding:4px 16px;border:1px solid #eee8ee;border-radius:13px;background:#fffafd}.order-summary>div,.qris-total{display:flex;padding:13px 0;align-items:center;justify-content:space-between;gap:18px;border-bottom:1px solid #eee8ee}.order-summary>div:last-child{border-bottom:0}.order-summary span,.qris-total span{color:#6d526d;font-size:13px}.order-summary strong{font-size:13px}.order-total strong,.qris-total strong{color:#df37a7;font-size:18px}.payment-note{margin:14px 22px 0;color:#6d526d;font-size:12px;line-height:1.55}.payment-modal__footer{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px;padding:16px 22px 22px;border-top:1px solid #eee8ee}.payment-modal__footer--single{grid-template-columns:1fr}.modal-button{height:46px;padding:0 16px;border-radius:12px;font-size:14px;font-weight:700;white-space:nowrap;cursor:pointer}.modal-button--secondary{border:1px solid rgba(109,82,109,.4);background:#fff;color:#6d526d}.modal-button--primary{border:1px solid #df37a7;background:#df37a7;color:#fff}.modal-button--primary:hover{background:#c92f96}.qris-box{display:flex;margin:22px;padding:28px 22px;align-items:center;flex-direction:column;border:1px dashed rgba(223,55,167,.45);border-radius:14px;background:#fffafd;text-align:center}.qris-box>svg{width:92px;height:92px;color:#d4ccd4}.qris-box strong{margin-top:14px;font-size:15px}.qris-box span{max-width:280px;margin-top:6px;color:#6d526d;font-size:12px;line-height:1.5}.qris-total{margin:0 22px;padding:0 0 18px;border-bottom:0}
@media(max-width:767px){.subscription-page{width:calc(100% - 24px);padding:12px 0 36px}.subscription-hero{padding:28px 18px}.subscription-hero h1{font-size:38px}.subscription-hero p{font-size:15px}.plans{grid-template-columns:1fr;gap:14px;margin-top:14px}.plan-card{padding:22px 18px}.plan-description{min-height:0}.popular-badge{top:16px;right:16px}.price strong{font-size:30px}}
</style>
