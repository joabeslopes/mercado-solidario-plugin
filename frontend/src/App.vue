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
  justify-content: space-between;
  gap: 5%;
}

.divSubpage{
  display: flex;
  flex: 1;
  flex-flow: column;
  align-items: center;
}

.box{
  background-color: black;
  color: white;
  box-sizing: border-box;
  border-radius: 12px;
  padding: 20px;
}

.box > h1 {
  color: white;
}

@media (max-width: 768px) {
  .divPage{
    flex-direction: column;
  }
}
</style>