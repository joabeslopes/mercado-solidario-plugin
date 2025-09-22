<template>
  <div class="app">
    <nav class="borderRound">
      <RouterLink to="/checkout">Caixa</RouterLink>
      <RouterLink to="/families">Famílias</RouterLink>
      <RouterLink to="/checkin">Entrada estoque</RouterLink>
      <RouterLink to="/suppliers">Fornecedores</RouterLink>
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

a{
  color: yellow;
  padding: 10px;
  font-size: medium;
}

a:focus{
  box-shadow: none;
}

.router-link-exact-active {
  color: #c61c8bd4;
}
</style>


<style>
.divPage{
  display: flex;
  justify-content: space-between;
  gap: 5%;
}

.divSubpage{
  display: flex;
  flex: 1;
  flex-flow: column;
  align-items: center;
}

.blackPage{
  background-color: black;
  color: white;
  padding: 20px;
}

.blackPage > h1 {
  color: white;
}

@media (max-width: 768px) {
  .divPage{
    flex-direction: column;
  }
}

.borderRound {
  box-sizing: border-box;
  border-radius: 12px;
}
</style>