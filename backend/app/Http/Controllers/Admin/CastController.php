<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Occupation;


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
        //出演者登録()get
        return view('admin.casts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }

    public function publish(string $id)
    {

    }
}
