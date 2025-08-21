<script setup>
import { ref } from 'vue';

const emit = defineEmits(['submit', 'itemClick']);

const userInput = ref('');

const props = defineProps({
    searchData: Object
});

function handleInput(evt){
    if (evt.target.value.length > 2) {
        emit('submit', evt.target.value);
    };
};

</script>

<template>

    <div class="searchBar">
        <p>Pesquisa por nome</p>
        <input v-model="userInput" @input="handleInput" />
        <ul v-if="searchData">
            <li v-for="(obj, key) in searchData">
                <a href="#" @click="emit('itemClick', key, obj)"> {{searchData[key].name}} </a>
            </li>
        </ul>
    </div>

</template>

<style scoped>

.searchBar{
  display: flex;
  flex: 1;
  flex-flow: column;
  align-items: center;
  width: 50%;
}

input{
  width: 100%;
  height: 30px;
}

ul{
  background-color: white;
  margin: 0;
  width: 100%;
  max-height: 200px;
  overflow: scroll;
  padding: 0;
}

</style>