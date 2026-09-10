import { get, post, del, put } from './myApiClient';
import showPopup from './myPopup';

class entityPersonManager {
  async getPersonEntities(personId) {
    const response = await get(`/persons/${personId}/entities`);
    if (response.ok) {
      return response.data;
    } else {
      return [];
    }
  }

  async addPersonEntity(personId, entityId, status = 'planned', notes = '') {
    const response = await post(`/persons/${personId}/entities`, { person_id: personId, entity_id: entityId, status, notes });
    if (response.ok) {
      showPopup('Sucesso', 'Entidade associada com sucesso');
      return response.data;
    } else {
      showPopup('Erro ao associar entidade', response.message);
      return null;
    }
  }

  async updatePersonEntity(personId, entityId, data) {
    const response = await put(`/persons/${personId}/entities/${entityId}`, data);
    if (response.ok) {
      showPopup('Sucesso', 'Relação atualizada com sucesso');
      return true;
    } else {
      showPopup('Erro ao atualizar relação', response.message);
      return false;
    }
  }

  async removePersonEntity(personId, entityId) {
    const response = await del(`/persons/${personId}/entities/${entityId}`, { person_id: personId, entity_id: entityId });
    if (response.ok) {
      showPopup('Sucesso', 'Associação removida com sucesso');
      return true;
    } else {
      showPopup('Erro ao remover associação', response.message);
      return false;
    }
  }
}

export default new entityPersonManager();