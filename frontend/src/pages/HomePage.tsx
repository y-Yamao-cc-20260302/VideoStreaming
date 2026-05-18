import { Link } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'

export default function HomePage() {
  const { user, isAuthenticated, logout } = useAuth()

  return (
    <div className="min-h-screen flex flex-col items-center justify-center px-4 gap-6">
      <h1 className="text-2xl font-bold">Sample App</h1>
      <p className="text-sm text-gray-500">React SPA テンプレートのプレースホルダー画面です。</p>

      {isAuthenticated ? (
        <div className="text-center space-y-3">
          <p className="text-sm">ログイン中: {user?.name}（{user?.email}）</p>
          <button onClick={logout} className="btn-secondary">ログアウト</button>
        </div>
      ) : (
        <div className="flex gap-3">
          <Link to="/login" className="btn-primary">ログイン</Link>
          <Link to="/register" className="btn-secondary">新規登録</Link>
        </div>
      )}
    </div>
  )
}
