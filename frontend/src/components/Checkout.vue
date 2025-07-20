<script setup>
import { ref } from 'vue';
import ProductList from './ProductList.vue';
import { get, post, myResponseErrorString } from '../myApiClient';
import showPopup from '../myPopup';
import StockList from './StockList.vue';

const emptyCart = {
  'productSku': {},
  'skuList': [],
  'total': 0,
};

const searchSku = ref('');
const stock = ref({});
const cart = ref({});

async function getStock(){

  const savedStock = sessionStorage.getItem('mercado-solidario-stock');

  if (savedStock != null) {
    stock.value = JSON.parse(savedStock);
  } else {

    const response = await get('/stock');

    if (response.status == 200){
      stock.value = response.data;
      sessionStorage.setItem('mercado-solidario-stock', JSON.stringify(response.data));
    } else {
      showPopup("Erro", "Não foi possível buscar os produtos");
    };

  };

};

function getCart(){

  const savedCart = localStorage.getItem('mercado-solidario-cart');
  cart.value = savedCart == null ? structuredClone(emptyCart) : JSON.parse(savedCart);

};

function clearCart(){

  cart.value = structuredClone(emptyCart);
  clearCartLocalStorage();

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
    return null;
  };

  const newProduct = {
    'id': prodSearch.id,
    'name': prodSearch.name,
    'price': prodSearch.price,
    'quantity': 1
  };

  if (cart.value.productSku[sku]) { 
    cart.value.productSku[sku].quantity += 1;
  } else {
    cart.value.productSku[sku] = newProduct;
    cart.value.skuList.unshift(sku);
  };

  cart.value.total += prodSearch.price;

  searchSku.value = '';
  
  saveCartLocalStorage();

};

function subProduct(sku){

  const prodSearch = cart.value.productSku[sku];

  if (!prodSearch){
    return null;
  }

  if (prodSearch.quantity > 1){
    cart.value.productSku[sku].quantity -= 1;
    cart.value.total -= prodSearch.price;
  } else {
    deleteProd(sku);
  };

  saveCartLocalStorage();

};

function deleteProd(sku){

  const prodSearch = cart.value.productSku[sku];

  if (!prodSearch){
    return null;
  }

  cart.value.total -= prodSearch.quantity * prodSearch.price;

  delete cart.value.productSku[sku];

  const prodIndex = cart.value.skuList.indexOf(sku);

  cart.value.skuList.splice(prodIndex, 1);

  saveCartLocalStorage();

};

async function sendCart(){

  if (cart.value.total == 0){
    return null;
  }

  const userCart = cart.value.skuList.map(
    (sku) => {
      const prod = cart.value.productSku[sku];
      return {
        'sku': sku, 
        'quantity': prod.quantity
      };
    }
  );

  const request = {
    'cart': userCart
  };

  const response = await post( '/stock/cart', request );

  if (response.status == 200) {

    showPopup("Sucesso", "Compra efetuada");

    clearCart();

  } else {

    showPopup("Erro", myResponseErrorString(response));

  };

};


getStock();
getCart();

</script>


<template>

<div class="divPage">

  <StockList :stock="stock" @add="addProd" />

  <div class="divSubpage cart">

    <input v-model="searchSku" @keyup.enter="addProd(searchSku)" />

    <p>Total: {{ cart.total }}</p>

    <button @click="sendCart()">Finalizar</button>

    <ProductList :cart="cart" @add="addProd" @sub="subProduct" @del="deleteProd" />

  </div>

</div>

</template>

<style scoped>
.cart{
  background-color: black;
  color: white;
  box-sizing: border-box;
  border-radius: 12px;
  padding: 20px;
}

input {
  width: 50%;
  height: 30px;
}

input:focus{
  outline: 5px solid greenyellow;
}

</style>