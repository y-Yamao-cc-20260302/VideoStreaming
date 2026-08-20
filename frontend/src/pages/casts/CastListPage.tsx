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

    //  URLから現在の確定されたキーワードを取得
    const keyword = searchParams.get('keyword') ?? '';
    //  フォーム入力中のキーワードを取得（フォームに反映させるため、初期値はURLの値）
    const [inputKeyword, setInputKeyword] = useState(keyword);
    //  ブラウザの「戻る・進む」などでURLが変わった時に入力欄も同期させる
      useEffect(() => {
        setInputKeyword(keyword);
      }, [keyword]);

    // 職業プルダウンの設定
    useEffect(()=>{
      occupationsApi.list().then((r)=>setOccupations(r.data.data)).catch(()=>{})
    },[])

    // URLの更新
    const update = (patch: Record<string, string | number>) => {
      const next = new URLSearchParams(searchParams)
        for (const [k, v] of Object.entries(patch)) {
          if (v === '' || v === undefined || v === null) next.delete(k)
          else next.set(k, String(v))
        }
      if (!('page' in patch)) next.delete('page')
      setSearchParams(next)
    }

    // フォームに文字が入力された時の処理
    const handleChange = (e: React.FormEvent<HTMLInputElement>) => {
      const target = e.currentTarget;
      const value = target.value;
      setInputKeyword(value); 
      if (value.length > 255){
        target.setCustomValidity('255文字以内で入力してください');
      } else {
        target.setCustomValidity('');  
      }
    }

    // Enterが押されたときの処理
    const handleSubmit = (e: React.FormEvent) => {
      // ページ再読み込みを防止
      e.preventDefault()
      const keyword = inputKeyword.trim();
      // keywordの条件分岐処理
      if (keyword) {
        // 255文字を超えるならupdateしないようにする
        if (keyword.length > 255) {
          return; 
        }
        // 入力があれば更新または追加
      } else {
        searchParams.delete('keyword') // 空文字ならkeywordパラメータを削除
      }
      update({'keyword':keyword})
    }

    // 各検索条件の更新に伴いAPIを発火させる
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
              value={inputKeyword}
              onChange={handleChange}
              // 255文字までの正規表現(maxlengthと違い、バリデーションエラー表示がされる)
              pattern=".{0,255}"
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
