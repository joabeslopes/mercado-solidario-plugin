<script setup>
  import '../../css/global.css';
</script>

<template>
  <div class="app">
    <nav class="borderRound">
      <RouterLink to="/checkin">Entradas</RouterLink>
      <RouterLink to="/checkout">Saídas</RouterLink>
    </nav>

    <RouterView />

  </div>
</template>

<style scoped>
.app {
  display: flex;
  flex-direction: column;
}

nav{
  background-color: rgb(27, 134, 158);
  width: 100%;
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.router-link-exact-active {
  color: #c61c8bd4;
  font-weight: bold;
}
</style>
