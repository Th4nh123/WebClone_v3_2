<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public static function home_wiki($url_key,$id_key)
    {

//        $domain = "https://thuvienphapluat.vn/van-ban/Xay-dung-Do-thi/Thong-tu-06-2017-TT-BXD-huong-dan-hoat-dong-thi-nghiem-chuyen-nganh-xay-dung-348512.aspx";
//        $last=strpos($domain,"/",8);
//
//        dd(substr($domain,0,$last));
        $key = DB::table('hwp_key')->select('hwp_key.id', 'ten', 'tien_to', 'hau_to','ky_hieu', 'url_key_cha', 'id_cam','hwp_campaign.language')
            ->join('hwp_campaign','hwp_campaign.id','=','hwp_key.id_cam')
            ->where('url_key_cha', '=', $url_key)
            ->where('hwp_key.id','=',$id_key)->first();

        if (!empty($key->ky_hieu)){
            return $this->home_wiki_new($url_key,$id_key);
        }
        $video = DB::table('hwp_video')->where('hwp_video.id_key','=',$key->id)->first();

        if (!empty($video)){
            $video->link = str_replace('watch?v=','embed/',$video->link);
        }


        $post_detail = DB::table('hwp_posts')
            ->select('hwp_posts.post_author', 'hwp_url.url', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
            ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
            ->where('url_key_cha', '=', $url_key)
            ->where('twitter_image', 'not like', 'https://www.facebook.com%')
//                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
            ->orderBy('hwp_posts.id', 'asc')
            ->distinct()
            ->get()->toArray();

        $list_key = DB::table('hwp_key')
            ->where('check', '=', 1)
            ->where('id_cam', '=', $key->id_cam)->get()->toArray();
        $count = 0;
        foreach ($list_key as $k) {
            $count++;
            if ($key->id == $k->id) {
                break;
            }
        }


        $list_img = array();
        foreach ($list_key as $k) {
            $post_key = DB::table('hwp_posts')
                ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                    'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                    , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
                ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
                ->where('hwp_key.check', '=', 1)
                ->where('id_key', '=', $k->id)->first();
            if (!empty($post_key->twitter_image)) {

                $list_img[] = $post_key->twitter_image;
            }
        }


//        foreach ($post_detail as $post) {
//            if ($post->menu_order == 0) {
//                $contentH3_1 = str_replace('<h3', '<h4', $post->post_content);
//                $contentH3_2 = str_replace('/h3>', '/h4>', $contentH3_1);
//                $contentH2_1 = str_replace('<h2', '<h3', $contentH3_2);
//                $contentH2_2 = str_replace('/h2>', '/h3>', $contentH2_1);
//                $markupFixer = new \TOC\MarkupFixer();
//                $contentWithMenu = $markupFixer->fix($contentH2_2);
//                DB::table("hwp_posts")->where('ID', '=', $post->ID)->update([
//                    'post_content' => $contentWithMenu,
//                    'menu_order' => 1
//                ]);
//            }
//        }
        // Chủ đề nổi bật
        $select_list_chu_de_noi_bat = DB::table('hwp_hw_trending')
            ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
            ->where('post_type', '=', 'post')
            ->groupBy('post_id')
            ->orderBy('count', 'desc')
            ->limit(16)
            ->get()->toArray();


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
        if ($key->language == 'Vietnamese'){
            return view('top_list.top_list_wiki', compact('list_chu_de_noi_bat', 'key', 'post_detail', 'count', 'list_key', 'list_img','video'));
        }elseif($key->language == 'English'){
            return view('top_list.top_list_twiki_en', compact('list_chu_de_noi_bat', 'key', 'post_detail', 'count', 'list_key', 'list_img','video'));
        }
    }

    public function home_wiki_new($url_key,$id_key)
    {

//        $domain = "https://thuvienphapluat.vn/van-ban/Xay-dung-Do-thi/Thong-tu-06-2017-TT-BXD-huong-dan-hoat-dong-thi-nghiem-chuyen-nganh-xay-dung-348512.aspx";
//        $last=strpos($domain,"/",8);
//
//        dd(substr($domain,0,$last));
//        try {
//        $imageUrls = ImageSpider::find('https://covid19.sccgov.org/Documents/Mandatory-Directives-Construction-Projects-vi.pdf');
//        dd($imageUrls);

        $key = DB::table('hwp_key')->select('hwp_key.id', 'ten', 'tien_to', 'hau_to', 'url_key_cha', 'id_cam','hwp_campaign.language')
            ->join('hwp_campaign','hwp_campaign.id','=','hwp_key.id_cam')
            ->where('url_key_cha', '=', $url_key)
            ->where('hwp_key.id','=',$id_key)->first();


        $list_url = DB::table('hwp_url')
            ->select('hwp_posts.post_author', 'hwp_url.url','hwp_url.url_title', 'hwp_url.url_image','hwp_url.url_description','hwp_url.ky_hieu', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
            ->leftJoin('hwp_posts','hwp_url.id','=','hwp_posts.id_url')
            ->leftJoin('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->leftJoin('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
            ->where('hwp_url.id_key','=',$id_key)
            ->where('hwp_url.check','=',1)
            ->orderBy('hwp_url.stt','asc')->get()->toArray();
//        dd($list_url);
//
//        $post_detail = DB::table('hwp_posts')
//            ->select('hwp_posts.post_author', 'hwp_url.url', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
//                'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
//                , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
//            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
//            ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
//            ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
//            ->where('url_key_cha', '=', $url_key)
//            ->where('twitter_image', 'not like', 'https://www.facebook.com%')
////                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
//            ->orderBy('hwp_posts.id', 'asc')
//            ->distinct()
//            ->get()->toArray();


        $list_key = DB::table('hwp_key')
            ->where('check', '=', 1)
            ->where('id_cam', '=', $key->id_cam)
            ->whereBetween('id',[$id_key-16,$id_key-1])
            ->get()->toArray();

        $list_img = array();
        foreach ($list_key as $k) {
            $url = DB::table('hwp_url')
                ->where('id_key', '=', $k->id)
                ->where('ky_hieu' ,'!=','y')
                ->where('url_image','!=', '')->first();
            if (!empty($url->url_image)) {
                $list_img[] = $url->url_image;
            }
        }


//        foreach ($post_detail as $post) {
//            if ($post->menu_order == 0) {
//                $contentH3_1 = str_replace('<h3', '<h4', $post->post_content);
//                $contentH3_2 = str_replace('/h3>', '/h4>', $contentH3_1);
//                $contentH2_1 = str_replace('<h2', '<h3', $contentH3_2);
//                $contentH2_2 = str_replace('/h2>', '/h3>', $contentH2_1);
//                $markupFixer = new \TOC\MarkupFixer();
//                $contentWithMenu = $markupFixer->fix($contentH2_2);
//                DB::table("hwp_posts")->where('ID', '=', $post->ID)->update([
//                    'post_content' => $contentWithMenu,
//                    'menu_order' => 1
//                ]);
//            }
//        }
        // Chủ đề nổi bật
        $select_list_chu_de_noi_bat = DB::table('hwp_hw_trending')
            ->select('post_id', DB::raw('count(hwp_hw_trending.post_id) as count'))
            ->where('post_type', '=', 'post')
            ->groupBy('post_id')
            ->orderBy('count', 'desc')
            ->limit(16)
            ->get()->toArray();


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
        if ($key->language == 'Vietnamese'){
            return view('top_list.top_list_wiki_new', compact('list_chu_de_noi_bat', 'key', 'list_url', 'list_key', 'list_img'));
//        }elseif($key->language == 'English'){
//            return view('top_list.top_list_twiki_en', compact('list_chu_de_noi_bat', 'key', 'post_detail', 'count', 'list_key', 'list_img','video'));
        }
//        } catch (\Exception $e) {
//           return abort(404);
//        }

    }
}
