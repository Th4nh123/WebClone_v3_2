<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\HwpKeyGoogle;
use Illuminate\Http\Request;

class KeyGoogleController extends Controller
{
    public function getKeyGoogle()
    {
        return HwpKeyGoogle::where("type", '=', 'google')->where("count", '<', 100)->limit(1)->get();
    }
    

    public function getAllKeyGoogle()
    {
        return HwpKeyGoogle::where("type", '=', 'google')->get();
    }

    public function getNextKeyGoogle(HwpKeyGoogle $key)
    {
        if ($key->update(["count" => 100])) {
            return response([
                'message' => 'Next thành công Key Google',
                'success' => true
            ], 200);
        } else {
            return [
                'message' => 'Chuyển không thành công',
                'success' => false
            ];
        }
        return [
            'success' => true
        ];
    }

    public function saveKeyGoogle(Request $request)
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
                    "type" => 'google'
                ]);
            }
            return response([
                'message' => 'Lưu thành công, đã lưu Key Google vào cơ sở dữ liệu',
                'success' => true
            ], 201);
        } else {
            return response([
                'message' => 'Lưu không thành công',
                'success' => false
            ], 202);
        }
    }

    public function deleteKeyGoogle(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value["id_key_gg"]);
        }
        if (HwpKeyGoogle::whereIn('_id', $arr)->delete()) {
            return response([
                'message' => 'Xóa thành công Key Google',
                'success' => true
            ], 200);
        } else {
            return [
                'message' => 'Xóa không thành công . Vui lòng thử lại',
                'success' => false
            ];
        }
    }

    public function deleteAllKeyGoogle()
    {
        if (HwpKeyGoogle::where("type", "=", "google")->delete()) {
            return [
                'message' => 'Xóa thành công tất cả Key Google',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Xóa không thành công. Vui lòng thử lại',
                'success' => false
            ];
        }
    }

    public function updateCountKeyGoogle(HwpKeyGoogle $key_gg)
    {
        $count_key = [];
        if ($key_gg->count >= 0 && $key_gg->count < 100) {
            $count_key = ["count" => $key_gg->count + 1];
        } else {
            $count_key = ["count" => 100];
        }

        if ($key_gg->update($count_key)) {
            return [
                'message' => 'Update count key Google thành công',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Update count key Google thất bại',
                'success' => false
            ];
        }
    }

    public function getFirstKeyGoogle()
    {
        return HwpKeyGoogle::where("type", '=', 'google')->where("count", '>=', 100)->limit(1)->get();
    }

    public function resetAllKeyGoogle()
    {
        if (HwpKeyGoogle::where("type", '=', 'google')->update(["count" => 0])) {
            return [
                'message' => 'Reset count key google thành công',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Reset count key google thất bại',
                'success' => false
            ];
        }
    }
    }
