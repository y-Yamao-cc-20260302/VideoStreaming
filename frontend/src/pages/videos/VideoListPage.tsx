import { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import VideoGrid from '../../components/video/VideoGrid'
import Pagination from '../../components/common/Pagination'
import { categoriesApi, genresApi } from '../../api/categories'
import { videosApi } from '../../api/videos'
import type { Category, Genre, Paginated, VideoSummary } from '../../types'

export default function VideoListPage() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [categories, setCategories] = useState<Category[]>([])
  const [genres, setGenres] = useState<Genre[]>([])
  const [result, setResult] = useState<Paginated<VideoSummary> | null>(null)
  const [loading, setLoading] = useState(false)

  const category = searchParams.get('category') ?? ''
  const genre = searchParams.get('genre') ?? ''
  const sort = (searchParams.get('sort') as 'new' | 'popular' | 'release_date' | null) ?? 'new'
  const page = Number(searchParams.get('page') ?? 1)
  const keyword = searchParams.get('q') ?? ''

  useEffect(() => {
    categoriesApi.list().then((r) => setCategories(r.data.data)).catch(() => {})
    genresApi.list().then((r) => setGenres(r.data.data)).catch(() => {})
  }, [])

  useEffect(() => {
    setLoading(true)
    videosApi
      .list({
        category: category || undefined,
        genre: genre || undefined,
        sort,
        page,
        keyword: keyword || undefined,
      })
      .then((r) => setResult(r.data))
      .finally(() => setLoading(false))
  }, [category, genre, sort, page, keyword])

  const update = (patch: Record<string, string | number>) => {
    const next = new URLSearchParams(searchParams)
    for (const [k, v] of Object.entries(patch)) {
      if (v === '' || v === undefined || v === null) next.delete(k)
      else next.set(k, String(v))
    }
    if (!('page' in patch)) next.delete('page')
    setSearchParams(next)
  }

  return (
    <div className="grid grid-cols-12 gap-6">
      <aside className="col-span-12 md:col-span-3 space-y-4">
        <div>
          <h3 className="text-sm font-semibold mb-2">カテゴリ</h3>
          <select
            value={category}
            onChange={(e) => update({ category: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
          >
            <option value="">すべて</option>
            {categories.map((c) => (
              <option key={c.id} value={c.slug}>
                {c.name}
              </option>
            ))}
          </select>
        </div>
        <div>
          <h3 className="text-sm font-semibold mb-2">ジャンル</h3>
          <select
            value={genre}
            onChange={(e) => update({ genre: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
          >
            <option value="">すべて</option>
            {genres.map((g) => (
              <option key={g.id} value={g.slug}>
                {g.name}
              </option>
            ))}
          </select>
        </div>
        <div>
          <h3 className="text-sm font-semibold mb-2">並び順</h3>
          <select
            value={sort}
            onChange={(e) => update({ sort: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
          >
            <option value="new">新着順</option>
            <option value="release_date">公開日順</option>
            <option value="popular">人気順</option>
          </select>
        </div>
      </aside>
      <section className="col-span-12 md:col-span-9">
        <h1 className="text-lg font-bold mb-4">
          作品一覧{keyword && <span className="ml-2 text-sm text-gray-500">「{keyword}」の検索結果</span>}
        </h1>
        {loading && <p className="text-sm text-gray-500">読み込み中...</p>}
        {result && (
          <>
            <VideoGrid videos={result.data} />
            <Pagination
              currentPage={result.meta.current_page}
              lastPage={result.meta.last_page}
              onPageChange={(p) => update({ page: p })}
            />
          </>
        )}
      </section>
    </div>
  )
}
