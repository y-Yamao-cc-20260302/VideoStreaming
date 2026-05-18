import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import { authApi } from '../api/auth'
import type { User } from '../types'

interface AuthContextValue {
  user: User | null
  isLoading: boolean
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  register: (name: string, email: string, password: string, confirmation: string) => Promise<void>
  logout: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | null>(null)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    const token = localStorage.getItem('access_token')
    if (!token) {
      setIsLoading(false)
      return
    }
    authApi
      .me()
      .then(({ data }) => setUser(data.data))
      .catch(() => localStorage.removeItem('access_token'))
      .finally(() => setIsLoading(false))
  }, [])

  const login = useCallback(async (email: string, password: string) => {
    const { data } = await authApi.login(email, password)
    localStorage.setItem('access_token', data.access_token)
    const { data: me } = await authApi.me()
    setUser(me.data)
  }, [])

  const register = useCallback(
    async (name: string, email: string, password: string, confirmation: string) => {
      const { data } = await authApi.register(name, email, password, confirmation)
      localStorage.setItem('access_token', data.access_token)
      const { data: me } = await authApi.me()
      setUser(me.data)
    },
    []
  )

  const logout = useCallback(async () => {
    await authApi.logout().catch(() => {})
    localStorage.removeItem('access_token')
    setUser(null)
  }, [])

  return (
    <AuthContext.Provider
      value={{ user, isLoading, isAuthenticated: !!user, login, register, logout }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}
