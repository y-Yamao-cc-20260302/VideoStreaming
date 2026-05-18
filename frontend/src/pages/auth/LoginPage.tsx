import { useState } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'

export default function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const redirect = searchParams.get('redirect') ?? '/'

  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      await login(email, password)
      navigate(redirect, { replace: true })
    } catch {
      setError('メールアドレスまたはパスワードが正しくありません')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex flex-col items-center justify-center px-4">
      <Link to="/" className="text-xl font-bold tracking-widest mb-8">Sample App</Link>
      <div className="w-full max-w-sm space-y-6">
        <h1 className="text-xl font-bold text-center">ログイン</h1>
        {error && <p className="text-sm text-red-500 text-center">{error}</p>}
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">メールアドレス</label>
            <input type="email" required value={email} onChange={(e) => setEmail(e.target.value)}
              className="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-900"
            />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">パスワード</label>
            <input type="password" required value={password} onChange={(e) => setPassword(e.target.value)}
              className="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-900"
            />
          </div>
          <button type="submit" disabled={loading} className="btn-primary w-full">
            {loading ? 'ログイン中...' : 'ログイン'}
          </button>
        </form>
        <p className="text-sm text-center text-gray-500">
          アカウントをお持ちでない方は{' '}
          <Link to={`/register?redirect=${encodeURIComponent(redirect)}`} className="text-gray-900 underline">新規登録</Link>
        </p>
      </div>
    </div>
  )
}
