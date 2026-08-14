import apiClient from './client'
import type { Paginated, CastSummary } from '../types'

export const castfavoritesApi = {
  list: (page = 1) =>
    apiClient.get<Paginated<CastSummary>>('/castfavorites', { params: { page } }),

  add: (cast_id: number) =>
    apiClient.post<{ cast_id: number; favored_at: string }>('/castfavorites', { cast_id }),

  remove: (cast_id: number) => apiClient.delete(`/castfavorites/${cast_id}`),
}
