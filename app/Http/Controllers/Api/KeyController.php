<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\HwpKey;
use App\Models\HwpUrl;
use Illuminate\Http\Request;

class KeyController extends Controller
{
    public function getKey()
    {
        return HwpKey::all();
    }

    public function getKeyByIdCam($id_cam)
    {
        return HwpKey::where("id_cam", $id_cam)->get();
    }

    public function getIdKey($id_cam)
    {
        return HwpKey::where('id_cam', '=', $id_cam)->get();
    }

    public function getDataIdHaveVideo($id_cam)
    {
        $video = HwpKey::where("id_cam", '=', $id_cam)->leftJoin('hwp_urls', 'hwp_keys.id', '=', 'hwp_urls.id_key')->where("hwp_urls.ky_hieu", '=', 'y')->get()->toArray();
        return $video;
    }

    public function getDataIdHaveUrlGoogle($id_cam)
    {
        $video = HwpKey::query()->select("hwp_keys.id")
            ->where("hwp_keys.id_cam", '=', $id_cam)
            ->leftJoin('hwp_urls', 'hwp_keys.id', '=', 'hwp_urls.id_key')
            ->where("hwp_urls.ky_hieu", '=', 'w')
            ->get()->toArray();
        return $video;
    }

    public function findLikeKey($name)
    {
        return HwpKey::where('ten', 'like', '%' . $name . '%')->get();
    }

    public function getKeyNoneUrl($id_cam)
    {
        return HwpKey::query()
            ->leftJoin('hwp_url', 'hwp_key.id', '=', 'hwp_url.id_key')
            ->where("hwp_key.id_cam", '=', $id_cam)
            ->where("hwp_url.id_key", '=', null)
            ->get();
    }

    public function getKyHieu(HwpKey $key)
    {
        return $key;
    }

    public function saveKey(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['ten']);
        }
        return $arr;
        if (HwpKey::whereIn('ten', $arr)->count() == 0) {
            foreach ($request->data_json as $value) {
                $url_key = $value['tien_to'] . " " . $value['ten'] . " " . $value['hau_to'] . "-vi-cb";
                HwpKey::insert([
                    'tien_to' => $value['tien_to'],
                    'ten' => $value['ten'],
                    'hau_to' => $value['hau_to'],
                    'url_key_cha' => Addslashes($url_key),
                    'key_con_1' => $value['key_1'],
                    'url_key_con_1' => $value['url_key_1'],
                    'key_con_2' => $value['key_2'],
                    'url_key_con_2' => $value['url_key_2'],
                    'key_con_3' => $value['key_3'],
                    'url_key_con_3' => $value['url_key_3'],
                    'key_con_4' => $value['key_4'],
                    'url_key_con_4' => $value['url_key_4'],
                    'top_view_1' => $value['top_view_1'],
                    'url_top_view_1' => $value['url_top_view_1'],
                    'top_view_2' => $value['top_view_2'],
                    'url_top_view_2' => $value['url_top_view_2'],
                    'top_view_3' => $value['top_view_3'],
                    'url_top_view_3' => $value['url_top_view_3'],
                    'top_view_4' => $value['top_view_4'],
                    'url_top_view_4' => $value['url_top_view_4'],
                    'top_view_5' => $value['top_view_5'],
                    'url_top_view_5' => $value['url_top_view_5'],
                    'ky_hieu' => $value['ky_hieu'],
                    'id_list_vd' => $value['id_list_vd']
                ]);
            }
            return response([
                'message' => 'Th??m key theo chi???n d???ch th??nh c??ng',
                'success' => true
            ], 201);
        } else {
            return response([
                'message' => 'Th??m key theo chi???n d???ch kh??ng th??nh c??ng',
                'success' => false
            ], 202);
        }
    }

    public function saveKeyByIdCam(Request $request, $id_cam)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['ten']);
        }
        if (HwpKey::whereIn('ten', $arr)->where("id_cam", $id_cam)->count() == 0) {
            foreach ($request->data_json as $value) {
                $url_key = $value['tien_to'] . " " . $value['ten'] . " " . $value['hau_to'] . "-vi-cb";
                HwpKey::insert([
                    'tien_to' => $value['tien_to'],
                    'ten' => $value['ten'],
                    'hau_to' => $value['hau_to'],
                    'url_key_cha' => Addslashes($url_key),
                    'id_cam' => $id_cam,
                    'key_con_1' => $value['key_1'],
                    'url_key_con_1' => $value['url_key_1'],
                    'key_con_2' => $value['key_2'],
                    'url_key_con_2' => $value['url_key_2'],
                    'key_con_3' => $value['key_3'],
                    'url_key_con_3' => $value['url_key_3'],
                    'key_con_4' => $value['key_4'],
                    'url_key_con_4' => $value['url_key_4'],
                    'top_view_1' => $value['top_view_1'],
                    'url_top_view_1' => $value['url_top_view_1'],
                    'top_view_2' => $value['top_view_2'],
                    'url_top_view_2' => $value['url_top_view_2'],
                    'top_view_3' => $value['top_view_3'],
                    'url_top_view_3' => $value['url_top_view_3'],
                    'top_view_4' => $value['top_view_4'],
                    'url_top_view_4' => $value['url_top_view_4'],
                    'top_view_5' => $value['top_view_5'],
                    'url_top_view_5' => $value['url_top_view_5'],
                    'ky_hieu' => $value['ky_hieu'],
                    'id_list_vd' => $value['id_list_vd'] ?? ''
                ]);
            }
            return response([
                'message' => 'Th??m key theo chi???n d???ch th??nh c??ng',
                'success' => true
            ], 201);
        } else {
            return response([
                'message' => 'Th??m key theo chi???n d???ch kh??ng th??nh c??ng',
                'success' => false
            ], 202);
        }
    }

    public function resetKey(HwpKey $key_word)
    {
        if ($key_word->update(['check' => false])) {
            return [
                'message' => 'Reset Key Word th??nh c??ng',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Reset Key Word kh??ng th??nh c??ng',
                'success' => false
            ];
        }
    }

    public function updateKey(HwpKey $key_word)
    {
        if ($key_word->update(['check' => true])) {
            return [
                'message' => 'C???p nh???t key word th??nh c??ng',
                'success' => true
            ];
        } else {
            return [
                'message' => 'C???p nh???t key word kh??ng th??nh c??ng',
                'success' => false
            ];
        }
    }

    public function xoaKey(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value["id_key_word"]);
        }
        if (HwpKey::whereIn('_id', $arr)->delete()) {
            return response([
                'message' => 'X??a th??nh c??ng Key Word',
                'success' => true
            ], 200);
        } else {
            return [
                'message' => 'X??a kh??ng th??nh c??ng . Vui l??ng th??? l???i',
                'success' => false
            ];
        }
    }

    public function xoaAllKeyByIdCam($id_cam)
    {
        if (HwpKey::where("id_cam", "=", $id_cam)->delete()) {
            return [
                'message' => 'X??a th??nh c??ng t???t c??? Key Word',
                'success' => true
            ];
        } else {
            return [
                'message' => 'X??a kh??ng th??nh c??ng. Vui l??ng th??? l???i',
                'success' => false
            ];
        }
    }
}
