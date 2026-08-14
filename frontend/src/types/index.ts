export interface Paginated<T> {
  data: T[]
  meta: {
    current_page: number
    total: number
    per_page: number
    last_page: number
  }
}

export interface Subscription {
  id: number
  plan_code: string
  plan_name: string
  price_jpy?: number | null
  status: 'active' | 'canceled' | 'expired'
  started_at: string | null
  ended_at: string | null
}

export interface User {
  id: number
  email: string
  name: string
  nickname: string | null
  avatar_url: string | null
  status: 'active' | 'suspended' | 'withdrawn'
  subscription?: {
    plan_code: string | null
    plan_name: string | null
    status: string
    started_at: string | null
    ended_at: string | null
  } | null
}

export interface Category {
  id: number
  name: string
  slug: string
}

export interface Genre {
  id: number
  name: string
  slug: string
}

export interface VideoSummary {
  id: number
  title: string
  description: string | null
  thumbnail_url: string | null
  duration_sec: number
  release_date: string | null
  category?: Category
  genres?: Genre[]
}

export interface VideoDetail extends VideoSummary {
  stream_url: string
  rating_avg: number | null
  rating_count: number
  is_favored: boolean
  progress_sec: number
}

export interface ReviewItem {
  id: number
  rating: number
  comment: string | null
  created_at: string
  user?: {
    id: number
    nickname: string | null
    avatar_url: string | null
  }
}

export interface SubscriptionPlan {
  id: number
  code: string
  name: string
  price_jpy: number
  description: string | null
  is_active: boolean
}

export interface PaymentHistoryItem {
  id: number
  plan_code: string | null
  plan_name: string | null
  amount_jpy: number
  paid_at: string | null
}

export interface WatchHistoryItem {
  video: VideoSummary
  progress_sec: number
  watched_at: string | null
}

export interface Notice {
  id: number
  title: string
  body?: string
  body_excerpt?: string
  published_at: string | null
}

export interface AuthResponse {
  access_token: string
  token_type: string
  expires_in: number
  user: User
}

export interface CastSummary{
  id: string;
  name: string;
  picture_path: string;
}

export interface CastDetail extends CastSummary {
  birthday: string
  occupation_id: number
  is_favored: boolean
}

export interface Occupation {
  id: number
  name: string
  slug: string
}