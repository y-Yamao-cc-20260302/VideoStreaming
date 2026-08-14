import apiClient from './client'
import type { Occupation} from '../types'

export const occupationsApi = {
  list: () => apiClient.get<{ data: Occupation[] }>('/occupations'),
}

