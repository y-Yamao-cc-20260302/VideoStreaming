<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Occupation;
use App\Http\Requests\Admin\Cast\StoreCastRequest;
use App\Http\Requests\Admin\Cast\UpdateCastRequest;
use App\Helpers\ImageHelper;
use App\Helpers\ErrorHelper;
use Illuminate\Support\Facades\DB;
use Exception;

class CastController extends Controller
{
    public function index(Request $request, $pagenate = 20)
    {
        $query = Cast::with(['occupation']);
        //値の変換ができれば、queryに職業idでの検索を追加
        $occupationId = $request->integer('occupation_id');
        // バリデーションチェックが0(失敗)ではなく、かつ変換ができれば検索を行う
        if ($occupationId && is_int($occupationId)) {
            $query->where('occupation_id', $occupationId);
        }

        // 文字列として受け取り、文字列として判定する
        $publishFilter = $request->string('publish')->toString();
        if ($publishFilter === '1') {
            $query->where('is_publish', true);
        } elseif ($publishFilter === '0') {
            $query->where('is_publish', false);
        }

        //含む検索 (ilke:あいまい検索)
        $keyword = $request->string('keyword')->toString();
        if ($keyword && is_String($keyword)) {
            $like = '%' . $keyword . '%';
            $query->where('name', 'ILIKE', $like);
        }

        // 将来的にはページネーションを受け取る
        $casts = $query->orderByDesc('id')->paginate($pagenate)->withQueryString();
        $occupations = Occupation::get();
        return view('admin.casts.index', compact('casts', 'occupations'));
    }

    public function new()
    {
        //出演者登録画面に遷移
        $occupations = Occupation::get();
        return view('admin.casts.new', compact('occupations'));
    }

    public function store(StoreCastRequest $request, ImageHelper $image, ErrorHelper $errorHelper)
    {
        $data = $request->safe()->except(('picture'));
        // 画像アップロード処理
        if ($request->hasFile('picture')) {
            $img_path = $image->uplodePicture($request->file('picture'));
            // 出演者機能ではpictureフォルダに保存するため、頭にpicture/をつける
            $data['picture_path'] = 'picture/' . $img_path;
        }

        try {
            // トランザクション処理を行う
            // function () use ($data) で、$dataをトランザクション内で使用できるようにする
            DB::transaction(function () use ($data) {
                Cast::create($data);
            });

            return redirect()->route('admin.casts.index')->with('success', '出演者を登録しました');
        } catch (Exception $e) {
            $errorHelper->outputLog($e);
            // successに結果を代入して渡している
            return redirect()->route('admin.casts.index')->with('success', '登録に失敗しました');
        }
    }

    public function edit(Cast $cast)
    {
        //出演者編集(更新)画面へ遷移
        $occupations = Occupation::get();
        return view('admin.casts.edit', compact('cast', 'occupations'));
    }

    public function update(UpdateCastRequest $request, Cast $cast, ImageHelper $image, ErrorHelper $errorHelper)
    {
        // 出演者情報を更新する
        $data = $request->safe()->except(('picture'));
        try {
            $old_path = '';
            // 画像アップロード処理
            if ($request->hasFile('picture')) {
                // 古い画像のパスを一時保存
                if ($cast->picture_path) {
                    $old_path = $cast->picture_path;
                }
                $img_path = $image->uplodePicture($request->file('picture'));
                // 出演者機能ではpictureフォルダに保存するため、頭にpicture/をつける
                $data['picture_path'] = 'picture/' . $img_path;
            }

            // トランザクション処理を行う
            // function () use ($data) で、$dataをトランザクション内で使用できるようにする
            DB::transaction(function () use ($data, $cast) {
                $cast->update($data);
            });
            // 新しい画像のアップロードが完了すれば、古い写真を削除する
            if ($old_path && $cast->picture_path) {
                $image->deletePicture($old_path);
            }
            return redirect()->route('admin.casts.index')->with('success', '出演者を更新しました');
        } catch (Exception $e) {
            $errorHelper->outputLog($e);
            // successに結果を代入して渡している
            return redirect()->route('admin.casts.index')->with('success', '更新に失敗しました');
        }
    }

    public function destroy(Cast $cast, ImageHelper $image, ErrorHelper $errorHelper)
    {
        $img_path = $cast->picture_path;

        // 出演者を削除する
        try {
            DB::transaction(function () use ($cast) {
                $cast->delete();
            });

            if ($img_path) {
                $image->deletePicture($img_path);
            }
            return redirect()->route('admin.casts.index')->with('success', '出演者を削除しました');
        } catch (Exception $e) {
            $errorHelper->outputLog($e);
            return redirect()->route('admin.casts.index')->with('success', '削除に失敗しました');
        }
    }

    public function publish(Cast $cast, ErrorHelper $errorHelper)
    {
        // 公開状態を更新する
        try {
            DB::transaction(function () use ($cast) {
                // booleanを否定のみで更新できる
                $cast->update(['is_publish' => ! $cast->is_publish]);
            });
            // backで元のページへもどる
            return back()->with('success', $cast->is_publish ? '公開しました' : '非公開しました');
        } catch (Exception $e) {
            $errorHelper->outputLog($e);
            return redirect()->route('admin.casts.index')->with('success', '更新に失敗しました');
        }
    }
}
