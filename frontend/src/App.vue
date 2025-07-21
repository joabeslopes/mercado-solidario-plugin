<template>
  <div class="app">
    <nav>
      <RouterLink to="/checkout">
        <a> Caixa </a>
      </RouterLink>
      <RouterLink to="/families">
        <a> Famílias </a>
      </RouterLink>
      <RouterLink to="/checkin">
        <a> Entrada estoque </a>
      </RouterLink>
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

<style>
.divPage{
  display: flex;
  gap: 10%;
}

.divSubpage{
  display: flex;
  flex: 50%;
  flex-flow: column;
  align-items: center;
  width: 50%;
}
</style>