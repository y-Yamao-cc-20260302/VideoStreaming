import { NavLink, Outlet } from 'react-router-dom'

const items = [
  { to: '/my/profile', label: 'プロフィール' },
  { to: '/my/password', label: 'パスワード変更' },
  { to: '/my/favorites', label: 'マイリスト' },
  { to: '/my/history', label: '視聴履歴' },
  { to: '/my/subscription', label: 'プラン管理' },
  { to: '/my/payment-histories', label: '課金履歴' },
]

export default function MyPageLayout() {
  return (
    <div className="grid grid-cols-12 gap-6">
      <aside className="col-span-12 md:col-span-3">
        <nav className="bg-white border">
          <ul>
            {items.map((it) => (
              <li key={it.to}>
                <NavLink
                  to={it.to}
                  className={({ isActive }) =>
                    `block px-4 py-3 text-sm border-b last:border-b-0 ${
                      isActive ? 'bg-gray-900 text-white' : 'hover:bg-gray-50'
                    }`
                  }
                >
                  {it.label}
                </NavLink>
              </li>
            ))}
          </ul>
        </nav>
      </aside>
      <section className="col-span-12 md:col-span-9">
        <div className="bg-white border p-6">
          <Outlet />
        </div>
      </section>
    </div>
  )
}
