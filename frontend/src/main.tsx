import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './contexts/AuthContext'
import MainLayout from './components/layout/MainLayout'
import MyPageLayout from './components/layout/MyPageLayout'
import { RequireAuth } from './router/index'
import './index.css'
import HomePage from './pages/HomePage'
import LoginPage from './pages/auth/LoginPage'
import RegisterPage from './pages/auth/RegisterPage'
import VideoListPage from './pages/videos/VideoListPage'
import VideoDetailPage from './pages/videos/VideoDetailPage'
import SearchPage from './pages/search/SearchPage'
import PlansPage from './pages/PlansPage'
import NoticeListPage from './pages/notices/NoticeListPage'
import NoticeDetailPage from './pages/notices/NoticeDetailPage'
import ProfilePage from './pages/my/ProfilePage'
import PasswordPage from './pages/my/PasswordPage'
import FavoritesPage from './pages/my/FavoritesPage'
import HistoryPage from './pages/my/HistoryPage'
import SubscriptionPage from './pages/my/SubscriptionPage'
import PaymentHistoriesPage from './pages/my/PaymentHistoriesPage'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: { staleTime: 1000 * 60 * 5, retry: 1 },
  },
})

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />

            <Route element={<MainLayout />}>
              <Route path="/" element={<HomePage />} />
              <Route path="/videos" element={<VideoListPage />} />
              <Route path="/videos/:id" element={<VideoDetailPage />} />
              <Route path="/search" element={<SearchPage />} />
              <Route path="/plans" element={<PlansPage />} />
              <Route path="/notices" element={<NoticeListPage />} />
              <Route path="/notices/:id" element={<NoticeDetailPage />} />

              <Route element={<RequireAuth />}>
                <Route path="/my" element={<MyPageLayout />}>
                  <Route path="profile" element={<ProfilePage />} />
                  <Route path="password" element={<PasswordPage />} />
                  <Route path="favorites" element={<FavoritesPage />} />
                  <Route path="history" element={<HistoryPage />} />
                  <Route path="subscription" element={<SubscriptionPage />} />
                  <Route path="payment-histories" element={<PaymentHistoriesPage />} />
                </Route>
              </Route>
            </Route>
          </Routes>
        </BrowserRouter>
      </AuthProvider>
    </QueryClientProvider>
  </React.StrictMode>
)
