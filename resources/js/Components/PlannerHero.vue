<template>
  <header class="hero-card">
    <div class="hero-card__content">
      <span class="eyebrow">{{ eyebrow }}</span>
      <h1>{{ title }}</h1>
      <p>{{ description }}</p>
    </div>
    <slot name="action" />
  </header>
</template>

<script setup>
defineProps({
  eyebrow: { type: String, required: true },
  title: { type: String, required: true },
  description: { type: String, required: true },
});
</script>

<style scoped>
.hero-card{display:flex;min-height:190px;padding:34px 38px;align-items:center;justify-content:space-between;gap:28px;border:1px solid #d4ccd4;border-radius:14px;background:linear-gradient(145deg,#ffbdd3,#fff1bd 40%,#fff1bd 60%,#ffcb0f);box-shadow:0 2px 2px rgba(32,0,36,.05)}
.hero-card__content{min-width:0}.eyebrow{display:block;margin-bottom:7px;color:#240029;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:500;line-height:1.5;letter-spacing:.1em;text-transform:uppercase}.hero-card h1{color:#240029;font-family:'Bricolage Grotesque',Inter,system-ui,sans-serif;font-size:55px;font-weight:800;line-height:1.1;letter-spacing:.04em}.hero-card p{margin-top:9px;color:#240029;font-size:18px;line-height:1.5}
@media(max-width:767px){.hero-card{min-height:auto;padding:18px 16px;align-items:stretch;flex-direction:column;gap:14px}.hero-card h1{font-size:40px}.hero-card p{font-size:16px}}
</style>
