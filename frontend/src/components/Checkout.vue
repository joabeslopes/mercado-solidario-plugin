<script setup>
import { ref } from 'vue';
import ProductList from './ProductList.vue';
import { get } from '../myApiClient';

const emptyCart = {
  'products': {},
  'list': [],
  'total': 0
};

const searchSku = ref('');
const stock = ref({});
const cart = ref({});
const lastSku = ref('');

async function setStock(){

  const savedStock = sessionStorage.getItem('mercado-solidario-stock');

  if (savedStock != null) {
    stock.value = JSON.parse(savedStock);
  } else {

    const response = await get('/checkout/stock');

    if (response != null){
      stock.value = response;
      sessionStorage.setItem('mercado-solidario-stock', JSON.stringify(response));
    } else {
      alert('Erro ao buscar produtos');
    };

  };

};

function setCart(){

  const savedCart = localStorage.getItem('mercado-solidario-cart');
  cart.value = savedCart == null ? structuredClone(emptyCart) : JSON.parse(savedCart);

};

function clearCart(){

  cart.value = structuredClone(emptyCart);
  clearCartLocalStorage();
  lastSku.value = '';

};

function saveCartLocalStorage(){

  localStorage.setItem('mercado-solidario-cart', JSON.stringify(cart.value));

};

function clearCartLocalStorage(){

  localStorage.removeItem('mercado-solidario-cart');

};

function addProd(sku){

  const prodSearch = stock.value[sku];

  if (!prodSearch){
    return false;
  };

  const newProduct = {
    'id': prodSearch.id,
    'name': prodSearch.name,
    'price': prodSearch.price,
    'quantity': 1
  };

  if (cart.value.products[sku]) { 
    cart.value.products[sku].quantity += 1;
  } else {
    cart.value.products[sku] = newProduct;
    cart.value.list.unshift(sku);
  };

  cart.value.total += newProduct.price;

  searchSku.value = '';
  
  saveCartLocalStorage();

  lastSku.value = sku;

};

function subProduct(sku){

  if (!cart.value.products[sku]){
    return false;
  }

  if (cart.value.products[sku].quantity > 1){
    cart.value.products[sku].quantity -= 1;
    cart.value.total -= cart.value.products[sku].price;
  } else {
    deleteProd(sku);
  };

  saveCartLocalStorage();

};

function deleteProd(sku){

  if (!cart.value.products[sku]){
    return false;
  }

  const prod = cart.value.products[sku];

  const prodValue = prod.quantity * prod.price;

  cart.value.total -= prodValue;

  delete cart.value.products[sku];

  const listIndex = cart.value.list.indexOf(sku);

  cart.value.list.splice(listIndex, 1);

  saveCartLocalStorage();

};

function sendCart(){

  const finalCart = [];

  for (const [sku, prod] of Object.entries(cart.value.products)) {
    const newProd = {
      'id': prod.id,
      'quantity': prod.quantity
    };

    finalCart.push(newProd);
  };

  console.log(JSON.stringify(finalCart));

  clearCart();

};


setStock();
setCart();

</script>


<template>

<div class="page">

  <div class="stock">
    <div v-for="(prod, sku) in stock">

      <div v-if="prod.image" :key="sku" class="prodImg" @click="addProd(sku)">
        <img :src="prod.image" :class="{'selected': lastSku == sku}" width="100px" height="100px">
      </div>
    </div>
  </div>

  <div class="cart">

    <input v-model="searchSku" @keyup.enter="addProd(searchSku)" />

    <p>Total: {{ cart.total }}</p>

    <button @click="sendCart()">Finalizar</button>

    <ProductList :cart="cart" @sub="subProduct" @del="deleteProd"/>

  </div>

</div>


</template>

<style scoped>

.page{
  display: flex;
  gap: 20px;
}

.prodImg:hover{
  cursor: pointer;
}

.stock{
  background-color: black;
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
  background-color: black;
  color: white;
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

input:focus{
  outline: 3px solid greenyellow;
}

.selected{
  outline: 3px solid greenyellow;
}

</style>