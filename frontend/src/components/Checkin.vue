<script setup>
import { post } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from './CartList.vue';
import StockList from './StockList.vue';

const stockObj = new stockManager('checkin');

async function sendCart(){

  if (stockObj.cart.value.total == 0){
    return null;
  };

  const userCart = stockObj.cart.value.skuList.map(
    (sku) => {
      const prod = stockObj.cart.value.productSku[sku];
      return {
        'sku': sku, 
        'quantity': prod.quantity
      };
    }
  );

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