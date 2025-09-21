<script setup>
import { ref } from 'vue';
import stockManager from '../js/stockManager';
import SearchName from './SearchName.vue';
import SearchSku from './SearchSku.vue';

const props = defineProps({
  stockObj: stockManager
});

const mode = ref('nome');

</script>

<template>
  <div class="searchBar">
    <div class="searchInput">
      <SearchSku v-if="mode === 'sku'" :stockObj="stockObj" />
      <SearchName v-else :stockObj="stockObj" />
    </div>

    <div class="searchToggle">
        <span :class="{ 'active': mode === 'sku' }" @click="mode = 'sku'">
            SKU
        </span>
        <span :class="{ 'active': mode === 'nome' }" @click="mode = 'nome'">
            Nome
        </span>
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

.searchToggle {
  display: flex;
  border: 1px solid #fff;
  border-radius: 6px;
  overflow: hidden;
  height: 30px;
}

.searchToggle span {
  flex: 1;
  padding: 6px 12px;
  cursor: pointer;
  user-select: none;
  text-align: center;
  line-height: 18px;
}

.searchToggle span + span {
  border-left: 1px solid #ccc;
}

.active {
  background: #007bff;
  color: white;
  font-weight: bold;
}

:deep(.searchBox){
  display: flex;
  flex-flow: column;
  align-items: center;
  width: 100%;
}

:deep(input){
  width: 100%;
  height: 30px;
}

</style>