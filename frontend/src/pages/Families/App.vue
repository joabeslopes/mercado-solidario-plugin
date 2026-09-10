<script setup>
  import '../../css/global.css';
</script>

<template>
  <div class="app">
    <nav class="borderRound">
      <RouterLink to="/crud">Cadastro</RouterLink>
      <RouterLink to="/search">Pesquisa</RouterLink>
      <RouterLink to="/dependant">Dependentes</RouterLink>
      <RouterLink to="/relation">Relações</RouterLink>
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
  background-color:rgb(27, 134, 158);
  width: 100%;
  height: 60px;
  display: flex;
  justify-content: center;
}

.router-link-exact-active {
  color: #c61c8bd4;
}
</style>

<style>
.subpageSearch {
  flex: unset;
  width: 40%;
}

@media (max-width: 768px) {
    .subpageSearch{
      width: 100%;
    }
}
</style>