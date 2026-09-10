<script setup>
import { ref } from 'vue';
import FamilyCRUD from '../../components/FamilyCRUD.vue';
import FamilyDependantCRUD from '../../components/FamilyDependantCRUD.vue';
import familiesObj from '../../js/familyManager';
import familyDependantManager from '../../js/familyDependantManager';
import Button from 'primevue/button';
import showPopup from '../../js/myPopup';

const family = ref(familiesObj.getEmptyFamily());
const dependants = ref([]);

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

async function createFamily(){
  // 1. Validate titular data (basic check)
  if (!family.value.name || !family.value.cpf || !family.value.phone) {
    showPopup('Erro', 'Por favor, preencha os dados obrigatórios do titular (Nome, CPF e Telefone).');
    return;
  }

  // 2. Validate dependants
  const invalidDependant = dependants.value.some(d => !d.relation || d.relation.trim() === '');
  if (invalidDependant) {
    showPopup('Erro', 'Todos os dependentes devem ter o campo "Relação" preenchido.');
    return;
  }

  // 3. Save family (titular)
  const newFamily = await familiesObj.sendFamily(family.value);
  
  if (newFamily && newFamily.id) {
    // 4. Save dependants
    for (const d of dependants.value) {
      const formattedDate = formatDateForApi(d.birth_date);
      await familyDependantManager.addDependant(newFamily.id, d.name, formattedDate, d.relation);
    }
    
    // Reset state after successful creation
    family.value = familiesObj.getEmptyFamily();
    dependants.value = [];
  }
};

</script>

<template>
  <div class="divPage">
    <div class="divSubpage blackPage borderRound">
      <h1>Nova família</h1>
      
      <p style="margin-bottom: 20px;">
        Preencha os dados da família e adicione dependentes. Ao final, clique em "Criar".
      </p>

      <div style="display: flex; flex-direction: column; gap: 20px; width: 100%; align-items: center;">
        <!-- Conditionally hides internal button by omitting action_name -->
        <FamilyCRUD 
          :family="family" 
          @update:family="(val) => family = val" 
        />
        
        <FamilyDependantCRUD 
          v-model:dependants="dependants" 
        />

        <Button label="Criar" @click="createFamily" class="submitButton borderRound masterActionButton" />
      </div>

    </div>
  </div>
</template>

<style scoped>
  .masterActionButton {
    width: 20%;
    margin-top: 10px;
  }
</style>