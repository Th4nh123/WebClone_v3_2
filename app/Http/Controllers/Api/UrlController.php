<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HwpKey;
use App\Models\HwpPost;
use App\Models\HwpUrl;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class UrlController extends Controller
{
    public function getUrlByIdKey($id_key)
    {
        $list_url = HwpUrl::query()
            ->leftJoin('hwp_posts', 'hwp_urls.id', '=', 'hwp_posts.id_url')
            ->where("hwp_urls.id_key", '=', $id_key)->orderBy('hwp_urls.stt', 'asc')
            ->get();
        return $list_url;
    }

    public function getUrlByIdKey2($id_key)
    {
        return HwpUrl::where("id_key", '=', $id_key)->get();
    }


    public function resetUrl($id_key)
    {
        HwpUrl::where('id_key', '=', $id_key)->update(['check' => false]);
        return array(["code" => 200]);
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
        foreach ($request->data_json as $value) {
            // dịch sang tiếng Anh
            $desc = $value['url_description'];
            $desc = $tr_vi_en->translate($desc);
            //dịch sang tiếng nhật
            $desc = $tr_en_ja->translate($desc);
            //dịch sang tiếng pháp
            $desc = $tr_ja_fr->translate($desc);
            //dịch sang tiếng việt
            $desc = $tr_fr_vi->translate($desc);

            $url = HwpUrl::where('url', '=', $value['url'])
                ->where('id_key', '=', $value['id_key'])
                ->get()->toArray();
            if (count($url) == 0) {
                HwpUrl::insert([
                    "url" => $value['url'],
                    "url_image" => $value['url_image'],
                    "url_title" => Addslashes($value['url_title']),
                    "url_description" => Addslashes($desc),
                    "ky_hieu" => $value['ky_hieu'],
                    "id_key" => $value['id_key'],
                    "stt" => $value['stt']
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu video vào cơ sở dữ liệu',
            'success' => true
        );
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
            foreach ($request->data_json as $value) {
                //dịch sang tiếng Anh
                $desc = $value['url_description'];
                $desc = $tr_vi_en->translate($desc);
                //dịch sang tiếng nhật
                $desc = $tr_en_ja->translate($desc);
                //dịch sang tiếng pháp
                $desc = $tr_ja_fr->translate($desc);
                //dịch sang tiếng việt
                $desc = $tr_fr_vi->translate($desc);
                $url = HwpUrl::where('url', '=', $value['url'])
                    ->where('id_key', '=', $value['id_key'])
                    ->get();
                if (count($url) == 0) {
                    HwpUrl::query()->insert([
                        "url" => $value['url'],
                        "url_image" => $value['url_image'],
                        "url_title" => Addslashes($value['url_title']),
                        "url_description" => Addslashes($desc),
                        "ky_hieu" => $value['ky_hieu'],
                        "id_key" => $value['id_key'],
                        "stt" => $value['stt']
                    ]);
                }
            }
            return array(
                'message' => 'Lưu thành công, đã lưu Url web vào cơ sở dữ liệu',
                'success' => true
            );
        } catch (\Exception $e) {
            return array('code' => 500, 'message' => 'k lưu được web');
        }
    }

    public function saveImage(Request $request)
    {
        try {
            // viet->eng
            $tr_vi_en = new GoogleTranslate('en', 'vi');
            // eng->japan
            $tr_en_ja = new GoogleTranslate('ja', 'en');
            // japan->france
            $tr_ja_fr = new GoogleTranslate('fr', 'ja');
            // france->viet
            $tr_fr_vi = new GoogleTranslate('vi', 'fr');
            foreach ($request->data_json as $value) {
                $desc = $value['url_description'];
                $desc = $tr_vi_en->translate($desc);
                //dịch sang tiếng nhật
                $desc = $tr_en_ja->translate($desc);
                //dịch sang tiếng pháp
                $desc = $tr_ja_fr->translate($desc);
                //dịch sang tiếng việt
                $desc = $tr_fr_vi->translate($desc);
                HwpUrl::query()->create([
                    "url" => $value['url'],
                    "url_image" => $value['url_image'],
                    "url_title" => Addslashes($value['url_title']),
                    "url_description" => Addslashes($desc),
                    "ky_hieu" => $value['ky_hieu'],
                    "id_key" => $value['id_key'],
                    "stt" => $value['stt']
                ]);
            }
            return array(
                'message' => 'Lưu thành công, đã lưu file vào cơ sở dữ liệu',
                'success' => true
            );
        } catch (\Exception $e) {
            return array('code' => 500, 'message' => 'k lưu được image');
        }
    }

    public function updateViTri(HwpKey $key_word)
    {
        $list_kh = explode('.', $key_word->ky_hieu);
        //    $ar = array();
        for ($i = 0; $i < sizeof($list_kh); $i++) {
            if (str_contains($list_kh[$i], 'w')) {
                $num = str_replace("w", "", $list_kh[$i]); //3
                if (empty($num)) {
                    $num = 10;
                }
                //   $ar[$i+1]= $num;
                HwpUrl::where('id_key', '=', $key_word->_id)
                    ->where('ky_hieu', '=', 'w')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } elseif (str_contains($list_kh[$i], 'y')) {
                $num = str_replace("y", "", $list_kh[$i]); //3
                //                $ar[$i+1]= $num;
                if (empty($num)) {
                    $num = 1;
                }
                HwpUrl::where('id_key', '=', $key_word->_id)
                    ->where('ky_hieu', '=', 'y')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } elseif (str_contains($list_kh[$i], 'i')) {
                $num = str_replace("i", "", $list_kh[$i]); //3
                //   $ar[$i+1]= $num;
                if (empty($num)) {
                    $num = 1;
                }
                HwpUrl::where('id_key', '=', $key_word->_id)
                    ->where('ky_hieu', '=', 'i')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } elseif (str_contains($list_kh[$i], 'doc')) {
                $num = str_replace("doc", "", $list_kh[$i]); //3
                //                $ar[$i+1]= $num;
                if (empty($num)) {
                    $num = 1;
                }
                HwpUrl::where('id_key', '=', $key_word->_id)
                    ->where('ky_hieu', '=', 'doc')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } elseif (str_contains($list_kh[$i], 'pdf')) {
                $num = str_replace("pdf", "", $list_kh[$i]); //3
                //     $ar[$i+1]= $num;
                if (empty($num)) {
                    $num = 1;
                }
                HwpUrl::where('id_key', '=', $key_word->_id)
                    ->where('ky_hieu', '=', 'pdf')->where('stt', '=', 100)
                    ->limit($num)
                    ->update([
                        'stt' => $i + 1
                    ]);
            } else {
                HwpUrl::where('id_key', '=', $key_word->_id)
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

    public function xoaURLByIdKey($id_key)
    {
        HwpUrl::where('id_key', '=', $id_key)->delete();
        HwpPost::where('id_key', '=', $id_key)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }

    public function xoaURL(HwpUrl $url)
    {
        $url->delete();
        HwpPost::where('id_url', '=', $url->_id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }
}
