<template>
  <div class="loading-spinner">
    <div class="spinner-ring"></div>
  </div>
</template>

<style scoped>
.loading-spinner {
  display: inline-flex;
  justify-content: center;
  align-items: center;
}

.spinner-ring {
  width: 48px;
  height: 48px; 
  border: 6px solid #007bff;
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Animação de rotação */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>