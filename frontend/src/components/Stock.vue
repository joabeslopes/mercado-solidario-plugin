<script setup>
import { ref } from 'vue';
import ProductList from './ProductList.vue';

const savedCart = localStorage.getItem('mercado-solidario-cart');
const savedCartList = localStorage.getItem('mercado-solidario-cartList');
const savedCartTotal = localStorage.getItem('mercado-solidario-cartTotal');

const savedCartRef = savedCart == null ? {} : JSON.parse(savedCart);
const savedCartListRef = savedCartList == null ? [] : JSON.parse(savedCartList);
const savedCartTotalRef = savedCartTotal == null ? 0 : JSON.parse(savedCartTotal);

const searchSku = ref('');
const stock = ref({});
const cart = ref( savedCartRef );
const cartList = ref( savedCartListRef );
const total = ref( savedCartTotalRef );

function saveCartLocalStorage(){

  localStorage.setItem('mercado-solidario-cart', JSON.stringify(cart.value));
  localStorage.setItem('mercado-solidario-cartList', JSON.stringify(cartList.value));
  localStorage.setItem('mercado-solidario-cartTotal', JSON.stringify(total.value));

};

function clearCartLocalStorage(){

  localStorage.removeItem('mercado-solidario-cart');
  localStorage.removeItem('mercado-solidario-cartList');
  localStorage.removeItem('mercado-solidario-cartTotal');

};

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
  
  saveCartLocalStorage();

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

  saveCartLocalStorage();

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

  saveCartLocalStorage();

};

function sendCart(){

  console.log(JSON.stringify(cart.value));
  cart.value = {};
  cartList.value = [];
  total.value = 0;
  clearCartLocalStorage();

};

fetch( '/wp-json/mercado-solidario/v1/stock'
).then(
  (data) => data.json()
).then(
  (data) => {
    stock.value = data;
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