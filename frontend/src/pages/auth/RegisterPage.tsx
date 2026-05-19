import { useState } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'

export default function RegisterPage() {
  const { register } = useAuth()
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const redirect = searchParams.get('redirect') ?? '/'

  const [form, setForm] = useState({
    name: '',
    nickname: '',
    email: '',
    password: '',
    password_confirmation: '',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [loading, setLoading] = useState(false)

  const set = (key: string) => (e: React.ChangeEvent<HTMLInputElement>) =>
    setForm((f) => ({ ...f, [key]: e.target.value }))

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setErrors({})
    if (form.password !== form.password_confirmation) {
      setErrors({ password_confirmation: 'パスワードが一致しません' })
      return
    }
    setLoading(true)
    try {
      await register(form)
      navigate(redirect, { replace: true })
    } catch (err: unknown) {
      const e = err as { response?: { data?: { errors?: Record<string, string[]> } } }
      const apiErrors: Record<string, string> = {}
      for (const [k, msgs] of Object.entries(e.response?.data?.errors ?? {})) {
        apiErrors[k] = msgs[0]
      }
      setErrors(Object.keys(apiErrors).length ? apiErrors : { general: '登録に失敗しました' })
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex flex-col items-center justify-center px-4">
      <Link to="/" className="text-xl font-bold tracking-widest mb-8">動画配信サービス</Link>
      <div className="w-full max-w-sm space-y-6">
        <h1 className="text-xl font-bold text-center">新規会員登録</h1>
        {errors.general && <p className="text-sm text-red-500 text-center">{errors.general}</p>}
        <form onSubmit={handleSubmit} className="space-y-4">
          {[
            { key: 'name', label: 'お名前', type: 'text', required: true },
            { key: 'nickname', label: 'ニックネーム（任意）', type: 'text', required: false },
            { key: 'email', label: 'メールアドレス', type: 'email', required: true },
            { key: 'password', label: 'パスワード（8文字以上）', type: 'password', required: true },
            { key: 'password_confirmation', label: 'パスワード（確認）', type: 'password', required: true },
          ].map(({ key, label, type, required }) => (
            <div key={key}>
              <label className="block text-sm font-medium mb-1">{label}</label>
              <input
                type={type}
                required={required}
                value={form[key as keyof typeof form]}
                onChange={set(key)}
                className={`w-full border px-3 py-2 text-sm focus:outline-none focus:border-gray-900 ${
                  errors[key] ? 'border-red-400' : 'border-gray-200'
                }`}
              />
              {errors[key] && <p className="text-xs text-red-500 mt-0.5">{errors[key]}</p>}
            </div>
          ))}
          <button type="submit" disabled={loading} className="btn-primary w-full">
            {loading ? '登録中...' : '登録する'}
          </button>
        </form>
        <p className="text-sm text-center text-gray-500">
          すでにアカウントをお持ちの方は{' '}
          <Link to={`/login?redirect=${encodeURIComponent(redirect)}`} className="text-gray-900 underline">
            ログイン
          </Link>
        </p>
      </div>
    </div>
  )
}
