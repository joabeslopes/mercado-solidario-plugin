<script setup>
import { ref } from 'vue';
import Loading from '../../components/Loading.vue';
import familiesObj from '../../js/familyManager';
import FamilyCRUD from '../../components/FamilyCRUD.vue';
import FamilyResult from '../../components/FamilyResult.vue';

const family = familiesObj.getEmptyFamily();
const loading = ref(false);

async function search(family){
  loading.value = true;
  await familiesObj.searchFamilies(family);
  loading.value = false;
};

</script>

<template>

  <div class="divPage">

    <div class="divSubpage blackPage borderRound subpageSearch">
      <h2>Buscar famílias</h2>
      <FamilyCRUD :family="family" @send="search" action_name="Buscar" />
    </div>

    <div v-if="loading" class="divSubpage blackPage borderRound">
      <Loading />
    </div>
    <div v-else class="divSubpage blackPage borderRound">
      <h2>Resultados</h2>
      <FamilyResult :families="familiesObj.families.value" />
    </div>

  </div>

</template>