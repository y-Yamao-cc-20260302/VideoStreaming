import apiClient from './client'
import type { Category, Genre } from '../types'

export const categoriesApi = {
  list: () => apiClient.get<{ data: Category[] }>('/categories'),
}

export const genresApi = {
  list: () => apiClient.get<{ data: Genre[] }>('/genres'),
}
