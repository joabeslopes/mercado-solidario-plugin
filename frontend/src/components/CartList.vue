<script setup>
import Product from './Product.vue';
import stockManager from '../js/stockManager';
import SearchBar from './SearchBar.vue';
import { ref } from 'vue';

const props = defineProps({
    stockObj: stockManager,
});

const searchResult = ref({});

function fsearch(input){
    searchResult.value = {};
    const regex = new RegExp("^"+input, "i");

    Object.keys(props.stockObj.stock.value)
    .forEach(sku => {
        const prod = props.stockObj.stock.value[sku];

        if ( prod.name.match(regex) ){
            searchResult.value[sku] = prod;
        };
    });
};

function fclick(key, obj){
  searchResult.value = {};
  props.stockObj.addProd(key);
};

</script>

<template>

  <div class="divSubpage box">
    <SearchBar :searchData="searchResult" @submit="fsearch" @itemClick="fclick" />

    <p>SKU</p>
    <input v-model="stockObj.searchSku.value" @keyup.enter="stockObj.addProd(stockObj.searchSku.value)"  />

    <p>Total: {{ stockObj.cart.value.total }}</p>

    <button @click="$emit('send')">Finalizar</button>

    <table>
        <thead>
            <th class="produto">Produto</th>
            <th class="preco">Preço</th>
            <th class="quantidade">Qtd.</th>
            <th class="add"></th>
            <th class="sub"></th>
            <th class="del"></th>
        </thead>
        <tbody>
            <tr v-for="sku in stockObj.cart.value.skuList">
                <Product :key="sku"
                        :sku="sku"
                        :product="stockObj.cart.value.productSku[sku]" 
                        :stockObj="stockObj"  />
            </tr>
        </tbody>
    </table>

  </div>

</template>

<style scoped>

@media (max-width: 768px) {
  .divSubpage {
    order: 1;
  }
}

input {
  width: 50%;
  height: 30px;
}

input:focus{
  outline: 5px solid greenyellow;
}

table{
    border-collapse: collapse;
    text-align: left;
    margin-top: 15px;
}

tr{
    line-height: 30px;
}

.produto{
    width: 30%;
}

.quantidade{
    width: 15%;
}

.preco{
    width: 15%;
}

.add{
    width: 10%;
}

.sub{
    width: 10%;
}

.del{
    width: 10%;
}

</style>