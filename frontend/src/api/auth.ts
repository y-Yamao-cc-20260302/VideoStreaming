import apiClient from './client'
import type { User } from '../types'

export const authApi = {
  login: (email: string, password: string) =>
    apiClient.post<{ access_token: string }>('/auth/login', { email, password }),

  register: (name: string, email: string, password: string, password_confirmation: string) =>
    apiClient.post<{ access_token: string }>('/auth/register', {
      name, email, password, password_confirmation,
    }),

  logout: () => apiClient.post('/auth/logout'),

  me: () => apiClient.get<{ data: User }>('/auth/me'),

  refresh: () => apiClient.post<{ access_token: string }>('/auth/refresh'),
}
