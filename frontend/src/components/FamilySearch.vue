<script setup>
import { ref } from 'vue';
import FamilySearchName from './FamilySearchName.vue';
import FamilySearchCPF from './FamilySearchCPF.vue';

const mode = ref('nome');

</script>

<template>
  <div class="searchBar">
    <div class="searchInput">
      <FamilySearchCPF v-if="mode === 'cpf'" />
      <FamilySearchName v-else />
    </div>

    <div class="searchToggle">
        <span :class="{ 'active': mode === 'cpf' }" @click="mode = 'cpf'">
            CPF
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