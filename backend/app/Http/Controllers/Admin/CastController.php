<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Occupation;
use App\Http\Requests\Admin\Cast\StoreCastRequest;

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

        Cast::create($data);

        //登録ページ保存押下
        // $casts = new Cast();
        // $casts->name = $request->name;
        // $casts->gender = $request->gender;
        // $casts->birthday = $request->birthday;
        // $casts->occupation_id = $request->occupation_id;
        // $casts->picture_path = $request->picture_path;
        // $casts->is_publish = $request->is_publish;

        // $casts->save();
        return redirect()->route('admin.casts.index')->with('success','出演者を登録しました');
    }

    public function edit(string $id)
    {
        //出演者編集
        // $casts = Cast::where('id',$id)->get();
        // return view('admin.casts.edit',compact('casts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //編集ページ、保存押下 仮実装
        // $casts = Cast::where('id',$id)->get();

        // $casts->name = $request->name;
        // $casts->gender = $request->gender;
        // $casts->birthday = $request->birthday;
        // $casts->occupation_id = $request->occupation_id;
        // $casts->picture_path = $request->picture_path;
        // $casts->is_publish = $request->is_publish;

        // $casts->save();

        // return view('admin.casts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //出演者削除
        // $casts = Cast::where('id',$id)->get();
        // $casts->delete();
        // return view('admin.casts.index');
    }

    public function publish(string $id)
    {
        //公開設定押下
        // return view('casts'); リターンしちゃダメと思う
    }
}
