import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { noticesApi } from '../../api/notices'
import type { Notice } from '../../types'
import { formatDate } from '../../utils/format'

export default function NoticeDetailPage() {
  const { id } = useParams<{ id: string }>()
  const [notice, setNotice] = useState<Notice | null>(null)

  useEffect(() => {
    noticesApi.show(Number(id)).then((r) => setNotice(r.data))
  }, [id])

  if (!notice) return <p className="text-sm text-gray-500">読み込み中...</p>

  return (
    <article className="bg-white border p-6">
      <Link to="/notices" className="text-sm text-gray-500">← お知らせ一覧へ</Link>
      <p className="text-xs text-gray-500 mt-4">{formatDate(notice.published_at)}</p>
      <h1 className="text-xl font-bold mt-1">{notice.title}</h1>
      <p className="text-sm whitespace-pre-wrap mt-4 leading-relaxed">{notice.body}</p>
    </article>
  )
}
