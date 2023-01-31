<?php

namespace App\Http\Controllers;


use App\Exports\PostExport;
use App\Models\HwpPost;
use App\Models\Post;
use Buchin\GoogleImageGrabber\GoogleImageGrabber;
use DevDojo\GoogleImageSearch\ImageSearch;
use Google\Cloud\Translate\V2\TranslateClient;
use GoogleSearchResults;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Excel;
use File;

use Illuminate\Support\Facades;
use phpDocumentor\Reflection\DocBlock\Tag;
//require_once ('vendor/autoload.php');
use Statickidz\GoogleTranslate;
use ZipArchive;
use Zipper;


class PostController extends Controller
{

    public function show($post_name = null)
    {
// nếu nó null thì em nhảy về trang home
//dd($post_name);
        try {
            if (!empty($post_name)) {
                switch ($post_name) {
                    case "search":
                        return $this->search();
                    case "tag":
                        return TagController::index($post_name);
                    case "category":
                        return TagController::index($post_name);
                    case "hd":
                        return PostController::post_huong_dan($post_name);
                    case "top_list":
                        return TopListController::index($post_name);
                    default:
                        return $this->postDetail($post_name);
                }
            } else {
                return $this->home();
            }
        } catch (\Exception $e) {
            return abort(404);
        }


    }


    public function postDetail($post_name)
    {

//        DB::table('hwp_posts')->where('post_name', '=', $post_name)->increment('post_view');
//        try {
//        ImageSearch::config()->apiKey('AIzaSyA5DPUO7i3pf-bO1w6EsGMX9NIuZ19L2lg');
//        ImageSearch::config()->cx('622357283d8f7426e');
//        $img = ImageSearch::search('cơ khí xây dựng là gì');
//        dd($img);
//        $tr_vi = new \Stichoza\GoogleTranslate\GoogleTranslate('jp', 'vi');
//        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
//        dd( $tr->setSource('en')->setTarget('ja')->translate('Goodbye'));
        $post_detail = DB::table('hwp_posts')
            ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view',
                'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot','hwp_posts.id_key')
            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->where('post_name', '=', $post_name)
            ->get()->toArray();

        $str = date('y-m');
        $post_view = json_decode($post_detail[0]->post_view, true);
        if (empty($post_view) || !is_array($post_view)) {
            $post_view = array();
        }
        if (empty($post_view["" . $str])) {
            $arr = array($str => "1");
            $post_view = array_merge($post_view, $arr);
        }
        $current_view = (int)$post_view["" . $str];
        $post_view["" . $str] = "" . ($current_view + 1);
        $post_detail[0]->postHD_view = $post_view;
//        dd($post_view);
        DB::table('hwp_posts')
            ->where('post_name', '=', $post_name)
            ->where("post_type", "=", "post")
            ->update(["hwp_posts.post_view" => json_encode($post_view)]);


//        $post_detail_hd = cache()->remember('PostController-hwp_posts_a' . $post_name, 120, function () use ($post_name) {
//            return DB::table('hwp_posts_hd')
//                ->where('postHD_name', '=', $post_name)
//                ->get()->toArray();
//        });
            $post_detail_hd = DB::table('hwp_posts_hd')
                ->where('postHD_name', '=', $post_name)
                ->get()->toArray();
            if ($post_detail[0]->id_key !=0){
                return TopListController::post_detail($post_name);
            }
            if (count($post_detail) <= 0) {
                if (count($post_detail_hd) <= 0) {
                    return abort(404);
                } else {
                    return PostController::post_huong_dan($post_name);
                }
            }


        $post_detail[0]->post_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $post_detail[0]->post_content);

        $id_tham_khao = DB::table('hwp_posts')
            ->select('hwp_baivietlq.ID_BV_LQ')
            ->join('hwp_baivietlq', 'hwp_baivietlq.ID_BV_Chinh', '=', 'hwp_posts.id')
            ->where('post_name', '=', $post_name)
            ->get()->toArray();

        $list_bv_tham_khao = array();

        foreach ($id_tham_khao as $value) {
            $post_detail0 =
                DB::table('hwp_posts')
                    ->select('hwp_yoast_indexable.id', 'hwp_yoast_indexable.object_id', 'hwp_posts.post_title', 'hwp_posts.post_name', 'hwp_yoast_indexable.twitter_image','hwp_yoast_indexable.description')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->where('hwp_posts.id', '=', $value->ID_BV_LQ)
                    ->where('post_type', '=', 'post')
                    ->where('object_type', '=', 'post')
                    ->whereNotNull('hwp_yoast_indexable.twitter_image')
                    ->get()->toArray();
            $list_bv_tham_khao = array_merge($list_bv_tham_khao, $post_detail0);
        }


        $hwp_term_relationships = cache()->remember('PostController-post_detail_id_' . $post_detail[0]->ID . $post_name, 120, function () use ($post_detail) {
            return DB::table('hwp_term_relationships')
                ->select('term_taxonomy_id')
                ->where('object_id', '=', $post_detail[0]->ID)->get()->toArray();
        });

            $hwp_term_taxonomy = cache()->remember('PostController-hwp_term_relationships_' . $hwp_term_relationships[0]->term_taxonomy_id . $post_name, 120, function () use ($hwp_term_relationships) {
                return DB::table('hwp_term_taxonomy')
                    ->select('term_id')
                    ->where('term_taxonomy_id', '=', $hwp_term_relationships[0]->term_taxonomy_id)->get()->toArray();
            });


            $hwp_terms = cache()->remember('PostController-hwp_term_taxonomy_' . $hwp_term_taxonomy[0]->term_id, 120, function () use ($hwp_term_taxonomy) {
                return DB::table('hwp_terms')
                    ->where('term_id', '=', $hwp_term_taxonomy[0]->term_id)->get()->toArray();
            });

        // Chủ đề nổi bật
        $select_list_chu_de_noi_bat = cache()->remember('PostController-hwp_hw_trending' . $post_name, 120, function () {
            return DB::table('hwp_hw_trending')
                ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
                ->where('post_type', '=', 'post')
                ->groupBy('post_id')
                ->orderBy('count', 'desc')
                ->limit(16)
                ->get()->toArray();
        });

        $list_chu_de_noi_bat = array();
        foreach ($select_list_chu_de_noi_bat as $value) {
            $post_detail2 =
                DB::table('hwp_posts')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_yoast_indexable.description')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->where('hwp_posts.id', '=', $value->post_id)
                    ->get()->toArray();
            $list_chu_de_noi_bat = array_merge($list_chu_de_noi_bat, $post_detail2);
        }
        $list_tag = BaseController::getListTag();
        $banner_ngang = DB::table('hwp_banner')->select('link')
            ->where('vi_tri', '=', 'banner-ngang')
            ->orderBy('id', 'desc')
            ->first();
        if (!empty($banner_ngang)) {
            $banner_ngang = substr($banner_ngang->link, 25);
        }
        $banner_doc = DB::table('hwp_banner')->select('link')->where('vi_tri', '=', 'banner-doc')
            ->orderBy('id', 'desc')
            ->first();
        if (!empty($banner_doc)) {
            $banner_doc = substr($banner_doc->link, 25);
        }

        return view('postPage.post', compact('list_tag', 'post_detail', 'hwp_terms', 'list_chu_de_noi_bat', 'list_bv_tham_khao', 'banner_doc', 'banner_ngang'));
//        } catch (\Exception $e) {
//            return abort(404);
//        }
    }

    public function post_huong_dan($post_name)
    {
        try {

            $post_detail_hd = DB::table('hwp_posts_hd')
                ->select('hwp_posts_hd.postHD_author', 'hwp_posts_hd.ID_HD', 'hwp_posts_hd.postHD_date', 'hwp_posts_hd.postHD_content', 'hwp_posts_hd.postHD_title', 'hwp_posts_hd.postHD_view',
                    'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                    , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
                ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts_hd.ID_HD')
                ->where('postHD_name', '=', $post_name)
                ->get()->toArray();
            $post_detail_hd[0]->postHD_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $post_detail_hd[0]->postHD_content);

            $str = date('y-m');
            $post_view_hd = json_decode($post_detail_hd[0]->postHD_view, true);
            if (empty($post_view_hd)) {
                $post_view_hd = array();
            }
            if (empty($post_view_hd["" . $str])) {
                $arr = array($str => "1");
                $post_view_hd = array_merge($post_view_hd, $arr);
            }
            $current_view = (int)$post_view_hd["" . $str];
            $post_view_hd["" . $str] = "" . ($current_view + 1);
            $post_detail_hd[0]->postHD_view = $post_view_hd;
            DB::table('hwp_posts_hd')
                ->where('postHD_name', '=', $post_name)
                ->update(["postHD_view" => json_encode($post_view_hd)]);

            $id_tham_khao = DB::table('hwp_posts_hd')
                ->select('hwp_baivietlq.ID_BV_LQ')
                ->join('hwp_baivietlq', 'hwp_baivietlq.ID_BV_Chinh', '=', 'hwp_posts_hd.ID_HD')
                ->where('postHD_name', '=', $post_name)
                ->get()->toArray();
            $list_bv_tham_khao = array();

            foreach ($id_tham_khao as $value) {
                $post_detail0 =
                    DB::table('hwp_posts')
                        ->select('hwp_yoast_indexable.id', 'hwp_yoast_indexable.object_id', 'hwp_posts.post_title', 'hwp_posts.post_name', 'hwp_yoast_indexable.twitter_image','hwp_yoast_indexable.description')
                        ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                        ->where('hwp_posts.id', '=', $value->ID_BV_LQ)
                        ->where('post_type', '=', 'post')
                        ->where('object_type', '=', 'post')
                        ->whereNotNull('hwp_yoast_indexable.twitter_image')
                        ->get()->toArray();
                $list_bv_tham_khao = array_merge($list_bv_tham_khao, $post_detail0);
            }


            $hwp_term_relationships = DB::table('hwp_term_relationships')
                ->select('term_taxonomy_id')
                ->where('object_id', '=', $post_detail_hd[0]->ID_HD)->get()->toArray();

            $hwp_term_taxonomy = DB::table('hwp_term_taxonomy')
                ->select('term_id')
                ->where('term_taxonomy_id', '=', $hwp_term_relationships[0]->term_taxonomy_id)->get()->toArray();


            $hwp_terms = DB::table('hwp_terms')
                ->where('term_id', '=', $hwp_term_taxonomy[0]->term_id)->get()->toArray();


            // Chủ đề nổi bật
            $select_list_chu_de_noi_bat = cache()->remember('PostController-hwp_hw_trending' . $post_name, 120, function () {
                return DB::table('hwp_hw_trending')
                    ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
                    ->where('post_type', '=', 'post')
                    ->groupBy('post_id')
                    ->orderBy('count', 'desc')
                    ->limit(16)
                    ->get()->toArray();
            });

            $list_chu_de_noi_bat = array();
            foreach ($select_list_chu_de_noi_bat as $value) {
                $post_detail2 =
                    DB::table('hwp_posts')
                        ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_yoast_indexable.description')
                        ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                        ->where('hwp_posts.id', '=', $value->post_id)
                        ->get()->toArray();
                $list_chu_de_noi_bat = array_merge($list_chu_de_noi_bat, $post_detail2);
            }
            $list_tag = BaseController::getListTag();
            $banner_ngang = DB::table('hwp_banner')->select('link')
                ->where('vi_tri', '=', 'banner-ngang')
                ->orderBy('id', 'desc')
                ->first();
            if (!empty($banner_ngang)) {
                $banner_ngang = substr($banner_ngang->link, 25);
            }
            $banner_doc = DB::table('hwp_banner')->select('link')->where('vi_tri', '=', 'banner-doc')
                ->orderBy('id', 'desc')
                ->first();
            if (!empty($banner_doc)) {
                $banner_doc = substr($banner_doc->link, 25);
            }


//        dd($chu_de_noi_bat);
//        dd($list_post_lien_quan);
            return view('postPage.post_huong_dan', compact('list_tag', 'post_detail_hd', 'hwp_terms', 'list_chu_de_noi_bat', 'list_bv_tham_khao', 'banner_doc', 'banner_ngang'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function home()
    {
        try {
            //Category: Phần mềm xây dựng
            $term_relationship_7 = cache()->remember('HomeController-term_relationship_7', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '7')
                    ->orWhere('term_taxonomy_id', '=', '9')
                    ->orWhere('term_taxonomy_id', '=', '10')
                    ->orWhere('term_taxonomy_id', '=', '11')
                    ->orWhere('term_taxonomy_id', '=', '14')
                    ->orderBy('hwp_term_relationships.object_id', 'desc')
                    ->distinct('hwp_term_relationships.object_id')->limit(5)->get()->toArray();
            });

            //Category: Tài liệu thi công
            $term_relationship_5 = cache()->remember('HomeController-term_relationship_5', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '5')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });

            //Category: Tài liệu thiết kế
            $term_relationship_6 = cache()->remember('HomeController-term_relationship_6', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '6')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });

            //Category: Tiêu chuẩn
            $term_relationship_17 = cache()->remember('HomeController-term_relationship_17', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '17')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Catgory : Thư viện
            $term_relationship_8 = cache()->remember('HomeController-term_relationship_8', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '8')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Category: Bảng tính excel
            $term_relationship_21 = cache()->remember('HomeController-term_relationship_21', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '21')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();

            });

            //Category: Dự toán
            $term_relationship_70 = cache()->remember('HomeController-term_relationship_70', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '70')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Category: Thư viện Sketchup
            $term_relationship_86 = cache()->remember('HomeController-term_relationship_86', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '86')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Category: Thư viện Revit
            $term_relationship_85 = cache()->remember('HomeController-term_relationship_85', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '85')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Category: Thư viện 3DsMax
            $term_relationship_87 = cache()->remember('HomeController-term_relationship_87', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '87')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(5)->get()->toArray();
            });


            //Category: Đồ án xây dựng
            $term_relationship_47 = cache()->remember('HomeController-term_relationship_47', 120, function () {
                return Db::table('hwp_term_relationships')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_term_relationships.object_id')
                    ->join('hwp_posts', 'hwp_term_relationships.object_id', '=', 'hwp_posts.ID')
                    ->join('hwp_yoast_indexable', 'hwp_term_relationships.object_id', '=', 'hwp_yoast_indexable.object_id')
                    ->where('term_taxonomy_id', '=', '47')->orderBy('hwp_term_relationships.object_id', 'desc')->limit(8)->get()->toArray();
            });

            $list_tag = BaseController::getListTag();


            // Chủ đề nổi bật
            $select_list_chu_de_noi_bat = cache()->remember('HomeController-hwp_hw_trending', 120, function () {
                return DB::table('hwp_hw_trending')
                    ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
                    ->where('post_type', '=', 'post')
                    ->groupBy('post_id')
                    ->orderBy('count', 'desc')
                    ->limit(16)
                    ->get()->toArray();
            });


            $list_chu_de_noi_bat = array();
            foreach ($select_list_chu_de_noi_bat as $value) {
                $post_detail2 = DB::table('hwp_posts')
                    ->select('hwp_posts.post_name', 'hwp_posts.post_title',
                        'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_yoast_indexable.description')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->where('hwp_posts.id', '=', $value->post_id)->get()->toArray();
                $list_chu_de_noi_bat = array_merge($list_chu_de_noi_bat, $post_detail2);
            }


            $banner_ngang = DB::table('hwp_banner')->select('link')
                ->where('vi_tri', '=', 'banner-ngang')
                ->orderBy('id', 'desc')
                ->first();
            if (!empty($banner_ngang)) {
                $banner_ngang = substr($banner_ngang->link, 25);
            }
            $banner_doc = DB::table('hwp_banner')->select('link')->where('vi_tri', '=', 'banner-doc')
                ->orderBy('id', 'desc')
                ->first();
            if (!empty($banner_doc)) {
                $banner_doc = substr($banner_doc->link, 25);
            }


            return view('homePage.home', compact('term_relationship_7',
                'term_relationship_5',
                'term_relationship_6',
                'term_relationship_17',
                'term_relationship_8',
                'term_relationship_21',
                'term_relationship_70',
                'term_relationship_86',
                'term_relationship_85',
                'term_relationship_87',
                'term_relationship_47',
                'list_tag',
                'list_chu_de_noi_bat',
                'banner_doc', 'banner_ngang'
            ));
        } catch (\Exception $e) {
            return abort(404);
        }
        // phần trang chủ đây anh ạ
        // nó đang không chạy vào chỗ này anh ạ // anh xem cái route của em đã anh
    }

    public function search()
    {
        try {
            //Chủ đề nổi bật
        $select_list_chu_de_noi_bat = DB::table('hwp_hw_trending')
            ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
            ->where('post_type', '=', 'post')
            ->groupBy('post_id')
            ->orderBy('count', 'desc')
            ->limit(16)
            ->get()->toArray();
        $list_chu_de_noi_bat = array();
        foreach ($select_list_chu_de_noi_bat as $value) {
            $post_detail2 = DB::table('hwp_posts')
                ->select('hwp_posts.post_name', 'hwp_posts.post_title', 'hwp_yoast_indexable.twitter_image', 'hwp_posts.post_content', 'hwp_yoast_indexable.description')
                ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                ->where('hwp_posts.id', '=', $value->post_id)->get()->toArray();
            $list_chu_de_noi_bat = array_merge($list_chu_de_noi_bat, $post_detail2);
        }

        //Search
        $s = request()->s;
        $item_search = DB::table('hwp_posts')
            ->join('hwp_yoast_indexable', 'hwp_posts.ID', '=', 'hwp_yoast_indexable.object_id')
            ->where('hwp_posts.post_title', 'like', '%' . $s . '%')
            ->where('hwp_posts.post_type', '=', 'post')
            ->orderBy('hwp_posts.ID', 'desc')->paginate(10);

        $list_tag = BaseController::getListTag();
        $banner_ngang = DB::table('hwp_banner')->select('link')
            ->where('vi_tri', '=', 'banner-ngang')
            ->orderBy('id', 'desc')
            ->first();
        if (!empty($banner_ngang)) {
            $banner_ngang = substr($banner_ngang->link, 25);
        }
        $banner_doc = DB::table('hwp_banner')->select('link')->where('vi_tri', '=', 'banner-doc')
            ->orderBy('id', 'desc')
            ->first();
        if (!empty($banner_doc)) {
            $banner_doc = substr($banner_doc->link, 25);
        }
        if (count($item_search) <= 0) {
            $item_search = null;
            return view('postPage.search', compact('list_tag', 'list_chu_de_noi_bat', 's', 'banner_doc', 'banner_ngang'));
        }

        return view('postPage.search', compact('list_tag', 'list_chu_de_noi_bat', 'item_search', 's', 'banner_doc', 'banner_ngang'));
        } catch (\Exception $e) {
            return abort(404); //Đoạn này mình chèn thêm / vào để nó trả về 404
        }
    }

    public function randomkey(Request $request)
    {

        try {
            $list_key = DB::table('hwp_key_hd')
                ->where('list_link', '!=', '')
                ->get()->toArray();
            $min = pow($request->val - $list_key[0]->valueHD, 2);
            $i = 0;
            $current_val = 0;
            $current_key = "";
            foreach ($list_key as $item) {
                $h = pow($request->val - $item->valueHD, 2);
//                echo $min;
//                dd($h);
                if ($h <= $min) {
                    $min = $h;
                    $current_val = $item->valueHD;
                    $current_key = $item->keyHD;
                }
            }
//            dd($current_val);
            $list_key = DB::table('hwp_key_hd')
                ->where('keyHD', '=', $current_key)
                ->where('valueHD', '=', $current_val)
                ->get()->toArray();
//            dd($list_key);
            $list_url = explode("@@", $list_key[0]->list_link);
            $index = rand(0, count($list_url) - 1);
            return redirect($list_url[$index]);
        } catch (\Exception $e) {
            return abort(404); //Đoạn này mình chèn thêm / vào để nó trả về 404
        }

    }

    public function random_bv(Request $request)
    {
//        $list_key = DB::table('hwp_posts_hd_has_key_hd')->where('id_post_hd', '=', $request->id)
//            ->get()->toArray();
//        $rs = array();
//        foreach ($list_key as $item) {
//            $key = DB::table('hwp_key_hd')
//                ->where('hwp_key_hd.id', '=', $item->id_key_hd)
//                ->get()->toArray();
//            array_push($rs, $key[0]);
//        }
//        $check = false;
//        dd($rs);
//        $list_url = explode("@@", $key[0]->list_link);
        $list_key = DB::table('hwp_key_hd')
            ->where('list_link', '!=', '')
            ->orderBy("valueHD", 'desc')
            ->get()->toArray();

        $ran = rand(0,count($list_key)-1);
        $random_val = rand(1000, (int)$list_key[$ran]->valueHD * 1);
        return redirect('/random-pass/?val=' . $random_val);
//        $index = rand(0, count($list_url)-1);
//        return view('random_key.random_key', compact('rs', 'check'));
    }


//    public function export(Request $request)
//    {
//        return Excel::download(new PostExport, 'post.xlsx');
//    }

    public function export()
    {

        $list_bai_viet = [
            'toplist-19-chieu-sau-so-voi-chi-gioi-xay-dung-la-gi-toplist-19-35-vi-cb',
            'co-nen-dat-phong-tho-ben-tren-nha-ve-sinh-hay-khong-3766664-vi-cb',
            '10-mau-nha-ngang-7m-1-tang-dep-hien-dai-gia-re-2567535-vi-cb',
            'mau-thiet-ke-biet-thu-3-tang-nong-thon-dep-nhat-lon-nho-2022-5368127-vi-cb',
            'mau-thiet-ke-nha-1-tang-8x12m-2-phong-ngu-mai-thai-o-ninh-binh-1367719-vi-cb',
            'kham-pha-biet-thu-tan-co-dien-3-tang-10x20-nhin-la-muon-xay-5767317-vi-cb',
            'biet-thu-3-tang-tan-co-dien-90m2-quot-song-la-phai-chat-quot-cong-ty-co-phan-kien-truc-va-dau-tu-xay-dung-ang-9767787-vi-cb',
            'tu-vi-1974-tuoi-giap-dan-lam-nha-nam-2022-duoc-khong-10065692-vi-cb',
            'thiet-ke-biet-thu-nha-pho-dep-thi-cong-xay-dung-cong-trinh-cao-cap-4965035-vi-cb',
            'be-nuoc-ngam-nen-dat-o-dau-hop-phong-thuy-dem-lai-tai-loc-3966443-vi-cb',
            'co-nen-xay-be-nuoc-ngam-tin-tuc-su-kien-8666440-vi-cb',
            'gap-4-truong-hop-sau-gia-chu-khong-can-thac-mac-nen-sua-nha-hay-xay-moi-7764997-vi-cb',
            'cach-tinh-so-phong-trong-nha-phan-chia-theo-phong-thuy-2022-6366247-vi-cb',
            'ban-ve-thiet-ke-biet-thu-4-tang-2-mat-tien-tan-co-dien-dep-2768627-vi-cb',
            'kts-tu-van-thiet-ke-san-vuon-sau-nha-tao-khong-gian-doc-dao-9366742-vi-cb',
            'xay-nha-tuong-10-co-tot-khong-nen-xay-nha-tuong-10-hay-20-1866022-vi-cb',
            'nha-vuon-1-tang-4-phong-ngu-dep-20x14m-tai-hai-duong-ndbt1t115-2967169-vi-cb',
            'ban-ve-nha-nho-dep-50m2-xay-dung-anh-hau-thu-duc-chuyen-thiet-ke-biet-thu-nha-pho-nha-cap-4-dep-1466880-vi-cb',
            'xay-nha-phan-tho-phan-hoan-thien-la-gi-gom-hang-muc-cong-viec-vat-lieu-nao-6765611-vi-cb',
            'nen-lam-nha-vao-thang-may-la-tot-nhat-8565223-vi-cb',
            '15-mau-ho-ca-xi-mang-dep-tao-khong-gian-thu-gian-cuc-chill-tokyometro-5466763-vi-cb',
            'xay-nha-4-tang-50m2-het-bao-nhieu-tien-t07-2022-8661899-vi-cb',
            'so-huu-ban-ve-thiet-ke-nha-biet-thu-2-tang-mai-bang-dep-hien-dai-sieu-thi-nha-mau-8267896-vi-cb',
            'kich-thuoc-va-dien-tich-nha-nuoi-chim-yen-hieu-qua-nhat-2021-1566535-vi-cb',
            'biet-thu-3-tang-mai-thai-tan-co-dien-mat-tien-12m-dep-xuat-sac-6067968-vi-cb',
            '3-dieu-can-luu-y-khi-mua-nha-xay-san-3365256-vi-cb',
            'top-6-mau-biet-thu-2-tang-70m2-dep-khong-the-bo-qua-1067557-vi-cb',
            'vay-1-ty-xay-10-phong-tro-kinh-doanh-ban-thao-tai-san-tra-no-ngan-hang-8466497-vi-cb',
            'kham-pha-mau-biet-thu-san-vuon-tan-co-dien-2-tang-220m2-7767941-vi-cb',
            'thiet-ke-nha-biet-thu-3-tang-70m2-mai-thai-kien-truc-le-7967579-vi-cb',
            'mau-thiet-ke-nha-1-tang-dien-tich-50m2-2-phong-ngu-bt32975-9466882-vi-cb',
            'gia-xay-dung-nha-bang-vat-lieu-nhe-2020-va-cac-mau-dep-nhat-7765909-vi-cb',
            'thiet-ke-biet-thu-hien-dai-4-tang-mat-tien-7m-sieu-dep-6467554-vi-cb',
            'ldquo-con-ai-khac-rdquo-chua-biet-co-nen-xay-nha-lech-tang-khong-3065746-vi-cb',
            '23-mau-nha-mai-bang-1-tang-o-nong-thon-dep-co-chi-phi-thap-2665432-vi-cb',
            'xem-ngay-cung-xem-download-ban-ve-cad-nha-2-tang-mai-thai-10-5-x-12m-day-du-ban-ve-noi-that-xinh-thiet-ke-va-thi-cong-noi-that-can-ho-chung-cu-biet-thu-nha-pho-dinh-review-6567268-vi-cb',
            'chia-khoa-trao-tay-can-tho-bang-gia-xay-nha-tron-goi-can-tho-2021-5265567-vi-cb',
            '10-ban-ve-thiet-ke-biet-thu-ban-chay-nhat-kien-truc-movic-1168632-vi-cb',
            'thiet-ke-biet-thu-2-tang-mat-tien-12m-o-ha-giang-dep-me-ly-7967956-vi-cb',
            'ban-ve-biet-thu-2-tang-6x10m-mai-bang-ndbt2t160-hien-dai-tinh-te-t04-2022-7167910-vi-cb',
            'don-gia-xay-nha-tron-goi-bao-nhieu-7566591-vi-cb',
            'chuyen-gia-giai-dap-co-nen-xay-nha-vao-cuoi-nam-khong-vi-sao-7265208-vi-cb',
            'top-10-ban-ve-biet-thu-2-tang-mai-thai-day-du-cong-nang-9168666-vi-cb',
            'nha-bao-nhieu-m2-co-the-xay-lech-tang-xay-dung-song-phat-8865761-vi-cb',
            '13-ban-ve-mat-bang-biet-thu-2-tang-hien-dai-full-cong-nang-5767899-vi-cb',
            'nen-mua-nha-xay-san-hay-dat-nen-noi-ban-khoan-cua-nhieu-nha-dau-tu-2562661-vi-cb',
            '5-phut-giai-dap-nen-sua-nha-hay-xay-moi-5965048-vi-cb',
            '5-khac-biet-to-lon-giua-nha-thau-xay-dung-bai-ban-va-tu-thue-nhan-cong-9064982-vi-cb',
            'mau-thiet-ke-nha-biet-thu-1-tang-4-phong-ngu-full-noi-that-8867165-vi-cb',
            'ban-ve-thiet-ke-nha-cap-4-dien-tich-90m2-mai-thai-truyen-thong-1467088-vi-cb',
            '20-ban-ve-mau-thiet-ke-noi-that-can-ho-3-phong-ngu-dep-2022-1967030-vi-cb',
            'co-nen-xay-dung-nha-cap-4-co-gac-lung-hay-khong-katahome-9066004-vi-cb',
            'biet-thu-vuon-2-tang-70m2-hinh-chu-l-phong-cach-hien-dai-tai-phu-tho-3066986-vi-cb',
            '6-mau-nha-vuon-dep-1-tang-mai-thai-dep-nhat-nong-thon-2022-9167522-vi-cb',
            'co-nen-thue-hoa-vien-thiet-ke-khi-xay-nha-hoan-thien-tai-phan-thiet-khong-3866080-vi-cb',
            'top-10-mau-nha-chu-l-2-tang-70m2-dep-nhat-2022-8567570-vi-cb',
            '5-mau-kien-truc-nha-ong-mat-tien-7m-gia-biet-thu-dep-hien-dai-tai-hai-phong-dien-dan-hoi-dap-kien-truc-phong-thuy-8867542-vi-cb',
            'mot-so-cam-ky-gay-hao-tai-cho-gia-chu-khi-dat-be-ca-va-hon-non-bo-4166769-vi-cb',
            'xay-nha-hinh-chu-l-co-tot-khong-tuyen-tap-nha-chu-l-dang-cap-an-loc-3366290-vi-cb',
            'mau-biet-thu-vuon-a-dong-2-tang-1-tum-dep-long-lanh-bt19048-5968027-vi-cb',
            'vat-lieu-chong-tham-dot-top-12-dung-dich-hoa-chat-chong-tham-tot-nhat-2022-9665646-vi-cb',
            'xay-nha-nen-xem-tuoi-vo-hay-tuoi-chong-middot-kien-truc-duy-tan-nano-machine-9465482-vi-cb',
            '15-mau-nha-1-tang-5-phong-ngu-dep-co-chi-phi-dau-tu-thap-7867106-vi-cb',
            '20-mau-biet-thu-mai-nhat-dep-hien-dai-dan-dau-2022-6067802-vi-cb',
            'mau-nha-cap-4-voi-75m2-ban-ve-thiet-ke-cuc-hot-autocad-8967021-vi-cb',
            'nen-sua-chua-cai-tao-nha-cu-hay-xay-moi-toan-bo-7465022-vi-cb',
            'file-cad-biet-thu-san-vuon-dep-me-hon-duoc-ua-chuong-nhat-movic-8067925-vi-cb',
            'ban-ve-biet-thu-1-tang-co-gac-lung-5-phong-ngu-800tr-tai-hcm-6567676-vi-cb',
            'xay-moi-hay-cai-tao-nha-cu-cong-ty-tnhh-adal-home-4765067-vi-cb',
            'mau-nha-cap-4-mai-thai-tren-dien-tich-45m2-don-gian-o-nong-thon-aht-homes-tu-van-thiet-ke-thi-cong-nha-dep-xay-dung-nha-tron-goi-1566831-vi-cb',
            'cach-lua-chon-xi-mang-khi-xay-nha-gia-ca-hay-chat-luong-9364601-vi-cb',
            'cach-xem-huong-nha-hop-tuoi-vo-chong-chuan-nhat-truoc-khi-lam-4665465-vi-cb',
            'ky-thuat-xay-nha-yen-tot-nhat-2022-1-von-10-loi-9166554-vi-cb',
            'gia-chu-la-ai-gia-chu-la-chong-hay-vo-nhung-dieu-can-biet-khi-xem-tuoi-lam-nha-8465491-vi-cb',
            'thang-7-am-co-nen-chuyen-nha-khong-9765683-vi-cb',
            'tat-tan-tat-ve-nha-pho-lech-tang-hien-dai-4265800-vi-cb',
            '36-ban-ve-thiet-ke-nha-biet-thu-3-tang-hien-dai-autocad-6067686-vi-cb',
            'cac-mau-thiet-ke-phong-ngu-co-toilet-khep-kin-va-hop-phong-thuy-4666696-vi-cb',
            'cung-xem-download-ban-ve-cad-nha-2-tang-mai-thai-10-5-x-12m-day-du-ban-ve-noi-that-xinh-7467977-vi-cb',
            'kien-truc-biet-thu-1-tang-hien-dai-10x20-mai-thai-vuon-dep-2867314-vi-cb',
            'thep-cuon-can-nguoi-bao-gia-chi-tiet-va-moi-nhat-2021-5548723-vi-cb',
            'chon-xay-nha-phan-tho-hay-xay-nha-tron-goi-bai-13-cong-ty-co-phan-xay-dung-va-noi-that-kinghouse-4066599-vi-cb',
            'nhung-mau-nha-biet-thu-2-tang-1-tum-sieu-thi-nha-mau-7468022-vi-cb',
            'cung-xem-17-ban-ve-biet-thu-2-tang-mai-thai-co-file-cad-downloard-noi-that-hang-phat-noi-that-xinh-5467921-vi-cb',
            'nen-lam-nha-may-tang-mach-ban-lua-chon-7266267-vi-cb',
            'mau-biet-thu-vuon-2-tang-200m2-hien-dai-tai-thai-binh-bt36-7068653-vi-cb',
            'mau-thiet-ke-biet-thu-2-tang-hien-dai-200m2-tien-nghi-2022-8568647-vi-cb',
            'biet-thu-mini-1-tang-mau-biet-thu-1-tang-6x10m-300-trieu-2167513-vi-cb',
            'tat-ca-san-pham-7068629-vi-cb',
            'goi-y-4-mau-biet-thu-2-tang-80m2-dep-khong-the-roi-mat-10067677-vi-cb',
            'ban-ve-biet-thu-1-tang-3-phong-ngu-gia-duoi-600-trieu-7067701-vi-cb',
            '3-cach-thiet-ke-nha-o-huong-tay-ma-gia-chu-nao-cung-can-1752271-vi-cb',
            'nen-xay-be-nuoc-ngam-trong-nha-the-nao-cho-dung-ky-thuat-4466454-vi-cb',
            'xay-nha-tron-goi-chia-khoa-trao-tay-co-an-toan-khong-cong-ty-xay-dung-hbuild-6265508-vi-cb',
            'kham-pha-thiet-ke-biet-thu-tan-co-dien-2-tang-150m2-dep-2023-6968054-vi-cb',
            'tong-hop-mau-nha-2-tang-3-phong-ngu-gia-re-duoi-1-ty-dong-ma-van-sieu-dep-1866941-vi-cb',
            'tcxdvn-170-2007-ket-cau-thep-gia-cong-lap-rap-va-nghiem-thu-yeu-cau-ky-thuat-hoc-that-nhanh-1616716-vi-cb',
            'biet-thu-3-tang-kien-truc-phap-8x12m-xuat-hien-day-sang-trong-9867699-vi-cb',
            'ban-ve-thiet-ke-nha-rong-3m-dai-10m-12m-15m-18m-8567972-vi-cb',
            'mau-biet-thu-mini-2-tang-hien-dai-don-gian-cua-nha-anh-phuc-8167022-vi-cb',
            'xem-ngay-dep-lam-nha-sua-nha-thang-3-nam-2022-ngay-tot-lam-nha-sua-nha-thang-3-3365671-vi-cb',
            'nhung-mau-thiet-ke-biet-thu-tan-co-dien-mai-thai-dep-nhat-2019-9866943-vi-cb',
            'xay-nha-bang-vat-lieu-nhe-co-nhung-uu-nhuoc-diem-gi-9065898-vi-cb',
            'quy-dinh-ve-viec-xu-ly-hanh-vi-san-lap-kenh-muong-thuy-loi-tu-nam-2004-5265163-vi-cb',
            'ban-ve-nha-biet-thu-2-tang-mai-thai-dien-tich-200m2-6768671-vi-cb',
            'mau-thiet-ke-biet-thu-70m2-2-tang-4-phong-ngu-mai-thai-hien-dai-o-ha-noi-2766989-vi-cb',
            '50-mau-thiet-ke-noi-that-biet-thu-dep-sang-trong-cao-cap-nhat-8566967-vi-cb',
            'nha-biet-thu-1-tang-o-que-bst125067-sieu-thi-nha-mau-8666682-vi-cb',
            '4-kinh-nghiem-giam-sat-tho-khi-xay-nha-khong-the-bo-qua-neu-muon-tiet-kiem-chi-phi-4366035-vi-cb',
            'thiet-ke-biet-thu-tan-co-dien-3-tang-2-mat-tien-dien-tich-11-9m-x-15-5m-tai-sai-gon-3266846-vi-cb',
            'thiet-ke-biet-thu-tan-co-dien-3-tang-12m-x-25m-nha-dep-kien-sang-5967965-vi-cb',
            'mau-thiet-ke-nha-2-tang-chu-l-dien-tich-70m2-xay-dung-biet-thu-dep-chuyen-trang-tong-hop-va-chia-se-mau-nha-kinh-nghiem-xay-dung-nha-o-gia-tot-4867003-vi-cb',
            'nen-dat-be-phot-o-dau-trong-nha-la-hop-phong-thuy-nhat-5466108-vi-cb',
            'mua-dat-nen-xem-tuoi-vo-hay-chong-thi-tot-nhat-4265495-vi-cb',
            'co-nen-cai-tao-nha-cap-4-hay-xay-nha-moi-truong-thang-4365043-vi-cb',
            'me-man-voi-15-mau-thiet-ke-nha-30m2-dep-hien-dai-thong-thoang-9066716-vi-cb',
            '6-mau-thiet-ke-mat-bang-biet-thu-2-tang-khong-bao-gio-loi-mot-trang-thong-tin-chinh-thuc-reti-proptech-3167049-vi-cb',
            'co-nen-su-dung-tam-vat-lieu-nhe-lam-tuong-xay-nha-4965907-vi-cb',
            'mau-biet-thu-3-tang-hien-dai-mai-bang-day-tien-nghi-2022-8167895-vi-cb',
            '9-biet-thu-mai-bang-2-tang-hien-dai-co-san-vuon-villa-dep-1867884-vi-cb',
            'xay-lai-nha-moi-hay-chi-can-sua-nha-ndash-dau-la-giai-phap-hay-7365020-vi-cb',
            'mau-thiet-ke-biet-thu-3-tang-1-tum-mai-thai-kien-truc-c-b-4667424-vi-cb',
            'bat-mi-mau-thiet-ke-biet-thu-dep-8x12-khien-ban-thich-me-an-loc-2567716-vi-cb',
            'mau-thiet-ke-biet-thu-mai-lech-gia-dinh-anh-quy-hoa-binh-cong-ty-co-phan-kien-truc-xay-dung-viet-home-4567210-vi-cb',
            'cach-xay-nha-lech-tang-phong-thuy-de-tang-vuong-khi-ngoi-nha-6565778-vi-cb',
            'ban-ve-mat-bang-biet-thu-1-tang-hien-dai-mai-ngoi-thoang-dang-5267616-vi-cb',
            '5-luu-y-khi-dau-tu-nha-pho-xay-san-6665281-vi-cb',
            'be-nuoc-ngam-nen-dat-o-trong-nha-hay-truoc-nha-thi-tot-nhat-6666451-vi-cb',
            'co-nen-xay-nha-tren-muong-khong-bi-lam-sao-ma-khong-nen-2265103-vi-cb',
            'phong-thuy-nha-chu-l-tot-hay-xau-20-mau-nha-chu-l-dep-duoi-600-trieu-5066279-vi-cb',
            '9-mau-thiet-ke-biet-thu-mini-2-tang-dep-hien-dai-1966913-vi-cb',
            'thiet-ke-ban-ve-biet-thu-tan-co-dien-2-tang-dien-tich-200m2-1668669-vi-cb',
            'mau-biet-thu-mini-2-mat-tien-75m2-kien-truc-hien-dai-4167007-vi-cb',
            '1-du-toan-chi-phi-xay-nha-2-tang-bang-ke-file-excel-2022-8467587-vi-cb',
            'mau-biet-thu-mai-nhat-2-tang-10m-x-12m-co-phong-tho-tai-tang-1-bt2104-9467963-vi-cb',
            'thiet-ke-nha-dien-tich-100m2-1-tang-mat-tien-9m-bt22975-2367759-vi-cb',
            'xay-nha-tron-goi-gia-re-co-nen-giao-thau-xay-nha-tron-goi-hay-khong-3462449-vi-cb',
            'biet-thu-san-vuon-500m2-2-tang-voi-kien-truc-hien-dai-doc-dao-4267933-vi-cb',
            '99-ban-ve-nha-2-tang-chu-l-mai-bang-don-gian-dep-1367891-vi-cb',
            'dat-tho-cu-la-gi-xay-nha-duoc-khong-co-dong-thue-moi-nam-4564809-vi-cb',
            'c-n-n-x-y-nh-g-c-l-ng-kh-ng-kinh-nghi-m-thi-t-k-t-ng-l-ng-p-hi-n-i-cho-gia-nh-7265973-vi-cb',
            '7-thiet-ke-nha-vuong-8x12m-2-tang-dep-chi-co-tai-hung-anh-7067717-vi-cb',
            'full-ban-ve-biet-thu-2-tang-mai-nhat-hien-dai-4-phong-ngu-zh005-1668588-vi-cb',
            'huong-dan-cach-xay-be-nuoc-ngam-dung-ky-thuat-inox-trung-thanh-2766485-vi-cb',
            'gia-xay-be-nuoc-ngam-yeu-cau-va-luu-y-4766455-vi-cb',
            'biet-thu-tan-co-dien-3-tang-12m-x-20m-ket-hop-kinh-doanh-2667961-vi-cb',
            'biet-thu-hien-dai-3-tang-mai-thai-mat-tien-7m-mb1721-kien-truc-movic-5167537-vi-cb',
            'biet-thu-tan-co-dien-2-tang-200m2-doc-dao-va-sang-trong-xay-dung-phuc-thien-gia-4868643-vi-cb',
            'muon-co-biet-thu-dep-thi-nen-xay-nha-mai-bang-hay-mai-thai-7965338-vi-cb',
            '23-mau-ban-ve-thiet-ke-nha-1-tang-dep-don-gian-hien-dai-3066878-vi-cb',
            'giai-dap-co-nen-xay-tuong-bang-vat-lieu-nhe-nhanh-chong-4965931-vi-cb',
            'can-ho-45m2-da-xay-la-phai-chat-du-dien-tich-co-nho-katahome-5966841-vi-cb',
            'bat-song-xay-nha-chu-l-2-tang-400-trieu-dep-lung-linh-1366327-vi-cb',
            'biet-thu-2-tang-mai-nhat-800-trieu-voi-thiet-ke-dep-hut-hon-2267804-vi-cb',
            'tham-khao-biet-thu-2-tang-12x15m-mai-nhat-tien-nghi-2022-7767810-vi-cb',
            'chia-se-cach-xay-nha-tro-tiet-kiem-nhat-quot-mot-von-bon-loi-quot-9966498-vi-cb',
            'nhung-dai-ky-bo-tri-phong-tho-co-the-khien-ban-tan-gia-bai-san-8066688-vi-cb',
            'ly-do-ban-khong-nen-chon-dich-vu-xay-nha-tron-goi-moneydaily-6264970-vi-cb',
            'tranh-cai-nay-lua-nen-xay-nha-bang-mai-bang-hay-mai-thai-5965335-vi-cb',
            'tong-hop-cac-loai-vat-lieu-trong-xay-dung-nha-o-sieu-thi-nha-mau-6665595-vi-cb',
            'tang-tum-la-gi-co-nen-thiet-ke-tang-tum-hay-khong-5665891-vi-cb',
            'cac-mau-nha-cap-4-mai-thai-dep-gia-re-duoc-ua-chuong-nhat-hien-nay-6264943-vi-cb',
            'xay-nha-co-bao-nhieu-phong-la-tot-cach-tinh-so-phong-trong-nha-top1onhadep-com-1066232-vi-cb',
            'co-nen-dau-tu-nha-tro-cho-thue-khong-9566494-vi-cb',
            'canh-bao-nen-xem-tuoi-sua-chua-nha-de-tranh-nhung-dieu-sao-3465467-vi-cb',
            'phong-tho-gan-nha-ve-sinh-va-bep-co-nen-hay-khong-cach-hoa-giai-9466673-vi-cb',
            'thiet-ke-phong-ngu-bao-nhieu-m2-la-hop-ly-cung-chuyen-gia-giai-dap-5766263-vi-cb',
            'thi-cong-san-go-tron-goi-a-z-moi-nhat-tong-cong-ty-xay-dung-ha-noi-4965651-vi-cb',
            'ban-ve-thiet-ke-kien-truc-biet-thu-4-tang-tan-co-dien-90m2-neoac-6967784-vi-cb',
            'ban-ve-thiet-ke-nha-tren-dat-no-hau-3-5-tang-nho-don-gian-5766959-vi-cb',
            'mau-biet-thu-2-tang-hien-dai-100m2-co-4-phong-ngu-o-nong-thon-7867991-vi-cb',
            'xay-ho-ca-truoc-nha-theo-phong-thuy-nhung-luu-y-nen-lam-giup-mang-van-menh-tot-cho-gia-chu-3466774-vi-cb',
            'cach-thiet-ke-biet-thu-vuon-3-gian-2-tang-mai-ngoi-phong-thuy-anh-hanh-t05-2022-5968600-vi-cb',
            'mau-biet-thu-co-dien-2-tang-1-tum-sang-trong-nam-2023-9868012-vi-cb',
            'xem-ngay-biet-thu-mini-2-tang-80m2-thiet-ke-chuan-ty-le-dang-xay-nhat-hien-nay-nbsp-me-nha-dep-6867656-vi-cb',
            'nha-co-tang-co-nen-sua-nha-khong-nhung-dieu-cam-ky-la-gi-1365086-vi-cb',
            'thiet-ke-biet-thu-2-tang-1-tum-120m2-dep-rang-ngoi-tai-phu-quoc-roman-6068035-vi-cb',
            'biet-thu-2-tang-mini-nho-xinh-sieu-thi-nha-mau-1768596-vi-cb',
            '4-mau-thiet-ke-nha-chu-l-2-tang-60m2-an-tuong-dep-1766896-vi-cb',
            '30-nhung-mau-nha-dep-su-dung-vat-lieu-nhe-co-gia-duoi-100-trieu-2065915-vi-cb',
            'xay-nha-1-tret-1-lau-khoang-bao-nhieu-tien-hop-ly-nam-2022-5167652-vi-cb',
            'ung-dung-tieu-chuan-thiet-ke-ban-ve-mat-bang-nha-biet-thu-3-tang-tien-nghi-7867040-vi-cb',
            'xay-nha-2-tang-co-gac-lung-va-mot-so-dieu-ban-nen-biet-tbox-viet-nam-6566019-vi-cb',
            'nen-xay-nha-vao-mua-nao-trong-nam-la-tot-va-tiet-kiem-chi-phi-7965217-vi-cb',
            'ban-ve-thiet-ke-biet-thu-3-tang-65m2-hien-dai-2166921-vi-cb',
            '25-trieu-thi-cong-tron-goi-noi-that-phong-ngu-20m2-7368601-vi-cb',
            'co-nen-thue-xay-nha-tron-goi-khong-tai-sao-9866047-vi-cb',
            '5-mau-nha-1-tang-4-phong-ngu-don-gian-dep-chi-phi-tot-nhat-4767172-vi-cb',
            'mau-biet-thu-pho-2-tang-mat-tien-8m-hien-dai-dep-kien-truc-movic-9367620-vi-cb',
            '10-dieu-can-phai-biet-ve-x-y-nh-nu-i-chim-yen-1566552-vi-cb',
            'ban-ve-mat-bang-biet-thu-1-tang-dep-nhat-2022-8666966-vi-cb',
            'ban-ve-nha-pho-2-tang-mai-bang-6x10m-hien-dai-tinh-te-ndnp2t38-2467905-vi-cb',
            'the-nao-la-nha-mai-thai-mai-bang-loai-mai-nao-tiet-kiem-hon-6865378-vi-cb',
            'mau-khach-san-5-tang-mat-tien-12m-tieu-chuan-3-sao-tai-phan-thiet-7267970-vi-cb',
            'co-nen-xay-nha-tron-goi-an-toan-hay-nguy-hiem-tiet-kiem-hay-lang-phi-9766575-vi-cb',
            'thiet-ke-biet-thu-2-tang-2-mat-tien-35-mau-ban-ve-full-ban-ve-cuc-dep-9968614-vi-cb',
            'mau-nha-vuon-biet-thu-2-tang-don-gian-10x20-8m-co-4-phong-ngu-o-phan-thiet-5968115-vi-cb',
            'top-6-ban-ve-mat-bang-nha-chu-l-2-tang-pho-bien-nhat-4366314-vi-cb',
            'co-nen-giao-thau-xay-nha-tron-goi-hay-chi-khoan-phan-tho-webxaynha-8066598-vi-cb',
            '55-mau-thiet-ke-nha-dien-tich-nho-30m2-tro-nen-rong-rai-7566709-vi-cb',
            'chi-phi-xay-phong-tro-moi-nhat-tham-khao-ngay-30-mau-phong-tro-dep-2022-7966515-vi-cb',
            'xem-ngay-top-7-mau-nha-2-tang-800-trieu-dep-thinh-hanh-hien-nay-me-nha-dep-5866950-vi-cb',
            'co-nen-xay-nha-tro-bang-gach-be-tong-nhe-khong-ton-lop-sat-thep-son-nuoc-quang-nhat-4265912-vi-cb',
            'xay-nha-khung-thep-2-tang-gia-bao-nhieu-7966420-vi-cb',
            'xay-nha-chia-khoa-trao-tay-hau-giang-bao-nhieu-tien-gia-xay-nha-2022-5965553-vi-cb',
            'can-biet-ngay-10-dieu-kieng-ky-khi-xay-nha-moi-phong-thuy-hoc-can-chu-y-1765177-vi-cb',
            'thiet-ke-biet-thu-2-tang-ket-hop-kinh-doanh-10x20-tai-phu-tho-8968124-vi-cb',
            'xay-nha-thang-3-am-lich-duoc-khong-9265657-vi-cb',
            'xi-mang-xay-dung-loai-nao-tot-nhat-gia-re-nhat-nam-2022-2464612-vi-cb',
            'co-nen-xay-nha-lech-tang-khong-mau-nha-lech-tang-dep-5565794-vi-cb',
            'nhung-thang-khong-nen-cuoi-ban-nen-biet-3365726-vi-cb',
            'ban-ve-nha-2-tang-thiet-ke-chi-tiet-day-du-nhat-5066971-vi-cb',
            'nha-ve-sinh-duoi-gam-cau-thang-nen-hay-khong-1666198-vi-cb',
            'top-10-thiet-ke-nha-2-tang-20m2-tien-nghi-dep-rong-rai-nhat-6568593-vi-cb',
            'co-nen-sua-nha-dau-nam-xay-dung-so-9265234-vi-cb',
            'thiet-ke-thi-cong-noi-that-can-ho-galaxy-9-mot-phong-ngu-6366885-vi-cb',
            '7-mau-ban-ve-nha-2-tang-mat-tien-7m-xuat-sac-nhat-hien-nay-2767531-vi-cb',
            'chia-se-nhung-kinh-nghiem-tuyen-tho-xay-nha-tu-a-z-4166083-vi-cb',
            '15-mau-thiet-ke-nha-ve-sinh-duoi-cau-thang-dep-hop-phong-thuy-6866180-vi-cb',
            'xay-be-nuoc-ngam-trong-nha-nhu-nao-cho-dung-quy-trinh-8966476-vi-cb',
            'be-nuoc-ngam-cho-nen-va-khong-nen-dat-be-nuoc-ngam-phong-thuy-4166450-vi-cb',
            '20-mau-thiet-ke-nha-50m2-1-tang-dep-doc-dao-day-du-tien-nghi-2466868-vi-cb',
            'mau-nha-cap-4-mai-lech-dep-3-phong-ngu-1-phong-tho-2021-3867197-vi-cb',
            'nen-chon-cong-ty-xay-nha-tron-goi-hay-tu-thue-nhan-cong-xay-dung-1564942-vi-cb',
            'nhung-vat-lieu-lam-nen-nha-tot-va-dep-nhat-2019-8065600-vi-cb',
            'kinh-nghiem-mua-dat-xay-nha-khong-nen-mua-nha-xay-san-2865253-vi-cb',
            'nen-chon-loai-xi-mang-nao-de-tot-cho-viec-trat-tuong-nha-1664604-vi-cb',
            'biet-thu-2-tang-hien-dai-mai-lech-o-binh-duong-m255-8267202-vi-cb',
            'thiet-ke-biet-thu-cach-tan-2-tang-70m2-tai-phu-tho-bt43-4767561-vi-cb',
            '05-uu-diem-khi-xay-nha-tron-goi-so-voi-xay-dung-phan-tho-5266614-vi-cb',
            '4-buoc-lat-gach-nen-nha-dung-ky-thuat-5765622-vi-cb',
            'co-nen-lam-nha-ve-sinh-duoi-cau-thang-khong-1566165-vi-cb',
            'mau-biet-thu-vuon-1-tang-mai-thai-4-phong-ngu-o-long-an-m300-3667185-vi-cb',
            'nhung-luu-y-khi-mua-dat-nen-khong-phai-ai-cung-biet-3764816-vi-cb',
            '5-mau-nha-3-gian-2-tang-dep-nhat-mai-ngoi-tret-lau-an-tuong-8967980-vi-cb',
            'mau-nha-dep-kien-truc-mai-thai-duoc-yeu-thich-nhat-9767023-vi-cb',
            'kham-pha-20-ban-ve-nha-2-tang-dep-hien-dai-update-2022-1867813-vi-cb',
            'xu-ly-nen-mong-tren-nen-dat-yeu-nhu-ao-ho-dat-muon-8365138-vi-cb',
            'vi-tri-dat-be-phot-hop-phong-thuy-va-thuan-tien-4166137-vi-cb',
            'mau-thiet-ke-nha-2-tang-50m2-3-phong-ngu-chi-dung-xay-dung-phuc-thien-gia-4666887-vi-cb',
            'thiet-ke-xay-nha-2-tang-200m2-mat-tien-15m-o-hoa-binh-bt291655-8368668-vi-cb',

            'nen-xay-nha-tron-goi-binh-thuan-hay-thue-nhan-cong-va-tu-mua-vat-tu-1464946-vi-cb',
            'thiet-ke-nha-mai-thai-1-tang-70m2-mau-biet-thu-1-tang-mai-thai-ndbt1t28-4067558-vi-cb',
            '3-mau-thiet-ke-nha-8x12m-2-tang-don-gian-voi-ban-ve-mat-bang-t06-2022-7167697-vi-cb',
            'co-nen-xay-nha-bang-be-tong-sieu-nhe-san-panel-sieu-nhe-duc-lam-7865954-vi-cb',
            'mau-ban-ve-biet-thu-1-tang-khoa-hoc-tien-nghi-1167118-vi-cb',
            '22-mau-thiet-ke-nha-ong-5-tang-va-ban-ve-chi-tiet-2022-1566752-vi-cb',
            'ban-ve-biet-thu-2-tang-125m2-voi-4-phong-ngu-ms-231251-3768050-vi-cb',
            'thiet-ke-nha-chu-l-80m2-2-tang-30-mau-ban-ve-dep-2022-5567679-vi-cb',
            'xay-nha-nuoi-chim-yen-ldquo-vang-trang-rdquo-chua-thay-da-bi-ldquo-trang-tay-rdquo-5266551-vi-cb',
            'mau-nha-lech-tang-thiet-ke-nha-xu-huong-cua-nam-2022-3165811-vi-cb',
            '11-mau-nha-1-tang-3-phong-ngu-80m2-dep-cong-nang-toi-uu-9467634-vi-cb',
            'cac-bo-ban-ve-nha-2-tang-dep-hay-1-tret-1-lau-mai-thai-2022-8467946-vi-cb',
            'co-nen-khoan-tron-goi-hay-khong-txd-vn-3666594-vi-cb',
            '12-mau-nha-8x12-1-tang-dep-gia-re-day-du-cong-nang-6667704-vi-cb',
            'lua-chon-xi-mang-xay-nha-nao-tot-gia-re-nhat-nam-2021-tap-doan-tran-anh-group-5864582-vi-cb',
            '1-dac-biet-luu-y-khi-chon-mua-va-xay-nha-theo-phong-thuy-minh-chau-group-7865114-vi-cb',
            'top-28-mau-nha-2-tang-7m-x-12m-2022-4967975-vi-cb',
            'biet-thu-nha-vuon-1-tang-180m2-dep-o-nong-thon-moi-6467351-vi-cb',
            'thiet-ke-phong-tho-gia-dinh-hien-dai-hop-phong-thuy-8366685-vi-cb',
            'corrective-action-fund-6666463-vi-cb',
            '10-mau-ban-ve-thiet-ke-nha-50m2-1-tang-dep-doc-la-2022-2466873-vi-cb',
            'lua-chon-nha-thep-tien-che-hay-nha-be-tong-cot-thep-8966422-vi-cb',
            'thiet-ke-nha-biet-thu-2-tang-150m2-mai-thai-kien-truc-le-8268052-vi-cb',
            'file-cad-ban-ve-kien-truc-biet-thu-1-tang-7-18-1m-chia-se-ho-so-xay-dung-5867548-vi-cb',
            'nen-xay-nha-tron-goi-hay-thue-nhan-cong-8166584-vi-cb',
            'mau-thiet-ke-nha-chu-l-2-tang-80m2-hien-dai-tai-lam-dong-9167664-vi-cb',
            'biet-thu-1-tang-tan-co-dien-35-mau-thiet-ke-full-ban-ve-cong-nang-dep-5467420-vi-cb',
            'die-u-kie-n-ca-n-va-du-de-xay-du-ng-nha-nuoi-ye-n-9966542-vi-cb',
            'mau-biet-thu-hien-dai-1-tang-dep-co-noi-that-sang-trong-chieu-ngang-8m-5567609-vi-cb',
            'thiet-ke-noi-that-phong-khach-25m2-dep-53-mau-bao-gia-2022-5166648-vi-cb',
            'biet-thu-mini-2-tang-80m2-thiet-ke-chuan-ty-le-dang-xay-nhat-8867635-vi-cb',
            '32-ban-ve-thiet-ke-nha-biet-thu-2-tang-mai-thai-mai-nhat-autocad-sketchup-2867800-vi-cb',
            'mau-thiet-ke-nha-2-tang-5x14m-hien-dai-ban-ve-noi-that-7266991-vi-cb',
            'mau-thiet-ke-biet-thu-4-phong-ngu-mai-thai-be-the-uy-nghi-7667626-vi-cb',
            'ban-ve-nha-biet-thu-2-tang-voi-dien-tich-80m2-bt-25052-katahome-1868581-vi-cb',
            'thiet-ke-nha-2-tang-mai-thai-5-phong-ngu-ms-231502-2268055-vi-cb',
            'dieu-kien-quy-trinh-thuc-hien-du-an-phan-lo-ban-nen-cho-nguoi-dan-2964864-vi-cb',
            'co-nen-xay-nha-bang-tam-3d-khong-chi-phi-xay-bang-tam-3d-chi-tiet-1065961-vi-cb',
            'tu-van-thiet-ke-nha-chu-l-dien-tich-80m2-2-tang-4-phong-ngu-kien-truc-bo-ba-2167060-vi-cb',
            'sua-nha-co-can-xem-tuoi-khong-2665498-vi-cb',
            'thiet-ke-biet-thu-2-tang-1-tum-phong-cach-co-dien-dep-tai-quan-9-tphcm-4868017-vi-cb',
            'giai-dap-ban-khoan-nen-sua-nha-hay-xay-moi-6865001-vi-cb',
            'top-25-mau-thiet-ke-nha-30m2-hien-dai-an-tuong-7266705-vi-cb',
            'thiet-ke-biet-thu-tan-co-dien-1-tang-mai-thai-357-25m2-tai-vinh-long-btcd-0059-9266694-vi-cb',
            'nha-lap-ghep-co-thuc-su-tiet-kiem-chi-phi-va-thoi-gian-thi-cong-co-nen-xay-nha-lap-ghep-9965965-vi-cb',
            'lua-chon-xi-mang-xay-nha-nao-tot-gia-re-nhat-nam-2021-tap-doan-tran-anh-group-5664656-vi-cb',
            'co-nen-xay-be-nuoc-ngam-trong-nha-hay-khong-svg-engineering-3766467-vi-cb',
            'thiet-ke-mau-biet-thu-3-tang-mai-thai-150m2-2966889-vi-cb',
            'cach-hoa-giai-phong-tho-ben-tren-nha-ve-sinh-cho-dung-phong-thuy-8266669-vi-cb',
            'biet-thu-mai-thai-va-20-mau-thiet-ke-dan-dau-xu-huong-2022-9968625-vi-cb',
            'cung-xem-ban-ve-thiet-ke-nha-90m2-2-tang-mat-tien-8m-o-hung-yen-bt11125-noi-that-xinh-3467086-vi-cb',
            'welcome-to-legal-aid-services-of-oklahoma-s-guide-to-free-legal-help-in-oklahoma-3166504-vi-cb',
            'nha-lech-tang-la-gi-ly-do-nen-xay-nha-ong-lech-tang-2021-6165808-vi-cb',
            'ban-ve-thiet-ke-nha-pho-2-tang-75m2-mai-ton-5x15m-3567018-vi-cb',
            'thiet-ke-nha-ve-sinh-chuan-phong-thuy-va-nhung-dieu-kieng-ky-nen-tranh-1166710-vi-cb',
            'mua-nha-xay-san-nen-hay-khong-nen-8865266-vi-cb',
            '7-mau-mat-bang-biet-thu-2-tang-thiet-ke-chi-tiet-khoa-hoc-roman-10068630-vi-cb',
            'cach-hoa-giai-phong-tho-ben-tren-nha-ve-sinh-dung-cach-1566672-vi-cb',
            'quy-dinh-chieu-cao-tung-tang-nha-chieu-cao-tu-san-den-tran-nha-chuan-5267639-vi-cb',
            'nen-lam-nha-khung-thep-mai-ton-hay-nha-khung-thep-be-tong-nhe-sua-cua-sat-tai-nha-1166418-vi-cb',
            'nhung-dieu-ve-su-dung-xi-mang-ban-can-biet-7164609-vi-cb',

            '20-mau-mat-bang-chung-cu-2-phong-ngu-dep-ban-ve-bo-tri-2022-1366829-vi-cb',
            'mua-nha-bao-phap-ly-tren-dat-nong-nghiep-nhieu-ho-dan-trang-tay-9166341-vi-cb',
            'mau-thiet-ke-biet-thu-2-tang-4-phong-ngu-full-noi-that-7267855-vi-cb',
            'xay-nha-tron-goi-chia-khoa-trao-tay-lam-sao-de-hieu-qua-1765546-vi-cb',
            'tuyen-chon-20-chi-phi-xay-dung-gom-nhung-gi-tuyen-chon-20-97-vi-cb',
            'phong-thuy-nha-bep-nhung-dieu-can-biet-khi-dat-huong-nha-bep-5565187-vi-cb',
            'ban-ve-nha-pho-1-tret-1-lau-1-san-thuong-dien-tich-70m2-t03-2022-3467001-vi-cb',
            'vi-tri-dat-be-phot-trong-nha-hop-phong-thuy-ban-nen-biet-1466151-vi-cb',
            'cach-dat-be-nuoc-ngam-trong-nha-theo-phong-thuy-4666452-vi-cb',
            'nhung-sai-lam-chet-nguoi-khi-mua-nha-khong-xem-tuoi-vo-chong-con-cai-5365487-vi-cb',
            'mau-biet-thu-3-tang-mat-tien-8m-hien-dai-dep-khong-goc-chet-9067613-vi-cb',
            '6-mau-nha-biet-thu-2-tang-mai-thai-dep-o-nong-thon-1-3-ty-7367999-vi-cb',
            'tong-hop-nhung-mau-nha-chu-l-2-tang-70m2-dep-van-nguoi-me-8967567-vi-cb',
            'du-toan-chi-phi-xay-nha-2-tang-50m2-chi-tiet-noi-that-diem-nhan-1559953-vi-cb',
            'loi-phong-thuy-khi-bo-tri-nha-ve-sinh-duoi-gam-cau-thang-va-cach-hoa-9866177-vi-cb',
            'lay-tuoi-dan-ba-lam-nha-co-duoc-hay-khong-4565501-vi-cb',
            'top-15-mau-thiet-ke-nha-2-tang-70m2-dep-soul-concept-9967568-vi-cb',
            'giai-dap-thac-mac-co-nen-thiet-ke-ban-ve-nha-chu-l-2-tang-80m2-hay-khong-8067669-vi-cb',
            'top-10-xem-nhieu-nhat-phong-thuy-ho-ca-sau-nha-moi-nhat-10-2022-top-like-saigonhkphone-com-3266751-vi-cb',
            'mau-nha-3-gian-chu-l-mai-thai-hinh-chu-l-mat-bang-cong-trinh-nha-anh-binh-t05-2022-7767642-vi-cb',
            'be-phot-be-tu-hoai-nen-dat-o-dau-sao-cho-hop-phong-thuy-5466125-vi-cb',
            'co-nen-xay-nha-hinh-chu-l-khong-cach-hoa-giai-phong-thuy-8966302-vi-cb',
            'thiet-ke-biet-thu-3-tang-chu-l-mat-tien-7m-tai-binh-thuan-3567544-vi-cb',
            'mau-thiet-ke-nha-2-tang-7x12m-sang-trong-hien-dai-co-ban-ve-an-loc-9867605-vi-cb',
            'thiet-ke-ban-ve-nha-2-tang-mai-lech-85m2-bt119422-t06-2022-2967212-vi-cb',
            'ban-ve-thiet-ke-biet-thu-2-tang-hinh-chu-l-mai-thai-dep-co-4-phong-ngu-2022-t06-2022-8768060-vi-cb',
            'ban-ve-thiet-ke-bet-thu-2-tang-60m2-dep-nam-2018-kiesang-com-9667502-vi-cb',
            '5-mau-ban-ve-nha-cap-4-6x10m-dep-re-de-xay-dung-tap-doan-tran-anh-group-10066897-vi-cb',
            '20-mau-nha-cap-4-dep-2020-duoc-yeu-thich-hien-nay-nandesign-2363612-vi-cb',
            'rat-hay-10-mau-thiet-ke-biet-thu-2-tang-hien-dai-100m2-dep-me-man-an-loc-1068004-vi-cb',
            'xay-nha-tron-goi-400-trieu-khong-phat-sinh-chi-phi-9866028-vi-cb',
            'tieu-chuan-xay-be-nuoc-ngam-2022-9866446-vi-cb',
            'giai-ma-bi-mat-mau-nha-vuong-2-tang-hot-nhat-nam-2022-4267631-vi-cb',
            'mau-nha-20m2-xay-4-tang-khien-ai-cung-tram-tro-homemy-6768608-vi-cb',
            'biet-thu-2-tang-o-nong-thon-sieu-thi-nha-mau-8067658-vi-cb',
            'xay-nha-tron-goi-gia-nbsp-xay-nha-tron-goi-tp-hcm-2022-6866610-vi-cb',
            'vi-sao-nen-xay-nha-cap-4-do-mai-bang-sieu-thi-nha-mau-5365346-vi-cb',
            'tu-van-thiet-ke-nha-ong-ban-co-dien-3-tang-75m2-tai-quang-ninh-6567016-vi-cb',
            'tai-sao-nen-thue-giam-sat-9166038-vi-cb',
            '3-mau-biet-thu-2-tang-10m-x-12m-dep-tuyet-pham-kem-ban-ve-chi-tiet-3767951-vi-cb',
            'nen-xay-nha-vao-thang-may-la-hop-ly-nhat-7365707-vi-cb',
            'tu-thiet-ke-nha-hay-thue-kien-truc-su-bai-15-cong-ty-co-phan-xay-dung-va-noi-that-kinghouse-3364954-vi-cb',
            'biet-thu-mini-2-tang-60m2-4-phong-ngu-o-mien-que-dep-t05-2022-6366895-vi-cb',
            'co-nen-xay-nha-bang-tam-tuong-be-tong-sieu-nhe-duc-san-tai-ha-tinh-5465959-vi-cb',
            'mau-biet-thu-tan-co-dien-3-tang-dep-xuat-sac-o-hai-phong-1966855-vi-cb',
            'xem-huo-ng-nha-theo-tuo-i-cho-ng-hay-vo-6865470-vi-cb',
            '20-mau-nha-chu-l-dep-va-cach-hoa-giai-phong-thuy-nha-chu-l-vtkong-5966306-vi-cb',
            '30-mau-thiet-ke-nha-cap-4-5-phong-ngu-dang-de-dau-tu-nhat-2022-6867105-vi-cb',
            'xay-nha-chu-l-co-tot-khong-cach-hoa-giai-8566294-vi-cb',
            'chi-phi-xay-nha-nuoi-yen-3-tang-gia-re-be-tong-bao-phat-dinh-review-7466559-vi-cb',
            'xay-nha-bang-vat-lieu-nhe-xu-huong-moi-trong-nganh-xay-dung-7665920-vi-cb',
            '3-cach-hoa-giai-bep-nam-tren-be-phot-hieu-qua-ban-nen-ap-dung-2466154-vi-cb',
            '6-mau-biet-thu-2-tang-mai-lech-doc-dao-dep-loi-cuon-1067217-vi-cb',
            'mau-biet-thu-3-tang-180m2-tan-co-dien-voi-ve-dep-vuot-thoi-gian-6667348-vi-cb',
            '5-chon-vat-lieu-xay-dung-khi-xay-nha-1965605-vi-cb',
            'san-pham-giai-phap-moi-co-nen-xay-nha-bang-gach-khong-nung-6965968-vi-cb',
            'biet-thu-hien-dai-80m2-thiet-ke-dep-cong-nang-toi-uu-chi-phi-thap-7167059-vi-cb',
            'nen-xay-nha-moi-hay-sua-chua-cai-tao-nha-cu-chu-tung-thi-cong-9565006-vi-cb',
            'co-dat-rong-chon-xay-phong-tro-cho-thue-hay-ban-dat-tiep-tuc-dau-tu-kiem-loi-2166510-vi-cb',
            'hoa-giai-ao-sau-nha-dung-phong-thuy-giup-xua-tan-van-han-cho-gia-dinh-1665170-vi-cb',
            'nen-xay-nha-yen-may-tang-la-tot-nhat-2666533-vi-cb',
            'tong-hop-cac-mau-xay-nha-1-tang-60m2-dep-2022-4867499-vi-cb',
            'tong-hop-nhung-mau-nha-chu-l-dep-chuan-phong-thuy-gia-chu-nen-biet-tokyometro-3266299-vi-cb',
            'xay-nha-cap-4-chu-u-hoac-l-hop-mot-tiet-kiem-thi-khong-the-bo-qua-nhung-mau-nay-3766312-vi-cb',
            'ngam-nhin-sieu-biet-thu-3-tang-2-mat-tien-7m-kieu-phap-dep-tai-binh-dinh-t04-2022-6367533-vi-cb',
            'kham-pha-biet-thu-2-tang-mai-thai-100m2-tien-nghi-2022-1968006-vi-cb',
            'hinh-anh-nghe-nuoi-chim-yen-9566557-vi-cb',
            'mau-biet-thu-lau-dai-4-tang-1-tum-mat-tien-8m-kien-truc-tuyet-dep-1167610-vi-cb',
            'ho-ca-koi-san-vuon-truoc-va-sau-nha-phong-thuy-ho-ca-koi-truoc-nha-1-7866719-vi-cb',
            'ho-so-kien-truc-biet-thu-file-cad-full-ban-ve-chi-tiet-2022-2667923-vi-cb',
            'vat-lieu-san-nha-tam-nen-chon-chat-lieu-nao-homemas-3265627-vi-cb',
            '35-mau-thiet-ke-nha-2-tang-60m2-chi-phi-xay-nha-chi-tiet-5267506-vi-cb',
            'ban-ve-2-tang-1-tum-dep-biet-thu-mai-thai-hien-dai-ms-24085-1368032-vi-cb',
            'nha-mai-thai-1-tang-5-phong-ngu-lua-chon-hang-dau-cua-cac-gia-dinh-da-he-movic-1767099-vi-cb',
            'ban-ve-thiet-ke-nha-6x15m-nhung-goi-y-khong-the-bo-qua-tap-doan-tran-anh-group-8467084-vi-cb',
            'mau-biet-thu-dep-2-tang-80m2-tan-co-dien-tai-long-bien-hn-bt53-4367687-vi-cb',
            'mau-thiet-ke-biet-thu-2-tang-200m2-hinh-chu-u-doc-dao-bt2t131-t08-2022-8068649-vi-cb',
            'vat-lieu-xay-nha-gom-nhung-gi-luu-y-de-chuan-bi-xay-nha-rat-can-thiet-sieu-thi-nha-mau-6865586-vi-cb',
            'rat-hay-tu-van-thiet-ke-mau-nha-70m2-2-tang-dep-hien-dai-2022-an-loc-5167580-vi-cb',
            'cach-bo-tri-be-nuoc-va-be-phot-trong-nha-phong-thuy-tranh-dai-ky-5066465-vi-cb',
            '20-thiet-ke-phong-bep-20m2-neu-bo-lo-se-hoi-tiec-ca-doi-6068583-vi-cb',
            'bi-quyet-giam-sat-cong-trinh-danh-cho-chu-nha-khong-biet-gi-ve-xay-dung-nha-cua-minh-8666042-vi-cb',
            'khi-xay-nha-co-nen-xay-tang-lung-thiet-ke-nha-dep-bien-hoa-dong-nai-9265992-vi-cb',
            'thi-cong-nha-tron-goi-bao-gia-thi-cong-tron-goi-cu-the-4665537-vi-cb',
            'chuan-bi-vat-lieu-xay-nha-vat-lieu-xay-nha-bao-gom-nhung-gi-cong-ty-tnhh-adal-home-9365582-vi-cb',
            'cach-bo-tri-dat-nha-ve-sinh-chuan-hop-phong-thuy-va-dieu-can-tranh-6166695-vi-cb',
            'ban-ve-thiet-ke-nha-biet-thu-2-tang-mai-nhat-8m-x-12m-autocad-sketchup-7367953-vi-cb',
            'tu-van-thiet-ke-nha-dep-2-tang-1-tum-3-phong-ngu-tren-dien-tich-45m2-9566851-vi-cb',
            'nuoi-yen-tu-phat-coi-chung-trang-tay-bao-nguoi-lao-dong-1766561-vi-cb',
            'giai-dap-co-nen-xay-nha-thep-tien-che-de-o-hay-khong-8966428-vi-cb',
            'mau-nha-biet-thu-2-tang-hien-dai-dien-tich-70m2-tai-binh-duong-6666992-vi-cb',
            'ban-ve-cad-biet-thu-2-tang-hoan-chinh-7767932-vi-cb',
            'nha-bao-nhieu-m2-nen-xay-lech-tang-6165775-vi-cb',
            'tong-hop-cac-ban-ve-mong-nha-cap-4-thong-dung-nhat-tap-doan-tran-anh-group-9564187-vi-cb',
            'mau-biet-thu-mini-2-tang-hien-dai-dien-tich-90m2-dep-gia-re-tai-ha-nam-6967791-vi-cb',
            'cac-mau-biet-thu-1-tang-4-phong-ngu-sieu-thi-nha-mau-1167174-vi-cb',
            'mua-nha-chuyen-nha-xem-tuoi-chong-hay-vo-thi-chuan-1265473-vi-cb',
            'tim-hieu-xem-xay-nha-khung-thep-co-re-khong-vlxd-hiep-ha-9066406-vi-cb',
            'hoa-giai-bep-dat-tren-be-phot-va-nhung-vi-tri-dat-be-phot-chuan-phong-thuy-3966150-vi-cb',
            'duoc-va-mat-khi-dau-tu-phong-tro-cho-thue-vnexpress-kinh-doanh-8066489-vi-cb',
            '35-mau-thiet-ke-biet-thu-nha-vuon-tren-dat-200m2-dep-2022-3368661-vi-cb',
            'mau-thiet-ke-biet-thu-mini-2-tang-100m2-9268002-vi-cb',
            '5-mau-xay-nha-bang-vat-lieu-nhe-an-toan-tiet-kiem-chi-phi-3665896-vi-cb',
            'nen-sua-nha-hay-xay-nha-moi-khi-ngoi-nha-bi-xuong-cap-xd-quang-minh-7165081-vi-cb',
            'mau-thiet-ke-ban-ve-nha-pho-2-tang-1-tum-hien-dai-2020-3168023-vi-cb',
            'mau-nha-biet-thu-2-tang-mai-bang-theo-phong-cach-hien-dai-aht-homes-tu-van-thiet-ke-thi-cong-nha-dep-xay-dung-nha-tron-goi-1867913-vi-cb',
            'chi-phi-xay-nha-yen-100m2-cho-2-3-4-tang-bao-nhieu-9866565-vi-cb',
            '10-mau-ban-ve-thiet-ke-biet-thu-2-tang-dep-me-ly-2021-9668073-vi-cb',
            'kts-tu-van-thiet-ke-ban-ve-thiet-ke-nha-2-tang-8x15m-dep-mat-uy-nghi-3867636-vi-cb',
            'nha-lech-tang-la-gi-co-nen-xay-nha-lech-tang-hay-khong-7765797-vi-cb',
            'nha-1-tang-5-phong-ngu-dep-gia-re-o-nong-thon-1867104-vi-cb',
            'mau-thiet-ke-nha-ong-75m2-hien-dai-gia-re-dang-hot-hien-nay-5167027-vi-cb',
            'co-nen-lam-ho-ca-canh-phia-sau-nha-khong-2166728-vi-cb',
            'kinh-nghiem-thiet-ke-mau-nha-chu-l-1-tang-co-gac-lung-tien-nghi-tiet-9566317-vi-cb',
            'bat-mi-kinh-nghiem-giam-sat-cong-trinh-nha-o-cuc-huu-ich-cho-gia-chu-nha-dep-online-9766066-vi-cb',
            '10-mau-nha-cap-4-gac-lung-dep-gia-re-200-300-trieu-5161465-vi-cb',
            'nen-xay-nha-vao-thang-may-de-thuan-loi-va-may-man-nhat-4265667-vi-cb',
            'giai-dap-thac-mac-co-nen-xay-nha-lech-tang-hay-khong-6665767-vi-cb',
            'cach-hoa-giai-phong-tho-dat-tren-hoac-doi-dien-nha-ve-sinh-chuyen-gia-tu-van-1866671-vi-cb',
            'mau-biet-thu-vuon-phong-cach-nhat-thiet-ke-nha-dep-7267707-vi-cb',
            'ngam-biet-thu-tan-co-dien-3-tang-10x20-dep-long-lay-kieu-sa-8068122-vi-cb',
            'nguyen-tac-bo-tri-phong-tho-gac-lung-chuan-phong-thuy-1366704-vi-cb',
            'huong-dan-cach-lam-nha-nuoi-yen-don-gian-hieu-qua-dung-ky-thuat-cop-pha-viet-5066569-vi-cb',
            'tam-cemboard-vat-lieu-nhe-xay-nha-kinh-nghiem-lam-nha-ban-nen-biet-8365902-vi-cb',
            '8-cach-hoa-giai-nha-ve-sinh-duoi-gam-cau-thang-phong-thuy-3666189-vi-cb',
            'c-n-n-thu-x-y-nh-tron-g-i-ch-a-kh-a-trao-tay-kh-ng-8866602-vi-cb',
            'nha-lech-tang-cam-nang-nhung-thong-tin-can-biet-truoc-khi-xay-bat-dong-san-phu-dong-1665804-vi-cb',
            'nen-dung-xi-mang-de-xay-dung-phan-tho-nha-o-loai-nao-la-tot-constar-4564596-vi-cb',
            'mau-nha-dep-2-tang-mai-thai-sang-trong-hot-nhat-nam-2021-1468604-vi-cb',
            'mau-thiet-ke-nha-pho-5-tang-dep-tai-bac-ninh-dien-tich-90m2-3966945-vi-cb',
            'tu-van-thiet-ke-nha-ong-cho-gia-dinh-4-nguoi-vua-dep-vua-thoang-9366231-vi-cb',
            '15-mau-nha-cap-4-3-phong-ngu-1-phong-tho-dep-va-tien-nghi-an-loc-7664898-vi-cb',
            'xay-dung-nha-goi-chia-khoa-trao-tay-co-loi-khong-2665526-vi-cb',
            'ban-ve-nha-cap-4-co-gac-lung-mai-lech-hien-dai-tai-hung-yen-ndbt2t159-10067215-vi-cb',
            'top-50-mau-thiet-ke-nha-ve-sinh-duoi-gam-cau-thang-nha-ong-dep-kien-thiet-viet-10066212-vi-cb',
            'ban-ve-chi-tiet-thiet-ke-biet-thu-200m2-voi-2-mat-tien-tp-thu-duc-4368635-vi-cb',
            'sau-nha-co-ao-nuoc-co-tot-khong-cach-hoa-giai-the-nao-7366733-vi-cb',
            'mau-thiet-ke-biet-thu-3-tang-1-tum-hien-dai-bt-36158-katahome-7067406-vi-cb',
            '9-mau-biet-thu-2-tang-90m2-dep-cong-nang-toi-uu-nhieu-nguoi-thich-rem-manh-dep-6068589-vi-cb',
            'ham-tu-hoai-nen-xay-dung-o-dau-la-hop-ly-5566132-vi-cb',
            'mau-biet-thu-2-tang-hien-dai-mai-bang-tien-nghi-2022-1667889-vi-cb',
            'rat-hay-cac-mau-biet-thu-2-tang-3-phong-ngu-sieu-thi-nha-mau-7666951-vi-cb',
            'be-phot-nen-dat-o-dau-nhung-vu-tri-thuong-dat-be-phot-nhat-5366144-vi-cb',
            'goc-giai-dap-xi-mang-nao-tot-nhat-hien-nay-vat-lieu-an-vinh-6564660-vi-cb',
            'khac-biet-giua-du-an-ban-dat-nen-va-du-an-ban-nha-theo-mau-xay-san-quy-hoach-du-an-mua-ban-cho-thue-nha-dat-can-ho-dat-nen-va-van-phong-7564794-vi-cb',
            'nhung-y-tuong-thiet-ke-phong-ngu-20m2-co-toilet-doc-dao-la-mat-1268603-vi-cb',
            '8-ban-ve-cad-nha-2-tang-7x12m-dep-hien-dai-tien-nghi-4267592-vi-cb',
            'dau-tu-kinh-doanh-nha-tro-duoc-va-mat-gi-trong-thoi-diem-hien-tai-7666512-vi-cb',
            'toi-mua-nha-xay-san-tuong-re-ma-hoa-dat-vnexpress-doi-song-5065288-vi-cb',
            'vi-tri-dat-be-phot-trong-nha-ong-hop-phong-thuy-7866106-vi-cb',
            'chiem-nguong-biet-thu-2-tang-200m2-hien-dai-sang-trong-tai-da-nang-roman-4268645-vi-cb',
            'tu-van-thiet-ke-mau-nha-70m2-2-tang-dep-hien-dai-2022-an-loc-9467566-vi-cb',
            'tu-van-thiet-ke-kien-truc-nha-pho-4-tang-dien-tich-60m2-cho-4-nguoi-sinh-song-9466916-vi-cb',
            '6-cach-chong-am-nen-nha-de-ap-dung-hieu-qua-cao-cong-ty-phuong-dong-9565631-vi-cb',
            'ban-ve-thiet-ke-nha-30m2-dep-hien-dai-thong-thoang-7466701-vi-cb',
            'suu-tam-ban-ve-thiet-ke-biet-thu-mini-2-tang-hoan-chinh-nhat-9867063-vi-cb',
            'mau-biet-thu-2-tang-1-tum-150m2-kieu-phap-sang-trong-6868056-vi-cb',
            '10-ban-ve-mat-bang-phong-ngu-master-20m2-30m2-40m2-1268572-vi-cb',
            'xay-phong-tro-dien-tich-bao-nhieu-la-phu-hop-va-tiet-kiem-chi-phi-nhat-3266266-vi-cb',
            'mach-ban-bi-kip-thiet-ke-phong-bep-30m2-dep-thu-hut-tai-loc-2366713-vi-cb',
            'con-ai-khac-chua-biet-nha-lech-tang-la-gi-sieu-thi-nha-mau-1765764-vi-cb',
            '6-mau-nha-ngang-dep-cap-4-1-tang-5-phong-ngu-2022-2023-4767091-vi-cb',
            'van-nan-mua-nha-xay-san-cua-cac-chu-dau-tu-6165274-vi-cb',
            'mau-biet-thu-2-tang-mai-nhat-tan-co-dien-voi-san-vuon-dep-mat-4567793-vi-cb',
            'tong-hop-cac-loai-xi-mang-va-cach-chon-xi-mang-tot-nhat-de-xay-nha-8464591-vi-cb',
            'thiet-ke-biet-thu-2-tang-mai-thai-30-mau-ban-ve-phoi-canh-dep-2022-1167947-vi-cb',
            'mau-thiet-ke-nha-biet-thu-1-tang-80m2-dep-kien-sang-4867680-vi-cb',
            'xay-nha-tro-voi-so-von-0-dong-9-nam-sau-thu-lai-nha-khong-mot-dong-no-va-bai-toan-cho-thue-cua-nguoi-lam-kinh-doanh-7266519-vi-cb',
            'kham-pha-cong-trinh-biet-thu-co-dien-2-tang-10x20-dep-me-ly-6468105-vi-cb',
            'biet-thu-lau-dai-4-tang-1-tum-thiet-ke-kieu-co-dien-rong-530m2-5067412-vi-cb',
            'vnt-co-bat-buoc-phai-thue-tu-van-giam-sat-khi-xay-dung-cong-trinh-5866061-vi-cb',
            'thiet-ke-biet-thu-2-tang-hien-dai-150m2-nha-mat-pho-7268058-vi-cb',
            'sua-nha-co-can-xem-ngay-khong-giai-dap-chi-tiet-nhat-4465093-vi-cb',
        ];

        $list_url = array();
        foreach ($list_bai_viet as $bv){;
            $post = DB::table('hwp_posts')
                ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
//                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                ->where('post_name', '=',$bv)
//                    ->where('post_content', '!=', '')
                ->first();
            if (!empty($post)){
                $list_url[] = $post;
            }

        }
        return json_encode($list_url);
//        return response()->download($fileStorePath);
    }
}
