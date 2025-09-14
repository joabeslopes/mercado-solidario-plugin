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

  <div class="searchBox">
    <input v-model="userInput" @input="handleInput" />
    <ul v-if="userInput">
      <li v-for="(obj, sku) in stockObj.namesSearch.value">
        <span @click="handleClick(sku)">{{stockObj.namesSearch.value[sku].name}}</span>
      </li>
    </ul>
  </div>

</template>

<style scoped>

ul{
  background-color: white;
  margin: 0;
  width: 100%;
  max-height: 200px;
  overflow: scroll;
}

.searchBox span{
  cursor: pointer;
  color: #007bff;
  padding: 5px;
}

</style>