<script setup>
const props = defineProps({
    sku: String,
    product: Object,
    stockObj: Object
});

function quantityInput(evt){
    const newQuantity = Number( evt.target.value.replace(/[^0-9]/g,'') );

    if (newQuantity > 0){
        props.product.quantity = newQuantity;
    } else {
        props.product.quantity = 1;
    };

    props.stockObj.updateCartTotal();
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
    <td>
        <button @click="stockObj.delProd(sku)">Delete</button>  
    </td>

</template>

<style scoped>
input{
    width: 50%;
}
</style>