import apiClient from './client'
import type { Paginated, VideoSummary } from '../types'

export const favoritesApi = {
  list: (page = 1) =>
    apiClient.get<Paginated<VideoSummary>>('/favorites', { params: { page } }),

  add: (video_id: number) =>
    apiClient.post<{ video_id: number; favored_at: string }>('/favorites', { video_id }),

  remove: (video_id: number) => apiClient.delete(`/favorites/${video_id}`),
}
