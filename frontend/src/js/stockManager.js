import { ref } from 'vue';
import { get } from './myApiClient';
import showPopup from './myPopup';

export default class stockManager {

  stock = ref({});
  cart = ref({});
  searchSku = ref('');
  lastSku = ref('');

  emptyCart = {
    'productSku': {},
    'skuList': [],
    'total': 0,
  };

  constructor(page){
    this.cartSessionStorage = 'mercado-solidario-cart-'+page;
    this.stockSessionStorage = 'mercado-solidario-stock';
    this.getStockRef();
    this.getCartRef();
  };

  async getStockRef(){

    const savedStock = sessionStorage.getItem(this.stockSessionStorage);

    if (savedStock != null) {
      this.stock.value = JSON.parse(savedStock);
    } else {

      const response = await get('/stock');

      if (response.status == 200){
        this.stock.value = response.data;
        sessionStorage.setItem(this.stockSessionStorage, JSON.stringify(response.data));
      } else {
        showPopup("Erro", "Não foi possível buscar os produtos");
      };

    };

  };

  getCartRef(){

    const savedCart = localStorage.getItem(this.cartSessionStorage);
    this.cart.value = savedCart == null ? structuredClone(this.emptyCart) : JSON.parse(savedCart);

  };

  clearCart(){

    this.cart.value = structuredClone(this.emptyCart);
    localStorage.removeItem(this.cartSessionStorage);

  };

  saveCartLocalStorage(){

    localStorage.setItem(this.cartSessionStorage, JSON.stringify(this.cart.value));

  };

  addProd(sku){

    const prodSearch = this.stock.value[sku];

    if (!prodSearch){
      return null;
    };

    const newProduct = {
      'id': prodSearch.id,
      'name': prodSearch.name,
      'price': prodSearch.price,
      'quantity': 1
    };

    if (this.cart.value.productSku[sku]) { 
      this.cart.value.productSku[sku].quantity += 1;
    } else {
      this.cart.value.productSku[sku] = newProduct;
      this.cart.value.skuList.unshift(sku);
    };

    this.cart.value.total += prodSearch.price;

    this.searchSku.value = '';
    this.lastSku.value = sku;

    this.saveCartLocalStorage();

  };

  subProd(sku){

    const prodSearch = this.cart.value.productSku[sku];

    if (!prodSearch){
      return null;
    };

    if (prodSearch.quantity > 1){
      this.cart.value.productSku[sku].quantity -= 1;
      this.cart.value.total -= prodSearch.price;
    } else {
      this.delProd(sku);
    };

    this.saveCartLocalStorage();

  };

  delProd(sku){

    const prodSearch = this.cart.value.productSku[sku];

    if (!prodSearch){
      return null;
    };

    this.cart.value.total -= prodSearch.quantity * prodSearch.price;

    delete this.cart.value.productSku[sku];

    const prodIndex = this.cart.value.skuList.indexOf(sku);

    this.cart.value.skuList.splice(prodIndex, 1);

    this.saveCartLocalStorage();

  };

  updateCartTotal(){
    let soma = 0;

    Object.keys(this.cart.value.productSku).forEach(
      sku => {
        const prod = this.cart.value.productSku[sku];
        soma += prod.quantity * prod.price;
      }
    );

    this.cart.value.total = soma;
    this.saveCartLocalStorage();

  }

}