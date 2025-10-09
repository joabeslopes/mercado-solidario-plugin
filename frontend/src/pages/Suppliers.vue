<script setup>
import { ref } from 'vue';
import { get, post, del } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import supplierManager from '../js/supplierManager';

const suppliers = ref([]);

const emptySupplier = {
  'id': '',
  'name': ''
};

const newSupplier = ref({...emptySupplier});

const supplierObj = new supplierManager();

async function sendSupplier(){
  const request = {
    'supplier': newSupplier.value
  };

  const response = await post('/supplier', request);

  if (response.status == 200){

    supplierObj.createSupplier({...response.data});

    showPopup('Sucesso', 'Criou novo fornecedor');
  } else {
    showPopup('Erro', response.message);
  };

};

async function deleteSupplier(id){
  const request = {
    'id': id
  };

  const response = await del('/supplier', request);

  if (response.status == 200){

    supplierObj.deleteSupplier(id);

    showPopup('Sucesso', 'Fornecedor deletado');

  } else {
    showPopup('Erro', response.message);
  };

};

</script>

<template>
  <div class="divPage">

    <div class="divSubpage blackPage borderRound">

      <h1>Novo fornecedor</h1>
      <div>
        <p>Nome</p>
        <input v-model="newSupplier.name" />
        <p></p>
        <button class="submitButton borderRound" @click="sendSupplier()">Criar</button>
      </div>

    </div>

    <div class="divSubpage blackPage borderRound">

      <h1>Fornecedores atuais</h1>
      <div v-for="supplier in supplierObj.allSuppliers.value">
        <p>
          Nome: {{ supplier.name }}
        </p>
        <button class="submitButton borderRound" @click="deleteSupplier(supplier.id)">Deletar</button>
      </div>

    </div>

  </div>
</template>