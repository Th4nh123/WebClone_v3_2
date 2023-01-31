<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function themBanner(){
        $banner_ngang = DB::table('hwp_banner')->select('link')
            ->where('vi_tri','=','banner-ngang')
            ->orderBy('id','desc')
            ->first();
        if (!empty($banner_ngang)) {
            $banner_ngang = substr($banner_ngang->link, 25);
        }
        return view('admin/banner/them_banner',compact('banner_ngang'));
    }

    public function luuBanner(Request $request){
        if ($request->has('image_upload')) {
            $file_image = $request->file('image_upload');
            $ext = $request->file('image_upload')->extension();
            $name_image = now()->toDateString() . '-' . time() . '-' . 'banner.' . $ext;
            $file_image->move(public_path('banners'), $name_image);
        }
        DB::table('hwp_banner')->insert([
           'vi_tri' => $request->vi_tri,
            'link'=> 'https://rdone.net/banner/' . $name_image
        ]);
        return redirect()->route('trang_chu');
    }
}
