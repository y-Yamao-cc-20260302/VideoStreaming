interface Props {
  currentPage: number
  lastPage: number
  onPageChange: (page: number) => void
}

export default function Pagination({ currentPage, lastPage, onPageChange }: Props) {
  if (lastPage <= 1) return null

  const pages = Array.from({ length: Math.min(lastPage, 7) }, (_, i) => {
    if (lastPage <= 7) return i + 1
    if (currentPage <= 4) return i + 1
    if (currentPage >= lastPage - 3) return lastPage - 6 + i
    return currentPage - 3 + i
  })

  return (
    <div className="flex items-center justify-center gap-1 mt-10">
      <PageBtn disabled={currentPage === 1} onClick={() => onPageChange(currentPage - 1)}>←</PageBtn>
      {pages.map((p) => (
        <PageBtn key={p} active={p === currentPage} onClick={() => onPageChange(p)}>{p}</PageBtn>
      ))}
      <PageBtn disabled={currentPage === lastPage} onClick={() => onPageChange(currentPage + 1)}>→</PageBtn>
    </div>
  )
}

function PageBtn({
  children, onClick, active, disabled,
}: {
  children: React.ReactNode
  onClick: () => void
  active?: boolean
  disabled?: boolean
}) {
  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={`w-9 h-9 text-sm flex items-center justify-center border transition-colors
        ${active ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-200 hover:border-gray-900'}
        disabled:opacity-30 disabled:cursor-not-allowed`}
    >
      {children}
    </button>
  )
}
