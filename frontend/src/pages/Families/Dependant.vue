<script setup>
import { ref } from 'vue';
import familiesObj from '../../js/familyManager';
import familyDependantManager from '../../js/familyDependantManager';
import FamilyCRUD from '../../components/FamilyCRUD.vue';
import FamilyResult from '../../components/FamilyResult.vue';
import FamilyDependantCRUD from '../../components/FamilyDependantCRUD.vue';
import Loading from '../../components/Loading.vue';
import Button from 'primevue/button';

const familyFilter = familiesObj.getEmptyFamily();
const loadingFamilies = ref(false);

const selectedFamily = ref(null);
const dependants = ref([]);
const loadingDependants = ref(false);

async function searchFamilies(filter) {
  loadingFamilies.value = true;
  await familiesObj.searchFamilies(filter);
  loadingFamilies.value = false;
  selectedFamily.value = null;
}

async function selectFamily(family) {
  selectedFamily.value = family;
  await loadDependants();
}

async function loadDependants() {
  if (!selectedFamily.value) return;
  loadingDependants.value = true;
  const dependantsList = await familyDependantManager.getDependants(selectedFamily.value.id);

  if (dependantsList){
    dependants.value = dependantsList.map( 
      (person) => { person.birth_date = formatDateForDisplay(person.birth_date);
                    return person;
                  }
    );
  } else {
    dependants.value = [];
  }
  loadingDependants.value = false;
}

// format string DD/MM/YYYY to YYYY-MM-DD
function formatDateForApi(dateStr) {
  if (!dateStr) return null;
  const d = String(dateStr).replace(/\D/g, '');
  if (d.length < 8) return null;
  const day = d.substring(0, 2);
  const month = d.substring(2, 4);
  const year = d.substring(4, 8);
  return `${year}-${month}-${day}`;
}

// format string YYYY-MM-DD to DD/MM/YYYY
function formatDateForDisplay(dateStr) {
  if (!dateStr) return null;
  const parts = dateStr.split('-');
  if (parts.length !== 3) return dateStr;
  const day = parts[2];
  const month = parts[1];
  const year = parts[0];
  return `${day}/${month}/${year}`;
}

async function saveDependants(newList) {
  loadingDependants.value = true;
  const familyId = selectedFamily.value.id;

  // Find removed items
  const newIds = newList.filter(d => d.id).map(d => d.id);
  const removed = dependants.value.filter(d => d.id && !newIds.includes(d.id));

  for (const d of removed) {
    await familyDependantManager.removeDependant(familyId, d.id);
  }

  // Find added and updated items
  for (const d of newList) {
    const formattedDate = formatDateForApi(d.birth_date);
    
    if (!d.id) {
      // Added
      await familyDependantManager.addDependant(familyId, d.name, formattedDate, d.relation);
    } else {
      // Updated
      const original = dependants.value.find(old => old.id === d.id);
      const originalDate = formatDateForApi(original.birth_date);
      if (original.name !== d.name || originalDate !== formattedDate || original.relation !== d.relation) {
         await familyDependantManager.updateDependant(familyId, d.id, {
            name: d.name,
            birth_date: formattedDate,
            relation: d.relation
         });
      }
    }
  }

  await loadDependants();
}

</script>

<template>
  <div class="divPage">

    <div v-if="!selectedFamily" class="divSubpage blackPage borderRound subpageSearch">
      <h2>Buscar Família para Gerenciar Dependentes</h2>
      <FamilyCRUD :family="familyFilter" @send="searchFamilies" action_name="Buscar" />
    </div>

    <div v-if="!selectedFamily" class="divSubpage blackPage borderRound">
      <div v-if="loadingFamilies">
        <Loading />
      </div>
      <div v-else class="miniSubPage fullWidth">
        <h2>Resultados</h2>
        <FamilyResult :families="familiesObj.families.value" showButton="users" @select="selectFamily" />
      </div>
    </div>

    <div v-else class="divSubpage blackPage borderRound">
      <Button label="Voltar para Busca" icon="pi pi-arrow-left" @click="selectedFamily = null" style="margin-bottom: 20px;" />

      <h1>Família Selecionada</h1>
      <FamilyResult :families="[selectedFamily]" />

      <hr style="margin: 20px 0; width: 100%;">

      <div v-if="loadingDependants">
        <Loading />
      </div>
      <div v-else class="fullWidth">
        <FamilyDependantCRUD 
          :dependants="dependants" 
          action_name="Salvar Alterações" 
          @send="saveDependants" 
        />
      </div>

    </div>

  </div>
</template>

<style scoped>
  .divBox {
    width: 100%;
    margin: 10px 0;
    padding: .7em 2em 1em;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    box-sizing: border-box;
  }
  hr {
    border-top: 1px solid #ccc;
    width: 100%;
  }
</style>
