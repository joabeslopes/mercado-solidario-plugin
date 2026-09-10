<script setup>
import { ref, defineEmits, defineProps, watch } from 'vue';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';

const props = defineProps(['family', 'action_name']);
const emit = defineEmits(['send', 'update:family']);

const newFamily = ref({...props.family});

// Watch for prop changes to update the internal ref
watch(() => props.family, (newValue) => {
  const currentStr = JSON.stringify(newFamily.value);
  const newStr = JSON.stringify(newValue);
  if (currentStr !== newStr) {
    newFamily.value = {...newValue};
  }
}, { deep: true, immediate: true });

// Emit internal changes to parent
watch(newFamily, (newValue) => {
  emit('update:family', newValue);
}, { deep: true });

const periods = ref([
    { label: '1 (Janeiro a Junho)', value: 1 },
    { label: '2 (Julho a Dezembro)', value: 2 },
    { label: 'Todos', value: '' }
]);

</script>

<template>

  <div class="divSubpage borderRound fullWidth">

    <div class="field fullWidth">
        <p>Nome responsável</p>
        <InputText v-model="newFamily.name" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Ano</p>
        <InputNumber v-model="newFamily.year" :useGrouping="false" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Período</p>
        <Select v-model="newFamily.period" :options="periods" optionLabel="label" optionValue="value" placeholder="Selecione o período" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>CPF</p>
        <InputMask v-model="newFamily.cpf" mask="***.***.***-99" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Telefone</p>
        <InputMask v-model="newFamily.phone" mask="(99) 99999-9999" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Data de Nascimento</p>
        <InputMask v-model="newFamily.birth_date" mask="99/99/9999" placeholder="Ex: 01/12/1999" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>CEP</p>
        <InputMask v-model="newFamily.addr_cep" mask="99999-999" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Número da casa</p>
        <InputNumber v-model="newFamily.addr_number" :useGrouping="false" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Complemento do endereço</p>
        <InputText v-model="newFamily.addr_compl" class="fullWidth" />
    </div>

    <div class="field fullWidth">
        <p>Observações</p>
        <Textarea v-model="newFamily.notes" rows="3" class="fullWidth" />
    </div>

    <p></p>
    <Button v-if="props.action_name" :label="props.action_name" @click="emit('send', newFamily)" class="submitButton borderRound familyActionButton" />

  </div>

</template>

<style scoped>
  .field {
    margin-bottom: 1rem;
  }

  .familyActionButton{
    width: 15%;
  }
</style>