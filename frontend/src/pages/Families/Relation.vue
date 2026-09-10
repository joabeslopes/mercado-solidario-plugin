<script setup>
import { ref, onMounted } from 'vue';
import familiesObj from '../../js/familyManager';
import familyDependantManager from '../../js/familyDependantManager';
import entityManager from '../../js/entityManager';
import entityPersonManager from '../../js/entityPersonManager';
import FamilyCRUD from '../../components/FamilyCRUD.vue';
import FamilyResult from '../../components/FamilyResult.vue';
import Loading from '../../components/Loading.vue';
import Button from 'primevue/button';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import showPopup from '../../js/myPopup';

const familyFilter = familiesObj.getEmptyFamily();
const loadingFamilies = ref(false);

const entities = ref([]);
const loadingEntities = ref(true);

const selectedFamily = ref(null);
const familyMembers = ref([]); 
const loadingMembers = ref(false);
const editingRows = ref([]);
const isSaving = ref(false);

onMounted(async () => {
  entities.value = await entityManager.getEntities();
  loadingEntities.value = false;
});

async function searchFamilies(filter) {
  loadingFamilies.value = true;
  await familiesObj.searchFamilies(filter);
  loadingFamilies.value = false;
  selectedFamily.value = null;
}

async function selectFamily(family) {
  selectedFamily.value = family;
  await loadMembersData();
}

async function loadMembersData() {
  if (!selectedFamily.value) return;
  loadingMembers.value = true;
  
  const members = [];
  
  // 1. Titular
  members.push({
    id: selectedFamily.value.id,
    name: selectedFamily.value.name,
    type: 'Titular',
    entities: [],
    originalEntities: [],
    loading: true
  });

  // 2. Dependentes
  const dependants = await familyDependantManager.getDependants(selectedFamily.value.id);
  if (dependants && dependants.length > 0) {
    dependants.forEach(d => {
      members.push({
        id: d.id,
        name: d.name,
        type: d.relation,
        entities: [],
        originalEntities: [],
        loading: true
      });
    });
  }

  familyMembers.value = members;

  for (let member of familyMembers.value) {
    const data = await entityPersonManager.getPersonEntities(member.id);
    // Armazenamos uma cópia profunda para comparar depois
    member.originalEntities = JSON.parse(JSON.stringify(data));
    member.entities = data.map(item => ({ ...item, _uid: `existing-${member.id}-${item.entity_id}` }));
    member.loading = false;
  }

  loadingMembers.value = false;
}

function addNewRelationRow(member) {
  const newRow = {
    _uid: `new-${member.id}-${Date.now()}`,
    person_id: member.id,
    entity_id: null,
    status: 'planned',
    notes: '',
    isNew: true
  };
  member.entities.push(newRow);
  editingRows.value = [...editingRows.value, newRow];
}

const onRowEditSave = (event, member) => {
    let { newData, index } = event;
    
    if (newData.isNew && !newData.entity_id) {
        member.entities.splice(index, 1);
        return;
    }
    
    member.entities[index] = newData;
};

const onRowEditCancel = (event, member) => {
    let { data, index } = event;
    if (data.isNew) {
        member.entities.splice(index, 1);
    }
};

function removeRelation(member, entityId) {
    // Remove apenas localmente
    member.entities = member.entities.filter(e => e.entity_id !== entityId);
}

async function saveAll() {
    // Sincroniza manualmente as linhas que estão em edição aberta
    editingRows.value.forEach(editedRow => {
        for (let member of familyMembers.value) {
            const index = member.entities.findIndex(e => e._uid === editedRow._uid);
            if (index !== -1) {
                member.entities[index] = { ...editedRow };
            }
        }
    });

    // Fecha visualmente as edições
    editingRows.value = [];
    
    isSaving.value = true;
    let hasError = false;

    for (let member of familyMembers.value) {
        const original = member.originalEntities;
        const current = member.entities;

        // 1. Identificar Removidos
        const currentIds = current.filter(e => !e.isNew).map(e => Number(e.entity_id));
        const removed = original.filter(o => !currentIds.includes(Number(o.entity_id)));

        for (let r of removed) {
            const ok = await entityPersonManager.removePersonEntity(member.id, r.entity_id);
            if (!ok) hasError = true;
        }

        // 2. Identificar Novos e Alterados
        for (let c of current) {
            if (c.isNew) {
                if (c.entity_id) {
                    const result = await entityPersonManager.addPersonEntity(member.id, c.entity_id, c.status, c.notes);
                    if (!result) hasError = true;
                }
            } else {
                const orig = original.find(o => Number(o.entity_id) === Number(c.entity_id));
                if (orig && (orig.status !== c.status || orig.notes !== c.notes)) {
                    const ok = await entityPersonManager.updatePersonEntity(member.id, c.entity_id, c);
                    if (!ok) hasError = true;
                }
            }
        }
    }

    if (!hasError) {
        showPopup('Sucesso', 'Todas as alterações foram salvas.');
        await loadMembersData(); // Recarrega para limpar os estados temporários
    }
    isSaving.value = false;
}

function getEntityName(entityId) {
  if (!entityId) return '---';
  const entity = entities.value.find(e => Number(e.id) === Number(entityId));
  return entity ? entity.name : `Desconhecida (ID: ${entityId})`;
}

const statusOptions = [
  { value: 'planned', label: 'Planejada' },
  { value: 'active', label: 'Ativa' },
  { value: 'completed', label: 'Concluída' },
  { value: 'inactive', label: 'Inativa' }
];

function getStatusLabel(status) {
  const option = statusOptions.find(o => o.value === status);
  return option ? option.label : status;
}

</script>

<template>
  <div class="divPage">

    <div v-if="!selectedFamily" class="divSubpage blackPage borderRound subpageSearch">
      <h2>Buscar Família para Associar</h2>
      <FamilyCRUD :family="familyFilter" @send="searchFamilies" action_name="Buscar" />
    </div>

    <div v-if="!selectedFamily" class="divSubpage blackPage borderRound">
      <div v-if="loadingFamilies">
        <Loading />
      </div>
      <div v-else class="miniSubPage fullWidth">
        <h2>Resultados</h2>
        <FamilyResult :families="familiesObj.families.value" showButton="building" @select="selectFamily" />
      </div>
    </div>

    <div v-else class="divSubpage blackPage borderRound">
      <Button label="Voltar para Busca" icon="pi pi-arrow-left" @click="selectedFamily = null" style="margin-bottom: 20px;" />

      <h1>Gerenciar Associações: {{ selectedFamily.name }}</h1>
      
      <div v-if="loadingMembers" class="fullWidth">
        <Loading />
      </div>
      
      <div v-else class="fullWidth membersList">
        <div v-for="member in familyMembers" :key="member.id" class="person-card">
          <div class="person-header">
             <h3>{{ member.name }} <small>({{ member.type }})</small></h3>
          </div>

          <div class="person-content">
            <div class="relations-section">
              <DataTable 
                :value="member.entities" 
                editMode="row" 
                v-model:editingRows="editingRows"
                @row-edit-save="(e) => onRowEditSave(e, member)" 
                @row-edit-cancel="(e) => onRowEditCancel(e, member)"
                dataKey="_uid" 
                tableStyle="min-width: 50rem"
                class="fullWidth"
                scrollable
              >
                <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center" headerStyle="text-align:center">
                    <template #header>
                        <div style="width: 100%; display: flex; justify-content: center;">
                            <Button icon="pi pi-plus" severity="success" text rounded @click="addNewRelationRow(member)" title="Adicionar Associação" />
                        </div>
                    </template>
                </Column>

                <Column header="Ações" style="width: 10%" bodyStyle="text-align:center">
                  <template #body="slotProps">
                    <Button icon="pi pi-trash" severity="danger" text rounded @click="removeRelation(member, slotProps.data.entity_id)" />
                  </template>
                </Column>

                <Column field="entity_id" header="Entidade">
                  <template #body="slotProps">
                    {{ getEntityName(slotProps.data.entity_id) }}
                  </template>
                  <template #editor="{ data, field }">
                    <Select v-if="data.isNew" v-model="data[field]" :options="entities" optionLabel="name" optionValue="id" placeholder="Selecione" filter class="fullWidth" />
                    <span v-else>{{ getEntityName(data[field]) }}</span>
                  </template>
                </Column>

                <Column field="status" header="Status">
                  <template #body="slotProps">
                    {{ getStatusLabel(slotProps.data.status) }}
                  </template>
                  <template #editor="{ data, field }">
                    <Select v-model="data[field]" :options="statusOptions" optionLabel="label" optionValue="value" class="fullWidth" />
                  </template>
                </Column>

                <Column field="notes" header="Observações">
                  <template #body="slotProps">
                    {{ slotProps.data.notes }}
                  </template>
                  <template #editor="{ data, field }">
                    <Textarea v-model="data[field]" rows="1" class="fullWidth" />
                  </template>
                </Column>
                
                <template #empty>
                    <div style="text-align: center; padding: 20px; color: #999;">Nenhuma associação encontrada. Clique no + para adicionar.</div>
                </template>
              </DataTable>
            </div>
          </div>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: center; width: 100%;">
            <Button label="Salvar Todas as Alterações" @click="saveAll" :loading="isSaving" severity="success" class="submitButton borderRound masterActionButton" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .membersList {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  .person-card {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 15px;
    width: 100%;
  }
  .person-header h3 {
    margin-top: 0;
    border-bottom: 1px solid #444;
    padding-bottom: 10px;
  }
  .person-header small {
    font-weight: normal;
    color: #aaa;
  }
  .person-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  .fullWidth {
    width: 100%;
  }
</style>