<script setup>
import { post } from '../js/myApiClient';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from '../components/CartList.vue';
import StockList from '../components/StockList.vue';

const stockObj = new stockManager('checkout');

async function sendCart(){

  const userCart = stockObj.getCart();

  if (userCart == null){
    showPopup("Erro", "Carrinho vazio");
    return null;
  };

  const request = {
    'cart': userCart
  };

  const response = await post( '/checkout', request );

  if (response.status == 200) {

    showPopup("Sucesso", "Compra efetuada");

    stockObj.clearCart();

  } else {

    showPopup("Erro", response.message);

  };

};

</script>


<template>

<div class="divPage">

  <StockList class="stock" :stockObj="stockObj" />

  <CartList class="cart" :stockObj="stockObj" @send="sendCart" />

</div>

</template>

<style scoped>

@media (max-width: 768px) {
  .cart {
    order: 1;
  }
}

@media (max-width: 768px) {
  .stock {
    order: 2;
  }
}

</style>