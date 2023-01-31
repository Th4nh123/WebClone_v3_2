<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\HwpBlackList;
use Illuminate\Http\Request;

class BlackListController extends Controller
{
    public function getBlackListByIdCam($id_cam)
    {
        return HwpBlackList::where("id_cam", $id_cam)->get();
    }

    public function saveBlackListByIdCam(Request $request, $id_cam)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['domain']);
        }
        if (HwpBlackList::query()->whereIn("domain", $arr)->where("id_cam", $id_cam)->count() == 0) {
            foreach ($request->data_json as $value) {
                HwpBlackList::insert([
                    "domain" => $value['domain'],
                    "loai" => $value['loai'],
                    'id_cam' => $id_cam
                ]);
            }
            return response([
                'message' => 'Lưu thành công, đã lưu BlackList vào cơ sở dữ liệu',
                'success' => true
            ], 201);
        } else {
            return response([
                'message' => 'Lưu không thành công. Vui lòng thử lại',
                'success' => false
            ], 202);
        }
    }

    public function deleteBlackKey(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value["id_black_list"]);
        }
        if (HwpBlackList::whereIn('_id', $arr)->delete()) {
            return response([
                'message' => 'Xóa thành công BlackList',
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Xóa không thành công . Vui lòng thử lại',
                'success' => false
            ], 202);
        }
    }

    public function deleteAllBlackKeyByIdCam($id_cam)
    {
        if (HwpBlackList::where('id_cam', $id_cam)->delete()) {
            return response([
                'message' => 'Xóa thành công tất cả BlackList',
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Xóa không thành công. Vui lòng thử lại',
                'success' => false
            ], 202);
        }
    }
}
