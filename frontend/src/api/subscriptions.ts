import apiClient from './client'
import type { Paginated, PaymentHistoryItem, Subscription, SubscriptionPlan } from '../types'

export const subscriptionPlansApi = {
  list: () => apiClient.get<{ data: SubscriptionPlan[] }>('/subscription-plans'),
}

export const subscriptionsApi = {
  current: () => apiClient.get<Subscription | null>('/subscriptions/current'),
  subscribe: (plan_code: string) =>
    apiClient.post<Subscription>('/subscriptions', { plan_code }),
  cancel: () => apiClient.delete<Subscription>('/subscriptions/current'),
}

export const paymentHistoriesApi = {
  list: (page = 1) =>
    apiClient.get<Paginated<PaymentHistoryItem>>('/payment-histories', { params: { page } }),
}
