<template>
  <div class="app">
    <nav>
      <RouterLink to="/">
        <a> Caixa </a>
      </RouterLink>
      <RouterLink to="/familys">
        <a> Famílias </a>
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
  width: 100%;
}

nav{
  background-image: linear-gradient(to bottom right, purple, #f047f0);
  width: 100%;
  height: 60px;
  display: flex;
  justify-content: center;
  border-radius: 12px;
  box-sizing: border-box;
}

a{
  color: yellow;
  padding: 10px;
  font-size: medium;
}

a:focus{
  box-shadow: none;
}
</style>