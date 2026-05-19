import apiClient from './client'
import type { Paginated, VideoDetail, VideoSummary } from '../types'

export interface VideoListParams {
  category?: string
  genre?: string
  keyword?: string
  sort?: 'new' | 'popular' | 'release_date'
  page?: number
  per_page?: number
}

export const videosApi = {
  list: (params: VideoListParams = {}) =>
    apiClient.get<Paginated<VideoSummary>>('/videos', { params }),

  show: (id: number) => apiClient.get<VideoDetail>(`/videos/${id}`),

  newReleases: () => apiClient.get<{ data: VideoSummary[] }>('/videos/new'),

  popular: () => apiClient.get<{ data: VideoSummary[] }>('/videos/popular'),

  recommended: () => apiClient.get<{ data: VideoSummary[] }>('/videos/recommended'),

  reportProgress: (id: number, progress_sec: number) =>
    apiClient.post(`/videos/${id}/progress`, { progress_sec }),
}
