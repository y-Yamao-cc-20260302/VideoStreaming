import apiClient from './client'
import type { AuthResponse, User } from '../types'

export const authApi = {
  login: (email: string, password: string) =>
    apiClient.post<AuthResponse>('/auth/login', { email, password }),

  register: (payload: {
    name: string
    nickname?: string
    email: string
    password: string
    password_confirmation: string
  }) => apiClient.post<AuthResponse>('/auth/register', payload),

  logout: () => apiClient.post('/auth/logout'),

  me: () => apiClient.get<User>('/auth/me'),

  refresh: () => apiClient.post<AuthResponse>('/auth/refresh'),

  updateProfile: (payload: { name?: string; nickname?: string | null; email?: string }) =>
    apiClient.patch<User>('/auth/me', payload),

  changePassword: (current_password: string, new_password: string, new_password_confirmation: string) =>
    apiClient.patch('/auth/me/password', { current_password, new_password, new_password_confirmation }),

  withdraw: () => apiClient.delete('/auth/me'),
}
