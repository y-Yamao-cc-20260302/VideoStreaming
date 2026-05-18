export interface Paginated<T> {
  data: T[]
  meta: {
    current_page: number
    total: number
    per_page: number
    last_page: number
  }
}

export interface User {
  id: number
  name: string
  email: string
}
