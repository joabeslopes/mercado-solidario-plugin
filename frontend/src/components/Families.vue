<script setup>
import { ref } from 'vue';
import { get, post } from '../js/myApiClient';
import showPopup from '../js/myPopup';

const families = ref({});

const emptyFamily = {
  'name': '',
  'cpf': '',
  'phone': '',
  'balance': '',
  'valid_until': '',
  'notes': ''
};

const newFamily = ref({...emptyFamily});

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
      showPopup('Erro', response.message);
    };

  };

};

async function sendFamily(){
  const request = {
    'newFamily': newFamily.value
  };

  const response = await post('/families', request);

  if (response.status == 200){

    if (Array.isArray(families.value)){
      families.value.push({...newFamily.value});
    } else {
      families.value = [{...newFamily.value}];
    };

    newFamily.value = {...emptyFamily};
    sessionStorage.setItem('mercado-solidario-families', JSON.stringify(families.value));

    showPopup('Sucesso', 'Enviou nova família');
  } else {
    showPopup('Erro', 'Não foi possível criar a nova família');
  };

};


function numericInput(evt){
  const property = evt.target.name;
  newFamily.value[property] = newFamily.value[property].replace(/[^0-9]/g,'');
};

function alphaNumericInput(evt){
  const property = evt.target.name;
  newFamily.value[property] = newFamily.value[property].replace(/[^a-z0-9]/gi,'').toUpperCase();
};


getFamilies();
</script>

<template>
  <div class="divPage">

    <div class="divSubpage">

      <h1>Famílias atuais</h1>
      <div v-for="family in families">
        <p>
          Responsável: {{ family.name }}
        </p>
        <p>
          Saldo: {{ family.balance }}
        </p>
        <p>
          Válida até: <input type="date"
                        :value="family.valid_until"
                        :min="family.valid_until" 
                        :max="family.valid_until" />
        </p>
        <p>
          Observações: {{ family.notes }}
        </p>
      </div>

    </div>

    <div class="divSubpage">

      <h1>Nova família</h1>
      <div>
        <p>Nome</p>
        <input v-model="newFamily.name" />
        <p>CPF</p>
        <input v-model="newFamily.cpf" name="cpf" @input="alphaNumericInput" />
        <p>Telefone</p>
        <input v-model="newFamily.phone" name="phone" @input="numericInput" />
        <p>Saldo</p>
        <input v-model="newFamily.balance" name="balance" @input="numericInput" />
        <p>Válida até</p>
        <input v-model="newFamily.valid_until" type="date" />
        <p>Observações</p>
        <textarea v-model="newFamily.notes" />
        <p></p>
        <button @click="sendFamily()">Criar</button>
      </div>

    </div>

  </div>
</template>