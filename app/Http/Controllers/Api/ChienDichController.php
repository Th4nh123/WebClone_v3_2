<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HwpBlackList;
use Illuminate\Http\Request;
use App\Models\HwpCampaign;
use App\Models\HwpKey;
use Illuminate\Support\Facades\Artisan;

class ChienDichController extends Controller
{
    public function getCam()
    {
        return HwpCampaign::all();
    }

    public function saveCam(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value['campaign']);
        }
        if (HwpCampaign::whereIn('campaign', $arr)->count() == 0) {
            foreach ($request->data_json as $value) {
                HwpCampaign::insert([
                    "campaign" => $value['campaign'],
                    "language" => $value['language'],
                    "check" => 0
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
            'message' => 'Lưu không thành công. Vui lòng thử lại',
            'success' => true
        ], 201);
    }

    public function deleteCam(Request $request)
    {
        $arr = [];
        foreach ($request->data_json as $value) {
            array_push($arr, $value["id_cam"]);
        }
        if (HwpCampaign::whereIn('_id', $arr)->delete() || HwpKey::whereIn('id_cam', $arr)->delete() || HwpBlackList::whereIn('id_cam', $arr)->delete()) {
            return response([
                'message' => 'Xóa chiến dịch thành công',
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Xóa chiến dịch thất bại . Vui lòng thử lại',
                'success' => false
            ], 202);
        }
    }

    public function deleteAllCam()
    {
        if (!Artisan::call('migrate:refresh', ['--force' => true])) {
            return response([
                'message' => 'Xóa tất cả chiến dịch thành công ',
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Xóa chiến dịch thất bại',
                'success' => false
            ], 202);
        }
    }

    public function updateStatusCam(HwpCampaign $id_campaign)
    {
        if ($id_campaign->update(['check' => true])) {
            return response([
                'message' => "Chiến dịch này đã được dừng lại, vui lòng đợi chạy nốt URL này",
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Cập nhật trạng thái chiến dịch không thành công',
                'success' => false
            ], 202);
        }
    }

    public function resetStatusCam(HwpCampaign $id_campaign)
    {
        if ($id_campaign->update(['check' => false])) {
            return response([
                'message' => 'Reset trạng thái chiến dịch thành công ',
                'success' => true
            ], 200);
        } else {
            return response([
                'message' => 'Reset trạng thái chiến dịch không thành công',
                'success' => false
            ], 202);
        }
    }
}
