<script setup>
import { ref } from 'vue';
import { get } from '../myApiClient';
import showPopup from '../myPopup';

const families = ref({});

async function getFamilies(){

  const savedFamilies = sessionStorage.getItem('mercado-solidario-families');

  if (savedFamilies != null) {
    families.value = JSON.parse(savedFamilies);
  } 
  else {
    const response = await get('/families');

    if (response.status == 200){
      families.value = response.data;
      sessionStorage.setItem('mercado-solidario-families', JSON.stringify(response.data));
    } else {
      showPopup('Erro', 'Não foi possível buscar as famílias');
    };

  };

};

getFamilies();

</script>

<template>
  <h1>Famílias</h1>

  <div>
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