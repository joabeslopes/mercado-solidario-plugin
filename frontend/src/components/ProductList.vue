<script setup>
import Product from './Product.vue';

const props = defineProps({
    cart: Object
});

</script>

<template>
    <table>
        <thead>
            <th class="produto">Produto</th>
            <th class="quantidade">Qtd.</th>
            <th class="preco">Preço</th>
            <th class="sub"></th>
            <th class="del"></th>
        </thead>
        <tbody>
            <tr v-for="sku in cart.skuList">
                <Product :key="sku" :product="cart.productSku[sku]" @sub="$emit('sub', sku)" @del="$emit('del', sku)" />
            </tr>
        </tbody>
    </table>
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

.sub{
    width: 10%;
}

.del{
    width: 10%;
}

</style>