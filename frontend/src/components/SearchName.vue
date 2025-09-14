<script setup>
import { ref } from 'vue';
import stockManager from '../js/stockManager';

const props = defineProps({
  stockObj: stockManager
});

const userInput = ref('');

function handleInput(evt){
  if (evt.target.value.length > 2) {
      props.stockObj.searchName(evt.target.value);
  } else {
      props.stockObj.clearNamesSearch();
  };
};

function handleClick(sku){
  props.stockObj.addProd(sku);
  props.stockObj.clearNamesSearch();
  userInput.value = '';
};

</script>

<template>

  <div class="searchBar">
    <p>Pesquisa por nome</p>
    <input v-model="userInput" @input="handleInput" />
    <ul v-if="userInput">
      <li v-for="(obj, sku) in stockObj.namesSearch.value">
        <a href="#" @click="handleClick(sku)"> {{stockObj.namesSearch.value[sku].name}} </a>
      </li>
    </ul>
  </div>

</template>

<style scoped>

.searchBar{
  display: flex;
  flex-flow: column;
  align-items: center;
  width: 50%;
}

input{
  width: 100%;
  height: 30px;
}

ul{
  background-color: white;
  margin: 0;
  width: 100%;
  max-height: 200px;
  overflow: scroll;
  padding: 0;
}

</style>