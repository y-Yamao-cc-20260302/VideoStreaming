import { useEffect, useState } from 'react'
import { reviewsApi } from '../../api/reviews'
import { useAuth } from '../../contexts/AuthContext'
import type { Paginated, ReviewItem } from '../../types'
import { formatDateTime } from '../../utils/format'

export default function ReviewSection({ videoId }: { videoId: number }) {
  const { isAuthenticated } = useAuth()
  const [result, setResult] = useState<Paginated<ReviewItem> | null>(null)
  const [rating, setRating] = useState(5)
  const [comment, setComment] = useState('')
  const [submitting, setSubmitting] = useState(false)

  const load = () => {
    reviewsApi.list(videoId).then((r) => setResult(r.data))
  }

  useEffect(() => {
    load()
  }, [videoId])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setSubmitting(true)
    try {
      await reviewsApi.create(videoId, rating, comment || null)
      setComment('')
      load()
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <section className="mt-8">
      <h2 className="text-lg font-bold mb-3">レビュー</h2>
      {isAuthenticated && (
        <form onSubmit={handleSubmit} className="bg-gray-50 p-4 mb-6 space-y-3">
          <div>
            <label className="block text-sm font-medium mb-1">評価</label>
            <select
              value={rating}
              onChange={(e) => setRating(Number(e.target.value))}
              className="border border-gray-200 px-3 py-2 text-sm"
            >
              {[5, 4, 3, 2, 1].map((r) => (
                <option key={r} value={r}>
                  {'★'.repeat(r)} ({r})
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">コメント</label>
            <textarea
              value={comment}
              onChange={(e) => setComment(e.target.value)}
              rows={3}
              className="w-full border border-gray-200 px-3 py-2 text-sm"
            />
          </div>
          <button
            type="submit"
            disabled={submitting}
            className="bg-gray-900 text-white px-4 py-2 text-sm disabled:opacity-50"
          >
            {submitting ? '送信中...' : '投稿する'}
          </button>
        </form>
      )}

      {result && (
        <ul className="space-y-3">
          {result.data.length === 0 && (
            <li className="text-sm text-gray-500">まだレビューがありません</li>
          )}
          {result.data.map((r) => (
            <li key={r.id} className="border-b pb-3">
              <div className="flex items-center justify-between">
                <span className="text-sm font-semibold">{r.user?.nickname ?? '匿名'}</span>
                <span className="text-xs text-gray-500">{formatDateTime(r.created_at)}</span>
              </div>
              <p className="text-sm mt-1">{'★'.repeat(r.rating)}</p>
              {r.comment && <p className="text-sm mt-1 whitespace-pre-wrap">{r.comment}</p>}
            </li>
          ))}
        </ul>
      )}
    </section>
  )
}
