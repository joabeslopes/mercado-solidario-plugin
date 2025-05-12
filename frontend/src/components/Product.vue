<script setup>

const props = defineProps({
    product: Object
});

</script>

<template>

    <td class="produto">{{ product.name }}</td>
    <td class="quantidade">{{ product.quantity }}</td>
    <td class="preco">R$ {{ product.price }}</td>
    <td class="sub">
        <button @click="$emit('sub')">-</button>
    </td>
    <td class="del">
        <button @click="$emit('del')">Delete</button>  
    </td>

</template>

<style scoped>

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