// 出演者リストページ
// Hooksの導入
import {useState,useEffect} from 'react';
import { useSearchParams } from 'react-router-dom'
import CastGrid from '../../components/cast/CastGrid';
import Pagination from '../../components/common/Pagination'
// 必要なApiをimportする
import { castsApi} from '../../api/casts';
import { occupationsApi} from '../../api/occupations';
// 必要なインターフェースをimportする
import type {Paginated,CastSummary,Occupation} from '../../types'

export default function CastListPage() {
    const [searchParams, setSearchParams] = useSearchParams()
    // const [casts,setCasts] = useState<Cast[]>([])
    const [loading,setLoading] = useState<boolean>(true)
    // setResultの値に変更があれば、useStateが動きresultに値がsetされる。
    const [result,setResult] = useState<Paginated<CastSummary>| null>(null)
    // 職業テーブルを取得
    const [occupations,setOccupations] = useState<Occupation[]>([])
    // 並び替えを取得
    const sort = (searchParams.get('sort') as 'name' | 'occupation' | 'birthday' | null) ?? 'name'  
    // 職業を取得
    const occupation = searchParams.get('occupation') ?? ''
    // ページ番号を取得
    const page = Number(searchParams.get('page') ?? 1)

    useEffect(()=>{
      occupationsApi.list().then((r)=>setOccupations(r.data.data)).catch(()=>{})
    },[])

    const update = (patch: Record<string, string | number>) => {
    const next = new URLSearchParams(searchParams)
      for (const [k, v] of Object.entries(patch)) {
        if (v === '' || v === undefined || v === null) next.delete(k)
        else next.set(k, String(v))
      }
    if (!('page' in patch)) next.delete('page')
    setSearchParams(next)
    }

    // URLの既存の 'keyword' を初期値にする（なければ空文字）
    const [keyword, setKeyword] = useState(searchParams.get('keyword') || '')
    // handleSubmitが呼び出されたときに処理を開始する
    const handleSubmit = (e: React.FormEvent) => {
      // ページ再読み込みを防止
      e.preventDefault()
      // keywordの条件分岐処理
      if (keyword.trim()) {
        searchParams.set('keyword', keyword.trim()) // 入力があれば更新または追加
      } else {
        searchParams.delete('keyword') // 空文字ならkeywordパラメータを削除
      }
      // 関数内で触るときはこの書き方'代入先変数名':代入する変数
      update({'keyword':keyword})
    }

    useEffect(()=>{
        setLoading(true)
        // r(レスポンス)が届いたら、setResultを実行。
        //setResult(r.data)は、r.dataを取得する
        castsApi
            .list({
              occupation: occupation||undefined,
              sort,
              page,
              keyword: keyword || undefined,
            }).then((r)=>setResult(r.data))
            .finally(()=>setLoading(false))
    },[occupation,sort,keyword,page]);

    return (
    <div className="grid grid-cols-12 gap-6">
      <aside className="col-span-12 md:col-span-3 space-y-4">
        <div>
          <h3 className="text-sm font-semibold mb-2">名前</h3>
          <form onSubmit={handleSubmit} className="flex-1 max-w-md">
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="出演者を検索"
              className="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-900"
            />
          </form>
        </div>

        <div>
          <h3 className="text-sm font-semibold mb-2">職業</h3>
          <select
            value={occupation}
            onChange={(e) => update({ occupation: e.target.value })}
            className="w-full border border-gray-200 px-3 py-2 text-sm"
          >
            <option value="">すべて</option>
            {occupations.map((o) => (
              <option key={o.id} value={o.slug}>
                {o.name}
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
            <option value="name">名前順</option>
            <option value="occupation">職業順</option>
            <option value="birthday">誕生日順</option>
          </select>
        </div>
        
      </aside>
        <section className="col-span-12 md:col-span-9">
        <h1 className="text-lg font-bold mb-4">
          出演者一覧
        </h1>
        {/* ロード中表示を、カード表示位置にのみにするため */}
        {loading && <p className="text-sm text-gray-500">読み込み中...</p>}
        {result && (
            <>
                <CastGrid casts={result.data} />
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
