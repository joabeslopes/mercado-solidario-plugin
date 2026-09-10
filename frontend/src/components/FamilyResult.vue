<script setup>
import { defineProps, ref, defineEmits } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import familiesObj from '../js/familyManager';

const props = defineProps(['families', 'showButton']);
const emit = defineEmits(['select']);

const addedButtonIcon = `pi pi-${props.showButton}`;

const editingRows = ref([]);

const periods = ref([
    { label: '1 (Janeiro a Junho)', value: 1 },
    { label: '2 (Julho a Dezembro)', value: 2 }
]);

const onRowEditSave = async (event) => {
    let { newData, index } = event;
    const success = await familiesObj.updateFamily(newData);
    if (success) {
        props.families[index] = newData;
    }
};

const deleteFamily = async (id) => {
    if (confirm('Tem certeza que deseja deletar esta família?')) {
        await familiesObj.deleteFamily(id);
    }
};

</script>

<template>
    <div class="fullWidth" style="max-width: 100%; overflow-x: auto;">
        <DataTable :value="families" editMode="row" v-model:editingRows="editingRows" @row-edit-save="onRowEditSave" dataKey="id" tableStyle="min-width: 40rem" scrollable>

            <Column :rowEditor="true" bodyStyle="text-align:center"></Column>

            <Column header="Ações" style="width: 15%">
                <template #body="slotProps">
                    <div class="flex gapActions">
                        <Button v-if="showButton" :icon="addedButtonIcon" severity="success" text rounded @click="emit('select', slotProps.data)" title="Associar" />
                        <Button icon="pi pi-trash" severity="danger" text rounded @click="deleteFamily(slotProps.data.id)" title="Deletar" />
                    </div>
                </template>
            </Column>

            <Column field="name" header="Nome responsável" sortable>
                <template #editor="{ data, field }">
                    <InputText v-model="data[field]" />
                </template>
            </Column>
            <Column field="year" header="Ano" sortable>
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false" />
                </template>
            </Column>
            <Column field="period" header="Período" sortable>
                <template #editor="{ data, field }">
                    <Select v-model="data[field]" :options="periods" optionLabel="label" optionValue="value" />
                </template>
            </Column>
            <Column field="cpf" header="CPF">
                <template #editor="{ data, field }">
                    <InputMask v-model="data[field]" mask="***.***.***-99" />
                </template>
            </Column>
            <Column field="phone" header="Telefone">
                <template #editor="{ data, field }">
                    <InputMask v-model="data[field]" mask="(99) 99999-9999" />
                </template>
            </Column>
            <Column field="birth_date" header="Data de nascimento">
                <template #body="slotProps">
                    {{ slotProps.data.birth_date }}
                </template>
                <template #editor="{ data, field }">
                    <InputMask v-model="data[field]" mask="99/99/9999" placeholder="Ex: 01/12/1999" />
                </template>
            </Column>
            <Column field="addr_cep" header="CEP">
                <template #editor="{ data, field }">
                    <InputMask v-model="data[field]" mask="99999-999" />
                </template>
            </Column>
            <Column field="addr_number" header="Nº">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false" />
                </template>
            </Column>
            <Column field="addr_compl" header="Compl.">
                <template #editor="{ data, field }">
                    <InputText v-model="data[field]" />
                </template>
            </Column>
            <Column field="notes" header="Obs." style="min-width: 20rem">
                <template #editor="{ data, field }">
                    <Textarea v-model="data[field]" rows="3" style="resize: vertical" />
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<style scoped>
.flex {
    display: flex;
}
.gapActions {
    gap: 0.5rem;
}
</style>