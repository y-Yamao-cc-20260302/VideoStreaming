import { Link, NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'
import { useState } from 'react'

export default function MainLayout() {
  const { user, isAuthenticated, logout } = useAuth()
  const navigate = useNavigate()
  const [keyword, setKeyword] = useState('')

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (keyword.trim()) {
      navigate(`/search?q=${encodeURIComponent(keyword.trim())}`)
    }
  }

  const handleLogout = async () => {
    await logout()
    navigate('/')
  }

  return (
    <div className="min-h-screen bg-gray-50 text-gray-900">
      <header className="bg-white border-b">
        <div className="max-w-7xl mx-auto px-4 py-3 flex items-center gap-6">
          <Link to="/" className="text-xl font-bold tracking-wider whitespace-nowrap">
            動画配信サービス
          </Link>
          <form onSubmit={handleSubmit} className="flex-1 max-w-md">
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="作品を検索"
              className="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-900"
            />
          </form>
          <nav className="flex items-center gap-4 text-sm">
            <NavLink to="/videos" className={({ isActive }) => (isActive ? 'font-semibold' : '')}>
              作品一覧
            </NavLink>
            <NavLink to="/plans" className={({ isActive }) => (isActive ? 'font-semibold' : '')}>
              プラン
            </NavLink>
            <NavLink to="/notices" className={({ isActive }) => (isActive ? 'font-semibold' : '')}>
              お知らせ
            </NavLink>
            <NavLink to="/casts" className={({ isActive }) => (isActive ? 'font-semibold' : '')}>
                  出演者一覧
            </NavLink>
            {isAuthenticated ? (
              <div className="flex items-center gap-3">
                <Link to="/my/profile" className="text-gray-700">
                  {user?.nickname ?? user?.name}
                </Link>
                <button onClick={handleLogout} className="text-gray-500 hover:text-gray-900">
                  ログアウト
                </button>
              </div>
            ) : (
              <>
                <Link to="/login">ログイン</Link>
                <Link to="/register" className="bg-gray-900 text-white px-3 py-1.5">
                  新規登録
                </Link>
              </>
            )}
          </nav>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 py-6">
        <Outlet />
      </main>

      <footer className="border-t mt-12 py-6 text-center text-xs text-gray-500">
        © {new Date().getFullYear()} 動画配信サービス
      </footer>
    </div>
  )
}
