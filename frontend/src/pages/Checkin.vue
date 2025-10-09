<script setup>
import { post } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from '../components/CartList.vue';
import StockList from '../components/StockList.vue';
import SearchSupplier from '../components/SearchSupplier.vue';
import supplierManager from '../js/supplierManager';
import { ref } from 'vue';

const stockObj = new stockManager('checkin');
const supplierObj = new supplierManager();

const now = new Date();
const tzOffset = now.getTimezoneOffset() * 60000;
const localTime = new Date(now - tzOffset);
const localISOTime = localTime.toISOString().slice(0, 16);

const created_at = ref(localISOTime);
const obs = ref("");

async function sendCart(){

  const userCart = stockObj.getCart();

  if (userCart == null){
    return null;
  };

  const supplier = supplierObj.getSupplier();

  const request = {
    'cart': userCart,
    'supplier_id': supplier.id,
    'created_at': created_at.value,
    'obs': obs.value
  };

  const response = await post( '/checkin', request );

  if (response.status == 200) {

    showPopup("Sucesso", "Estoque abastecido");

    stockObj.clearCart();

  } else {

    showPopup("Erro", response.message);

  };

};

</script>


<template>

<div class="divPage">

  <StockList class="stock" :stockObj="stockObj" />

  <div class="wrapper">

    <SearchSupplier class="search" :supplierObj="supplierObj" />

    <div class="dados miniSubPage blackPage borderRound">
      <p>Data de criação</p>
      <input v-model="created_at" type="datetime-local" />
      <p>Observações</p>
      <textarea v-model="obs" />
    </div>

    <CartList class="cart" :stockObj="stockObj" @send="sendCart" />
  </div>

</div>

</template>

<style scoped>

@media (max-width: 768px) {
  .search {
    order: 1;
  }
  .dados {
    order: 2;
  }
  .cart {
    order: 3;
  }
  .stock {
    order: 4;
  }
}

.dados > textarea{
  width: 70%;
  height: 60px;
}

.wrapper{
  display: flex;
  flex-direction: column;
  flex: 1;
}

</style>