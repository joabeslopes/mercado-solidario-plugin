<script setup>
import { post } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from '../components/CartList.vue';
import StockList from '../components/StockList.vue';
import SearchSupplier from '../components/SearchSupplier.vue';
import supplierManager from '../js/supplierManager';

const stockObj = new stockManager('checkin');
const supplierObj = new supplierManager();

async function sendCart(){

  const userCart = stockObj.getCart();

  if (userCart == null){
    return null;
  };

  const request = {
    'cart': userCart
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

    <CartList class="cart" :stockObj="stockObj" @send="sendCart" />
  </div>

</div>

</template>

<style scoped>

@media (max-width: 768px) {
  .search {
    order: 1;
  }
  .cart {
    order: 2;
  }
  .stock {
    order: 3;
  }
}

.wrapper{
  display: flex;
  flex-direction: column;
  flex: 1;
}

</style>