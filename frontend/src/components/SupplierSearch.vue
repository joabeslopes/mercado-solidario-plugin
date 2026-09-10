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
  <div class="miniSubPage blackPage borderRound">
    <div class="searchBar">
      <div class="searchInput">
          <input v-model="userInput" @input="handleInput" />
          <ul v-if="userInput">
            <li v-for="(obj, id) in supplierObj.suppliersSearch.value">
                <span @click="handleClick(id)">{{obj.name}}</span>
            </li>
          </ul>
      </div>
      <p> Selecionar fornecedor </p>
    </div>
    <a>{{supplierObj.supplier.value.name}}</a>
  </div>
</template>

<style scoped>
.searchBar {
  display: flex;
  flex-flow: row;
  gap: 10%;
  width: 70%;
}

.searchBar p{
  margin: 0;
}

.searchInput input {
  width: 100%;
}

ul{
  background-color: white;
  margin: 0;
  width: 100%;
  max-height: 200px;
  overflow: scroll;
}

.searchInput span{
  cursor: pointer;
  color: #007bff;
  padding: 5px;
}
</style>