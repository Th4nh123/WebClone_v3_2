<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class KeyController extends Controller
{
    public function index()
    {
        $index = 1;
        $key = DB::table("hwp_key_hd")->orderBy('id', 'desc')
            ->paginate(15);;

        return view('admin.key.danh_sach_key', compact('key', 'index'));
    }

    public function themKey($err_key = "", $err_val = "")
    {
        try {
            $list_link = DB::table('hwp_posts_hd')
                ->select("hwp_posts_hd.ID_HD", "hwp_posts_hd.postHD_title")
                ->get()->toArray();
            return view('admin.key.them_key', compact('list_link', 'err_key', 'err_val'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function luuKey(Request $request)
    {
        try {
            $err_key = "";
            $err_val = "";
//            $list_link = array();
//            if (!empty($request->input('selected'))) {
////                dd($request->input('selected'));
//                foreach ($request->input('selected') as $value) {
//                    $post = DB::table('hwp_posts_hd')
//                        ->select('ID_HD', 'postHD_name')
//                        ->where('postHD_title', '=', $value)
//                        ->where('postHD_type', '=', 'hd')
//                        ->where('postHD_status', '=', 'publish')
//                        ->first();
//                    $url_hd = 'https://rdone.net/hd/' . $post->postHD_name;
//                    array_push($list_link, $url_hd);
//                }
//            }
//            $list_link = implode('@@', $list_link);
//            dd($list_link);
//                DB::table("hwp_key_hd")->where('keyHD', '=', $request->keyHD)->delete();

//            $key_hd = DB::table("hwp_key_hd")->where('keyHD', '=', $request->keyHD)->get()->toArray();
//            if (count($key_hd) > 0) {
//                $err_key = "Key đã tồn tại vui lòng chọn key khác";
//                return $this->themKey($err_key,$err_val);
//            }

            $val_hd = DB::table("hwp_key_hd")->where('valueHD', '=', $request->valueHD)->get()->toArray();
            if (count($val_hd) > 0) {
                $err_val = "Value đã tồn tại vui lòng chọn value khác";
                return $this->themKey($err_key, $err_val);
            }
            DB::table("hwp_key_hd")->insert([
                'keyHD' => $request->keyHD,
                'valueHD' => $request->valueHD,
                'list_link' => ""
            ]);
//            $key = DB::table('hwp_key_hd')
//                ->where('keyHD', '=', $request->keyHD)
//                ->orderBy("id", "desc")->first();
//            $posts = DB::table('hwp_posts_hd')
//                ->select('ID_HD', 'postHD_name')
//                ->where('postHD_title', '=', $value)
//                ->where('postHD_type', '=', 'post')
//                ->where('postHD_status', '=', 'publish')
//                ->first();
//
//            DB::table('hwp_posts_hd_has_key_hd')->insert([
//                'id_key_hd' => $key->id,
//                'id_post_hd' => $posts->ID_HD
//            ]);
            return redirect()->route('ds_key');
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function suaKey($id, Request $request)
    {
        try {
            $key_HD = DB::table('hwp_key_hd')->where('id', '=', $id)->first();

            $list_link = array();
            $sub_link = explode('@@', $key_HD->list_link);
            foreach ($sub_link as $item_a) {
                $item_a = substr($item_a, 18);
                array_push($list_link, $item_a);
            }
            if (!empty($key_HD->list_link)) {

                $ds_lienquan = DB::table('hwp_posts_hd')
                    ->select("postHD_title", "ID_HD")
                    ->where('postHD_type', '=', 'hd')
                    ->where('postHD_status', '=', 'publish')
                    ->whereIN("postHD_name", $list_link)
                    ->get()->toArray();
                $ds_post = DB::table('hwp_posts_hd')
                    ->select("postHD_title", "ID_HD")
                    ->where('postHD_type', '=', 'hd')
                    ->where('postHD_status', '=', 'publish')
                    ->whereNotIn("postHD_name", $list_link)
                    ->get()->toArray();
            } else {
                $ds_lienquan = null;
                $ds_post = DB::table('hwp_posts_hd')
                    ->select("postHD_title", "ID_HD")
                    ->where('postHD_type', '=', 'hd')
                    ->where('postHD_status', '=', 'publish')
                    ->get()->toArray();
            }


            return view('admin.key.sua_key', compact('key_HD', 'ds_post', 'ds_lienquan'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function updateKey(Request $request)
    {
//        try {
        $ses = $request->session()->get('tk_user');

        if (isset($ses) && ($request->session()->get('role')[0] == 'admin' ||
                $request->session()->get('role')[0] == 'nv')) {
            $list_link = array();
            if (!empty($request->input('selected'))) {
                foreach ($request->input('selected') as $value) {
                    $post = DB::table('hwp_posts_hd')
                        ->select('postHD_name')
                        ->where('postHD_title', '=', $value)
                        ->where('postHD_type', '=', 'hd')
                        ->where('postHD_status', '=', 'publish')
                        ->first();
                    $url_hd = 'https://rdone.net/hd/' . $post->postHD_name;
                    array_push($list_link, $url_hd);
                }
            }
            $list_link = implode('@@', $list_link);
            DB::table("hwp_key_hd")->where('id', '=', $request->id)
                ->update([
                    'keyHD' => $request->keyHD,
                    'valueHD' => $request->valueHD,
                    'list_link' => $list_link
                ]);

//            $key = DB::table('hwp_key_hd')->where('id', '=', $request->id)->first();
//            DB::table("hwp_posts_hd_has_key_hd")
//                ->where('id_key_hd', '=', $request->id)
//                ->delete();
//            $posts = DB::table('hwp_posts_hd')
//                ->select('ID_HD', 'postHD_name')
//                ->where('postHD_title', '=', $value)
//                ->where('postHD_type', '=', 'post')
//                ->where('postHD_status', '=', 'publish')
//                ->first();
//            DB::table('hwp_posts_hd_has_key_hd')->insert([
//                'id_key_hd' => $key->id,
//                'id_post_hd' => $posts->ID_HD
//            ]);
            return redirect()->route('ds_key');
        } else {
            return redirect('/admin/login');

//            }
//        } catch (\Exception $e) {
//            return abort(404);
        }
    }

    public function xoaKey($id, Request $request)
    {
        $ses = $request->session()->get('tk_user');

        if (isset($ses) && $request->session()->get('role')[0] == 'admin') {
            DB::table('hwp_posts_hd_has_key_hd')->where('id_key_hd', '=', $id)->delete();
            DB::table("hwp_key_hd")->where('id', '=', $id)->delete();

        }
        return redirect()->back();
    }

    public function searchkey(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $index = 1;
                if (isset($_GET['s']) && strlen($_GET['s']) >= 1) {
                    $search_text = $_GET['s'];
                    $key = DB::table('hwp_key_hd')
                        ->where('keyHD', 'like', '%' . $search_text . '%')
                        ->orderBy('id', 'desc')->paginate(15);
                    return view("admin.key.danh_sach_key", compact('key', 'index'));
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
