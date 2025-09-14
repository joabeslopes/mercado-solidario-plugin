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
  <div class="searchBox">
    <input v-model="userInput" @keyup.enter="handleInput" />
  </div>
</template>

<style scoped>

input:focus{
  outline: 5px solid yellow;
}

</style>