<script setup>
import { ref } from 'vue';

const emit = defineEmits(['add']);

const lastSku = ref('');

const props = defineProps({
    stock: Object
});

function added(sku){
    lastSku.value = sku;
    emit('add', sku);
};

</script>

<template>
  <div class="stock">
    <div v-for="(prod, sku) in stock">
      <div :key="sku" class="prodImg" @click="added(sku)">
        <img :src="prod.image" :class="{'selected': lastSku == sku}" width="100px" height="100px">
        <p>{{prod.name}}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stock{
  background-color: black;
  color: white;
  border-radius: 12px;
  box-sizing: border-box;
  padding: 20px;
  width: 50%;
  height: 100%;
  display: flex;
  flex: 50%;
  flex-flow: row wrap;
  gap: 10px;
  align-items: flex-start;
}

.prodImg:hover{
  cursor: pointer;
}

.selected{
  outline: 5px solid greenyellow;
}

</style>