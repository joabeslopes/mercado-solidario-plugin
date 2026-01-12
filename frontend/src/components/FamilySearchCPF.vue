<script setup>
import { ref } from 'vue';
import { get } from '../js/myApiClient';

const userInput = ref('');
const userSearch = ref({});

async function handleInput(evt){
  const cpf = evt.target.value;

  if (cpf.length > 10) {
    const search = await get(`/families?cpf=${cpf}`);

    if (search.status == 200){
      userSearch.value = search.data;
    };

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
    <ul v-if="userInput.length > 10">
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