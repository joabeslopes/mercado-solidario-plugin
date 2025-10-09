import { ref } from 'vue';
import { get } from './myApiClient';

export default class supplierManager {

  supplier = ref({});
  allSuppliers = ref({});
  suppliersSearch = ref({});

  constructor(){
    this.suppliersStorage = 'mercado-solidario-suppliers';
    this.loadAll();
    this.load();
  };

  async loadAll(){

    const savedSuppliers = sessionStorage.getItem(this.suppliersStorage);

    if (savedSuppliers != null) {
      this.allSuppliers.value = JSON.parse(savedSuppliers);
    } else {

      const response = await get('/supplier');

      if (response.status == 200){
        this.allSuppliers.value = response.data;
        this.saveAll();
      };

    };
  };

  saveAll(){
    sessionStorage.setItem(this.suppliersStorage, JSON.stringify(this.allSuppliers.value));
  };

  load(){
    const savedSuppliers = localStorage.getItem(this.suppliersStorage);

    if (savedSuppliers != null) {
      this.supplier.value = JSON.parse(savedSuppliers);
    };
  };

  save(){
    localStorage.setItem(this.suppliersStorage, JSON.stringify(this.supplier.value));
  };

  createSupplier(supplier){
    this.allSuppliers.value[supplier.id] = supplier;
    this.saveAll();
  }

  deleteSupplier(id){
    delete this.allSuppliers.value[id];
    this.saveAll();
  }

  addSupplier(id){

    const supplierSearch = this.allSuppliers.value[id];
    if (!supplierSearch){
      return null;
    };

    this.supplier.value = supplierSearch;
    this.save();

  };

  delSupplier(id){

    const supplierSearch = this.allSuppliers.value[id];
    if (!supplierSearch){
      return null;
    };

    this.supplier.value = {};
    this.save();
  };

  getSupplier(){
    return this.supplier.value;
  };

  searchName(name){
    this.clearNamesSearch();

    const regex = new RegExp(name, "i");

    Object.keys(this.allSuppliers.value)
    .forEach(id => {
        const supplier = this.allSuppliers.value[id];

        if ( supplier.name.match(regex) ){
          this.suppliersSearch.value[id] = supplier;
        };
    });
  };

  clearNamesSearch(){
    this.suppliersSearch.value = {};
  };

};