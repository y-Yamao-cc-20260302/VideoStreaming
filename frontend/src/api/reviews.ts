import apiClient from './client'
import type { Paginated, ReviewItem } from '../types'

export const reviewsApi = {
  list: (videoId: number, page = 1) =>
    apiClient.get<Paginated<ReviewItem>>(`/videos/${videoId}/reviews`, { params: { page } }),

  create: (videoId: number, rating: number, comment: string | null) =>
    apiClient.post<ReviewItem>(`/videos/${videoId}/reviews`, { rating, comment }),

  update: (id: number, payload: { rating?: number; comment?: string | null }) =>
    apiClient.patch<ReviewItem>(`/reviews/${id}`, payload),

  remove: (id: number) => apiClient.delete(`/reviews/${id}`),
}
