<script setup>
import {  ref, reactive } from 'vue';

const pessoas = ref({});

fetch( '/wp-json/mercado-solidario/v1/people'
).then(
  (data) => data.json()
).then(
  (data) => {
    pessoas.value = data;
    console.log(data);
  }
).catch(
  () => alert('Erro ao buscar pessoas')
);

</script>

<template>
  <h1>Pessoas</h1>

  <div class="card">
    <div v-for="pessoa in pessoas">

      <p>
        Id: {{ pessoa.id }}
      </p>
      <p>
        Nome: {{ pessoa.name }}
      </p>
      <p>
        Idade: {{ pessoa.age }}
      </p>

    </div>
  </div>

</template>