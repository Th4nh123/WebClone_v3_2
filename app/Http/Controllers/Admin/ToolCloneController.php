<?php

namespace App\Http\Controllers\Admin;

use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use phpDocumentor\Reflection\Utils;
use PHPUnit\Exception;
use Spatie\Sitemap\SitemapGenerator;

//use Stichoza\GoogleTranslate\GoogleTranslate;
use Stichoza\GoogleTranslate\GoogleTranslate;
use function Illuminate\Events\queueable;
use function MongoDB\BSON\toJSON;
use function PHPUnit\Framework\isEmpty;
use File;

include "simple_html_dom.php";


class ToolCloneController
{
    public function __construct()
    {
        set_time_limit(8000000);
    }

    public function parseURL(Request $request)
    {
        try {
            $body = json_decode($request->getContent());
            // // thư viện dịch
            $url_check = DB::table('hwp_url')
                ->where('id', '=', $body->id)
                ->first();
            if (empty($url_check->ky_hieu) || $url_check->ky_hieu == 'w') {
            //   ----------------- dịch việt---------------------
                //viet->eng
                $tr_vi_en = new GoogleTranslate('en', 'vi');
                //eng->japan
                $tr_en_ja = new GoogleTranslate('ja', 'en');
                //japan->france
                $tr_ja_fr = new GoogleTranslate('fr', 'ja');
                //france->viet
                $tr_fr_vi = new GoogleTranslate('vi', 'fr');

            //   ----------------- dịch anh----------------------


//        for ($x= 0; $x<count($body[0]->list_id_key))
                // chạy từng lần 1 thôi
                // lấy ra danh sách url 1p xử lý tối đa 50 url
                $list_url = DB::table("hwp_url")
                    ->where('id', '=', $body->id)
                    ->where('check', '=', false)->get()->toArray();
                if (count($list_url) == 0) return array('code' => 500, 'message' => 'không thấy url');
                // lấy ra id post cuối cùng trong bảng
                $last_post = DB::select('SELECT * FROM hwp_posts order by cast(id as UNSIGNED) desc limit 1');
                $id = null;
                if (empty($last_post)) {
                    $id = 1;
                } else
                    $id = (int)$last_post[0]->ID;
                // duyệt vòng lặp đào url
                for ($i = 0; $i < count($list_url); $i++) {
                    // cập nhật trạng thái của url sau khi đọc thành true
                    DB::table("hwp_url")->where('id', '=', $list_url[$i]->id)->update(['check' => true]);
                    // lấy ra key tương ứng với url đó
                    $key = DB::table("hwp_key")->where('id', '=', $list_url[$i]->id_key)->get()->toArray();

                    $dom = "";
                    try {
                        // parse html
                        $dom = file_get_html(str_replace(" ", "", trim($list_url[$i]->url)));
                    } catch (\Exception $e) {
                        return array('code' => 500, 'message' => 'không lấy được html');
                    }
                    if (empty($dom)) {
                        return array('code' => 500, 'message' => 'không tồn tại dom');
                    }
                    // lấy ra các meta
                    $meta_key_word = $dom->find("meta[name=keywords]", 0);
                    if (!empty($meta_key_word)) {
                        $meta_key_word = $meta_key_word->getAttribute('content');
                    }
                    $meta_title = $dom->find("title", 0);
                    if (!empty($meta_title)) {
                        $meta_title = $meta_title->plaintext;
                    }
                    $meta_description = $dom->find("meta[name=description]", 0);
                    if (!empty($meta_description)) {
                        $meta_description = $meta_description->getAttribute('content');
                    }
                    // lấy ra danh sách thẻ <p></p>
                    $p = $dom->find('p');
                    $root_class = "";
                    if (!empty($p)) {
//                    $arr_tag_p = array();
//                    foreach ($dom->find('p') as $item) {
//                        if (empty($arr_tag_p[$item->parent()->getAttribute('class')])) {
//                            $arr_tag_p[$item->parent()->getAttribute('class')] = 1;
//                        } else $arr_tag_p[$item->parent()->getAttribute('class')] += 1;
//                    }
//                    $root_class = max(array_keys($arr_tag_p));

                        if (count($p) > 0) {
                            // lấy thẻ <p> ở giữa rồi lấy cha của nó
                            $index = (int)round(count($p) * 0.5, 0);
                            // tạm thời comment cái này
//                    if (strlen($p[$index]->parent()->getAttribute('class')) > 0) {
//                        $root_class = $p[$index]->parent()->getAttribute('class');
//                    } else {
//                        $p[$index]->parent()->setAttribute('class', 'demo' . $index);
//                        $root_class = $p[$index]->parent()->getAttribute('class');
//                    }
                            $p[$index]->parent()->setAttribute('class', 'demoheherdsic');
                            $root_class = "demoheherdsic";
                            $dom->save();
                        }
                    }
                    // nếu  là 1 chuỗi chứa nhiều class thì phải thay khoảng trắng bằng dấu chấm
                    $root_class = str_replace(' ', '.', trim($root_class));
                    // lấy ra html bài viết
                    $element = $dom->find('.' . $root_class);
                    // Cào lần 2 nếu số lượng ký tự quá ít
                    if (!empty($element)) {
                        if (strlen($element[0]) < 5000) {
                            // lấy thẻ <p> ở giữa rồi lấy cha của nó
                            $index = (int)round(count($p) * 0.3, 0);
                            $p[$index]->parent()->setAttribute('class', 'demoheherdsic');
                            $root_class = "demoheherdsic";
                            $dom->save();
                        }
                    } else {
                        return array('code' => 500, 'message' => 'không tồn tại');
                    }
//                    $element = $dom->find('.' . $root_class);

//                if (strlen($element[0]) < 5000)
//                    return array('code' => 500, 'message' => 'Lỗi cào bài');

//            return $dom->find('img')[1]->attr['src'];
                    if (!empty($element)) {
                        // xử lý link ảnh
//                        foreach ($dom->find('img') as $item) {
//                            try {
//                                if (!empty($item->attr['data-src'])) {
//                                    $item->attr['src'] = $item->attr['data-src'];
//                                }
//                                if (!empty($item->attr['data-lazy-src'])) {
//                                    $item->attr['src'] = $item->attr['data-lazy-src'];
//                                }
//                                if (!empty($item->attr['data-original'])) {
//                                    $item->attr['src'] = $item->attr['data-original'];
//                                }
//                                if (strpos($item->attr['src'], "/") == 0 && strpos($item->attr['src'], "//") != 0) {
//                                    $last = strpos(trim($list_url[$i]->url), "/", 8);
//                                    $domain = substr(trim($list_url[$i]->url), 0, $last);
//                                    $item->attr['src'] = $domain . $item->attr['src'];
//                                }
//
//                                if (!str_contains($item->attr['src'], "http")) {
//                                    $last = strpos(trim($list_url[$i]->url), "/", 8);
//                                    $domain = substr(trim($list_url[$i]->url), 0, $last + 1);
//                                    $item->attr['src'] = $domain . $item->attr['src'];
//                                }
//
//
////                        $item->attr['src'] = $this->replaceImage($item->attr['src'], parse_url($list_url[$i]->url)['host']);
//                            } catch (\Exception $e) {
//                            }

//                        }
                        $index_key = rand(1, 4);
                        $key_copy = (array)$key[0];
                        foreach ($dom->find('a') as $item) {
                            if (!empty($item->attr['href'])) {
                                $item->tag = 'span';
                                $item->attr['href'] = null;
                            }

                        }


                        $campaign = DB::table('hwp_campaign')->where('id', '=', $key[0]->id_cam)->first();
//                $key_table = DB::table('hwp_spin_word')->where('id_cam', '=', $key[0]->id_cam)->get()->toArray();

                        if ($campaign->language == "Vietnamese") {
                            // return str_contains($dom->find('p')[12]->innertext,'<img');
                            foreach ($dom->find('p') as $item) {
                                if (str_contains($item->innertext, '<img') != 1 && strlen($item->plaintext) > 300 && strlen($item->plaintext) < 600) {
                                    //dịch sang tiếng Anh
                                    $item->innertext = $tr_vi_en->translate($item->plaintext);
                                    //dịch sang tiếng nhật
                                    $item->innertext = $tr_en_ja->translate($item->plaintext);
                                    //dịch sang tiếng pháp
                                    $item->innertext = $tr_ja_fr->translate($item->plaintext);
                                    //dịch sang tiếng việt
                                    $item->innertext = $tr_fr_vi->translate($item->plaintext);
                                    $item->style = "font-size: 120%";
                                    $meta_description = $item->plaintext;
                                    break;
                                    //dịch thẻ p

                                }

                            }
//                            foreach ($dom->find('p') as $item) {
//                                if (strlen($item->plaintext) > 300 && strlen($item->plaintext) < 600) {
//                                    $meta_description = $item->plaintext;
//                                    break;
//                                }
//                            }

//                    foreach ($dom->find('h2') as $item) {
//                        //dịch sang tiếng Anh
//                        $item->innertext = $tr_vi_en->translate($item->plaintext);
//                        //dịch sang tiếng nhật
//                        $item->innertext = $tr_en_ja->translate($item->plaintext);
//                        //dịch sang tiếng pháp
//                        $item->innertext = $tr_ja_fr->translate($item->plaintext);
//                        //dịch sang tiếng việt
//                        $item->innertext = $tr_fr_vi->translate($item->plaintext);
//                    }
//                    foreach ($dom->find('h3') as $item) {
//                        //dịch sang tiếng Anh
//                        $item->innertext = $tr_vi_en->translate($item->plaintext);
//                        //dịch sang tiếng nhật
//                        $item->innertext = $tr_en_ja->translate($item->plaintext);
//                        //dịch sang tiếng pháp
//                        $item->innertext = $tr_ja_fr->translate($item->plaintext);
//                        //dịch sang tiếng việt
//                        $item->innertext = $tr_fr_vi->translate($item->plaintext);
//                        $item->style = "font-size: 120%";
//                    }
                            $post_name = $this->stripVN($meta_title) . "-vi-cb";
                        } else if ($campaign->language == "English") {
//                    dịch thẻ p
                            foreach ($dom->find('p') as $item) {
                                if (str_contains($item->innertext, '<img') != 1 && strlen($item->plaintext) > 300 && strlen($item->plaintext) < 600) {

                                    //dịch sang tiếng nhật
                                    $item->innertext = $tr_en_ja->translate($item->plaintext);
                                    //dịch sang tiếng pháp
                                    $item->innertext = $tr_ja_fr->translate($item->plaintext);
                                    //dịch sang tiếng việt
                                    $item->innertext = $tr_fr_vi->translate($item->plaintext);
                                    //dịch sang tiếng Anh
                                    $item->innertext = $tr_vi_en->translate($item->plaintext);
                                    $item->style = "font-size: 120%";


                                }

                            }
//                            foreach ($dom->find('p') as $item) {
//                                if (strlen($item->plaintext) > 300 && strlen($item->plaintext) < 600) {
//                                    $meta_description = $item->plaintext;
//                                    break;
//                                }
//                            }
//
//                            foreach ($dom->find('h2') as $item) {
//                                //dịch sang tiếng nhật
//                                $item->innertext = $tr_en_ja->translate($item->plaintext);
//                                //dịch sang tiếng pháp
//                                $item->innertext = $tr_ja_fr->translate($item->plaintext);
//                                //dịch sang tiếng việt
//                                $item->innertext = $tr_fr_vi->translate($item->plaintext);
//                                //dịch sang tiếng Anh
//                                $item->innertext = $tr_vi_en->translate($item->plaintext);
//                            }
//                            foreach ($dom->find('h3') as $item) {
//                                //dịch sang tiếng nhật
//                                $item->innertext = $tr_en_ja->translate($item->plaintext);
//                                //dịch sang tiếng pháp
//                                $item->innertext = $tr_ja_fr->translate($item->plaintext);
//                                //dịch sang tiếng việt
//                                $item->innertext = $tr_fr_vi->translate($item->plaintext);
//                                //dịch sang tiếng Anh
//                                $item->innertext = $tr_vi_en->translate($item->plaintext);
//                            }
                            $post_name = $this->stripVN($meta_title) . "-en-cb";
                        }

                        $element[0] = str_replace_first(trim($key[0]->key_con_1), '<a href="' . $key[0]->url_key_con_1 . '">' . $key[0]->key_con_1 . '</a>', $element[0]);
                        $element[0] = str_replace_first(trim($key[0]->key_con_2), '<a href="' . $key[0]->url_key_con_2 . '">' . $key[0]->key_con_2 . '</a>', $element[0]);
                        $element[0] = str_replace_first(trim($key[0]->key_con_3), '<a href="' . $key[0]->url_key_con_3 . '">' . $key[0]->key_con_3 . '</a>', $element[0]);
                        $element[0] = str_replace_first(trim($key[0]->key_con_4), '<a href="' . $key[0]->url_key_con_4 . '">' . $key[0]->key_con_4 . '</a>', $element[0]);

                        $element[0] = preg_replace('!\s+!smi', ' ', $element[0]);
//                    $meta_title = trim($dom->find('h1')[0]->plaintext);

                        $dom->save();
                        $id = (int)$id + 1;
//                return $element[0];
                        // tạo Slug
//                $post_name = $this->stripVN($meta_title) . '-' . rand(10, 100) . $id . "-vi-cb";
                        $id_rs_post = DB::table('hwp_posts')->insertGetId([
                            'post_title' => $meta_title == null ? '' : $meta_title,
                            'post_content' => $element[0],
                            'post_author' => 1,
                            'post_name' => $post_name,
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
                            'id_url' => $list_url[$i]->id,
                            'id_key' => $key[0]->id
                        ]);
                        // lưu đc bài là chuyển check ngay
                        DB::table("hwp_url")->where('id', '=', $list_url[$i]->id)->update(['check' => true]);
                        // thêm ảnh
                        $img = DB::table('hwp_url')->select('url_image')
                            ->where('id', '=', $body->id)
                            ->where('url_image', '!=', '')->first();
                        if (!empty($img) && str_contains($img->url_image, "https")) {
                            $name_image = $img->url_image;
                        } else {
                            $vi_tri = count($dom->find('img')) / 2;
                            $name_image = '';
                            for ($i = $vi_tri; $i < count($dom->find('img')); $i++) {
                                if (str_contains($dom->find('img')[$i]->attr['src'], "http") && !str_contains($dom->find('img')[$i]->attr['src'], "https://www.facebook.com") && !str_contains($dom->find('img')[$i]->attr['src'], "data:image")) {
                                    $name_image = $dom->find('img')[$i]->attr['src'];
                                    break;
                                }
                            }
                            if (empty($name_image)) {
                                $name_image = "http://nganhxaydung.edu.vn/wp-content/uploads/2018/11/hqdefault-280x280.jpg";
                            }
                        }


                        DB::table("hwp_yoast_indexable")->insert([
                            'object_id' => $id_rs_post,
                            'object_type' => 'post',
                            'object_sub_type' => 'post',
                            'author_id' => 1,
                            'description' => $meta_description,
                            'breadcrumb_title' => $meta_title,
                            'post_status' => 'publish',
                            'created_at' => date('y-m-d h:i:s'),
                            'updated_at' => date('y-m-d h:i:s'),
                            'twitter_image' => $name_image,
                            'primary_focus_keyword' => substr($meta_key_word, 0, 170) . '',
                            'meta_robot' => 'index,follow',
                            'permalink' => "",
                            'permalink_hash' => '']);

                        // đoạn dưới này lưu mấy cái khác
                        $hwp_terms = DB::table('hwp_terms')->where('name', '=', $key[0]->ten)->get()->toArray();
//
//                    dd(hwp_terms);
                        $id_hwp_terms = null;
                        if (count($hwp_terms) == 0) {
                            $slug = $this->stripVN($key[0]->ten) . '-' . rand(10, 100);
                            $id_hwp_terms = DB::table('hwp_terms')->insertGetId([
                                'name' => $key[0]->ten,
                                'slug' => $slug
                            ]);
                        } else {
                            $id_hwp_terms = $hwp_terms[0]->term_id;
                        }
                        $hwp_term_taxonomy = DB::table('hwp_term_taxonomy')->where('term_id', '=', $id_hwp_terms)->get()->toArray();

                        $id_hwp_term_taxonomy = null;
                        if (count($hwp_term_taxonomy) == 0 && $id_hwp_terms != null) {
                            $hwp_term_taxonomy = DB::table('hwp_term_taxonomy')->insertGetId([
                                'term_id' => $id_hwp_terms,
                                'taxonomy' => 'category',
                                'description' => '',
                                'parent' => 0,
                                'count' => 0
                            ]);
                            $id_hwp_term_taxonomy = $hwp_term_taxonomy;
                        } else $id_hwp_term_taxonomy = $hwp_term_taxonomy[0]->term_taxonomy_id;
                        try {
                            DB::table('hwp_term_relationships')->insert([
                                'term_taxonomy_id' => $id_hwp_term_taxonomy,
                                'object_id' => $id_rs_post,
                                'term_order' => '0'
                            ]);
                        } catch (\Exception $e) {

                        }


                    }
                }
//       $sitemap =  SitemapGenerator::create('https://rdone.net/')->getSitemap();
                return array('code' => 200, 'message' => 'Thành công', 'post_name' => $post_name);
            } else {
                DB::table("hwp_url")->where('id', '=', $body->id)->update(['check' => true]);
            }
        } catch (\Exception $e) {
            return $e;
        }

    }

    function replaceImage($str, $domain)
    {
        $str = str_replace("\"./../../", 'https://' . $domain . '/', $str);
        $str = str_replace("\"./../ ", 'https://' . $domain . '/', $str);
        $str = str_replace("\"./", 'https://' . $domain . '/', $str);
        $str = str_replace("\"", 'https://' . $domain . '/', $str);
//        dd($str);
        return $str;
    }


    function stripVN($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = str_replace(array('[\', \']'), '', $str);
        $str = preg_replace('/\[.*\]/U', '', $str);
        $str = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $str);
        $str = htmlentities($str, ENT_COMPAT, 'utf-8');
        $str = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $str);
        $str = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $str);
        return strtolower(trim($str, '-'));
    }

    public function saveKey(Request $request)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $key = DB::table('hwp_key')->where('ten', '=', $body[$j]->ten)->get()->toArray();
            if (count($key) == 0) {
                // lưu url key cha
                $url_key = $body[$j]->tien_to . " " . $body[$j]->ten . " " . $body[$j]->hau_to . "-vi-cb";
                DB::table('hwp_key')->insert([
                    'tien_to' => $body[$j]->tien_to,
                    'ten' => $body[$j]->ten,
                    'hau_to' => $body[$j]->hau_to,
                    'url_key_cha' => str_slug($url_key),
                    'key_con_1' => $body[$j]->key_1,
                    'url_key_con_1' => $body[$j]->url_key_1,
                    'key_con_2' => $body[$j]->key_2,
                    'url_key_con_2' => $body[$j]->url_key_2,
                    'key_con_3' => $body[$j]->key_3,
                    'url_key_con_3' => $body[$j]->url_key_3,
                    'key_con_4' => $body[$j]->key_4,
                    'url_key_con_4' => $body[$j]->url_key_4,
                    'top_view_1' => $body[$j]->top_view_1,
                    'url_top_view_1' => $body[$j]->url_top_view_1,
                    'top_view_2' => $body[$j]->top_view_2,
                    'url_top_view_2' => $body[$j]->url_top_view_2,
                    'top_view_3' => $body[$j]->top_view_3,
                    'url_top_view_3' => $body[$j]->url_top_view_3,
                    'top_view_4' => $body[$j]->top_view_4,
                    'url_top_view_4' => $body[$j]->url_top_view_4,
                    'top_view_5' => $body[$j]->top_view_5,
                    'url_top_view_5' => $body[$j]->url_top_view_5,
                    'ky_hieu' => $body[$j]->ky_hieu,
                    'id_list_vd' => $body[$j]->id_list_vd
                ]);
            }

        }
        return array(
            'message' => 'Lưu thành công, đã lưu key vào cơ sở dữ liệu',
            'success' => true
        );
    }

    public function saveKeyByIdCam(Request $request, $id_cam)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $key = DB::table('hwp_key')->where('ten', '=', $body[$j]->ten)->where('hwp_key.id_cam', '=', $id_cam)->get()->toArray();
            if (count($key) == 0) {
                // lưu url key cha
                $url_key = $body[$j]->tien_to . " " . $body[$j]->ten . " " . $body[$j]->hau_to . "-vi-cb";
                DB::table('hwp_key')->insert([
                    'tien_to' => $body[$j]->tien_to,
                    'ten' => $body[$j]->ten,
                    'hau_to' => $body[$j]->hau_to,
                    'url_key_cha' => str_slug($url_key),
                    'id_cam' => $id_cam,
                    'key_con_1' => $body[$j]->key_1,
                    'url_key_con_1' => $body[$j]->url_key_1,
                    'key_con_2' => $body[$j]->key_2,
                    'url_key_con_2' => $body[$j]->url_key_2,
                    'key_con_3' => $body[$j]->key_3,
                    'url_key_con_3' => $body[$j]->url_key_3,
                    'key_con_4' => $body[$j]->key_4,
                    'url_key_con_4' => $body[$j]->url_key_4,
                    'top_view_1' => $body[$j]->top_view_1,
                    'url_top_view_1' => $body[$j]->url_top_view_1,
                    'top_view_2' => $body[$j]->top_view_2,
                    'url_top_view_2' => $body[$j]->url_top_view_2,
                    'top_view_3' => $body[$j]->top_view_3,
                    'url_top_view_3' => $body[$j]->url_top_view_3,
                    'top_view_4' => $body[$j]->top_view_4,
                    'url_top_view_4' => $body[$j]->url_top_view_4,
                    'top_view_5' => $body[$j]->top_view_5,
                    'url_top_view_5' => $body[$j]->url_top_view_5,
                    'ky_hieu' => $body[$j]->ky_hieu,
                    'id_list_vd' => $body[$j]->id_list_vd
                ]);
            }

        }
        return array(
            'message' => 'Lưu thành công, đã lưu key vào cơ sở dữ liệu',
            'success' => true
        );
    }

    public function saveSpinWord(Request $request)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $key = DB::table('hwp_spin_word')->where('key_1', '=', $body[$j]->key_1)->get()->toArray();
            if (count($key) == 0) {
                DB::table('hwp_spin_word')->insert([
                    'key_1' => $body[$j]->key_1,
                    'key_2' => $body[$j]->key_2,
                    'key_3' => $body[$j]->key_3,
                    'key_4' => $body[$j]->key_4,
                    'key_5' => $body[$j]->key_5,
                    'key_6' => $body[$j]->key_6,
                    'key_7' => $body[$j]->key_7,
                    'key_8' => $body[$j]->key_8,
                    'key_9' => $body[$j]->key_9,
                    'key_10' => $body[$j]->key_10,
                    'key_11' => $body[$j]->key_11

                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu Word vào cơ sở dữ liệu',
            'success' => true
        );
    }

    public function saveSpinWordByIdCam(Request $request, $id_cam)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $key = DB::table('hwp_spin_word')->where('key_1', '=', $body[$j]->key_1)->where('hwp_spin_word.id_cam', '=', $id_cam)->get()->toArray();
            if (count($key) == 0) {
                DB::table('hwp_spin_word')->insert([
                    'key_1' => $body[$j]->key_1,
                    'key_2' => $body[$j]->key_2,
                    'key_3' => $body[$j]->key_3,
                    'key_4' => $body[$j]->key_4,
                    'key_5' => $body[$j]->key_5,
                    'key_6' => $body[$j]->key_6,
                    'key_7' => $body[$j]->key_7,
                    'key_8' => $body[$j]->key_8,
                    'key_9' => $body[$j]->key_9,
                    'key_10' => $body[$j]->key_10,
                    'key_11' => $body[$j]->key_11,
                    'id_cam' => $id_cam
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu Word vào cơ sở dữ liệu',
            'success' => true
        );
    }


    public function updateKey($id_key)
    {
        DB::table('hwp_key')->where('id', '=', $id_key)->update(['check' => true]);
        return array(["code" => 200]);
    }

    public function saveBlackList(Request $request)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $key = DB::table('hwp_black_list')->where('domain', '=', $body[$j]->domain)->get()->toArray();
            if (count($key) == 0) {
                DB::table('hwp_black_list')->insert([
                    'domain' => $body[$j]->domain,
                    'loai' => $body[$j]->loai
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu BlackList vào cơ sở dữ liệu',
            'success' => true
        );
    }

    public function saveBlackListByIdCam(Request $request, $id_cam)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {

            $key = DB::table('hwp_black_list')->where('domain', '=', $body[$j]->domain)->where('hwp_black_list.id_cam', '=', $id_cam)->get()->toArray();

            if (count($key) == 0) {
                DB::table('hwp_black_list')->insert([
                    'domain' => $body[$j]->domain,
                    'loai' => $body[$j]->loai,
                    'id_cam' => $id_cam
                ]);
            }

        }
        return array(
            'message' => 'Lưu thành công, đã lưu BlackList vào cơ sở dữ liệu',
            'success' => true
        );
    }


    public function resetKey(Request $request, $id_key)
    {
        $key = DB::table('hwp_key')->where('id', '=', $id_key)->first();
        DB::table('hwp_key')->where('id', '=', $id_key)->update(['check' => false]);
        return array(["code" => 200]);
    }

    public function resetUrl(Request $request, $id_key)
    {
        DB::table('hwp_url')->where('hwp_url.id_key', '=', $id_key)->update(['check' => false]);
        return array(["code" => 200]);
    }

    public function saveUrl(Request $request, $id_key)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $url = DB::table('hwp_url')->where('url', '=', $body[$j]->url)
                ->where('id_key', '=', $id_key)
                ->get()->toArray();
            if (count($url) == 0) {
                DB::table('hwp_url')->insert([
                    'url' => $body[$j]->url,
                    'url_image' => $body[$j]->url_image,
                    "id_key" => $id_key
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu url vào cơ sở dữ liệu',
            'success' => true
        );
    }


    public function getUrl($id_key)
    {
        $list_url = DB::table('hwp_url')->where("id_key", '=', $id_key)->get()->toArray();
        return json_encode($list_url);
    }

    public function getUrlById($id_url)
    {
        $list_url = DB::table('hwp_url')->where("id", '=', $id_url)->get()->toArray();
        return json_encode($list_url);
    }

    public function getUrlByIdKey($id_key)
    {

        $list_url = DB::table('hwp_url')
            ->leftJoin('hwp_posts', 'hwp_url.id', '=', 'hwp_posts.id_url')
            ->where("hwp_url.id_key", '=', $id_key)->orderBy('hwp_url.stt','asc')
            ->get()->toArray();
        return json_encode($list_url);
    }

    public function getUrlByIdKey2($id_key)
    {

        $list_url = DB::table('hwp_url')->where("hwp_url.id_key", '=', $id_key)->get()->toArray();
        return json_encode($list_url);
    }


    public function getKey()
    {
        $list_key = DB::table('hwp_key')->get()->toArray();
        return json_encode($list_key);
    }


    public function getBaiVietByUrl($id_url)
    {
        $list_bv = DB::table('hwp_posts')->where('id_url', '=', $id_url)->get()->toArray();
        return json_encode($list_bv);
    }

    public function getBaiVietAll()
    {
        $list_bv = DB::table('hwp_posts')->orderBy("id", "desc")->limit(50)->get()->toArray();
        return json_encode($list_bv);
    }

    public function getBlackList()
    {
        $list_black = DB::table('hwp_black_list')->get()->toArray();
        return json_encode($list_black);
    }


    public function getSpinWord()

    {
        $list_spin = DB::table('hwp_spin_word')->get()->toArray();
        return json_encode($list_spin);
    }


    public function xoaSpinWord($id)
    {
        DB::table('hwp_spin_word')->where('id', '=', $id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaAllSpinWord($id_cam)
    {
        DB::table('hwp_spin_word')->where('hwp_spin_word.id_cam', '=', $id_cam)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaBlackKey($id)
    {
        DB::table('hwp_black_list')->where('id', '=', $id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaAllBlackKey($id_cam)
    {
        DB::table('hwp_black_list')->where('hwp_black_list.id_cam', '=', $id_cam)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }


    public function xoaKey($id)
    {
        DB::table('hwp_key')->where('id', '=', $id)->delete();
        DB::table('hwp_url')->where("hwp_url.id_key", '=', $id)->delete();
        DB::table('hwp_posts')->where("hwp_posts.id_key", '=', $id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaAllKey($id_cam)
    {
        $list_key = DB::table('hwp_key')->select("id")->where("id_cam", '=', $id_cam)->get()->toArray();
        if (!empty($list_key)) {
            foreach ($list_key as $key) {
                DB::table('hwp_url')->where('hwp_url.id_key', '=', $key->id)->delete();
                DB::table('hwp_posts')->where('hwp_posts.id_key', '=', $key->id)->delete();
            }
        }
        DB::table('hwp_key')->where('id_cam', '=', $id_cam)->delete();

        return array(["code" => 200, 'message' => 'Success']);

    }


    public function findLikeKey($name)
    {
        $list = DB::table('hwp_key')->where('ten', 'like', '%' . $name . '%')->get()->toArray();
        return json_encode($list);
    }

    public function findLikeUrl($name)
    {
        $list = DB::table('hwp_posts')
            ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
            ->where("hwp_posts.post_title", 'like', '%' . $name . '%')->limit(50)->get()->toArray();
        return json_encode($list);
    }

    public function xoaURL($id)
    {
        DB::table('hwp_url')->where('id', '=', $id)->delete();
        DB::table('hwp_posts')->where('id_url', '=', $id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaURLByIdKey($id_key)
    {
        DB::table('hwp_url')->where('id_key', '=', $id_key)->delete();
        DB::table('hwp_posts')->where('id_key', '=', $id_key)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaPostByIdKey($id_key)
    {
        DB::table('hwp_posts')->where('id_key', '=', $id_key)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function getDetailPost($id)
    {
        $detail = DB::table('hwp_posts')->where('id', '=', $id)->get()->toArray();
        return json_encode($detail);

    }

    public function getKeyByIdCam($id)
    {
        $key = DB::table('hwp_key')->where("hwp_key.id_cam", '=', $id)->get()->toArray();
        return json_encode($key);
    }

    public function updateCountRequest($count_y, $count_g, $id_key)
    {
        DB::table('hwp_key')->where('id', '=', $id_key)->update(['count_request_y' => $count_y]);
        DB::table('hwp_key')->where('id', '=', $id_key)->update(['count_request_g' => $count_g]);
        return array(["code" => 200]);
    }

    public function getTotalRequest($id_cam)
    {
        // DB::table('hwp_key')->select("id_cam", SUM("count_request_y", co))
        $result = DB::table('hwp_key')->where('hwp_key.id_cam','=', $id_cam)
            ->select([DB::raw("SUM(count_request_y) as total_y"), DB::raw("SUM(count_request_g) as total_g")])
            ->groupBy('hwp_key.id_cam')
            ->get();
        return json_encode($result);
    }


    // public function countVideoOfKey($id_cam)
    // {
    //     $stack = array();
    //     $key = DB::table('hwp_key')->select("hwp_key.id")
    //     ->where("hwp_key.id_cam", '=', $id_cam)->get()->toArray();
    //     for($j = 0; $j < count($key); $j++) {
    //         array_push($stack, countVideoOfKey2($key[$j]->id));
    //     }
    //     return json_decode($stack);

    // }

    public function getDataIdHaveVideo($id_cam)
    {
        $video = DB::table('hwp_key')->select("hwp_key.id")
            ->where("hwp_key.id_cam", '=', $id_cam)->leftJoin('hwp_url', 'hwp_key.id', '=', 'hwp_url.id_key')->where("hwp_url.ky_hieu", '=', 'y')->get()->toArray();
        return $video;
    }

    public function getDataIdHaveUrlGoogle($id_cam)
    {
        $video = DB::table('hwp_key')->select("hwp_key.id")
            ->where("hwp_key.id_cam", '=', $id_cam)
            ->leftJoin('hwp_url', 'hwp_key.id', '=', 'hwp_url.id_key')
            ->where("hwp_url.ky_hieu", '=', 'w')
            ->get()->toArray();
        return $video;
    }


    public function getKeyNoneUrl($id)
    {

        // $key_in_url = DB::table("hwp_url")->select("hwp_url.id_key")->get()->toArray();
        $key_in_url = DB::table('hwp_key')->select("hwp_key.id", 'hwp_key.tien_to', 'hwp_key.ten', 'hwp_key.hau_to', 'hwp_key.url_key_cha', 'hwp_key.check', 'hwp_key.id_cam', 'hwp_key.key_con_1', 'hwp_key.key_con_2', 'hwp_key.key_con_3', 'hwp_key.key_con_4', 'hwp_key.url_key_con_1', 'hwp_key.url_key_con_2', 'hwp_key.url_key_con_3', 'hwp_key.url_key_con_4', 'hwp_key.top_view_1', 'hwp_key.url_top_view_1', 'hwp_key.top_view_2', 'hwp_key.url_top_view_2', 'hwp_key.top_view_3', 'hwp_key.url_top_view_3', 'hwp_key.top_view_4', 'hwp_key.url_top_view_4', 'hwp_key.top_view_5', 'hwp_key.url_top_view_5', 'hwp_key.ky_hieu', 'hwp_key.id_list_vd')
            ->where("hwp_key.id_cam", '=', $id)->leftJoin('hwp_url', 'hwp_key.id', '=', 'hwp_url.id_key')->where("hwp_url.id_key", '=', null)->get()->toArray();
        return json_encode($key_in_url);
    }

    public function getIdKey($id_cam)
    {
        $key = DB::table("hwp_key")->select("id")->where('hwp_key.id_cam', '=', $id_cam)->get()->toArray();
        return json_encode($key);
    }

//   public function getUrlByIdKey($id_key)
// {

//     $list_url = DB::table('hwp_url')->leftJoin('hwp_posts', 'hwp_url.id', '=', 'hwp_posts.id_url')->where("hwp_url.id_key", '=', $id_key)->limit(50)->get()->toArray();
//     return json_encode($list_url);
// }

    public function getBlackListByIdCam($id)
    {
        $black_list = DB::table("hwp_black_list")->where("id_cam", '=', $id)->get()->toArray();
        return json_encode($black_list);
    }

    public function getSpinWordByIdCam($id)
    {
        $spin_word = DB::table("hwp_spin_word")->where("id_cam", '=', $id)->get()->toArray();
        return json_encode($spin_word);
    }


    public function saveCam(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['campaign']);
        }
        if (DB::table('hwp_campaign')->whereIn('campaign', $arr)->count() == 0) {
            foreach ($request->data_json as $value) {
                DB::table('hwp_campaign')->insert([
                    "campaign" => $value['campaign'],
                    "language" => $value['language']
                ]);
            }
            return response([
                'message' => 'Lưu thành công, đã lưu chiến dịch vào cơ sở dữ liệu',
                'success' => true
            ], 201);
        } else {
            return response([
                'message' => 'Lưu không thành công. Vui lòng thử lại',
                'success' => false
            ], 202);
        }
        return response([
            'message' => 'Lưu thành công, đã lưu chiến dịch vào cơ sở dữ liệu',
            'success' => true
        ], 201);
    }

    public function deleteCam($id_cam)
    {

        $list_key = DB::table('hwp_key')->select("id")->where("id_cam", '=', $id_cam)->get()->toArray();
        if (!empty($list_key)) {
            foreach ($list_key as $key) {
                DB::table('hwp_url')->where('hwp_url.id_key', '=', $key->id)->delete();
                DB::table('hwp_posts')->where('hwp_posts.id_key', '=', $key->id)->delete();
                DB::table('hwp_video')->where('hwp_video.id_key', '=', $key->id)->delete();

            }
        }
        DB::table('hwp_campaign')->where('id', '=', $id_cam)->delete();
        DB::table('hwp_key')->where('id_cam', '=', $id_cam)->delete();
        DB::table('hwp_black_list')->where('hwp_black_list.id_cam', '=', $id_cam)->delete();
        DB::table('hwp_spin_word')->where('hwp_spin_word.id_cam', '=', $id_cam)->delete();


        return array(["code" => 200, 'message' => 'Success']);
    }


    public function updateStatusCam($id_cam)
    {
        DB::table('hwp_campaign')->where('id', '=', $id_cam)->update(['check' => true]);
        return array(["code" => 200]);
    }

    public function resetStatusCam($id_cam)
    {
        DB::table('hwp_campaign')->where('id', '=', $id_cam)->update(['check' => false]);
        return array(["code" => 200]);
    }


    public function getCam()
    {
        // $cam = DB::table('hwp_campaign')->get()->toArray();
        // return json_encode($cam);
        return DB::table('hwp_campaign')->get();
    }

    public function getIdPlayList()
    {
        $cam = DB::table('hw')->get()->toArray();
        return json_encode($cam);
    }

    public function export_js($from, $to, $id_cam)
    {

        $list_bai_viet = array();
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }

        if (!empty($list_key)) {
            foreach ($list_key as $key) {
                if ($key->check == 1) {
                    $post = DB::table('hwp_posts')
                        ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                        ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
//                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                        ->where('post_title', '!=', '')
                        ->where('comment_count', '=', 0)
//                    ->where('post_content', '!=', '')
                        ->where('hwp_key.id', '=', $key->id)
                        ->where('hwp_posts.id_url', '=', 0)
                        ->get()->toArray();
                    $list_bai_viet = array_merge($list_bai_viet, $post);
                }
            }
        }

        return $list_bai_viet;
//        return response()->download($fileStorePath);
    }

    public function export_txt_rd($from, $to, $id_cam)
    {
        $list_bai_viet = array();
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->select("id", "tien_to", "ten", "hau_to", "url_key_cha")
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();

            //chèn bài tổng hợp

            $i = 0;
            $vi_tri = 0;
            foreach ($list_key as $th) {
                if ($th->check == 1) {
                    $post = DB::table('hwp_posts')
                        ->select("hwp_posts.post_title", "hwp_posts.post_name")
                        ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                        ->join('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
//                        ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                        ->where('hwp_key.id', '=', $th->id)
                        ->where('comment_count', '=', 0)
                        ->orderBy('hwp_posts.id', 'desc')
                        ->get()->toArray();
                    foreach ($post as $p) {
                        $p->post_name = "https://rdone.net/" . $p->post_name;
                    }
                    $list_bai_viet = array_merge($list_bai_viet, $post);
                }

            }
//            $list_bai_viet = array_merge($list_bai_viet, $bai_th);

//            if (!empty($list_key)){
//                foreach ($list_key as $key) {
//
//
//                }
//            }
        }


        return $list_bai_viet;
    }

    public function export_txt_wiki($from, $to, $id_cam)
    {
        $list_bai_viet = array();
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                // ->select("id", "tien_to", "ten", "hau_to", "url_key_cha")
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
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

                    DB::table('hwp_posts')
                        ->where('id_key', '=', $th->id)->delete();
                    DB::table('hwp_key')->where('id', '=', $th->id)->update(['check' => false]);
                }

            }
        }


        return $list_bai_viet;
    }

    public function createTH_rd($from, $to, $id_cam)
    {
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->select('hwp_key.id', 'tien_to', 'ten', 'hau_to', 'hwp_key.check', 'url_key_cha', 'language')
                ->join('hwp_campaign', 'hwp_campaign.id', '=', 'hwp_key.id_cam')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }

        foreach ($list_key as $key) {
            $check = DB::table('hwp_posts')
                ->select("post_name")
                ->where("post_name", '=', $key->url_key_cha)
                ->where('id_key', '=', $key->id)->first();
            if (empty($check) && $key->check == 1) {
                $title = $key->tien_to . ' ' . $key->ten . ' ' . $key->hau_to;
//                DB::table('hwp_posts')->where('post_title', '=', $title)->delete();
//                DB::table('hwp_yoast_indexable')->where('breadcrumb_title', '=', $title)->delete();
                $post_detail = DB::table('hwp_posts')
                    ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                        'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                        , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot', 'hwp_posts.id_key', 'hwp_url.url')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                    ->where('hwp_posts.id_key', '=', $key->id)
//                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
                    ->orderBy('hwp_posts.id', 'asc')
                    ->limit(10)
                    ->get()->toArray();
                if (count($post_detail) <= 10) {
                    $count_page = count($post_detail);
                } else if (count($post_detail) <= 20 && count($post_detail) > 10) {
                    $count_page = 10;
                    $count_bvlq = count($post_detail);
                } else if (count($post_detail) > 20) {
                    $count_page = 10;
                    $count_bvlq = 20;
                }
                if ($key->language == 'Vietnamese') {
                    $content = '<div class="entry-content"><p>Chủ đề cập nhật <b><a href="' . route('postShow', ['home' => $key->url_key_cha]) . '">' . $key->ten . '</a></b> được các kiến trúc sư và chủ đầu
                    tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển
                    chọn trong bài viết này.</p><div id="toc_container"><p class="toc_title">MỤC LỤC</p><ul>';
                    for ($i = 0; $i < $count_page; $i++) {
                        $content .= '<li><a href="#' . $post_detail[$i]->post_name . '">' . $post_detail[$i]->post_title . '</a></li>';

                        $tocGenerator = new \TOC\TocGenerator();
                        $htmlOut = $tocGenerator->getHtmlMenu($post_detail[$i]->post_content, 2, 2);
                        $content .= $htmlOut;

                    }


                    $content .= '</ul></div>';
                    for ($i = 0; $i < $count_page; $i++) {

                        $content .= '<h2 id="' . $post_detail[$i]->post_name . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>
<div class="non_toc" style="background: #eee; border: 1px solid #ccc; padding: 5px 10px;">';

                        $last = strpos($post_detail[$i]->url, "/", 8);
                        $domain = substr($post_detail[$i]->url, 0, $last);
                        $random = rand(1000, 5000);
                        $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p><p>Tóm tắt: ' . $post_detail[$i]->description . '</p>';
                        $content .= html_entity_decode($post_detail[$i]->post_content) . '</div>';
                    }


                    $content .= '</div>';
                    if (!empty($post_detail)) {
                        DB::table('hwp_posts')->insert([
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

                        $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                        $description = 'Chủ đề cập nhật ' . $key->ten . ' được các kiến trúc sư và chủ đầu tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển chọn trong bài viết này.';

                        DB::table("hwp_yoast_indexable")->insert([
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
                            'permalink_hash' => '']);
                        foreach ($post_detail as $post_id) {
                            DB::table('hwp_posts')->where('ID', '=', $post_id->ID)->update([
                                'comment_count' => 1
                            ]);
//                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
//                            'description'=>''
//                        ]);;
                        }

                    }
                } elseif ($key->language == 'English') {
                    $content = '<div class="entry-content"><p>Updated topic <b><a href="' . route('postShow', ['home' => $key->url_key_cha]) . '">' . $key->ten . '</a></b> by architects and owners
                     are interested in. Refer to the toplist of outstanding designs, newly recruited architectural trends
                     selected in this article. </p><div id="toc_container"><p class="toc_title">TABLE OF CONTENT</p><ul>';
                    for ($i = 0; $i < $count_page; $i++) {
                        $content .= '<li><a href="#' . $post_detail[$i]->post_name . '">' . $post_detail[$i]->post_title . '</a></li>';

                        $tocGenerator = new \TOC\TocGenerator();
                        $htmlOut = $tocGenerator->getHtmlMenu($post_detail[$i]->post_content, 2, 2);
                        $content .= $htmlOut;

                    }


                    $content .= '</ul></div>';
                    for ($i = 0; $i < $count_page; $i++) {

                        $content .= '<h2 id="' . $post_detail[$i]->post_name . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>
<div class="non_toc" style="background: #eee; border: 1px solid #ccc; padding: 5px 10px;">';

                        $last = strpos($post_detail[$i]->url, "/", 8);
                        $domain = substr($post_detail[$i]->url, 0, $last);
                        $random = rand(1000, 5000);
                        $content .= '<p>Author: ' . $domain . '</p><p>Rate: 5 ⭐ (' . $random . ' reviews)</p><p>Tóm tắt: ' . $post_detail[$i]->description . '</p>';
                        $content .= html_entity_decode($post_detail[$i]->post_content) . '</div>';
                    }


                    $content .= '</div>';
                    if (!empty($post_detail)) {
                        DB::table('hwp_posts')->insert([
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

                        $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                        $description = 'Updated topic ' . $key->ten . ' by architects and owners
                     are interested in. Refer to the toplist of outstanding designs, newly recruited architectural trends
                     selected in this article.';

                        DB::table("hwp_yoast_indexable")->insert([
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
                            'permalink_hash' => '']);
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
        return ToolCloneController::export_js($from, $to, $id_cam);
    }

    public function createTH_wiki($from, $to, $id_cam)
    {
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->select('hwp_key.id', 'tien_to', 'ten', 'hau_to', 'hwp_key.check', 'url_key_cha', 'language')
                ->join('hwp_campaign', 'hwp_campaign.id', '=', 'hwp_key.id_cam')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }
        foreach ($list_key as $key) {
            if ($key->check == 1) {
                $title = $key->tien_to . ' ' . $key->ten . ' ' . $key->hau_to;
//                DB::table('hwp_posts')->where('post_title', '=', $title)->delete();
//                DB::table('hwp_yoast_indexable')->where('breadcrumb_title', '=', $title)->delete();
                $video = DB::table('hwp_video')->where('id_key', '=', $key->id)->first();
                if (!empty($video)) {
                    $video->link = str_replace('watch?v=', 'embed/', $video->link);
                }
                $post_detail = DB::table('hwp_posts')
                    ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                        'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                        , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot', 'hwp_posts.id_key', 'hwp_url.url')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
                    ->where('hwp_posts.id_key', '=', $key->id)
//                ->where('hwp_posts.post_title','LIKE','%xây dựng%')->limit()
                    ->orderBy('hwp_posts.id', 'asc')
                    ->get()->toArray();
                if (!empty($post_detail)) {
                    if ($key->language == 'Vietnamese') {
                        $link_img = '';
                        $link_source_img = array();
                        for ($i = 0; $i < count($post_detail); $i++) {
                            $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->twitter_image . '"/>';
                            $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->twitter_image . '"/>';
                            $link_source_img[] = $post_detail[$i]->twitter_image;
                        }
                        $link_source_img = implode(" | ", $link_source_img);
                        $content = '<div class="entry-content"><p>Cập nhật thông tin và kiến thức về <b><a href="https://thoitrangwiki.com/' . $key->url_key_cha . '.html">' . $key->ten . '</a></b> chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên Thoitrangwiki.</p><div id="toc_container"><p class="toc_title">MỤC LỤC</p><ul>';
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
                        if (count($post_detail) < 4 && !empty($video)) {
                            $content .= '<li>
                                    <a href="#video"><b>YOUTUBE: </b>' . $video->video_title . '</a>
                                </li>';
                        }

                        $content .= '</ul></div>';
                        $content .= '<p style="font-size: 25px">Kết quả tìm kiếm Google: <b>' . $key->ten . '</b></p>
                                <img src="' . $post_detail[0]->twitter_image . '"
                                   alt="' . $post_detail[0]->post_title . '"
                                style="width: 760px;">';
                        for ($i = 0; $i < count($post_detail); $i++) {


                            $last = strpos($post_detail[$i]->url, "/", 8);
                            $domain = substr($post_detail[$i]->url, 0, $last);
                            $random = rand(1000, 5000);
                            $content .= '<blockquote style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:' . 'Gotham SSm A' . ',' . 'Gotham SSm B' . ',' . ' Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">' .
                                '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';


                            $content .= '<h2 id="' . $post_detail[$i]->post_name . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>';

                            $content .= '<div class="clearfix"><img src="' . $post_detail[$i]->twitter_image . '" alt="' . $key->ten . '" style="width: 130px;height:130px; object-fit:cover; margin-right: 20px;float: left">';
                            $content .= '<p class="text-decrip-4" style="line-height: 25px;">' . $post_detail[$i]->description . '...' . '<strong></strong></p><a href="' . $post_detail[$i]->url . '" target="_blank"
                                   style="float: right; font-size: 20px; margin-right: 30px"
                                   rel="noopener,nofollow"><b style="margin-left: 25px">Xem ngay</b></a></div></blockquote>';
                            if ($i == 4 && !empty($video)) {
                                $content .= '<blockquote
                            style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                            <h2 id="' . $i . '" class="post_title">' . $video->video_title . '</h2>
                            <p>' . $video->video_description . '</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="' . $video->link . '">
                            </iframe>
                        </div>

                        </blockquote>';
                            }
                        }
                        if (count($post_detail) < 4 && !empty($video)) {
                            $content .= '<blockquote
                            style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                            <h2 id="video" class="post_title">' . $video->video_title . '</h2>
                            <p>' . $video->video_description . '</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="' . $video->link . '">
                            </iframe>
                        </div>

                        </blockquote>';
                        }


                        $content .= '</div>';
                        $check = DB::table('hwp_posts')
                            ->select("post_name")
                            ->where("post_name", '=', $key->url_key_cha)
                            ->where('id_key', '=', $key->id)->first();
                        if (!empty($post_detail) && empty($check)) {
                            DB::table('hwp_posts')->insert([
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

                            $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Cập nhật thông tin và kiến thức về ' . $key->ten . '
                chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên Thoitrangwiki.';

                            DB::table("hwp_yoast_indexable")->insert([
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
                                'twitter_image_source' => $link_source_img,
                                'primary_focus_keyword' => '',
                                'meta_robot' => 'index,follow',
                                'permalink' => "",
                                'permalink_hash' => '']);
                            foreach ($post_detail as $post_id) {
                                DB::table('hwp_posts')->where('ID', '=', $post_id->ID)->update([
                                    'comment_count' => 1
                                ]);
                                //                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
                                //                            'description'=>''
                                //                        ]);;
                            }
                        } else {

                            DB::table('hwp_posts')->where("post_name", '=', $key->url_key_cha)
                                ->where('id_key', '=', $key->id)->update([
                                    'post_content' => $content
                                ]);

                        }
                    } elseif ($key->language == 'English') {
                        $link_img = '';
                        $link_source_img = array();
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '' && !empty($post_detail[$i]->twitter_image)) {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->twitter_image . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->twitter_image . '"/>';
                                $link_source_img[] = $post_detail[$i]->twitter_image;
                            } elseif ($post_detail[$i]->ky_hieu == 'i') {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url . '"/>';
                                $link_source_img[] = $post_detail[$i]->url;
                            } elseif ($post_detail[$i]->ky_hieu == 'y') {

                            } else {
                                $link_img .= '<link rel="dns-prefetch" href="' . $post_detail[$i]->url_image . '"/>';
                                $link_img .= '<link rel="preconnect"  href="' . $post_detail[$i]->url_image . '"/>';
                                $link_source_img[] = $post_detail[$i]->url_image;
                            }
                        }
                        $link_source_img = implode(" | ", $link_source_img);
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
                        $content .= '<p style="font-size: 25px">Google search result: <b>' . $key->ten . '</b></p>
                                <img src="' . $post_detail[0]->twitter_image . '"
                                   alt="' . $post_detail[0]->post_title . '"
                                width="760px" height="500px"';
                        for ($i = 0; $i < count($post_detail); $i++) {


                            $last = strpos($post_detail[$i]->url, "/", 8);
                            $domain = substr($post_detail[$i]->url, 0, $last);
                            $random = rand(1000, 5000);
                            $content .= '<blockquote style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:' . 'Gotham SSm A' . ',' . 'Gotham SSm B' . ',' . ' Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">' .
                                '<p>Author: ' . $domain . '</p><p>Rate: 5 ⭐ (' . $random . ' reviews)</p>';


                            $content .= '<h2 id="' . $post_detail[$i]->post_name . '" class="post_title">' . $post_detail[$i]->post_title . '</h2>';

                            $content .= '<div class="clearfix"><img src="' . $post_detail[$i]->twitter_image . '" alt="Nhà cấp 4 chữ L – 100 mẫu nhà chữ L một tầng hot nhất 2022" style="width: 130px;height:130px; object-fit:cover; margin-right: 20px;float: left">';
                            $content .= '<p class="text-decrip-4" style="line-height: 25px;">' . $post_detail[$i]->description . '...' . '<strong></strong></p><a href="' . $post_detail[$i]->url . '" target="_blank"
                                   style="float: right; font-size: 20px; margin-right: 30px"
                                   rel="noopener,nofollow"><span
                                        class="material-symbols-outlined" style="position: absolute">
                                            trending_flat
                                            </span><b style="margin-left: 25px">Watch now</b></a></div></blockquote>';
                            if ($i == 4 && !empty($video)) {
                                $content .= '<blockquote
                            style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                            <h2 id="' . $i . '" class="post_title">' . $video->video_title . '</h2>
                            <p>' . $video->video_description . '</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="' . $video->link . '">
                            </iframe>
                        </div>

                        </blockquote>';
                            }
                        }


                        $content .= '</div>';
                        $check = DB::table('hwp_posts')
                            ->select("post_name")
                            ->where("post_name", '=', $key->url_key_cha)
                            ->where('id_key', '=', $key->id)->first();
                        if (empty($check) && !empty($post_detail)) {
                            DB::table('hwp_posts')->insert([
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

                            $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Update information and knowledge about ' . $key->ten . '
                detailed and complete, this article is a topic of great interest, compiled by the editorial team of Thoitrangwiki.';

                            DB::table("hwp_yoast_indexable")->insert([
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
                                'twitter_image_source' => $link_source_img,
                                'primary_focus_keyword' => '',
                                'meta_robot' => 'index,follow',
                                'permalink' => "",
                                'permalink_hash' => '']);
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

        return ToolCloneController::export_js($from, $to, $id_cam);
    }

    public function createTH_xds($from, $to, $id_cam)
    {
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
                ->get()->toArray();
        }

        foreach ($list_key as $key) {
            $check = DB::table('hwp_posts')
                ->select("post_name")
                ->where("post_name", '=', $key->url_key_cha)
                ->where('id_key', '=', $key->id)->first();
            if (empty($check) && $key->check == 1) {
                $title = $key->tien_to . ' ' . $key->ten . ' ' . $key->hau_to;
//                DB::table('hwp_posts')->where('post_title', '=', $title)->delete();
//                DB::table('hwp_yoast_indexable')->where('breadcrumb_title', '=', $title)->delete();
                $post_detail = DB::table('hwp_posts')
                    ->select('hwp_posts.post_author', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                        'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.title', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                        , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot', 'hwp_posts.id_key', 'hwp_url.url')
                    ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->join('hwp_url', 'hwp_url.id', '=', 'hwp_posts.id_url')
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
                    DB::table('hwp_posts')->insert([
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

                    $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                    $description = 'Chủ đề cập nhật ' . $key->ten . ' được các kiến trúc sư và chủ đầu tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển chọn trong bài viết này.';

                    DB::table("hwp_yoast_indexable")->insert([
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
                        'permalink_hash' => '']);
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
        return ToolCloneController::export_js($from, $to, $id_cam);
    }

    public function getCountCheckURLByIdCam($id)
    {
        $count_url = DB::table("hwp_url")
            ->join('hwp_key', 'hwp_key.id', '=', 'hwp_url.id_key')
            ->where('hwp_key.id_cam', '=', $id)
            ->where('hwp_url.check', '=', 1)
            ->get()->count();
        return $count_url;
    }

//    public function saveVideo(Request $request)
//    {
//        $body = json_decode($request->getContent());
//        for ($j = 0; $j < count($body); $j++) {
//            $video = DB::table('hwp_video')->where('id_key', '=', $body[$j]->id_key)->get()->toArray();
//            if (count($video) == 0) {
//                DB::table('hwp_video')->insert([
//                    "link" => $body[$j]->link,
//                    "video_title" => $body[$j]->video_title,
//                    "video_description" => $body[$j]->video_description,
//                    "id_key" => $body[$j]->id_key
//                ]);
//            }
//        }
//        return array(
//            'message' => 'Lưu thành công, đã lưu video vào cơ sở dữ liệu',
//            'success' => true
//        );
//    }

    public function checkVideo($id_key)
    {
        $video = DB::table('hwp_video')->where('hwp_video.id_key', '=', $id_key)->get()->toArray();
        return json_encode($video);
    }

    public function createTH_wiki_new($from, $to, $id_cam,$chon)
    {
        if ($to - $from >= 0) {
            $list_key = DB::table('hwp_key')
                ->select('hwp_key.id', 'tien_to', 'ten', 'hau_to', 'hwp_key.check', 'url_key_cha', 'language')
                ->join('hwp_campaign', 'hwp_campaign.id', '=', 'hwp_key.id_cam')
                ->skip($from - 1)
                ->take($to - $from + 1)
                ->where("id_cam", '=', $id_cam)
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
                $post_detail = DB::table('hwp_url')
                    ->select('hwp_posts.post_author', 'hwp_url.url', 'hwp_url.url_title', 'hwp_url.url_image', 'hwp_url.url_description', 'hwp_url.ky_hieu', 'hwp_posts.ID', 'hwp_posts.post_date', 'hwp_posts.post_content', 'hwp_posts.post_title', 'hwp_posts.post_view', 'hwp_posts.post_name',
                        'hwp_posts.menu_order', 'hwp_yoast_indexable.twitter_image', 'hwp_yoast_indexable.permalink', 'hwp_yoast_indexable.description', 'hwp_yoast_indexable.breadcrumb_title'
                        , 'hwp_yoast_indexable.primary_focus_keyword', 'hwp_yoast_indexable.meta_robot')
                    ->leftJoin('hwp_posts', 'hwp_url.id', '=', 'hwp_posts.id_url')
                    ->leftJoin('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
                    ->leftJoin('hwp_key', 'hwp_key.id', '=', 'hwp_posts.id_key')
                    ->where('hwp_url.id_key', '=', $key->id)
                    ->where('hwp_url.check', '=', 1)
                    ->orderBy('hwp_url.stt','asc')->get()->toArray();
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
                                } else{
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
                        if ($chon == 1 && is_numeric($chon)){
                            $content .= '<a href="https://xaydungso.vn/' . $key->url_key_cha . '.html">' . $key->ten . '</a>';
                        }else{
                            $content .= '<a href="https://thoitrangwiki.com/' . $key->url_key_cha . '.html">' . $key->ten . '</a>';
                        }
                        $content .= '</b> chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên.</p><div id="toc_container"><p class="toc_title">MỤC LỤC</p><ul>';
                        for ($i = 0; $i < count($post_detail); $i++) {
                            if ($post_detail[$i]->ky_hieu == 'w' || $post_detail[$i]->ky_hieu == '') {
                                if (!empty($post_detail[$i]->twitter_image)) {
                                    $content .= '<li><a href="#' . $i . '">' . $post_detail[$i]->post_title . '</a></li>';
                                }else{
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
                                        href="'.$post_detail[$i]->url.'"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">'.$post_detail[$i]->post_title.'</a></h2>';
                                    $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';

                                    $content .= '<div class="d-flex flex-direc">';
                                    $content .= '<p class="" style="line-height: 25px; width: 100%">' . $post_detail[$i]->description . '...' . '</p>
                                   <img class="post_img" src="' . $post_detail[$i]->twitter_image . '" alt="' . $key->ten . '" style="object-fit:cover; margin-right: 20px;" width="380px" >
                                   </div>
                                   </div>';
                                }else{
                                    $content .= '<hr style="border-top: 2px solid black; margin: 30px 0">
                                                <div class="post_content">';
                                    $content .= '<h2 id="' . $i . '" class="post_title"><a
                                        href="'.$post_detail[$i]->url.'"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">'.$post_detail[$i]->url_title.'</a></h2>';
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
                                        href="'.$post_detail[$i]->url.'"
                                        target="_blank"
                                        style="font-size: 20px;"
                                        rel="noopener,nofollow">'.$post_detail[$i]->url_title.'</a></p></div>';
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
                                         <a href="'.$post_detail[$i]->url.'"
                                        target="_blank"
                                        style=""
                                        rel="noopener,nofollow">Download
                                            file ' . strtoupper($post_detail[$i]->ky_hieu) . ':'.$post_detail[$i]->url_title.'</a></h2>';
                                $content .= '<p>Tác giả: ' . $domain . '</p><p>Đánh giá: 5 ⭐ (' . $random . ' lượt đánh giá)</p>';

                                $content .= '<div class="d-flex flex-direc">';
                                $content .= '<p class="" style="line-height: 25px; width: 100%">' . $post_detail[$i]->url_description . '...' . '</p>
                                   <img class="post_img" src="' . $post_detail[$i]->url_image . '" alt="' . $key->ten . '" style="object-fit:cover; margin-right: 20px;" width="380px">
                                   </div>
                                   </div>';
                            }


                            if(($i+1) % 5 == 0 && $i >0){
                                $content .='<p class="text-center">_HOOK_</p>';

                            }
                        }


                        $content .= '</div>';


                        $check = DB::table('hwp_posts')
                            ->select("post_name")
                            ->where("post_name", '=', $key->url_key_cha)
                            ->where('id_key', '=', $key->id)->first();
                        if (!empty($post_detail) && empty($check)) {
                            DB::table('hwp_posts')->insert([
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

                            $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Cập nhật thông tin và kiến thức về ' . $key->ten . ' chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên.';
//                            for ($i = 0; $i < count($post_detail); $i++) {
//                                if (!empty($post_detail[$i]->url_image)){
//                                    $post_img = $post_detail[$i]->url_image;
//                                    break;
//                                }
//                            }
                            DB::table("hwp_yoast_indexable")->insert([
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
                                'permalink_hash' => '']);
                            foreach ($post_detail as $post_id) {
                                DB::table('hwp_posts')->where('ID', '=', $post_id->ID)->update([
                                    'comment_count' => 1
                                ]);
                                //                        DB::table('hwp_yoast_indexable')->where('object_id', '=', $post_id->ID)->update([
                                //                            'description'=>''
                                //                        ]);;
                            }

                        } else {
                            DB::table('hwp_posts')->where("post_name", '=', $key->url_key_cha)
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
                            DB::table('hwp_posts')->insert([
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

                            $post = DB::table('hwp_posts')->select('ID')->where('post_name', '=', $key->url_key_cha)->first();
                            $description = 'Update information and knowledge about ' . $key->ten . ' detailed and complete, this article is a topic of great interest, compiled by the editorial team of Thoitrangwiki.';

                            DB::table("hwp_yoast_indexable")->insert([
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
                                'permalink_hash' => '']);
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

        return ToolCloneController::export_js($from, $to, $id_cam);
    }

    public function saveWeb(Request $request)
    {
        try {
            //viet->eng
            $tr_vi_en = new GoogleTranslate('en', 'vi');
            //eng->japan
            $tr_en_ja = new GoogleTranslate('ja', 'en');
//        japan->france
            $tr_ja_fr = new GoogleTranslate('fr', 'ja');
            //france->viet
            $tr_fr_vi = new GoogleTranslate('vi', 'fr');
            $body = json_decode($request->getContent());
            for ($j = 0; $j < count($body); $j++) {
                //dịch sang tiếng Anh
                $desc = $body[$j]->url_description;
                $desc = $tr_vi_en->translate($desc);
                //dịch sang tiếng nhật
                $desc = $tr_en_ja->translate($desc);
                //dịch sang tiếng pháp
                $desc = $tr_ja_fr->translate($desc);
                //dịch sang tiếng việt
                $desc = $tr_fr_vi->translate($desc);
                $url = DB::table('hwp_url')->where('url', '=', $body[$j]->url)
                    ->where('id_key', '=', $body[$j]->id_key)
                    ->get()->toArray();
                if(count($url) == 0) {
                    DB::table('hwp_url')->insert([
                        "url" => $body[$j]->url,
                        "url_image" => $body[$j]->url_image,
                        "url_title" => Addslashes($body[$j]->url_title),
                        "url_description" => Addslashes($desc),
                        "ky_hieu" => $body[$j]->ky_hieu,
                        "id_key" => $body[$j]->id_key,
                        "stt" => $body[$j]->stt
                    ]);
                }
            }
            return array(
                'message' => 'Lưu thành công, đã lưu Url web vào cơ sở dữ liệu',
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k lưu được web');
        }

    }

    public function getKyHieu($id)
    {
        $key = DB::table("hwp_key")->select("hwp_key.id", "hwp_key.ky_hieu", "hwp_key.id_list_vd")->where("id", '=', $id)->get()->toArray();
        return json_encode($key);
    }

    public function saveVideo(Request $request)
    {
        // try {
        // viet->eng
        $tr_vi_en = new GoogleTranslate('en', 'vi');
        //eng->japan
        $tr_en_ja = new GoogleTranslate('ja', 'en');
        //japan->france
        $tr_ja_fr = new GoogleTranslate('fr', 'ja');
        //france->viet
        $tr_fr_vi = new GoogleTranslate('vi', 'fr');
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            // dịch sang tiếng Anh
            $desc = $body[$j]->url_description;
            $desc = $tr_vi_en->translate($desc);
            //dịch sang tiếng nhật
            $desc = $tr_en_ja->translate($desc);
            //dịch sang tiếng pháp
            $desc = $tr_ja_fr->translate($desc);
            //dịch sang tiếng việt
            $desc = $tr_fr_vi->translate($desc);
            $url = DB::table('hwp_url')->where('url', '=', $body[$j]->url)
                ->where('id_key', '=', $body[$j]->id_key)
                ->get()->toArray();
            if(count($url) == 0) {
                DB::table('hwp_url')->insert([
                    "url" => $body[$j]->url,
                    "url_title" => Addslashes($body[$j]->url_title),
                    "url_description" => Addslashes($desc),
                    "ky_hieu" => $body[$j]->ky_hieu,
                    "id_key" => $body[$j]->id_key,
                    "stt" => $body[$j]->stt
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu video vào cơ sở dữ liệu',
            'success' => true
        );
        // } catch(\Exception $e) {
        //         return array('code'=>500, message => 'k lưu được video');
        //     }

    }


    public function saveImage(Request $request)
    {
        try {
            //viet->eng
            $tr_vi_en = new GoogleTranslate('en', 'vi');
            //eng->japan
            $tr_en_ja = new GoogleTranslate('ja', 'en');
//        japan->france
            $tr_ja_fr = new GoogleTranslate('fr', 'ja');
            //france->viet
            $tr_fr_vi = new GoogleTranslate('vi', 'fr');
            $body = json_decode($request->getContent());
            for ($j = 0; $j < count($body); $j++) {
                //dịch sang tiếng Anh
                $desc = $body[$j]->url_description;
                $desc = $tr_vi_en->translate($desc);
                //dịch sang tiếng nhật
                $desc = $tr_en_ja->translate($desc);
                //dịch sang tiếng pháp
                $desc = $tr_ja_fr->translate($desc);
                //dịch sang tiếng việt
                $desc = $tr_fr_vi->translate($desc);
                // $url = DB::table('hwp_url')->where('url', '=', $body[$j]->url)
                // ->where('id_key', '=', $body[$j]->id_key)
                // ->get()->toArray();
                // if(count($url) == 0) {
                DB::table('hwp_url')->insert([
                    "url" => $body[$j]->url,
                    "url_image" => $body[$j]->url_image,
                    "url_title" => Addslashes($body[$j]->url_title),
                    "url_description" => Addslashes($desc),
                    "ky_hieu" => $body[$j]->ky_hieu,
                    "id_key" => $body[$j]->id_key,
                    "stt" => $body[$j]->stt
                ]);
                // }

            }
            return array(
                'message' => 'Lưu thành công, đã lưu file vào cơ sở dữ liệu',
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k lưu được image');
        }

    }

    public function saveFileType(Request $request)
    {
        try {
            //viet->eng
            $tr_vi_en = new GoogleTranslate('en', 'vi');
            //eng->japan
            $tr_en_ja = new GoogleTranslate('ja', 'en');
//        japan->france
            $tr_ja_fr = new GoogleTranslate('fr', 'ja');
            //france->viet
            $tr_fr_vi = new GoogleTranslate('vi', 'fr');
            $body = json_decode($request->getContent());

            for ($j = 0; $j < count($body); $j++) {
                //dịch sang tiếng Anh
                $desc = $body[$j]->url_description;
                $desc = $tr_vi_en->translate($desc);
                //dịch sang tiếng nhật
                $desc = $tr_en_ja->translate($desc);
                //dịch sang tiếng pháp
                $desc = $tr_ja_fr->translate($desc);
                //dịch sang tiếng việt
                $desc = $tr_fr_vi->translate($desc);
                $url = DB::table('hwp_url')->where('url', '=', $body[$j]->url)
                    ->where('id_key', '=', $body[$j]->id_key)
                    ->get()->toArray();
                if(count($url) == 0) {
                    DB::table('hwp_url')->insert([
                        "url" => $body[$j]->url,
                        "url_image" => $body[$j]->url_image,
                        "url_title" => Addslashes($body[$j]->url_title),
                        "url_description" => Addslashes($desc),
                        "ky_hieu" => $body[$j]->ky_hieu,
                        "id_key" => $body[$j]->id_key,
                        "stt" => $body[$j]->stt
                    ]);
                }
            }
            return array(
                'message' => 'Lưu thành công, đã lưu file vào cơ sở dữ liệu',
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k lưu được file');
        }

    }

    public function updateCountKeyGoogle($key_api)
    {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.key_api", '=', $key_api)->where("hwp_key_google.type", '=', 'google')->get();
            $id_current = $key[0]->id;
            $key_current = $key[0]->key_api;
            $count_current = $key[0]->count;
            if($count_current >= 0 && $count_current < 100) {
                DB::table("hwp_key_google")->where("hwp_key_google.id", '=', $id_current)->update(["hwp_key_google.count" => $count_current + 1]);
            } else {
                DB::table("hwp_key_google")->where("hwp_key_google.id", '=', $id_current)->update(["hwp_key_google.count" => 100]);
            }
            return array(
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k update được count key1');
        }

    }

    public function updateCountKeyYoutube($key_api)
    {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.key_api", '=', $key_api)->where("hwp_key_google.type", '=', 'youtube')->get();
            $id_current = $key[0]->id;
            $key_current = $key[0]->key_api;
            $count_current = $key[0]->count;
            if($count_current >= 0 && $count_current < 500) {
                DB::table("hwp_key_google")->where("hwp_key_google.id", '=', $id_current)->update(["hwp_key_google.count" => $count_current + 1]);
            } else {
                DB::table("hwp_key_google")->where("hwp_key_google.id", '=', $id_current)->update(["hwp_key_google.count" => 500]);
            }
            return array(
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k update được count key');
        }

    }

    public function getNextKeyGoogle($key)
    {
        try {
            DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'google')->where("hwp_key_google.key_api", '=', $key)->update(["hwp_key_google.count" => 100]);
            return array(
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k next được key gg');
        }

    }

    public function getNextKeyYoutube($key)
    {
        try {
            DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'youtube')->where("hwp_key_google.key_api", '=', $key)->update(["hwp_key_google.count" => 500]);
            return array(
                'success' => true
            );
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k next được key yt');
        }

    }

    public function getKeyGoogle() {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'google')->where("count", '<', '100')->limit(1)->get()->toArray();
            return json_encode($key);
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key gg');
        }
    }

    public function getKeyYoutube() {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'youtube')->where("count", '<', '500')->limit(1)->get()->toArray();
            return json_encode($key);
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key yt');
        }
    }

    public function getFistKeyGoogle() {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'google')->where("count", '=', '100')->limit(1)->get()->toArray();
            return json_encode($key);
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key gg');
        }
    }

    public function getFistKeyYoutube() {
        try {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'youtube')->where("count", '=', '500')->limit(1)->get()->toArray();
            return json_encode($key);
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key yt');
        }
    }

    public function getAllKeyGoogle() {
        try {
            return DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'google')->get();
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key gg');
        }

    }

    public function getAllKeyYoutube() {
        try {
            return DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'youtube')->get();
        } catch(\Exception $e) {
            return array('code'=>500, 'message' => 'k get được key yt');
        }

    }


    public function resetAllKeyGoogle() {
        DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'google')->update(["hwp_key_google.count" => 0]);
    }

    public function resetAllKeyYoutube() {
        DB::table("hwp_key_google")->where("hwp_key_google.type", '=', 'youtube')->update(["hwp_key_google.count" => 0]);
    }



    public function saveKeyGoogle(Request $request) {
        $body = json_decode($request->getContent());
        for($j = 0; $j < count($body); $j++) {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.key_api", '=', $body[$j]->key_api)->get()->toArray();
            if(count($key) == 0) {
                DB::table('hwp_key_google')->insert([
                    "key_api"=> $body[$j]->key_api,
                    "description" => $body[$j]->description,
                    "type" => 'google'
                ]);
            }
        }
        return array(
            'success' => true
        );
    }

    public function saveKeyYoutube(Request $request) {
        $body = json_decode($request->getContent());
        for($j = 0; $j < count($body); $j++) {
            $key = DB::table("hwp_key_google")->where("hwp_key_google.key_api", '=', $body[$j]->key_api)->get()->toArray();
            if(count($key) == 0) {
                DB::table('hwp_key_google')->insert([
                    "key_api"=> $body[$j]->key_api,
                    "description" => $body[$j]->description,
                    "type" => 'youtube'
                ]);
            }
        }
        return array(
            'success' => true
        );
    }

    public function deleteKeyGoogle($id) {
        DB::table("hwp_key_google")->where("hwp_key_google.id", '=', $id)->delete();
        return array(
            'success' => true
        );
    }


    public function updateViTri($id_key){
        $ky_hieu = DB::table('hwp_key')->where('id','=',$id_key)->first();
        $list_kh = explode('.',$ky_hieu->ky_hieu);
//        $ar = array();
        for($i=0;$i < sizeof($list_kh);$i++) {
            if (str_contains($list_kh[$i], 'w')) {
                $num = str_replace("w", "", $list_kh[$i]); //3
                if (empty($num)){
                    $num =10;
                }
//                $ar[$i+1]= $num;
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('ky_hieu', '=', 'w')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } elseif (str_contains($list_kh[$i], 'y')) {
                $num = str_replace("y", "", $list_kh[$i]); //3
//                $ar[$i+1]= $num;
                if (empty($num)){
                    $num =1;
                }
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('ky_hieu', '=', 'y')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            }
            elseif (str_contains($list_kh[$i], 'i')) {
                $num = str_replace("i", "", $list_kh[$i]); //3
//                $ar[$i+1]= $num;
                if (empty($num)){
                    $num =1;
                }
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('ky_hieu', '=', 'i')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            }
            elseif (str_contains($list_kh[$i], 'doc')) {
                $num = str_replace("doc", "", $list_kh[$i]); //3
//                $ar[$i+1]= $num;
                if (empty($num)){
                    $num =1;
                }
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('ky_hieu', '=', 'doc')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            }
            elseif (str_contains($list_kh[$i], 'pdf')) {
                $num = str_replace("pdf", "", $list_kh[$i]); //3
//                $ar[$i+1]= $num;
                if (empty($num)){
                    $num =1;
                }
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('ky_hieu', '=', 'pdf')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            }else{
                DB::table('hwp_url')->where('id_key', '=', $id_key)
                    ->where('stt', '=', 100)
                    ->update([
                        'stt' => $i + 1
                    ]);
            }
        }
        return array(
            'success' => true
        );
    }
}


