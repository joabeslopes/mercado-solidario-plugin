<script setup>
import stockManager from '../js/stockManager';

const props = defineProps({
    stockObj: stockManager,
});

</script>

<template>
  <div class="divSubpage blackPage borderRound stock">
    <div v-for="(prod, sku) in stockObj.stock.value">
      <div :key="sku" class="prodImg" @click="stockObj.addProd(sku)">
        <img :src="prod.image" :class="{'selected': stockObj.lastSku.value == sku, 'borderRound': true}" width="100px" height="100px">
        <p>{{prod.name}}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stock{
  height: 100%;
  flex-flow: row wrap;
  gap: 10px;
  align-items: flex-start;
}

.prodImg:hover{
  cursor: pointer;
}

.selected{
  outline: 5px solid yellow;
}

</style>