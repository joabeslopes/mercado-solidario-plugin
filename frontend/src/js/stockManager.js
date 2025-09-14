import { ref } from 'vue';
import { get } from './myApiClient';
import showPopup from './myPopup';

export default class stockManager {

  stock = ref({});
  cart = ref({});
  lastSku = ref('');
  namesSearch = ref({});

  emptyCart = {
    'productSku': {},
    'skuList': [],
    'total': 0,
  };

  constructor(page){
    this.cartSessionStorage = 'mercado-solidario-cart-'+page;
    this.stockSessionStorage = 'mercado-solidario-stock';
    this.loadStockRef();
    this.loadCartRef();
  };

  async loadStockRef(){

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

  loadCartRef(){

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

    const prod = this.cart.value.productSku[sku];

    if (!prod) {
      this.cart.value.productSku[sku] = newProduct;
      this.cart.value.skuList.unshift(sku);
    } else {
      prod.quantity = this.getQuantity(sku) + 1;
    };

    this.lastSku.value = sku;

    this.updateCartTotal();
  };

  subProd(sku){

    const prod = this.cart.value.productSku[sku];
    if (!prod){
      return null;
    };

    if (this.getQuantity(sku) > 1){
      prod.quantity = this.getQuantity(sku) - 1;
    } else {
      this.delProd(sku);
      return null;
    };

    this.updateCartTotal();
  };

  delProd(sku){

    const prod = this.cart.value.productSku[sku];
    if (!prod){
      return null;
    };

    delete this.cart.value.productSku[sku];

    const prodIndex = this.cart.value.skuList.indexOf(sku);

    this.cart.value.skuList.splice(prodIndex, 1);

    this.updateCartTotal();
  };

  getQuantity(sku){

    const prod = this.cart.value.productSku[sku];
    if (!prod){
      return null;
    };

    return Number(prod.quantity);
  };

  setQuantity(sku, value){

    const prod = this.cart.value.productSku[sku];
    if (!prod){
      return null;
    };

    const newQuantity = value.replace(/[^0-9]/g,'');

    prod.quantity = newQuantity == '' ? '' : Number(newQuantity);

    this.updateCartTotal();
  };

  updateCartTotal(){
    let soma = 0;

    for (let sku in this.cart.value.productSku){
      const prod = this.cart.value.productSku[sku];

      soma += this.getQuantity(sku) * prod.price;
    };

    this.cart.value.total = soma;
    this.saveCartLocalStorage();
  };

  getCart(){

    if (this.cart.value.total == 0){
      return null;
    };

    const userCart = [];

    for (let sku in this.cart.value.productSku){
      const prod = this.cart.value.productSku[sku];

      if (this.getQuantity(sku) > 0){
        userCart.push(
          {
            'sku': sku, 
            ...prod
          }
        );
      };
    };

    return userCart;
  };

  searchName(name){
    this.clearNamesSearch();

    const regex = new RegExp(name, "i");

    Object.keys(this.stock.value)
    .forEach(sku => {
        const prod = this.stock.value[sku];

        if ( prod.name.match(regex) ){
          this.namesSearch.value[sku] = prod;
        };
    });
  };

  clearNamesSearch(){
    this.namesSearch.value = {};
  };

};