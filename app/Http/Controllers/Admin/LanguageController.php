<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    public function saveLang(Request $request)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $lang = DB::table('hwp_language_import')->where('language_name', '=', $body[$j]->nameLanguage)->get()->toArray();
            if (count($lang) == 0) {
                // lưu url lang
                DB::table("hwp_language_import")->insert([
                    "language_name" => $body[$j]->nameLanguage,
                    "main_lang" => $body[$j]->mainLanguage,
                    "title_lang" => $body[$j]->titleLanguage,
                    "describe_lang" => $body[$j]->descriptionLanguage,
                    "author_lang" => $body[$j]->authorLanguage,
                    "rate_lang" => $body[$j]->rateLanguage,
                    "reviews_lang" => $body[$j]->reviewsLanguage,
                    "translate_list" => $body[$j]->transLanguage,
                    "check" => true,
                ]);
            }
        }
        return array(
            'message' => 'Lưu thành công, đã lưu chiến dịch vào cơ sở dữ liệu',
            'success' => true
        );
    }
    public function updateLang(Request $request, $id_lang)
    {
        $body = json_decode($request->getContent());
        for ($j = 0; $j < count($body); $j++) {
            $lang = DB::table('hwp_language_import')->where('id', '=', $id_lang)->get()->toArray();
            if (count($lang) != 0) {
                DB::table("hwp_language_import")->where('id', '=', $id_lang)->update([
                    "language_name" => $body[$j]->nameLanguage,
                    "main_lang" => $body[$j]->mainLanguage,
                    "title_lang" => $body[$j]->titleLanguage,
                    "describe_lang" => $body[$j]->descriptionLanguage,
                    "author_lang" => $body[$j]->authorLanguage,
                    "rate_lang" => $body[$j]->rateLanguage,
                    "reviews_lang" => $body[$j]->reviewsLanguage,
                    "translate_list" => $body[$j]->transLanguage,
                    "check" => true,
                ]);
            }
        }

        return array(["code" => 200]);
    }
    public function resetLang(Request $request, $id_lang)
    {
        DB::table('hwp_language_import')->where('id', '=', $id_lang)->update(['check' => false]);
        return array(["code" => 200]);
    }
    public function getLang()
    {
        $list_lang = DB::table('hwp_language_import')
            // ->where('id_cam','=',$id_cam)
            ->get()->toArray();
        //return json_encode($list_lang);
        return $list_lang;
    }
    public function xoaLang($id)
    {
        DB::table('hwp_language_import')->where('id', '=', $id)->delete();
        return array(["code" => 200, 'message' => 'Success']);
    }
    // public function xoaAllLang($id_cam){
    //     $list_key = DB::table('hwp_key')->select("id")->where("id_cam", '=', $id_cam)->get()->toArray();
    //     if (!empty($list_key)) {
    //         foreach ($list_key as $key) {
    //             DB::table('hwp_url')->where('hwp_url.id_key', '=', $key->id)->delete();
    //             DB::table('hwp_posts')->where('hwp_posts.id_key', '=', $key->id)->delete();
    //         }
    //     }
    //     DB::table('hwp_key')->where('id_cam', '=', $id_cam)->delete();

    //     return array(["code" => 200, 'message' => 'Success']);
    // }

}
