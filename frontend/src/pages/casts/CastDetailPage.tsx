import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { castsApi } from '../../api/casts'
import { castfavoritesApi } from '../../api/castfavorites'
import { useAuth } from '../../contexts/AuthContext'
import type { CastDetail } from '../../types'
import { formatDate} from '../../utils/format'

import VideoGrid from '../../components/video/VideoGrid'
import type { Paginated,VideoSummary } from '../../types'

export default function CastDetailPage() {
  const { id } = useParams<{ id: string }>()
  const castId = Number(id)
  const { isAuthenticated } = useAuth()
  const navigate = useNavigate()
  const [cast, setCast] = useState<CastDetail | null>(null)
  const [loading, setLoading] = useState(true)
  const [result, setResult] = useState<Paginated<VideoSummary> | null>(null)
  
  // 出演者情報取得と出演作品取得のAPIを同時に実行
  useEffect(() => {
    const fetchAllData = async () => {
      setLoading(true)
      try{
        // 2つのAPIを同時に呼び、両方終わるのを待つ
        const[casts,videos]=await Promise.all([
          castsApi.show(castId),
          castsApi.video(castId),
        ]);
        // 完了後、それぞれのデータをセット
        setCast(casts.data);
        setResult(videos.data);
      }catch(err){
        console.error('データ取得失敗:', err);
      }finally{
        // 両方終わったらローディング解除
        setLoading(false)
      }
    }
    fetchAllData();
  }, [castId]);
  
  const toggleCastFavorite = async () => {
    if (!isAuthenticated) {
      navigate('/login?redirect=' + encodeURIComponent(window.location.pathname))
      return
    }
    if (!cast) return
    if (cast.is_favored) {
      // 登録済みなら非登録状態にする
      await castfavoritesApi.favorite(cast.id)
      setCast({ ...cast, is_favored: false })
    } else {
      // 非登録状態なら登録状態にする
      await castfavoritesApi.favorite(cast.id)
      setCast({ ...cast, is_favored: true })
    }
  }

  // anyは型チェックをスキップするための書き方
  // 変数の値が格納されているのはstatusの方
  const getGender = ( status: any | undefined )=> {
  // 数値と表示文字の対応表を作る
    if(status==1) return '男性';
    if(status==2) return '女性';
    return 'その他';
  };

  if (loading) return <p className="text-sm text-gray-500">読み込み中...</p>
  if (!cast) return <p className="text-sm text-gray-500">出演者が見つかりませんでした</p>

  return (
    <div className="gap-4">
      <div className="h-1/2 flex gap-4">
        <div className="aspect-video bg-gray-200 overflow-hidden w-1/3">
        {cast.picture_path ? (
            <img
            src={cast.picture_path}
            alt={cast.name}
            className="w-full h-full object-cover group-hover:opacity-80 transition-opacity"
            />
        ) : (
            <div className="w-full h-full flex items-center justify-center text-gray-400 text-xs">
            No Thumbnail
            </div>
        )}
        </div>
        <div className="w-2/3">
          <div className="h-2/3 p-6 overflow-x-auto">
            <table className="w-full text-sm text-left border-collapse table-fixed">
              <thead>
                <tr>
                  <th>名前</th>
                  <th>性別</th>
                  <th>誕生日</th>
                  <th>職業</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{cast.name}</td>
                  <td>{getGender(cast?.gender) ?? 'その他'}</td>
                  <td>{cast.birthday &&  ` ${formatDate(cast.birthday)}`}</td>
                  <td>{cast.occupation?.name}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div className="h-1/3">
            <button
              onClick={toggleCastFavorite}
              className={`px-4 py-2 text-sm border ${
                cast.is_favored ? 'bg-yellow-100 border-yellow-400' : 'border-gray-300'
              }`}
            >
              {cast.is_favored ? '★ マイリスト登録済み' : '☆ マイリストに追加'}
            </button>
          </div>
        </div>

      </div>
      <br></br>
      <p>出演作品一覧</p>
      <div className="h-1/2">
            <section className="col-span-12 md:col-span-9">
              {result && (
                <>
                  <VideoGrid videos={result.data} />
                </>
              )}
            </section>  
      </div>
    </div>
  )
}
