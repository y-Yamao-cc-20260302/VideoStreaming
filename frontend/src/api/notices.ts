import apiClient from './client'
import type { Notice, Paginated } from '../types'

export const noticesApi = {
  list: (page = 1) => apiClient.get<Paginated<Notice>>('/notices', { params: { page } }),
  show: (id: number) => apiClient.get<Notice>(`/notices/${id}`),
}
