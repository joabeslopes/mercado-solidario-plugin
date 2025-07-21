<script setup>
import stockManager from '../js/stockManager';

const props = defineProps({
    stockObj: Object,
});

</script>

<template>
  <div class="divSubpage stock">
    <div v-for="(prod, sku) in stockObj.stock.value">
      <div :key="sku" class="prodImg" @click="stockObj.addProd(sku)">
        <img :src="prod.image" :class="{'selected': stockObj.lastSku.value == sku}" width="100px" height="100px">
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
  height: 100%;
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