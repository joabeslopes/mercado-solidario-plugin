<script setup>
import { post } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from '../components/CartList.vue';
import StockList from '../components/StockList.vue';

const stockObj = new stockManager('checkin');

async function sendCart(){

  const userCart = stockObj.getCart();

  if (userCart == null){
    return null;
  };

  const request = {
    'cart': userCart
  };

  const response = await post( '/stock/checkin', request );

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

    <StockList :stockObj="stockObj" />

    <CartList :stockObj="stockObj" @send="sendCart" />

</div>

</template>