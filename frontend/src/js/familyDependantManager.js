import { get, post, del, put } from './myApiClient';
import showPopup from './myPopup';

class familyDependantManager {
  async getDependants(familyId) {
    const response = await get(`/families/${familyId}/dependants`);
    if (response.ok) {
      return response.data;
    } else {
      return [];
    }
  }

  async addDependant(familyId, name, birth_date, relation) {
    const response = await post(`/families/${familyId}/dependants`, { name, birth_date, relation });
    if (response.ok) {
      showPopup('Sucesso', 'Dependente adicionado com sucesso');
      return response.data;
    } else {
      showPopup('Erro ao adicionar dependente', response.message);
      return null;
    }
  }

  async updateDependant(familyId, dependantId, data) {
    const response = await put(`/families/${familyId}/dependants/${dependantId}`, data);
    if (response.ok) {
      showPopup('Sucesso', 'Dependente atualizado com sucesso');
      return true;
    } else {
      showPopup('Erro ao atualizar dependente', response.message);
      return false;
    }
  }

  async removeDependant(familyId, dependantId) {
    const response = await del(`/families/${familyId}/dependants/${dependantId}`);
    if (response.ok) {
      showPopup('Sucesso', 'Dependente removido com sucesso');
      return true;
    } else {
      showPopup('Erro ao remover dependente', response.message);
      return false;
    }
  }
}

export default new familyDependantManager();
