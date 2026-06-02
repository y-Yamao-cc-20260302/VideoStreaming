<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Occupation;
use App\Http\Requests\Admin\Cast\StoreCastRequest;
use App\Http\Requests\Admin\Cast\UpdateCastRequest;

class CastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cast::with(['occupation']);
        //値の変換ができれば、queryに職業idでの検索を追加
        if ($occupationId = $request->integer('occupation_id')) {
            $query->where('occupation_id',$occupationId);
        }

        // 文字列として受け取り、文字列として判定する
        $publishFilter = $request->string('publish')->toString();
        if ($publishFilter === '1') {
            $query->where('is_publish', true);
        } elseif ($publishFilter === '0') {
            $query->where('is_publish', false);
        }

        //含む検索 (ilke:あいまい検索)
        if ($keyword = $request->string('keyword')->toString()) {
            $like = '%'.$keyword.'%';
            $query->where(fn ($q) => $q->where('name','ILIKE',$like));
        }

        $casts = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $occupations = Occupation::get();
        return view('admin.casts.index',compact('casts','occupations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //出演者登録
        $occupations = Occupation::get();
        
        return view('admin.casts.create',compact('occupations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCastRequest $request)
    {

        $data = $request->safe()->except(('picture'));
        if ($request->hasFile('picture')) {
            $data['picture_path'] = $request->file('picture')->store('picture', 'public');
        }

        // テーブルそのものに命令をしている書き方createなのでエラーなく通る
        Cast::create($data);

        return redirect()->route('admin.casts.index')->with('success','出演者を登録しました');
    }

    public function edit(Cast $cast)
    {
        //出演者編集
        $occupations = Occupation::get();
        return view('admin.casts.edit',compact('cast','occupations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCastRequest $request,Cast $cast)
    {
        $data = $request->safe()->except(('picture'));

        if ($request->hasFile('picture')) {
            $data['picture_path'] = $request->file('picture')->store('picture', 'public');
        }

        // 一つのデータに対して更新をかける書き方
        $cast->update($data);
        return redirect()->route('admin.casts.index')->with('success','出演者を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cast $cast)
    {
        //出演者削除
        $cast->delete();
        return redirect()->route('admin.casts.index')->with('success','出演者を削除しました');
    }

    public function publish(Cast $cast)
    {

    }
}
