<script setup>
import { ref } from 'vue';

const userInput = ref('');
const userSearch = ref({});

function handleInput(evt){
  if (evt.target.value.length > 3) {

  };
};

function handleClick(id){
    userInput.value = '';
    userSearch.value = {};
};

</script>

<template>

  <div class="searchBox">
    <input v-model="userInput" @input="handleInput" />
    <ul v-if="userInput.length > 3">
      <li v-for="(obj, id) in userSearch">
        <span @click="handleClick(id)">{{userSearch[id].name}}</span>
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