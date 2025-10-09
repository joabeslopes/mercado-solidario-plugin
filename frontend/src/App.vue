<template>
  <div class="app">
    <nav class="borderRound">
      <RouterLink to="/checkout">Caixa</RouterLink>
      <RouterLink to="/families">Famílias</RouterLink>
      <RouterLink to="/checkin">Entrada estoque</RouterLink>
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
.divPage{
  display: flex;
  gap: 90px;
}

@media (max-width: 768px) {
  .divPage{
    flex-direction: column;
  }
}

a{
  color: yellow;
  padding: 10px;
  font-size: medium;
}

a:focus{
  box-shadow: none;
}

.submitButton {
  border: none;
  outline: none;
  background-color: #007bff;
  color: white;
  padding: 5px 10px;
  height: auto;
  font-weight: bold;
  margin-top: 5px;
}

.submitButton:hover {
  cursor: pointer;
}

.submitButton:active {
  background-color: #0f4e92;
}

.divSubpage{
  display: flex;
  flex: 1;
  flex-flow: column;
  align-items: center;
}

.miniSubPage {
  display: flex;
  flex-flow: column;
  align-items: center;
  margin-bottom: 10px;
  height: auto;
}

.blackPage{
  background-color: black;
  color: white;
  padding: 20px;
}

.blackPage > h1 {
  color: white;
}

.borderRound {
  box-sizing: border-box;
  border-radius: 12px;
}
</style>