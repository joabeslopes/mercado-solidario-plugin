<script setup>
import { ref } from 'vue';
import supplierManager from '../js/supplierManager';

const props = defineProps({
  supplierObj: supplierManager
});

const userInput = ref('');

function handleInput(evt){
  if (evt.target.value.length > 2) {
      props.supplierObj.searchName(evt.target.value);
  } else {
      props.supplierObj.clearNamesSearch();
  };
};

function handleClick(id){
  props.supplierObj.addSupplier(id);
  props.supplierObj.clearNamesSearch();
  userInput.value = '';
};

</script>

<template>
  <div class="searchBar">
    <div class="searchInput">
        <input v-model="userInput" @input="handleInput" />
        <ul v-if="userInput">
        <li v-for="(obj, id) in supplierObj.suppliersSearch.value">
            <span @click="handleClick(id)">{{supplierObj.namesSearch.value[id].name}}</span>
        </li>
        </ul>
    </div>
  </div>
</template>

<style scoped>
.searchBar {
  display: flex;
  gap: 10px;
  width: 70%;
  align-items: start;
}

.searchInput {
  flex: 1;
}

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