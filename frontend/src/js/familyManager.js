import { ref } from 'vue';
import { get, post, del, put } from './myApiClient';
import showPopup from './myPopup';

class familyManager {

  families = ref([]);

  getEmptyFamily(){
    const date = new Date();
    const period = date.getMonth() < 6 ? 1 : 2;

    return {
      'id': '',
      'year': date.getFullYear(),
      'period': period,
      'name': '',
      'cpf': '',
      'phone': '',
      'birth_date': '',
      'addr_cep': '',
      'addr_number': '',
      'addr_compl': '',
      'notes': ''
    };
  };

  // convert DD/MM/YYYY (user) to YYYY-MM-DD (API)
  formatDateToApi(dateStr) {
    if (dateStr && typeof dateStr === 'string' && dateStr.includes('/')) {
      const parts = dateStr.split('/');
      if (parts.length === 3) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
      }
    }
    return dateStr;
  }

  // convert YYYY-MM-DD (API) to DD/MM/YYYY (user)
  formatDateToUser(dateStr) {
    if (dateStr && typeof dateStr === 'string' && dateStr.includes('-')) {
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
      }
    }
    return dateStr;
  }

  async sendFamily(family){
    const newFamily = structuredClone(family);
    delete newFamily.id;

    newFamily.birth_date = this.formatDateToApi(newFamily.birth_date);

    const request = {
      'newFamily': newFamily
    };

    const response = await post('/families', request);

    if (response.ok){
      const addedFamily = {...response.data};
      addedFamily.birth_date = this.formatDateToUser(addedFamily.birth_date);
      
      this.families.value.push(addedFamily);
  
      showPopup('Sucesso', 'Enviou nova família');
      return addedFamily;
    } else {
      showPopup('Erro', response.message);
      return null;
    };
  
  };

  async deleteFamily(id){
    const request = {
      'id': id
    };
  
    const response = await del('/families', request);
  
    if (response.ok){
  
      for (const index in this.families.value) {
        const family = this.families.value[index];
        
        if (family.id == id){
          this.families.value.splice(index, 1);
        };
      };
  
      showPopup('Sucesso', 'Família deletada');
  
    } else {
      showPopup('Erro', response.message);
    };
  
  };

  async searchFamilies(filter){

    const params = new URLSearchParams();

    if (filter === null || filter === undefined || typeof filter !== 'object' || Object.keys(filter).length === 0){
      params.append('year', new Date().getFullYear());
    } else {
      Object.entries(filter).forEach(([key, value]) => {
        const isEmpty = typeof value === 'string' && value.trim() === '';
        const isNull = value === null || value === undefined;

        if (!isEmpty && !isNull){
          if (typeof value === 'string'){
            value = value.replace(/[.,-\/]/g, '');
          };
          params.append(key, value);
        };
      });
    };

    const queryString = params.toString();
    const response = await get(`/families?${queryString}`);

    if (response.ok){
      response.data.forEach(family => {
        family.birth_date = this.formatDateToUser(family.birth_date);
      });
      this.families.value = response.data;
      return response.data;
    } else {
      showPopup('Erro', response.message);
      return null;
    };
  };

  async updateFamily(family){
    const familyToUpdate = structuredClone(family);
    
    familyToUpdate.birth_date = this.formatDateToApi(familyToUpdate.birth_date);

    const request = {
      'updatedFamily': familyToUpdate
    };

    const response = await put('/families', request);

    if (response.ok){
      showPopup('Sucesso', 'Cadastro atualizado');
    } else {
      showPopup('Erro', response.message);
    };

    return response.ok;
  };

};

export default new familyManager();