import apiClient from "./client";
import type {Paginated,CastDetail,CastSummary, VideoSummary} from '../types'

export interface CastListParams{
    id?: string
    name?: string
    picture_path?: string
    occupation?:string
    birthday? : string
    keyword?: string
    sort?: 'name' | 'occupation' | 'birthday'
    page?: number
    per_page?: number
}

export const castsApi = {
    list: (params: CastListParams = {}) =>
        apiClient.get<Paginated<CastSummary>>('/casts',{params}),

    show: (id: number) => apiClient.get<CastDetail>(`/casts/${id}`),

    video:(id:number)=>
    apiClient.get<Paginated<VideoSummary>>(`/casts/${id}/video`),

}