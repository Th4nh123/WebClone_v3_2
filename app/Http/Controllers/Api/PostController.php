<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HwpCamPaign;
use App\Models\HwpKey;
use App\Models\HwpPost;
use App\Models\HwpTerm;
use App\Models\HwpTermRelationship;
use App\Models\HwpTermTaxonomy;
use App\Models\HwpUrl;
use App\Models\HwpVideo;
use App\Models\HwpYoastIndexable;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class PostController extends Controller
{
    public function xoaPostByIdKey($id_key)
    {
        HwpPost::where('id_key', '=', $id_key)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function checkVideo($id_key)
    {
        $video = HwpVideo::query()->where('hwp_video.id_key', '=', $id_key)->get();
        return $video;
    }

    public function getDetailPost(HwpPost $id_post)
    {
        return $id_post;
    }

    public function findLikeUrl($name)
    {
        $list = HwpPost::query()
            ->join('hwp_url', 'hwp_urls.id', '=', 'hwp_posts.id_url')
            ->where("hwp_posts.post_title", 'like', '%' . $name . '%')->limit(50)->get();
        return $list;
    }

    public function parseURL(Request $request)
    {
        try {
            $body = json_decode($request->getContent());
            // // thư viện dịch
            $url_check = HwpUrl::query()
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
                $list_url = HwpUrl::query()
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
                    HwpUrl::query()->where('id', '=', $list_url[$i]->id)->update(['check' => true]);
                    // lấy ra key tương ứng với url đó
                    $key = HwpKey::query()->where('id', '=', $list_url[$i]->id_key)->get()->toArray();

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


                        $campaign = HwpCamPaign::query()->where('id', '=', $key[0]->id_cam)->first();
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
                        $id_rs_post = HwpPost::query()->insertGetId([
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
                        HwpUrl::query()->where('id', '=', $list_url[$i]->id)->update(['check' => true]);
                        // thêm ảnh
                        $img = HwpUrl::query()->select('url_image')
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


                        HwpYoastIndexable::query()->insert([
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
                            'permalink_hash' => ''
                        ]);

                        // đoạn dưới này lưu mấy cái khác
                        $hwp_terms = HwpTerm::query()->where('name', '=', $key[0]->ten)->get()->toArray();
                        //
                        //                    dd(hwp_terms);
                        $id_hwp_terms = null;
                        if (count($hwp_terms) == 0) {
                            $slug = $this->stripVN($key[0]->ten) . '-' . rand(10, 100);
                            $id_hwp_terms = HwpTerm::query()->insertGetId([
                                'name' => $key[0]->ten,
                                'slug' => $slug
                            ]);
                        } else {
                            $id_hwp_terms = $hwp_terms[0]->term_id;
                        }
                        $hwp_term_taxonomy = HwpTermTaxonomy::query()->where('term_id', '=', $id_hwp_terms)->get()->toArray();

                        $id_hwp_term_taxonomy = null;
                        if (count($hwp_term_taxonomy) == 0 && $id_hwp_terms != null) {
                            $hwp_term_taxonomy = HwpTermTaxonomy::query()->insertGetId([
                                'term_id' => $id_hwp_terms,
                                'taxonomy' => 'category',
                                'description' => '',
                                'parent' => 0,
                                'count' => 0
                            ]);
                            $id_hwp_term_taxonomy = $hwp_term_taxonomy;
                        } else $id_hwp_term_taxonomy = $hwp_term_taxonomy[0]->term_taxonomy_id;
                        try {
                            HwpTermRelationship::query()->insert([
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
                HwpUrl::query()->where('id', '=', $body->id)->update(['check' => true]);
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
}
