<script setup>
import { ref } from 'vue';
import stockManager from '../js/stockManager';

const props = defineProps({
  stockObj: stockManager
});

const userInput = ref('');

function handleInput(evt){
  props.stockObj.addProd(evt.target.value);
  userInput.value = '';
};

</script>

<template>
  <div class="searchBar">
    <p>Leitor etiqueta</p>
    <input v-model="userInput" @keyup.enter="handleInput" />
  </div>
</template>

<style scoped>

.searchBar{
  display: flex;
  flex-flow: column;
  align-items: center;
  width: 50%;
}

input {
  width: 100%;
  height: 30px;
}

input:focus{
  outline: 5px solid yellow;
}

</style>