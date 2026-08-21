import type { CastSummary } from '../../types'
import CastCard from './CastCard'

export default function castGrid({ casts }: { casts: CastSummary[] }) {
  if (casts.length === 0) {
    return <p className="text-sm text-gray-500 py-8 text-center">出演者がありません</p>
  }
  return (
    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
      {casts.map((c) => (
        <CastCard key={c.id} cast={c} />
      ))}
    </div>
  )
}
