import apiClient from './client'
import type { Paginated, CastSummary, CastDetail } from '../types'

export const castfavoritesApi = {
  favorite:(cast_id:number) =>
    apiClient.post<CastDetail>(`/casts/${cast_id}/favorite`,{cast_id}),
}
