export function formatDuration(seconds: number): string {
  if (!seconds || seconds <= 0) return ''
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  if (h > 0) return `${h}時間${m}分`
  if (m > 0) return `${m}分${s > 0 ? s + '秒' : ''}`
  return `${s}秒`
}

export function formatJpy(amount: number): string {
  return `¥${amount.toLocaleString('ja-JP')}`
}

export function formatDate(iso: string | null): string {
  if (!iso) return '-'
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return '-'
  return d.toLocaleDateString('ja-JP')
}

export function formatDateTime(iso: string | null): string {
  if (!iso) return '-'
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return '-'
  return d.toLocaleString('ja-JP')
}
