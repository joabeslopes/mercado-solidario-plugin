<template>
  <div class="app">
    <nav>
      <RouterLink to="/">
        <a> Caixa </a>
      </RouterLink>
      <RouterLink to="/people">
        <a> Pessoas </a>
      </RouterLink>
    </nav>

    <RouterView />

  </div>
</template>

<style scoped>
.app {
  align-items: center;
  display: flex;
  flex-direction: column;
}
</style>