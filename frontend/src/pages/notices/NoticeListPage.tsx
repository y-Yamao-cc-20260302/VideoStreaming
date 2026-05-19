import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Pagination from '../../components/common/Pagination'
import { noticesApi } from '../../api/notices'
import type { Notice, Paginated } from '../../types'
import { formatDate } from '../../utils/format'

export default function NoticeListPage() {
  const [result, setResult] = useState<Paginated<Notice> | null>(null)
  const [page, setPage] = useState(1)

  useEffect(() => {
    noticesApi.list(page).then((r) => setResult(r.data))
  }, [page])

  return (
    <div>
      <h1 className="text-xl font-bold mb-6">お知らせ</h1>
      <ul className="divide-y bg-white border">
        {result?.data.map((n) => (
          <li key={n.id} className="p-4">
            <Link to={`/notices/${n.id}`} className="hover:underline">
              <p className="text-xs text-gray-500">{formatDate(n.published_at)}</p>
              <h2 className="font-semibold">{n.title}</h2>
              {n.body_excerpt && <p className="text-sm text-gray-600 mt-1">{n.body_excerpt}</p>}
            </Link>
          </li>
        ))}
        {result && result.data.length === 0 && (
          <li className="p-6 text-center text-sm text-gray-500">お知らせはありません</li>
        )}
      </ul>
      {result && (
        <Pagination
          currentPage={result.meta.current_page}
          lastPage={result.meta.last_page}
          onPageChange={setPage}
        />
      )}
    </div>
  )
}
