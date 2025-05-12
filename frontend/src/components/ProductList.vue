<script setup>
import Product from './Product.vue';

const props = defineProps({
    cart: Object
});

</script>

<template>
    <table>
        <thead>
            <th>Produto</th>
            <th>Qtd.</th>
            <th>Preço</th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            <tr v-for="sku in cart.list">
                <Product :key="sku" :product="cart.products[sku]" @sub="$emit('sub', sku)" @del="$emit('delete', sku)" />
            </tr>
        </tbody>
    </table>
</template>

<style scoped>

table{
    border-collapse: collapse;
    width: 100%;
    text-align: left;
    margin-top: 15px;
}

tr{
    line-height: 30px;
}

</style>