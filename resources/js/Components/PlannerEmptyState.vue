<template>
  <div class="empty-card">
    <slot name="icon" />
    <h2>{{ title }}</h2>
    <p>{{ description }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  description: { type: String, required: true },
});
</script>

<style scoped>
.empty-card {
  display: flex;
  width: 100%;
  min-height: 250px;
  box-sizing: border-box;
  padding: 32px;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  text-align: center;
}

.empty-card:deep(> svg),
.empty-card:deep(.empty-icon) {
  width: 42px;
  height: 42px;
  color: #df37a7;
}

.empty-card h2 {
  margin: 12px 0 0;
  color: #240029;
  font-family: Inter, system-ui, sans-serif;
  font-size: 28px;
  font-weight: 700;
  line-height: 1.25;
}

.empty-card p {
  max-width: 440px;
  margin: 6px 0 17px;
  color: #6d526d;
  font-family: Inter, system-ui, sans-serif;
  font-size: 14px;
  line-height: 1.5;
}

.empty-card:deep(.primary-action),
.empty-card:deep(.secondary-action) {
  border-radius: 12px;
}

@media (max-width: 767px) {
  .empty-card {
    min-height: 220px;
    padding: 28px 16px;
  }

  .empty-card:deep(> svg),
  .empty-card:deep(.empty-icon) {
    width: 38px;
    height: 38px;
  }

  .empty-card h2 {
    margin-top: 12px;
    font-size: 20px;
  }

  .empty-card p {
    max-width: 300px;
    margin: 6px 0 16px;
    font-size: 13px;
  }

  .empty-card:deep(.primary-action),
  .empty-card:deep(.secondary-action) {
    width: 100%;
    max-width: 300px;
    min-height: 46px;
    white-space: nowrap;
  }
}
</style>
