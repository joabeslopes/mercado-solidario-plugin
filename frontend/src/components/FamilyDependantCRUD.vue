<script setup>
import { ref, defineEmits, defineProps, watch } from 'vue';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import showPopup from '../js/myPopup';

const props = defineProps(['dependants', 'action_name']);
const emit = defineEmits(['send', 'update:dependants']);

const localDependants = ref([]);
const editingRows = ref([]);
let nextId = 0;

// Watch for family changes to re-initialize (feedback loop removed)
watch(() => props.dependants, (newValue, oldValue) => {
  // Only re-initialize if the list actually changed externally (e.g. new family)
  // We compare lengths or simple content to avoid re-triggering during internal edits
  if (JSON.stringify(newValue) !== JSON.stringify(localDependants.value)) {
     localDependants.value = newValue ? [...newValue].map(d => ({
        ...d,
        _tempId: d._tempId || d.id || `temp-${nextId++}`
     })) : [];
  }
}, { deep: false, immediate: true });

function emitUpdate() {
  emit('update:dependants', localDependants.value);
}

function addNewRow() {
  const newRow = {
    _tempId: `temp-${nextId++}`,
    name: '',
    birth_date: '',
    relation: ''
  };
  localDependants.value.push(newRow);
  editingRows.value = [...editingRows.value, newRow];
  emitUpdate();
}

function removeDependant(index) {
  localDependants.value.splice(index, 1);
  emitUpdate();
}

const onRowEditSave = (event) => {
    let { newData, index } = event;
    
    if (!newData.relation || newData.relation.trim() === '') {
        showPopup('Erro', 'O campo Relação é obrigatório.');
        return;
    }

    localDependants.value[index] = newData;
    emitUpdate();
};

const onRowEditCancel = (event) => {
    let { data, index } = event;
    if (!data.id && (!data.name || data.name.trim() === '')) {
        localDependants.value.splice(index, 1);
        emitUpdate();
    }
};

function saveAll() {
    // Sincroniza manualmente as linhas que ainda estão abertas em edição
    editingRows.value.forEach(editedRow => {
        const index = localDependants.value.findIndex(d => d._tempId === editedRow._tempId);
        if (index !== -1) {
            localDependants.value[index] = { ...editedRow };
        }
    });

    editingRows.value = [];
    
    const invalid = localDependants.value.some(d => !d.relation || d.relation.trim() === '');
    if (invalid) {
        showPopup('Erro', 'Existem dependentes com o campo Relação vazio.');
        return;
    }

    emit('send', localDependants.value);
}

// format string YYYY-MM-DD to DD/MM/YYYY
function formatDateForDisplay(dateStr) {
  if (!dateStr) return null;
  if (dateStr.includes('/')) return dateStr;
  const parts = dateStr.split('-');
  if (parts.length !== 3) return dateStr;
  return `${parts[2]}/${parts[1]}/${parts[0]}`;
}

</script>

<template>
  <div class="divSubpage borderRound fullWidth">
    <div style="margin-bottom: 10px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
      <h2 style="margin: 0;">Dependentes</h2>
    </div>

    <DataTable :value="localDependants" editMode="row" v-model:editingRows="editingRows" @row-edit-save="onRowEditSave" @row-edit-cancel="onRowEditCancel" dataKey="_tempId" tableStyle="min-width: 50rem" class="fullWidth" scrollable>
        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" headerStyle="text-align:center">
             <template #header>
                <div style="width: 100%; display: flex; justify-content: center;">
                    <Button icon="pi pi-plus" severity="success" text rounded @click="addNewRow" title="Novo Dependente" />
                </div>
            </template>
        </Column>
        
        <Column header="Ações" style="width: 10%">
            <template #body="slotProps">
                <Button icon="pi pi-trash" severity="danger" text rounded @click="removeDependant(slotProps.index)" title="Remover Dependente" />
            </template>
        </Column>

        <Column field="name" header="Nome">
            <template #body="slotProps">
                {{ slotProps.data.name || '---' }}
            </template>
            <template #editor="{ data, field }">
                <InputText v-model="data[field]" class="fullWidth" placeholder="Nome do dependente" />
            </template>
        </Column>

        <Column field="birth_date" header="Data de Nascimento">
            <template #body="slotProps">
                {{ formatDateForDisplay(slotProps.data.birth_date) || '---' }}
            </template>
            <template #editor="{ data, field }">
                <InputMask v-model="data[field]" mask="99/99/9999" placeholder="Ex: 01/12/1999" class="fullWidth" />
            </template>
        </Column>

        <Column field="relation" header="Relação">
            <template #body="slotProps">
                {{ slotProps.data.relation || '---' }}
            </template>
            <template #editor="{ data, field }">
                <InputText v-model="data[field]" class="fullWidth" placeholder="Ex: filho, cônjuge..." />
            </template>
        </Column>

        <template #empty>
            <div style="text-align: center; padding: 20px; color: #666;">
                Nenhum dependente cadastrado.
            </div>
        </template>
    </DataTable>

    <p></p>
    <Button v-if="props.action_name" :label="props.action_name" @click="saveAll" class="submitButton borderRound familyActionButton" />
  </div>
</template>

<style scoped>
  .familyActionButton{
    width: 20%;
    margin-top: 20px;
  }
</style>