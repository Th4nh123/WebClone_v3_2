<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\HwpKeyGoogle;

class KeyYoutubeController extends Controller
{
    public function getKeyYoutube()
    {
        return HwpKeyGoogle::where("type", '=', 'youtube')->where("count", '<', 500)->limit(1)->get();
    }

    public function getAllKeyYoutube()
    {
        return HwpKeyGoogle::where("type", '=', 'youtube')->get();
    }

    public function saveKeyYoutube(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['key_api']);
        }
        if (HwpKeyGoogle::whereIn("key_api", $arr)->count() == 0) {
            foreach ($request->data_json as $value) {
                HwpKeyGoogle::insert([
                    "key_api" => $value['key_api'],
                    "description" => $value['description'],
                    "count" => 0,
                    "type" => 'youtube'
                ]);
            }
            return [
                'message' => 'Lưu thành công, đã lưu Key Youtube vào cơ sở dữ liệu',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Lưu không thành công',
                'success' => false
            ];
        }
    }

    public function deleteKeyYoutube(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value["id_key_gg"]);
        }
        if (HwpKeyGoogle::whereIn('_id', $arr)->delete()) {
            return [
                'message' => 'Xóa thành công Key Youtube',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Xóa không thành công . Vui lòng thử lại',
                'success' => false
            ];
        }
    }

    public function deleteAllKeyYoutube()
    {
        if (HwpKeyGoogle::where("type", "=", "youtube")->delete()) {
            return [
                'message' => 'Xóa thành công tất cả Key Youtube',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Xóa không thành công. Vui lòng thử lại',
                'success' => false
            ];
        }
    }

    public function updateCountKeyYoutube(HwpKeyGoogle $key_yt)
    {
        $count_key = [];
        if ($key_yt->count >= 0 && $key_yt->count < 500) {
            $count_key = ["count" => $key_yt->count + 1];
        } else {
            $count_key = ["count" => 500];
        }

        if ($key_yt->update($count_key)) {
            return [
                'message' => 'Update count key youtube thành công',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Update count key youtube thất bại',
                'success' => false
            ];
        }
    }

    public function getFirstKeyYoutube()
    {
        return HwpKeyGoogle::where("type", '=', 'youtube')->where("count", '>=', 500)->limit(1)->get();
    }

    public function getNextKeyYoutube(HwpKeyGoogle $key)
    {
        if ($key->update(["count" => 500])) {
            return [
                'message' => 'Đã chuyển sang Key Youtube tiếp',
                'success' => true
            ];
        } else {
            return [
                'message' => 'không chuyển sang Key Youtube tiếp',
                'success' => false
            ];
        }
    }

    public function resetAllKeyYoutube()
    {
        if (HwpKeyGoogle::where("type", '=', 'youtube')->update(["count" => 0])) {
            return [
                'message' => 'Reset count key youtube thành công',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Reset count key youtube thất bại',
                'success' => false
            ];
        }
    }
}
