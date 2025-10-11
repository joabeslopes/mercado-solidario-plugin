<script setup>
import { post } from '../js/myApiClient';
import { ref } from 'vue';
import showPopup from '../js/myPopup';
import stockManager from '../js/stockManager';
import CartList from '../components/CartList.vue';
import StockList from '../components/StockList.vue';
import Loading from '../components/Loading.vue';

const loading = ref(false);

const stockObj = new stockManager('checkout');

async function sendCart(){

  loading.value = true;

  const userCart = stockObj.getCart();

  if (userCart == null){
    showPopup("Erro", "Carrinho vazio");
    loading.value = false;
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

  loading.value = false;

};

</script>


<template>

  <div v-if="loading" class="blackPage borderRound divSubpage">
    <Loading />
  </div>

  <div v-else class="divPage">
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