<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HwpKey;
use App\Models\HwpPost;
use App\Models\HwpTerm;
use App\Models\HwpUrl;
use App\Models\HwpYoastIndexable;
use Illuminate\Http\Request;

class XuatDataController extends Controller
{
    public function export_txt_wiki($from, $to, $id_cam)
    {
        $list_bai_viet = array();
        if ($to - $from >= 0) {
            $list_key = HwpKey::where("id_cam", '=', $id_cam)
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->get()->toArray();

            //chèn bài tổng hợp

            $i = 0;
            $vi_tri = 0;
            foreach ($list_key as $th) {
                if ($th->check == 1) {
                    $bai_th = array();
                    $title_th = $th->tien_to . " " . $th->ten . " " . $th->hau_to;
                    $th->url_key_cha = "https://thoitrangwiki.com/" . $th->url_key_cha . ".html";
                    //                $bai_th = array_add($bai_th,"Title",$title_th);
                    //                $bai_th = array_add($bai_th,"URL",$th->url_key_cha);
                    $bai_th[$i]["Title"] = $title_th;
                    $bai_th[$i]["URL"] = $th->url_key_cha;
                    $list_bai_viet[] = $bai_th[$i];
                    $i++;

                    HwpPost::query()
                        ->where('id_key', '=', $th->id)->delete();
                    HwpKey::query()->where('id', '=', $th->id)->update(['check' => false]);
                }
            }
        }


        return $list_bai_viet;
    }

    public function createTH_wiki_new($from, $to, $id_cam, $chon)
    {
        if ($to - $from >= 0) {
            $list_key = HwpKey::where("id_cam", '=', $id_cam)
                ->join('hwp_campaign', 'hwp_campaign.id', '=', 'hwp_key.id_cam')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->get()->toArray();
        }
        foreach ($list_key as $key) {

            if ($key->check == 1) {
                $title = $key->tien_to . ' ' . $key->ten . ' ' . $key->hau_to;
                //                DB::table('hwp_posts')->where('post_title', '=', $title)->delete();
                //                DB::table('hwp_yoast_indexable')->where('breadcrumb_title', '=', $title)->delete();
                //                $video = DB::table('hwp_video')->where('id_key', '=', $key->id)->first();
                //                if (!empty($video)) {
                //                    $video->link = str_replace('watch?v=', 'embed/', $video->link);
                //                }
                $post_detail = HwpUrl::query()
                    ->select(
                        'hwp_posts.post_author',
                        'hwp_urls.url',
                        'hwp_urls.url_title',
                        'hwp_urls.url_image',
                        'hwp_urls.url_description',
                        'hwp_urls.ky_hieu',
                        'hwp_posts.ID',
                        'hwp_posts.post_date',
                        'hwp_posts.post_content',
                        'hwp_posts.post_title',
                        'hwp_posts.post_view',
                        'hwp_posts.post_name',
                        'hwp_posts.menu_order',
                        'hwp_yoast_indexables.twitter_image',
                        'hwp_yoast_indexables.permalink',
                        'hwp_yoast_indexables.description',
                        'hwp_yoast_indexables.breadcrumb_title',
                        'hwp_yoast_indexables.primary_focus_keyword',
                        'hwp_yoast_indexables.meta_robot'
                    )
                    ->leftJoin('hwp_posts', 'hwp_urls.id', '=', 'hwp_posts.id_url')
                    ->leftJoin('hwp_yoast_indexables', 'hwp_yoast_indexables.object_id', '=', 'hwp_posts.id')
                    ->leftJoin('hwp_keys', 'hwp_keys.id', '=', 'hwp_posts.id_key')
                    ->where('hwp_urls.id_key', '=', $key->id)
                    ->where('hwp_urls.check', '=', 1)
                    ->orderBy('hwp_urls.stt', 'asc')->get()->toArray();
                if (!empty($post_detail)) {
                    if ($key->language == 'Vietnamese') {
                        $check = 0;
                        $link_img = '';
                        //                        $link_source_img = array();
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                if (!empty($post_detail[$i]->twitter_image)) {
                                    $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->twitter_image . '"/>';
                                    $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->twitter_image . '"/>';
                                } else {
                                    $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url_image . '"/>';
                                    $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url_image . '"/>';
                                }
                            } elseif ($post_detail[$i]->ky_hieu == 'i') {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url . '"/>';
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {
                            } else {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url_image . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url_image . '"/>';
                            }
                        }

                        //                        MUC LUC
                        $content = '<div class="entry-content"><p>Cập nhật thông tin và kiến thức về <b>';
                        if ($chon == 1 && is_numeric($chon)) {
                            $content .= '<a href="https://xaydungso.vn/' . $key->url_key_cha . '.html">' . $key->ten . '</a>';
                        } else {
                            $content .= '<a href="https://thoitrangwiki.com/' . $key->url_key_cha . '.html">' . $key->ten . '</a>';
                        }
                        $content .= '</b> chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên.</p><div id="toc_container"><p class="toc_title">MỤC LỤC</p><ul>';
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                if (!empty($post_detail[$i]->twitter_image)) {
                                    $content .= '<li><a href="#' . $i . '">' . $post_detail[$i]->post_title . '</a></li>';
                                } else {
                                    $content .= '<li><a href="#' . $i . '">' . $post_detail[$i]->url_title . '</a></li>';
                                }
                            } elseif ($post_detail[$i]->ky_hieu == 'i' && $check == 0) {
                                $content .= '<li><a href="#danh-sach-img"><b>IMAGE: </b>Hình ảnh cho ' . $key->ten . '</a></li>';
                                $check++;
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {
                                $content .= '<li><a href="#' . $i . '"><b>YOUTUBE: </b>' . $post_detail[$i]->url_title . '</a></li>';
                            } elseif ($post_detail[$i]->ky_hieu != 'i') {
                                $content .= '<li><a href="#' . $i . '"><b>Download
                                            file ' . strtoupper($post_detail[$i]->ky_hieu) . ': </b>' . $post_detail[$i]->url_title . '</a></li>';
                            }
                            //                            $tocGenerator = new \TOC\TocGenerator();
                            //                    $htmlOut = $tocGenerator->getHtmlMenu($post_detail[$i]->post_content, 2, 2);
                            //                    $content .= $htmlOut;
                        }

                        //                       Ảnh đại diện
                        $content .= '</ul></div>';
                        // $content .= '<p style="font-size: 30px">Kết quả tìm kiếm Google: <b>' . $key->ten . '</b></p>';
                        //                        $content .='<img src="' . $post_detail[0]->twitter_image . '"
                        //                                   alt="' . $post_detail[0]->post_title . '"
                        //                                style="width: 760px;">';
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu != 'y' && $post_detail[$i]->ky_hieu != 'i') {
                                $content .= '<img src = "' . $post_detail[$i]->url_image . '"
                                     alt = "' . $post_detail[$i]->url_title . '" style = "width: 760px; height: 500px;">';
                                break;
                            }
                        }
                        for ($i = 0; $i < count($post_detail); $i++) {
                            $last = strpos($post_detail[$i]->url, "/", 8);
                            $domain = substr($post_detail[$i]->url, 0, $last);
                            $random = rand(1000, 5000);
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                if (!empty($post_detail[$i]->twitter_image)) {
                                    $content .= '<hr style="border-top: 2px solid black; margin: 30px 0">
                                                   <div class="post_content">';
                                    $content .= '<h2 id="' . $i . '" class="post_title"><a
                                        href="' . $post_detail[$i]->url . '"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">' . $post_detail[$i]->post_title . '</a></h2>';
                                    $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';

                                    $content .= '<div class="d-flex flex-direc">';
                                    $content .= '<p class="" style="line-height: 25px; width: 100%">' . $post_detail[$i]->description . '...' . '</p>
                                   <img class="post_img" src="' . $post_detail[$i]->twitter_image . '" alt="' . $key->ten . '" style="object-fit:cover; margin-right: 20px;" width="380px" >
                                   </div>
                                   </div>';
                                } else {
                                    $content .= '<hr style="border-top: 2px solid black; margin: 30px 0">
                                                <div class="post_content">';
                                    $content .= '<h2 id="' . $i . '" class="post_title"><a
                                        href="' . $post_detail[$i]->url . '"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">' . $post_detail[$i]->url_title . '</a></h2>';
                                    $content .=  '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';

                                    $content .= '<div class="d-flex flex-direc">';
                                    $content .= '<p class="" style="line-height: 25px; width: 100%">' . $post_detail[$i]->url_description . '...' . '</p>
                                   <img class="post_img" src="' . $post_detail[$i]->url_image . '" alt="' . $key->ten . '" style="object-fit:cover; margin-right: 20px;" width="380px" >
                                   </div>
                                   </div>';
                                }
                            } elseif ($post_detail[$i]->ky_hieu == 'i') {
                                if ($check == 1) {
                                    $content .= '<h2 id="danh-sach-img" style="font-size: 30px">Hình ảnh cho ' . $key->ten . ' :</h2>';
                                    $check++;
                                }
                                $content .= '<div class="post_content" style="margin: 40px 0">
                                                <img src="' . $post_detail[$i]->url_image . '"
                                                alt="' . $post_detail[$i]->url_title . '"
                                                style = "width: 760px; height: 500px;">
                                                <p class="" style=" margin-top: 5px"><a
                                        href="' . $post_detail[$i]->url . '"
                                        target="_blank"
                                        style="font-size: 20px;"
                                        rel="noopener,nofollow">' . $post_detail[$i]->url_title . '</a></p></div>';
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {
                                $post_detail[$i]->url = str_replace('watch?v=', 'embed/', $post_detail[$i]->url);
                                $content .= '<hr style="border-top: 2px solid black; margin: 30px 0">
                                               <div class="post_content" >
                            <h2 id="' . $i . '" class="post_title">' . $post_detail[$i]->url_title . '</h2>
                            <p>' . $post_detail[$i]->url_description . '</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="' . $post_detail[$i]->url . '">
                            </iframe>
                        </div>

                        </div>';
                            } else {
                                $content .= '<hr style="border-top: 2px solid black; margin: 30px 0">
                                             <div class="post_content" >';
                                $content .= '<h2 id="' . $i . '" class="post_title"><img src="https://cdn.pixabay.com/photo/2016/03/31/14/47/download-1292814__480.png" style="width: 30px; height: 30px; margin-right: 10px" alt="icon-arrow">
                                         <a href="' . $post_detail[$i]->url . '"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">Download
                                            file ' . strtoupper($post_detail[$i]->ky_hieu) . ':' . $post_detail[$i]->url_title . '</a></h2>';
                                $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';

                                $content .= '<div class="d-flex flex-direc">';
                                $content .= '<p class="" style="line-height: 25px; width: 100%">' . $post_detail[$i]->url_description . '...' . '</p>
                                   <img class="post_img" src="' . $post_detail[$i]->url_image . '" alt="' . $key->ten . '" style="object-fit:cover; margin-right: 20px;" width="380px">
                                   </div>
                                   </div>';
                            }


                            if (($i + 1) % 5 == 0 && $i > 0) {
                                $content .= '<p class="text-center">_HOOK_</p>';
                            }
                        }


                        $content .= '</div>';


                        $check = HwpPost::query()
                            ->select("post_name")
                            ->where("post_name", '=', $key->url_key_cha)
                            ->where('id_key', '=', $key->id)->first();
                        if (!empty($post_detail) && empty($check)) {
                            HwpPost::query()->insert([
                                'post_title' => $title,
                                'post_content' => $content,
                                'post_author' => 1,
                                'post_name' => $key->url_key_cha,
                                'post_date' => date('y-m-d h:i:s'),
                                'post_date_gmt' => date('y-m-d h:i:s'),
                                'post_modified' => date('y-m-d h:i:s'),
                                'post_modified_gmt' => date('y-m-d h:i:s'),
                                'post_excerpt' => "",
                                'to_ping' => "",
                                'pinged' => "",
                                'post_content_filtered' => "",
                                'post_type' => "post",
                                'post_status' => "publish",
                                'id_key' => $key->id
                            ]);

                            $post = HwpPost::query()->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Cập nhật thông tin và kiến thức về ' . $key->ten . ' chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên.';
                            //                            for ($i = 0; $i < count($post_detail); $i++) {
                            //                                if (!empty($post_detail[$i]->url_image)){
                            //                                    $post_img = $post_detail[$i]->url_image;
                            //                                    break;
                            //                                }
                            //                            }
                            HwpYoastIndexable::query()->insert([
                                'object_id' => $post->ID,
                                'object_type' => 'post',
                                'object_sub_type' => 'post',
                                'author_id' => 1,
                                'description' => $description,
                                'breadcrumb_title' => $title,
                                'post_status' => 'publish',
                                'created_at' => date('y-m-d h:i:s'),
                                'updated_at' => date('y-m-d h:i:s'),
                                'twitter_image' => $post_detail[0]->url_image,
                                'twitter_description' => $link_img,
                                'primary_focus_keyword' => '',
                                'meta_robot' => 'index,follow',
                                'permalink' => "",
                                'permalink_hash' => ''
                            ]);
                            foreach ($post_detail as $post_id) {
                                HwpPost::query()->where('ID', '=', $post_id->ID)->update([
                                    'comment_count' => 1
                                ]);
                                //                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
                                //                            'description'=>''
                                //                        ]);;
                            }
                        } else {
                            HwpPost::query()->where("post_name", '=', $key->url_key_cha)
                                ->where('id_key', '=', $key->id)->update([
                                    'post_content' => $content
                                ]);
                        }
                    } elseif ($key->language == 'English') {
                        $link_img = '';
                        //                        $link_source_img = array();
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->twitter_image . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->twitter_image . '"/>';
                                //                                $link_source_img[] = $post_detail[$i]->twitter_image;
                            } elseif ($post_detail[$i]->ky_hieu == 'i') {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url . '"/>';
                                //                                $link_source_img[] = $post_detail[$i]->url;
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {
                            } else {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url_image . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url_image . '"/>';
                                //                                $link_source_img[] = $post_detail[$i]->url_image;
                            }
                        }
                        //                        $link_source_img = json_encode($link_source_img);
                        $content = '<div class="entry-content"><p>Update information and knowledge about <b><a href="https://thoitrangwiki.com/' . $key->url_key_cha . '.html">' . $key->ten . '</a></b> detailed and complete, this article is a topic of great interest, compiled by the editorial team of Thoitrangwiki.</p><div id="toc_container"><p class="toc_title">TABLE OF CONTENT</p><ul>';
                        for ($i = 0; $i < count($post_detail); $i++) {
                            $content .= '<li><a href="#' . $post_detail[$i]->post_name . '">' . $post_detail[$i]->post_title . '</a></li>';

                            $tocGenerator = new \TOC\TocGenerator();
                            //                    $htmlOut = $tocGenerator->getHtmlMenu($post_detail[$i]->post_content, 2, 2);
                            //                    $content .= $htmlOut;
                            if ($i == 4 && !empty($video)) {
                                $content .= '<li>
                                    <a href="#' . $i . '"><b>YOUTUBE: </b>' . $video->video_title . '</a>
                                </li>';
                            }
                        }


                        $content .= '</ul></div>';
                        //                        $content .= '<p style="font-size: 25px">Google search result: <b>' . $key->ten . '</b></p>
                        //                                <img src="' . $post_detail[0]->twitter_image . '"
                        //                                   alt="' . $post_detail[0]->post_title . '"
                        //                                width="760px" height="500px"';
                        $content .= '<p style="font-size: 30px">Google search result: <b>' . $key->ten . '</b></p>';
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                $content .= '<img src = "' . $post_detail[$i]->twitter_image . '"
                                     alt = "' . $post_detail[$i]->url_title . '" style= "width : 760px; height: 500px;">';
                                break;
                            }
                        }
                        for ($i = 0; $i < count($post_detail); $i++) {
                            $last = strpos($post_detail[$i]->url, "/", 8);
                            $domain = substr($post_detail[$i]->url, 0, $last);
                            $random = rand(1000, 5000);
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {

                                $content .= '<blockquote style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:' . 'Gotham SSm A' . ',' . 'Gotham SSm B' . ',' . ' Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">' .
                                    '<p>Tác giả: ' . $domain . '</p><p>Rate: 5 ⭐ (' . $random . ' viewers)</p>';


                                $content .= '<h2 id="' . $i . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>';

                                $content .= '<div class="clearfix"><img src="' . $post_detail[$i]->twitter_image . '" alt="' . $key->ten . '" style="width: 130px;height:130px; object-fit:cover; margin-right: 20px;float: left">';
                                $content .= '<p class="text-decrip-4" style="line-height: 25px;">' . $post_detail[$i]->description . '...' . '<strong></strong></p><a href="' . $post_detail[$i]->url . '" target="_blank"
                                   style="float: right; font-size: 20px; margin-right: 30px"
                                   rel="noopener,nofollow"><b style="margin-left: 25px">Watch</b></a></div></blockquote>';
                            } elseif ($post_detail[$i]->ky_hieu == 'i') {
                                if ($check == 1) {
                                    $content .= '<h2 id="danh-sach-img" style="font-size: 30px">Image for ' . $key->ten . ' :</h2>';
                                    $check++;
                                }
                                $content .= '<blockquote
                                                style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                                                <p id="' . $i . '" class="post_title">
                                                ' . $post_detail[$i]->url_title . '
                                                </p>
                                                <img src="' . $post_detail[$i]->url . '"
                                                alt="' . $post_detail[$i]->url_title . '"
                                                style="width: 100%;"></blockquote>';
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {
                                $post_detail[$i]->url = str_replace('watch?v=', 'embed/', $post_detail[$i]->url);
                                $content .= '<blockquote
                            style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                            <h2 id="' . $i . '" class="post_title">' . $post_detail[$i]->url_title . '</h2>
                            <p>' . $post_detail[$i]->url_description . '</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="' . $post_detail[$i]->url . '">
                            </iframe>
                        </div>

                        </blockquote>';
                            } else {
                                $content .= '<blockquote style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:' . 'Gotham SSm A' . ',' . 'Gotham SSm B' . ',' . ' Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">' .
                                    '<p>Tác giả: ' . $domain . '</p><p>Rate: 5 ⭐ (' . $random . ' viewers)</p>';


                                $content .= '<h2 id="' . $i . '" class="post_title">' . $post_detail[$i]->url_title . '</h2>';

                                $content .= '<div class="clearfix"><img src="' . $post_detail[$i]->url_image . '" alt="' . $key->ten . '" style="width: 130px;height:130px; object-fit:cover; margin-right: 20px;float: left">';
                                $content .= '<p class="text-decrip-4" style="line-height: 25px;">' . $post_detail[$i]->url_description . '...' . '<strong></strong></p><a href="' . $post_detail[$i]->url . '" target="_blank"
                                   style="float: right; font-size: 20px; margin-right: 30px"
                                   rel="noopener,nofollow"><b style="margin-left: 25px">Download</b></a></div></blockquote>';
                            }
                        }


                        $content .= '</div>';
                        if (!empty($post_detail)) {
                            HwpPost::query()->insert([
                                'post_title' => $title,
                                'post_content' => $content,
                                'post_author' => 1,
                                'post_name' => $key->url_key_cha,
                                'post_date' => date('y-m-d h:i:s'),
                                'post_date_gmt' => date('y-m-d h:i:s'),
                                'post_modified' => date('y-m-d h:i:s'),
                                'post_modified_gmt' => date('y-m-d h:i:s'),
                                'post_excerpt' => "",
                                'to_ping' => "",
                                'pinged' => "",
                                'post_content_filtered' => "",
                                'post_type' => "post",
                                'post_status' => "publish",
                                'id_key' => $key->id
                            ]);

                            $post = HwpPost::query()->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Update information and knowledge about ' . $key->ten . ' detailed and complete, this article is a topic of great interest, compiled by the editorial team of Thoitrangwiki.';

                            HwpYoastIndexable::insert([
                                'object_id' => $post->ID,
                                'object_type' => 'post',
                                'object_sub_type' => 'post',
                                'author_id' => 1,
                                'description' => $description,
                                'breadcrumb_title' => $title,
                                'post_status' => 'publish',
                                'created_at' => date('y-m-d h:i:s'),
                                'updated_at' => date('y-m-d h:i:s'),
                                'twitter_image' => $post_detail[0]->twitter_image,
                                'twitter_description' => $link_img,
                                //                                'twitter_image_source' => $link_source_img,
                                'primary_focus_keyword' => '',
                                'meta_robot' => 'index,follow',
                                'permalink' => "",
                                'permalink_hash' => ''
                            ]);
                            foreach ($post_detail as $post_id) {
                                DB::table('hwp_posts')->where('ID', '=', $post_id->ID)->update([
                                    'comment_count' => 1
                                ]);
                                //                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
                                //                            'description'=>''
                                //                        ]);;
                            }
                        }
                    }
                }
            }
        }

        return $this->export_js($from, $to, $id_cam);
    }

    public function createTH_xds($from, $to, $id_cam)
    {
        if ($to - $from >= 0) {
            $list_key = HwpKey::query()
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }

        foreach ($list_key as $key) {
            $check = HwpPost::query()
                ->select("post_name")
                ->where("post_name", '=', $key->url_key_cha)
                ->where('id_key', '=', $key->id)->first();
            if (empty($check) && $key->check == 1) {
                $title = $key->tien_to . ' ' . $key->ten . ' ' . $key->hau_to;
                //                DB::table('hwp_posts')->where('post_title', '=', $title)->delete();
                //                DB::table('hwp_yoast_indexable')->where('breadcrumb_title', '=', $title)->delete();
                $post_detail = HwpPost::query()
                    ->select(
                        'hwp_posts.post_author',
                        'hwp_posts.ID',
                        'hwp_posts.post_date',
                        'hwp_posts.post_content',
                        'hwp_posts.post_title',
                        'hwp_posts.post_view',
                        'hwp_posts.post_name',
                        'hwp_posts.menu_order',
                        'hwp_yoast_indexables.twitter_image',
                        'hwp_yoast_indexables.permalink',
                        'hwp_yoast_indexables.title',
                        'hwp_yoast_indexables.description',
                        'hwp_yoast_indexables.breadcrumb_title',
                        'hwp_yoast_indexables.primary_focus_keyword',
                        'hwp_yoast_indexables.meta_robot',
                        'hwp_posts.id_key',
                        'hwp_urls.url'
                    )
                    ->join('hwp_yoast_indexables', 'hwp_yoast_indexables.object_id', '=', 'hwp_posts.id')
                    ->join('hwp_urls', 'hwp_urls.id', '=', 'hwp_posts.id_url')
                    ->where('hwp_posts.id_key', '=', $key->id)
                    //                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
                    ->orderBy('hwp_posts.id', 'asc')
                    ->get()->toArray();
                if (count($post_detail) <= 10) {
                    $count_page = count($post_detail);
                } else {
                    $count_page = 10;
                }
                $content = '<div class="entry-content"><p>Tài liệu chọn lọc chia sẻ về <b>' . $key->ten . '</b> được tổng hợp mới nhất. Thông tin được biên soạn bởi đội ngũ biên tập viên với nhiều năm kinh nghiệm của Xây Dựng Số.</p><div id="toc_container"><p class="toc_title">MỤC LỤC</p><ul>';
                for ($i = 0; $i < $count_page; $i++) {
                    $content .= '<li><a href="#' . $post_detail[$i]->post_name . '">' . $post_detail[$i]->post_title . '</a></li>';

                    $tocGenerator = new \TOC\TocGenerator();
                    $htmlOut = $tocGenerator->getHtmlMenu($post_detail[$i]->post_content, 2, 2);
                    $content .= $htmlOut;
                }


                $content .= '</ul></div>';
                for ($i = 0; $i < $count_page; $i++) {

                    $content .= '<h2 id="' . $post_detail[$i]->post_name . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>
<div class="non_toc" style="border: 1px solid #ccc; padding: 5px 10px;">';

                    $last = strpos($post_detail[$i]->url, "/", 8);
                    $domain = substr($post_detail[$i]->url, 0, $last);
                    $random = rand(1000, 5000);
                    $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p><p>Tóm tắt: ' . $post_detail[$i]->description . '</p>';
                    $content .= html_entity_decode($post_detail[$i]->post_content) . '</div>';
                }


                $content .= '</div>';
                if (!empty($post_detail)) {
                    HwpPost::query()->insert([
                        'post_title' => $title,
                        'post_content' => $content,
                        'post_author' => 1,
                        'post_name' => $key->url_key_cha,
                        'post_date' => date('y-m-d h:i:s'),
                        'post_date_gmt' => date('y-m-d h:i:s'),
                        'post_modified' => date('y-m-d h:i:s'),
                        'post_modified_gmt' => date('y-m-d h:i:s'),
                        'post_excerpt' => "",
                        'to_ping' => "",
                        'pinged' => "",
                        'post_content_filtered' => "",
                        'post_type' => "post",
                        'post_status' => "publish",
                        'id_key' => $key->id
                    ]);

                    $post = HwpPost::query()->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                    $description = 'Chủ đề cập nhật ' . $key->ten . ' được các kiến trúc sư và chủ đầu tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển chọn trong bài viết này.';

                    HwpYoastIndexable::insert([
                        'object_id' => $post->ID,
                        'object_type' => 'post',
                        'object_sub_type' => 'post',
                        'author_id' => 1,
                        'description' => $description,
                        'breadcrumb_title' => $title,
                        'post_status' => 'publish',
                        'created_at' => date('y-m-d h:i:s'),
                        'updated_at' => date('y-m-d h:i:s'),
                        'twitter_image' => $post_detail[0]->twitter_image,
                        'primary_focus_keyword' => '',
                        'meta_robot' => 'index,follow',
                        'permalink' => "",
                        'permalink_hash' => ''
                    ]);
                    foreach ($post_detail as $post_id) {
                        HwpPost::where('ID', '=', $post_id->ID)->update([
                            'comment_count' => 1
                        ]);
                        //                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
                        //                            'description'=>''
                        //                        ]);;
                    }
                }
            }
        }
        return $this->export_js($from, $to, $id_cam);
    }

    public function export_js($from, $to, $id_cam)
    {

        $list_bai_viet = array();
        if ($to - $from >= 0) {
            $list_key = HwpKey::query()
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }

        if (!empty($list_key)) {
            foreach ($list_key as $key) {
                if ($key->check == 1) {
                    $post = HwpPost::query()
                        ->join('hwp_yoast_indexables', 'hwp_yoast_indexables.object_id', '=', 'hwp_posts.id')
                        ->join('hwp_keys', 'hwp_keys.id', '=', 'hwp_posts.id_key')
                        //                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                        ->where('post_title', '!=', '')
                        ->where('comment_count', '=', 0)
                        //                    ->where('post_content', '!=', '')
                        ->where('hwp_keys.id', '=', $key->id)
                        ->where('hwp_posts.id_url', '=', 0)
                        ->get()->toArray();
                    $list_bai_viet = array_merge($list_bai_viet, $post);
                }
            }
        }

        return $list_bai_viet;
        //        return response()->download($fileStorePath);
    }
}
