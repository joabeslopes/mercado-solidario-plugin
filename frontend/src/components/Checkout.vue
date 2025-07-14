<script setup>
import { ref } from 'vue';
import ProductList from './ProductList.vue';
import { get } from '../myApiClient';

const emptyCart = {
  'productSku': {},
  'skuList': [],
  'total': 0
};

const searchSku = ref('');
const stock = ref({});
const cart = ref({});
const lastSku = ref('');

async function getStock(){

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

function getCart(){

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

  lastSku.value = sku;

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

function sendCart(){

  if (cart.value.total == 0){
    return null;
  }

  const userCart = [];

  for ( const [sku, prod] of Object.entries(cart.value.productSku) ) {
    const newProd = {
      'sku': sku,
      'quantity': prod.quantity
    };

    userCart.push(newProd);
  };

  const userCartJson = JSON.stringify(userCart);

  const sucessoPopup = new Popup({
    id: "sucesso-compra",
    title: "Sucesso",
    content: "Sucesso na efetuação da compra",
    showImmediately: true,
    hideCallback: () => {
        document.querySelectorAll(".sucesso-compra").forEach(e => e.remove());
    },
    css: `
      .popup-title {
        font-size: 30px;
      }
      .popup-body p {
        font-size: 20px;
      }
    `
  });

  clearCart();

};


getStock();
getCart();

</script>


<template>

<div class="page">

  <div class="stock">
    <div v-for="(prod, sku) in stock">
      <div :key="sku" class="prodImg" @click="addProd(sku)">
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
  gap: 10%;
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
  gap: 5px;
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

input {
  width: 50%;
  height: 30px;
}

input:focus{
  outline: 5px solid greenyellow;
}

.selected{
  outline: 5px solid greenyellow;
}

p {
  font-size: 15px;
}
</style>