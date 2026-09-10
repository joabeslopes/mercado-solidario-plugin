import { get } from './myApiClient';
import showPopup from './myPopup';

class entityManager {
  async getEntities() {
    const response = await get('/entity');
    if (response.ok) {
      // API returns an object indexed by ID, we map it to an array
      return Object.values(response.data);
    } else {
      showPopup('Erro', response.message);
      return [];
    }
  }
}

export default new entityManager();