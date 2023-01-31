<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TongHopController extends Controller
{
    public function danhsachTongHop(Request $request){
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $index = 1;
                $ds_bai_viet_TH = DB::table('hwp_key')
                    ->where('check','=','1')
                    ->orderBy('id', 'desc')
                    ->paginate(15);
                dd($ds_bai_viet_TH);
//                    $ds_bai_viet_TH = DB::table('hwp_key')
//                        ->whereExists(function ($query) {
//                            $query->from('hwp_key_has_post')
//                                ->whereColumn('hwp_key_has_post.id_key', 'hwp_key.id');
//                        })
//                        ->orderBy('id', 'desc')
//                        ->paginate(15);
//                    $ds_bai_viet_TH = DB::select('SELECT * FROM rd.hwp_key where exists
//                                                        (Select * from rd.hwp_key_has_post where hwp_key.id = hwp_key_has_post.id_key)');
                Session::put('tasks_url',$request->fullUrl());
                return view("admin.bai_tong_hop.danh_sach_TH", compact('ds_bai_viet_TH', 'index'));
            } else {
                return redirect('/admin/login');

            }

        } catch (\Exception $e) {
            return abort(404);
        }
    }

//    public function tim_key(){
//        return view("admin.bai_tong_hop.key_chon_post");
//    }
//
//
//    public function searchKey(Request $request){
//        try {
//            $ses = $request->session()->get('tk_user');
//            if (isset($ses)) {
//                $index = 1;
//                if (isset($_GET['s']) && strlen($_GET['s']) >= 1) {
//                    $search_text = $_GET['s'];
//                    $ds_key = DB::table('hwp_key')
//                        ->where('hwp_key.ten', 'like', '%' . $search_text . '%')
//                        ->orderBy('hwp_key.id', 'desc')->paginate(15);
//                    return view("admin.bai_tong_hop.key_chon_post", compact('search_text','ds_key', 'index'));
//                }
//                else{
//                    return view("admin.bai_tong_hop.key_chon_post");
//                }
//            } else {
//                return redirect('/admin/login');
//
//            }
//
//        } catch (\Exception $e) {
//            return abort(404);
//        }
//    }
//
//    public function them_post($id){
//        $key = DB::table('hwp_key')->select('id','ten')->where('id','=',$id)->first();
//        $post_detail = DB::table('hwp_posts')
//            ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view','hwp_posts.post_name',
//                'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title')
//            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
//            ->join('hwp_key','hwp_key.id','=','hwp_posts.id_key')
//            ->where('hwp_key.id', '=', $id)
//            ->where('twitter_image','like','http%')
//            ->where('twitter_image','not like','https://www.facebook.com%')
////                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
//            ->orderBy('hwp_posts.id','desc')
//            ->get()->toArray();
//        return view("admin.bai_tong_hop.them_post",compact('post_detail','key'));
//    }
//
//    public function luu_post(Request $request){
//        DB::table('hwp_key_has_post')->where('id_key','=',$request->id_key)->delete();
//        if (!empty($request->input('selectPost'))) {
//            foreach ($request->input('selectPost') as $value) {
//                $ID_will_insert = DB::table('hwp_posts')
//                    ->where('ID', '=', $value)->first();
//
//                    DB::table("hwp_key_has_post")->insert([
//                        'id_key' => $request->id_key,
//                        'id_post' => $ID_will_insert->ID
//                    ]);
//            }
//
//        }
//        return redirect()->route('ds_tong_hop');
//
//    }
}
