<script setup>
import Product from './Product.vue';

const props = defineProps({
    cart: Object
});

</script>


<template>

<div v-for="sku in cart.list">
    <div class="ms-card">

        <Product :product="cart.products[sku]" />

        <button @click="$emit('sub', sku)">-</button>
        <button @click="$emit('delete',sku)">Delete</button>
    </div>
</div>

</template>

<style scoped>

.ms-card{
    position: relative;
    margin-top: 20px;
    padding: .7em 2em 1em;
    width: 200px;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    background: #c0c6cf;
    box-sizing: border-box;
}

</style>