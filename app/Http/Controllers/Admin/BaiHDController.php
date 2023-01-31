<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use function Psy\debug;

class BaiHDController extends Controller
{
    public function danhsachHD(Request $request)
    {

        $ses = $request->session()->get('tk_user');
        if (isset($ses)) {
            $index = 1;
            $ds_bai_viet = DB::table('hwp_posts_hd')
                ->select('hwp_users.display_name', 'hwp_posts_hd.ID_HD', 'hwp_posts_hd.postHD_date', 'hwp_posts_hd.postHD_content', 'hwp_posts_hd.postHD_title',
                    'hwp_posts_hd.postHD_view', 'hwp_posts_hd.postHD_name', 'hwp_posts_hd.twitter_image',
                    'hwp_key_hd.keyHD', 'hwp_key_hd.valueHD')
                ->join('hwp_users', 'hwp_posts_hd.postHD_author', '=', 'hwp_users.id')
                ->join('hwp_posts_hd_has_key_hd', 'hwp_posts_hd_has_key_hd.id_post_hd', '=', 'hwp_posts_hd.ID_HD')
                ->join('hwp_key_hd', 'hwp_key_hd.id', '=', 'hwp_posts_hd_has_key_hd.id_key_hd')
                ->where('hwp_posts_hd.postHD_type', '=', 'hd')
                ->orderBy('hwp_posts_hd.ID_HD', 'desc')
                ->paginate(15);
            Session::put('tasks_url', $request->fullUrl());
            return view("admin.bai_huong_dan.danh_sach_HD", compact('ds_bai_viet', 'index'));
        } else {
            return redirect('/admin/login');

        }


    }

    public function themHD()
    {
        try {
            $users = DB::table('hwp_users')->select("ID", "user_login")->get()->toArray();
            $categories = DB::table('hwp_term_taxonomy')
                ->join('hwp_terms', 'hwp_terms.term_id', '=', 'hwp_term_taxonomy.term_taxonomy_id')
                ->where('taxonomy', '=', 'category')
                ->where('term_taxonomy_id', '<=', '98')
                ->where('term_taxonomy_id', '>=', '5')
                ->get()
                ->toArray();
            $keys = DB::table('hwp_key_hd')->get()->toArray();
            $posts = DB::table('hwp_posts')
                ->where('post_type', '=', 'post')
                ->where('post_status', '=', 'publish')
                ->where('comment_status', '=', 'open')
                ->get()->toArray();
            return view('admin.bai_huong_dan.them_HD', compact("users", 'categories', 'keys', 'posts'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function luuHD(Request $request)
    {
        try {

            if ($request->has('image_upload')) {
                $file_image = $request->file('image_upload');
                $ext = $request->file('image_upload')->extension();
                $name_image = now()->toDateString() . '-' . time() . '-' . 'postHD_img.' . $ext;
                $img = (new \Intervention\Image\ImageManager)->make($file_image->path())->fit(300)->encode('jpg');
                $path = public_path('images/').$name_image;

                $img->save($path);
            }
            $str = date('y-m');
            $post_view_hd = array();
            if (empty($post_view_hd)) {
                $post_view_hd = array();
            }
            if (empty($post_view_hd["" . $str])) {
                $arr = array($str => "1");
                $post_view_hd = array_merge($post_view_hd, $arr);
            }
            $current_view = (int)$post_view_hd["" . $str];
            $post_view_hd["" . $str] = "" . ($current_view + 1);
            $post_name = $request->post_name . '-' . rand(10, 100);
            $request->tieu_de = preg_replace('/\s+/', " ", $request->tieu_de);
            DB::table('hwp_posts_hd')->insert([
                'postHD_title' => $request->tieu_de,
                'postHD_content' => $request->noi_dung,
                'postHD_author' => $request->tac_gia,
                'postHD_name' => $post_name,
                'postHD_date' => date('y-m-d h:i:s'),
                'postHD_date_gmt' => date('y-m-d h:i:s'),
                'postHD_modified' => date('y-m-d h:i:s'),
                'postHD_modified_gmt' => date('y-m-d h:i:s'),
                'postHD_type' => "hd",
                'postHD_status' => "publish",
                'postHD_view' => json_encode($post_view_hd),
                'keyHD' => "",
                'description' => $request->mo_ta,
                'twitter_image' => URL::to('') . '/images/' . $name_image
            ]);

            $hwp_post_hd = DB::table('hwp_posts_hd')
                ->select('hwp_posts_hd.ID_HD')
                ->where("postHD_name", '=', $post_name)
                ->get()->toArray();
            DB::table("hwp_yoast_indexable")->insert([
                'object_id' => $hwp_post_hd[0]->ID_HD,
                'object_type' => 'hd',
                'object_sub_type' => 'hd',
                'author_id' => $request->tac_gia,
                'description' => $request->mo_ta,
                'breadcrumb_title' => $request->tieu_de,
                'post_status' => 'publish',
                'created_at' => date('y-m-d h:i:s'),
                'updated_at' => date('y-m-d h:i:s'),
                'twitter_image' => URL::to('') . '/images/' . $name_image,
                'primary_focus_keyword' => $request->meta_key,
                'meta_robot' => $request->meta_robot,
                'permalink' => 'https://rdone.net/images/' . $name_image,
                'permalink_hash' => '']);

            DB::table("hwp_term_relationships")->insert([
                'object_id' => $hwp_post_hd[0]->ID_HD,
                'term_taxonomy_id' => $request->the_loai,
                'term_order' => 100
            ]);

            if (!empty($request->input('selected'))) {


                foreach ($request->input('selected') as $value) {
                    $ID_will_insert = DB::table('hwp_posts')->where('post_title', '=', $value)->first();
                    DB::table("hwp_baivietlq")->insert([
                        'ID_BV_Chinh' => $hwp_post_hd[0]->ID_HD,
                        'ID_BV_LQ' => $ID_will_insert->ID
                    ]);
                }

            }
            DB::table("hwp_posts_hd_has_key_hd")->insert([
                'id_post_hd' => $hwp_post_hd[0]->ID_HD,
                'id_key_hd' => $request->key
            ]);
            $ds_post_lienquan = DB::table('hwp_posts_hd_has_key_hd')
                ->where('id_key_hd', '=', $request->key)->get()->toArray();
            $list_link = array();
            foreach ($ds_post_lienquan as $item) {
                $post = DB::table('hwp_posts_hd')->select('postHD_name')
                    ->where('ID_HD', '=', $item->id_post_hd)->get()->toArray();
                $url_hd = $request->getSchemeAndHttpHost() . '/hd/' . $post[0]->postHD_name;
                array_push($list_link, $url_hd);
            }
            $list_link = implode('@@', $list_link);
            DB::table("hwp_key_hd")->where('id', '=', $request->key)
                ->update([
                    'list_link' => $list_link
                ]);

            return redirect()->route('ds_huong_dan');
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function suaHD(Request $request)
    {
        try {

            $post_HD = DB::table('hwp_posts_hd')
                ->select('hwp_posts_hd.ID_HD', 'hwp_posts_hd.postHD_title', 'hwp_posts_hd.postHD_name',
                    'hwp_posts_hd.postHD_author', 'hwp_posts_hd.postHD_date', 'hwp_posts_hd.description',
                    'hwp_posts_hd.postHD_content', 'term_taxonomy_id', 'hwp_yoast_indexable.twitter_image',
                    'hwp_yoast_indexable.primary_focus_keyword')
                ->join("hwp_yoast_indexable", "hwp_yoast_indexable.object_id", "=", "hwp_posts_hd.ID_HD")
                ->join('hwp_term_relationships', "hwp_posts_hd.ID_HD", "=", "hwp_term_relationships.object_id")
                ->where("ID_HD", '=', $request->id)
                ->where("hwp_yoast_indexable.object_type", "=", "hd")
                ->where("hwp_term_relationships.term_order", "=", 100)
                ->where("postHD_type", '=', 'hd')
                ->orderByDesc('hwp_yoast_indexable.id')
                ->get()->toArray();


            $item = $post_HD[0];
            $users = DB::table('hwp_users')->select("ID", "user_login")->get()->toArray();
            $categories = DB::table('hwp_term_taxonomy')->select("term_taxonomy_id", 'name')
                ->join('hwp_terms', 'hwp_terms.term_id', '=', 'hwp_term_taxonomy.term_taxonomy_id')
                ->where('taxonomy', '=', 'category')
                ->where('term_taxonomy_id', '<=', '98')
                ->where('term_taxonomy_id', '>=', '5')
                ->get()
                ->toArray();

            $keys = DB::table('hwp_key_hd')->get()->toArray();
            $key_selected = DB::table('hwp_posts_hd_has_key_hd')->where('id_post_hd', '=', $item->ID_HD)->first();

            $ds_lienquan = DB::table('hwp_baivietlq')
                ->select("hwp_baivietlq.ID_BV_Chinh", 'hwp_baivietlq.ID_BV_LQ', "hwp_posts.post_title", "hwp_posts.ID")
                ->join('hwp_posts', 'hwp_baivietlq.ID_BV_LQ', 'hwp_posts.ID')
                ->where('post_type', '=', 'post')
                ->where('post_status', '=', 'publish')
                ->where('ID_BV_Chinh', '=', $request->id)
                ->get()->toArray();
            $list_id_ds_lienquan = array();
            foreach ($ds_lienquan as $item_a) {
                array_push($list_id_ds_lienquan, $item_a->ID);
            }
//            dd($ds_lienquan);
            $ds_post = DB::table('hwp_posts')
                ->select("hwp_posts.post_title", "hwp_posts.ID")
                ->where('post_type', '=', 'post')
                ->where('post_status', '=', 'publish')
                ->whereNotIn("hwp_posts.ID", $list_id_ds_lienquan)
                ->get()->toArray();
            return view('admin.bai_huong_dan.sua_HD', compact('item', 'users', 'categories', 'keys', 'key_selected', 'ds_lienquan', 'ds_post'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function updateHD(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
//dd($request->id);
            if (isset($ses) && ($request->session()->get('role')[0] == 'admin' || $request->session()->get('role')[0] == 'nv')) {
                $request->tieu_de = preg_replace('/\s+/', " ", $request->tieu_de);
                if ($request->image_upload != null) {
                    $file_image = $request->file('image_upload');
                    $ext = $request->file('image_upload')->extension();
                    $name_image = now()->toDateString() . '-' . time() . '-' . 'edit_postHD_img.' . $ext;
                    $img = (new \Intervention\Image\ImageManager)->make($file_image->path())->fit(300)->encode('jpg');
                    $path = public_path('images/').$name_image;

                    $img->save($path);

                    DB::table('hwp_posts_hd')->where('ID_HD', '=', $request->id)
                        ->update([
                            'postHD_title' => $request->tieu_de,
                            'postHD_content' => $request->noi_dung,
                            'postHD_author' => $request->tac_gia,
                            'postHD_name' => $request->post_name,
                            'postHD_date' => date('y-m-d h:i:s'),
                            'postHD_date_gmt' => date('y-m-d h:i:s'),
                            'postHD_modified' => date('y-m-d h:i:s'),
                            'postHD_modified_gmt' => date('y-m-d h:i:s'),
                            'description' => $request->mo_ta,
                            'twitter_image' => URL::to('') . '/images/' . $name_image,
                        ]);

                    DB::table("hwp_yoast_indexable")->where('object_id', '=', $request->id)
                        ->update([
                            'object_type' => 'hd',
                            'object_sub_type' => 'hd',
                            'author_id' => $request->tac_gia,
                            'description' => $request->mo_ta,
                            'breadcrumb_title' => $request->tieu_de,
                            'post_status' => 'publish',
                            'created_at' => $request->date,
                            'updated_at' => date('y-m-d h:i:s'),
                            'primary_focus_keyword' => $request->meta_key,
                            'meta_robot' => $request->meta_robot,
                            'twitter_image' => URL::to('') . '/images/' . $name_image,
                            'permalink' => 'https://rdone.net/images/' . $name_image,
                        ]);
                }
                DB::table('hwp_posts_hd')->where('ID_HD', '=', $request->id)
                    ->update([
                        'postHD_title' => $request->tieu_de,
                        'postHD_content' => $request->noi_dung,
                        'postHD_author' => $request->tac_gia,
                        'postHD_name' => $request->post_name,
                        'postHD_date' => date('y-m-d h:i:s'),
                        'postHD_date_gmt' => date('y-m-d h:i:s'),
                        'postHD_modified' => date('y-m-d h:i:s'),
                        'postHD_modified_gmt' => date('y-m-d h:i:s'),
                        'description' => $request->mo_ta,
                    ]);
                DB::table("hwp_yoast_indexable")->where('object_id', '=', $request->id)
                    ->update([
                        'object_type' => 'hd',
                        'object_sub_type' => 'hd',
                        'author_id' => $request->tac_gia,
                        'description' => $request->mo_ta,
                        'breadcrumb_title' => $request->tieu_de,
                        'post_status' => 'publish',
                        'created_at' => $request->date,
                        'updated_at' => date('y-m-d h:i:s'),
                        'primary_focus_keyword' => $request->meta_key,
                        'meta_robot' => $request->meta_robot,
                    ]);
//      dd($request->the_loai);
                DB::table("hwp_term_relationships")->where("object_id", "=", $request->id)->delete();
                DB::table("hwp_term_relationships")->insert([
                    'object_id' => $request->id,
                    'term_taxonomy_id' => $request->the_loai,
                    'term_order' => 100
                ]);

                if (!empty($request->input('selected'))) {
                    DB::table("hwp_baivietlq")->where('ID_BV_Chinh', '=', $request->id)->delete();

                    foreach ($request->input('selected') as $value) {
                        $ID_will_insert = DB::table('hwp_posts')
                            ->where('post_status', '=', 'publish')
                            ->where('post_type', '=', 'post')
                            ->where('post_title', '=', $value)->get()->toArray();
                        try {
                            DB::table("hwp_baivietlq")->insert([
                                'ID_BV_Chinh' => $request->id,
                                'ID_BV_LQ' => $ID_will_insert[0]->ID
                            ]);
                        } catch (\Exception $i) {
                            DB::table("hwp_baivietlq")->insert([
                                'ID_BV_Chinh' => $request->id,
                                'ID_BV_LQ' => $ID_will_insert[1]->ID
                            ]);
                        }

                    }
                } else {
                    DB::table("hwp_baivietlq")->where('ID_BV_Chinh', '=', $request->id)->delete();
                }
                DB::table("hwp_posts_hd_has_key_hd")->where('id_post_hd', '=', $request->id)->update([
                    'id_key_hd' => $request->key
                ]);

                $sl_key_co_post = DB::table('hwp_key_hd')
                    ->select('id')->groupBy('id')->get()->toArray();
                foreach ($sl_key_co_post as $item0) {
                    $ds_post_lienquan = DB::table('hwp_posts_hd_has_key_hd')
                        ->where('id_key_hd', '=', $item0->id)->get()->toArray();
                    if (empty($ds_post_lienquan)) {
                        DB::table("hwp_key_hd")->where('id', '=', $item0->id)
                            ->update([
                                'list_link' => ''
                            ]);
                    } else {
                        $list_link = array();
                        foreach ($ds_post_lienquan as $item1) {
                            $post = DB::table('hwp_posts_hd')->select('postHD_name')
                                ->where('ID_HD', '=', $item1->id_post_hd)->get()->toArray();
                            $url_hd = $request->getSchemeAndHttpHost() . '/hd/' . $post[0]->postHD_name;
                            array_push($list_link, $url_hd);
                        }
                        $list_link = implode('@@', $list_link);
                        DB::table("hwp_key_hd")->where('id', '=', $item0->id)
                            ->update([
                                'list_link' => $list_link
                            ]);
                    }
                }
                if (session("tasks_url")) {
                    return redirect(session("tasks_url"));
                }

                return redirect()->route('ds_huong_dan');
            } else {
                return redirect('/admin/login');

            }
        } catch (\Exception $e) {
            return redirect(session("tasks_url"));
        }
    }

    public function xoaHD($id, Request $request)
    {
        $ses = $request->session()->get('tk_user');
        if (isset($ses) && $request->session()->get('role')[0] == 'admin') {
            DB::table('hwp_posts_hd_has_key_hd')->where('id_post_hd', '=', $id)->delete();
            DB::table('hwp_posts_hd')->where('ID_HD', '=', $id)->delete();
            DB::table('hwp_baivietlq')->where('ID_BV_Chinh', '=', $id)->delete();
            DB::table("hwp_yoast_indexable")->where('object_id', '=', $id)
                ->where('object_type', '=', 'hd')->delete();

            DB::table("hwp_term_relationships")
                ->where("object_id", "=", $id)
                ->where("term_order", "=", 100)->delete();
            $sl_key_co_post = DB::table('hwp_key_hd')
                ->select('id')->groupBy('id')->get()->toArray();
            foreach ($sl_key_co_post as $item0) {
                $ds_post_lienquan = DB::table('hwp_posts_hd_has_key_hd')
                    ->where('id_key_hd', '=', $item0->id)->get()->toArray();
                if (empty($ds_post_lienquan)) {
                    DB::table("hwp_key_hd")->where('id', '=', $item0->id)
                        ->update([
                            'list_link' => ''
                        ]);
                } else {
                    $list_link = array();
                    foreach ($ds_post_lienquan as $item1) {
                        $post = DB::table('hwp_posts_hd')->select('postHD_name')
                            ->where('ID_HD', '=', $item1->id_post_hd)->get()->toArray();
                        $url_hd = $request->getSchemeAndHttpHost() . '/hd/' . $post[0]->postHD_name;
                        array_push($list_link, $url_hd);
                    }
                    $list_link = implode('@@', $list_link);
                    DB::table("hwp_key_hd")->where('id', '=', $item0->id)
                        ->update([
                            'list_link' => $list_link
                        ]);
                }

            }
        }
        return redirect()->back();
    }

    public function searchBaiVietHD(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $index = 1;
                if (isset($_GET['s']) && strlen($_GET['s']) >= 1) {
                    $search_text = $_GET['s'];
                    $ds_bai_viet = DB::table('hwp_posts_hd')
                        ->select('hwp_users.display_name', 'hwp_posts_hd.ID_HD', 'hwp_posts_hd.postHD_date',
                            'hwp_posts_hd.postHD_content', 'hwp_posts_hd.postHD_title',
                            'hwp_posts_hd.postHD_view', 'hwp_posts_hd.postHD_name', 'hwp_posts_hd.twitter_image')
                        ->join('hwp_users', 'hwp_posts_hd.postHD_author', '=', 'hwp_users.id')
                        ->where('hwp_posts_hd.postHD_title', 'like', '%' . $search_text . '%')
                        ->where('hwp_posts_hd.postHD_type', '=', 'hd')
                        ->orderBy('hwp_posts_hd.ID_HD', 'desc')
                        ->paginate(15);
                    Session::put('tasks_url', $request->fullUrl());
                    return view("admin.bai_huong_dan.danh_sach_HD", compact('search_text', 'ds_bai_viet', 'index'));
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function thongKeView(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $index = 1;
                $ds_bai_viet = DB::table('hwp_posts_hd')
                    ->select('hwp_users.display_name', 'hwp_posts_hd.ID_HD', 'hwp_posts_hd.postHD_date', 'hwp_posts_hd.postHD_content', 'hwp_posts_hd.postHD_title',
                        'hwp_posts_hd.twitter_image', 'hwp_posts_hd.postHD_name', 'hwp_posts_hd.postHD_view',
                        'hwp_posts_hd.description')
                    ->join('hwp_users', 'hwp_posts_hd.postHD_author', '=', 'hwp_users.id')
                    ->where('hwp_posts_hd.postHD_type', '=', 'hd')
                    ->orderBy('hwp_posts_hd.ID_HD', 'desc')
                    ->get()->toArray();

                $r_start = $request->start;
                $r_end = $request->end;
                $str_start = date('y-m', strtotime(str_replace('/', '-', $request->start)));
                $str_end = date('y-m', strtotime(str_replace('/', '-', $request->end)));
//            echo ($str_start );

//            $str = date('y-m');
                usort($ds_bai_viet, function ($first, $second) use ($str_start, $str_end, $r_start, $r_end) {
                    $v_f = json_decode($first->postHD_view, true);
                    $v_s = json_decode($second->postHD_view, true);
                    $post_f = 0;
                    $post_s = 0;
                    if ($str_start == $str_end) {
                        // trong cùng tháng
                        if (!is_array($v_f)) {
                            $v_f = array($str_start => 0);
                        }
                        if (!is_array($v_s)) {
                            $v_s = array($str_start => 0);
                        }
                        if (empty($v_f[$str_end]) || (empty($v_f[$str_end]) && empty($v_f[$str_start])) || empty($v_f[$str_start])) {
                            $post_f = 0;
                        } else {
                            $post_f = (int)$v_f[$str_start] + (int)$v_f[$str_end];
                        }
                        if (empty($v_s[$str_start]) || empty($v_s[$str_end]) || (empty($v_s[$str_end]) && empty($v_s[$str_start]))) {
                            $post_s = 0;
                        } else {
                            $post_s = (int)$v_s[$str_start] + (int)$v_s[$str_end];
                        }
//                    var_dump($v_f); ;
//                    echo $post_f;
//                    echo "<br/>";
//                    var_dump($v_s);
//                    echo $post_s;
//                    echo $v_s[$str_start];
//                    dd("");
                    } else {
                        // khác tháng
                        $m_start = date('m', strtotime(str_replace('/', '-', $r_start)));
                        $m_end = date('m', strtotime(str_replace('/', '-', $r_end)));
                        for ($i = 0; $i <= (int)$m_end - (int)$m_start; $i++) {
                            $tg = date('y-m', strtotime("+" . $i . " months", strtotime(str_replace('/', '-', $r_start))));
                            if (!empty($v_f[$tg])) {
                                $post_f += (int)$v_f[$tg];
                            }
                            if (!empty($v_s[$tg])) {
                                $post_s += (int)$v_s[$tg];
                            }
                        }
                    }

                    return $post_f < $post_s;
                });
//            dd($ds_bai_viet);

                return view("admin.bai_huong_dan.thong_ke_view_HD", compact('ds_bai_viet', 'index', 'str_start', 'str_end', 'r_start', 'r_end'));
            } else {
                return redirect('/admin/login');

            }

        } catch (\Exception $e) {
            return abort(404);
        }
    }
}



