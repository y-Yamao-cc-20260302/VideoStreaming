import apiClient from './client'
import type { Paginated, WatchHistoryItem } from '../types'

export const watchHistoriesApi = {
  list: (page = 1) =>
    apiClient.get<Paginated<WatchHistoryItem>>('/watch-histories', { params: { page } }),
}
