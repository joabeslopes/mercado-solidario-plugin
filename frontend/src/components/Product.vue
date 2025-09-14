<script setup>
import stockManager from '../js/stockManager';

const props = defineProps({
    sku: String,
    product: Object,
    stockObj: stockManager
});

function quantityInput(evt){
    props.stockObj.setQuantity(props.sku, evt.target.value);
};

</script>

<template>

    <td>{{ product.name }}</td>
    <td>R$ {{ product.price }}</td>
    <td><input v-model="product.quantity" @input="quantityInput" /> </td>
    <td>
        <button @click="stockObj.addProd(sku)">+</button>
    </td>
    <td>
        <button @click="stockObj.subProd(sku)">-</button>
    </td>
    <td class="del">
        <button @click="stockObj.delProd(sku)">Delete</button>  
    </td>

</template>

<style scoped>
input{
    width: 60%;
}

.del > button{
    width: 100%;
}
</style>