<script setup>
import '../css/global.css';
import { post } from '../js/myApiClient';
import { ref } from 'vue';
import showPopup from '../js/myPopup';
import Loading from './Loading.vue';
import FamilySearch from './FamilySearch.vue';

const loading = ref(false);
const familyId = ref('');

async function addQueue(){

  loading.value = true;

};

</script>


<template>

  <div v-if="loading" class="blackPage borderRound divSubpage">
    <Loading />
  </div>

  <div v-else class="divPage">
    <FamilySearch />
  </div>

</template>