<script setup>
import { ref, toRaw } from 'vue';
import ProductList from './ProductList.vue';

const searchSku = ref('');
const stock = ref({});
const cart = ref({});
const cartList = ref([]);
const total = ref(0);

function addProd(sku){

  const prodSearch = stock.value[sku];

  if (!prodSearch){
    return false;
  };

  const newProduct = {
    ...prodSearch,
    quantity: 1
  };

  if (cart.value[sku]) { 
    cart.value[sku].quantity += 1;
  } else {
    cart.value[sku] = newProduct;
    cartList.value.unshift(sku);
  };

  total.value += newProduct.price;

  searchSku.value = '';

};

function subProduct(sku){

  if (!cart.value[sku]){
    return false;
  }

  if (cart.value[sku].quantity > 1){
    cart.value[sku].quantity -= 1;
    total.value -= cart.value[sku].price;
  } else {
    deleteProd(sku);
  };

};

function deleteProd(sku){

  if (!cart.value[sku]){
    return false;
  }

  const prod = cart.value[sku];

  const prodValue = prod.quantity * prod.price;

  total.value -= prodValue;

  delete cart.value[sku];

  const listIndex = cartList.value.indexOf(sku);

  cartList.value.splice(listIndex, 1);

};

function sendCart(){

  console.log(toRaw(cart.value));

};

fetch( '/wp-json/mercado-solidario/v1/stock'
).then(
  (data) => data.json()
).then(
  (data) => {
    stock.value = data;
    console.log(data);
  }
).catch(
  () => alert('Erro ao buscar produtos')
);

</script>

<template>

<div class="page">

  <div class="stock">
    <div v-for="(prod, sku) in stock">

      <div v-if="prod.image">
        <a class="stockProd" @click="addProd(sku)">
          <img :src="prod.image" width="100px" height="100px">
        </a>
      </div>

    </div>
  </div>

  <div class="cart">

    <input v-model="searchSku" @keyup.enter="addProd(searchSku)" />

    <p>Total: {{ total }}</p>

    <button @click="sendCart()">Finalizar</button>

    <ProductList :cart="cart" :cartList="cartList" @sub="subProduct" @delete="deleteProd"/>

  </div>

</div>



</template>

<style scoped>

.page{
  display: flex;
  gap: 20px;
}

.stockProd:hover{
  cursor: pointer;
}

.stock{
  background-color: #505053;
  border-radius: 12px;
  box-sizing: border-box;
  padding: 20px;
  width: 50%;
  height: 100%;
  display: flex;
  flex: 50%;
  flex-flow: row wrap;
  gap: 10px;
  align-items: flex-start;
}

.cart{
  background-color: #9b9b9c;
  box-sizing: border-box;
  border-radius: 12px;
  padding: 20px;
  width: 50%;
  display: flex;
  flex: 50%;
  flex-flow: column;
  height: auto;
  align-items: center;
}

</style>