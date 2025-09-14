<script setup>
import Product from './Product.vue';
import stockManager from '../js/stockManager';
import SearchName from './SearchName.vue';
import SearchSku from './SearchSku.vue';

const props = defineProps({
    stockObj: stockManager,
});

</script>

<template>

  <div class="divSubpage blackPage borderRound">

    <SearchName :stockObj="stockObj" />

    <SearchSku :stockObj="stockObj" />
    
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