<script setup>
import { ref } from 'vue';
import { get } from '../myApiClient';

const families = ref({});

async function setFamilies(){

  const savedFamilies = sessionStorage.getItem('mercado-solidario-families');

  if (savedFamilies != null) {
    families.value = JSON.parse(savedFamilies);
  } 
  else {
    const response = await get('/families');

    if (response != null){
      families.value = response;
      sessionStorage.setItem('mercado-solidario-families', JSON.stringify(response));
    } else {
      alert('Erro ao buscar famílias');
    };

  };

};

setFamilies();

</script>

<template>
  <h1>Famílias</h1>

  <div class="card">
    <div v-for="family in families">

      <p>
        Id: {{ family.id }}
      </p>
      <p>
        Responsável: {{ family.main_person }}
      </p>
      <p>
        Saldo: {{ family.balance }}
      </p>

    </div>
  </div>

</template>