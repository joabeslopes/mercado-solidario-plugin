<script setup>

const props = defineProps({
    product: Object
});

</script>

<template>

    <p>
        {{ product.name }}
    </p>
    <p>
        R${{ product.price }}
    </p>
    <p>
        Qtd: {{ product.quantity }}
    </p>

</template>