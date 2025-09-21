<script setup>
import Product from './Product.vue';
import stockManager from '../js/stockManager';
import SearchProd from './SearchProd.vue';

const props = defineProps({
    stockObj: stockManager,
});

</script>

<template>

  <div class="divSubpage blackPage borderRound">

    <SearchProd :stockObj="stockObj" />
    
    <p>Total: ${{ stockObj.cart.value.total }}</p>

    <button @click="$emit('send')">Enviar</button>

    <table>
        <thead>
            <th class="produto">Produto</th>
            <th class="preco">Preço</th>
            <th class="quantidade">Qtd.</th>
            <th class="add"></th>
            <th class="sub"></th>
            <th class="del"><button @click="stockObj.clearCart()">Limpar carrinho</button></th>
        </thead>
        <tbody>
            <tr v-for="sku in stockObj.cart.value.skuList">
                <Product :key="sku"
                        :sku="sku"
                        :product="stockObj.cart.value.productSku[sku]" 
                        :stockObj="stockObj" />
            </tr>
        </tbody>
    </table>

  </div>

</template>

<style scoped>

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

.del > button{
    width: 100%;
}

</style>