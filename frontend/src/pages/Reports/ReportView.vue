<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { get } from '../../js/myApiClient';
import stockManager from '../../js/stockManager';
import showPopup from '../../js/myPopup';
import Loading from '../../components/Loading.vue';

const props = defineProps({
  type: {
    type: String,
    required: true
  }
});

const stockObj = new stockManager('reports');

const filterStartDate = ref('');
const filterEndDate = ref('');
const filterProductId = ref('');
const filterCategoryId = ref('');

const categories = ref([]);
const reportData = ref([]);
const loading = ref(false);

const productOptions = computed(() => {
  if (!stockObj.stock.value) {
    return [];
  }
  const list = Object.entries(stockObj.stock.value).map(([sku, prod]) => ({
    id: prod.id,
    sku: sku,
    name: `${prod.name} (${sku})`
  }));
  return list.sort((a, b) => a.name.localeCompare(b.name));
});

async function loadCategories() {
  const response = await get('/reports?action=categories');
  if (response.ok) {
    categories.value = response.data;
  }
}

async function fetchReport() {
  loading.value = true;
  let url = `/reports?type=${props.type}`;
  
  if (filterStartDate.value) {
    url += `&start_date=${filterStartDate.value}`;
  }
  if (filterEndDate.value) {
    url += `&end_date=${filterEndDate.value}`;
  }
  if (filterProductId.value) {
    url += `&product_id=${filterProductId.value}`;
  }
  if (filterCategoryId.value) {
    url += `&category_id=${filterCategoryId.value}`;
  }

  const response = await get(url);
  if (response.ok) {
    reportData.value = response.data;
  } else {
    showPopup("Erro", "Não foi possível carregar o relatório");
  }
  loading.value = false;
}

function clearFilters() {
  filterStartDate.value = '';
  filterEndDate.value = '';
  filterProductId.value = '';
  filterCategoryId.value = '';
}

onMounted(() => {
  loadCategories();
});

</script>

<template>
  <div class="divPage report">
    <!-- Split Div 1: Filters -->
    <div class="filter-container blackPage borderRound">
      <h3>Filtros de Relatório</h3>
      
      <div class="filter-row">
        <div class="div-filter-group">
          <label>Período Inicial</label>
          <input type="date" v-model="filterStartDate" />
        </div>

        <div class="div-filter-group">
          <label>Período Final</label>
          <input type="date" v-model="filterEndDate" />
        </div>

        <div class="div-filter-group">
          <label>Produto</label>
          <select v-model="filterProductId">
            <option value="">Todos os produtos</option>
            <option v-for="prod in productOptions" :key="prod.id" :value="prod.id">
              {{ prod.name }}
            </option>
          </select>
        </div>

        <div class="div-filter-group">
          <label>Categoria</label>
          <select v-model="filterCategoryId">
            <option value="">Todas as categorias</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div class="filter-actions">
          <button class="submitButton borderRound btn-action" @click="fetchReport" :disabled="loading">
            Pesquisar
          </button>

          <button class="submitButton borderRound clearBtn btn-action" @click="clearFilters" :disabled="loading">
            Limpar Filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Split Div 2: Results -->
    <div class="result-container blackPage borderRound">
      <div v-if="loading" class="loading-wrapper">
        <Loading />
      </div>

      <div v-else>
        <h3>Resultado (Ordenado por Quantidade)</h3>
        <table class="report-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>SKU</th>
              <th>Soma de Quantidade</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in reportData" :key="row.sku">
              <td>{{ row.name }}</td>
              <td>{{ row.sku }}</td>
              <td>{{ row.quantity }}</td>
            </tr>
            <tr v-if="reportData.length === 0">
              <td colspan="3" style="text-align: center; color: #aaa;">
                Nenhum dado encontrado para o filtro selecionado.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.report {
  flex-direction: column;
}

.filter-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 15px;
  height: fit-content;
  text-align: center;
}

.filter-row {
  display: flex;
  flex-flow: row wrap;
  gap: 15px;
  align-items: flex-end;
  width: 100%;
}

.result-container {
  width: 100%;
  text-align: center;
}

.div-filter-group {
  flex: 1;
  min-width: 180px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.filter-container label {
  font-weight: bold;
  font-size: 14px;
}

.filter-container input, 
.filter-container select {
  border-radius: 6px;
  border: 1px solid #ffffff;
  background: transparent;
  color: #ffffff;
  width: 100%;
  box-sizing: border-box;
  height: 38px;
  text-align: center;
}

.filter-container select option {
  background-color: #111111;
  color: #ffffff;
}

input:focus, select:focus {
  border-color: #007bff;
  outline: none;
}

.filter-actions {
  display: flex;
  gap: 10px;
  align-items: flex-end;
}

.btn-action {
  height: 38px;
  margin-top: 0;
  white-space: nowrap;
  padding: 0 20px;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

.report-table th, .report-table td {
  padding: 12px;
  border: 1px solid #333;
  text-align: left;
}

.report-table th {
  background-color: #111;
  font-weight: bold;
}

.report-table tr:nth-child(even) {
  background-color: #1a1a1a;
}

.clearBtn {
  background-color: #6c757d;
}

.clearBtn:hover {
  background-color: #5a6268;
}

.clearBtn:active {
  background-color: #4e555b;
}

.loading-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}

@media (max-width: 768px) {
  .filter-container {
    width: 100%;
  }

  .filter-container select{
    max-width: 100%;
  }

  .filter-row {
    flex-direction: column;
    align-items: stretch;
  }
  .div-filter-group {
    width: 100%;
  }
  .filter-actions {
    flex-direction: column;
    width: 100% ;
    align-items: stretch;
  }
  .btn-action {
    width: 100%;
  }
}
</style>
