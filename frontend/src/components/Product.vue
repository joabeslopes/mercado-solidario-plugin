<script setup>

const props = defineProps({
    product: Object
});

</script>

<template>

    <td>{{ product.name }}</td>
    <td>R$ {{ product.price }}</td>
    <td>{{ product.quantity }}</td>
    <td>
        <button @click="$emit('add')">+</button>
    </td>
    <td>
        <button @click="$emit('sub')">-</button>
    </td>
    <td class="del">
        <button @click="$emit('del')">Delete</button>  
    </td>

</template>