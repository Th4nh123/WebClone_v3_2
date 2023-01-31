<?php

use App\Http\Controllers\Admin\BaiVietController;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// API
Route::post('/rd/xml/a/tool-clone', 'Admin\ToolCloneController@parseURL')->name('tool_clone');

Route::post('/rd/xml/a/save-key', 'Admin\ToolCloneController@saveKey')->name('save_key');
Route::get('/rd/xml/a/update-key/{id_key}', 'Admin\ToolCloneController@updateKey')->name('update-key');
Route::get('/rd/xml/a/reset-key/{id_key}', 'Admin\ToolCloneController@resetKey')->name('reset-key');
Route::get('/rd/xml/a/get-key', 'Admin\ToolCloneController@getKey')->name('get-key');
Route::get('/rd/xml/a/find-key/{name}', 'Admin\ToolCloneController@findLikeKey')->name('findLikeKey');
Route::get('/rd/xml/a/delete-key/{id}', 'Admin\ToolCloneController@xoaKey')->name('xoaKey');
Route::get('/rd/xml/a/delete-all-key/{id_cam}', 'Admin\ToolCloneController@xoaAllKey')->name('xoaAllKey');
Route::get('/rd/xml/a/get-id-key/{id_cam}', 'Admin\ToolCloneController@getIdKey')->name('get-id-key');


Route::post('/rd/xml/a/save-url/{id_key}', 'Admin\ToolCloneController@saveUrl')->name('save_url');
Route::get('/rd/xml/a/delete-url/{id_url}', 'Admin\ToolCloneController@xoaURL')->name('xoaURL');
Route::get('/rd/xml/a/get-url/{id_key}', 'Admin\ToolCloneController@getUrl')->name('get-url');
Route::get('/rd/xml/a/get-url-by-id/{id_url}', 'Admin\ToolCloneController@getUrlById')->name('get-url-by-id');
Route::get('/rd/xml/a/get-url-by-id-key/{id_key}', 'Admin\ToolCloneController@getUrlByIdKey')->name('get-url-by-id-key');
Route::get('/rd/xml/a/get-url-by-id-key2/{id_key}', 'Admin\ToolCloneController@getUrlByIdKey2')->name('get-url-by-id-key2');
Route::get('/rd/xml/a/find-like-url/{name}', 'Admin\ToolCloneController@findLikeUrl')->name('findLikeUrl');
Route::get('/rd/xml/a/reset-url/{id_key}', 'Admin\ToolCloneController@resetUrl')->name('reset-url');



Route::post('/rd/xml/a/save-black-list', 'Admin\ToolCloneController@saveBlackList')->name('save_black_list');
Route::get('/rd/xml/a/get-black-list', 'Admin\ToolCloneController@getBlackList')->name('get-black-list');
Route::get('/rd/xml/a/delete-black-list/{id}', 'Admin\ToolCloneController@xoaBlackKey')->name('xoaBlackKey');
Route::get('/rd/xml/a/delete-all-black-list/{id_cam}', 'Admin\ToolCloneController@xoaAllBlackKey')->name('xoaAllBlackKey');
Route::get('/rd/xml/a/delete-url-by-id-key/{id_key}', 'Admin\ToolCloneController@xoaURLByIdKey')->name('xoaURLByIdKey');
Route::get('/rd/xml/a/delete-post-by-id-key/{id_key}', 'Admin\ToolCloneController@xoaPostByIdKey')->name('xoaPostByIdKey');




Route::get('/rd/xml/a/get-bai-viet-all', 'Admin\ToolCloneController@getBaiVietAll')->name('get-bv-all');
Route::get('/rd/xml/a/get-detail-post/{id}', 'Admin\ToolCloneController@getDetailPost')->name('get-bv-all');

Route::post('/rd/xml/a/save-spin-word', 'Admin\ToolCloneController@saveSpinWord')->name('save_spin_word');
Route::get('/rd/xml/a/get-spin-word', 'Admin\ToolCloneController@getSpinWord')->name('get-spin-word');
Route::get('/rd/xml/a/delete-spin-word/{id}', 'Admin\ToolCloneController@xoaSpinWord')->name('xoaSpinWord');
Route::get('/rd/xml/a/delete-all-spin-word/{id}', 'Admin\ToolCloneController@xoaAllSpinWord')->name('xoaAllSpinWord');
Route::get('/rd/xml/a/create_toplist/{id}', 'Admin\ToolCloneController@taoBaiTH')->name('create_toplist');


Route::get('/rd/xml/a/get-cam','Admin\ToolCloneController@getCam')->name("get-cam");
Route::post('/rd/xml/a/save-cam','Admin\ToolCloneController@saveCam')->name("save-cam");
Route::get('/rd/xml/a/delete-campaign/{id_cam}', 'Admin\ToolCloneController@deleteCam')->name('deleteCam');
Route::get('/rd/xml/a/update-cam/{id_cam}','Admin\ToolCloneController@updateStatusCam');
Route::get('/rd/xml/a/reset-cam/{id_cam}','Admin\ToolCloneController@resetStatusCam');

Route::get('/rd/xml/a/get-key-by-id-cam/{id_cam}','Admin\ToolCloneController@getKeyByIdCam');
Route::get('/rd/xml/a/get-key-none-url/{id_cam}','Admin\ToolCloneController@getKeyNoneUrl');
Route::post('/rd/xml/a/save-key-by-id-cam/{id_cam}', 'Admin\ToolCloneController@saveKeyByIdCam')->name('save_key_by_id_cam');

Route::get('/rd/xml/a/get-black-list-by-id-cam/{id_cam}','Admin\ToolCloneController@getBlackListByIdCam');
Route::post('/rd/xml/a/save-black-list-by-id-cam/{id_cam}', 'Admin\ToolCloneController@saveBlackListByIdCam')->name('save_black_list_by_id_cam');

Route::get('/rd/xml/a/get-spin-word-by-id-cam/{id_cam}','Admin\ToolCloneController@getSpinWordByIdCam');
Route::post('/rd/xml/a/save-spin-word-by-id-cam/{id_cam}', 'Admin\ToolCloneController@saveSpinWordByIdCam')->name('save_spin-word_by_id_cam');

Route::get('/rd/xml/a/get-count-check-url-by-id-cam/{id_cam}','Admin\ToolCloneController@getCountCheckURLByIdCam');
Route::post('/rd/xml/a/save-video', 'Admin\ToolCloneController@saveVideo')->name('save_video');
Route::get('/rd/xml/a/check-video/{id_key}', 'Admin\ToolCloneController@checkVideo')->name('checkVideo');

// Api post tool v2

Route::get('/rd/xml/a/get-data-id-have-video/{id_key}','Admin\ToolCloneController@getDataIdHaveVideo');
Route::get('/rd/xml/a/get-data-id-have-url-google/{id_key}','Admin\ToolCloneController@getDataIdHaveUrlGoogle');

Route::get('/rd/xml/a/get-ky-hieu/{id_key}','Admin\ToolCloneController@getKyHieu');
Route::post('/rd/xml/a/save-web', 'Admin\ToolCloneController@saveWeb')->name('save_web');
Route::post('/rd/xml/a/save-video', 'Admin\ToolCloneController@saveVideo')->name('save_video');
Route::post('/rd/xml/a/save-image', 'Admin\ToolCloneController@saveImage')->name('save_image');
Route::post('/rd/xml/a/save-file', 'Admin\ToolCloneController@saveFileType')->name('save_file');
Route::get('/rd/xml/a/update-count-key-google/{key}', 'Admin\ToolCloneController@updateCountKeyGoogle');
Route::get('/rd/xml/a/get-key-google', 'Admin\ToolCloneController@getKeyGoogle');
Route::get('/rd/xml/a/get-next-key-google/{key}', 'Admin\ToolCloneController@getNextKeyGoogle');
Route::get('/rd/xml/a/get-first-key-google', 'Admin\ToolCloneController@getFistKeyGoogle');
Route::get('/rd/xml/a/get-all-key-google', 'Admin\ToolCloneController@getAllKeyGoogle');
Route::post('/rd/xml/a/save-key-google', 'Admin\ToolCloneController@saveKeyGoogle')->name('save_key_google');
Route::get('/rd/xml/a/delete-key-google/{id_key_gg}', 'Admin\ToolCloneController@deleteKeyGoogle')->name('delete_key_google');
Route::get('/rd/xml/a/reset-all-key-google', 'Admin\ToolCloneController@resetAllKeyGoogle');

Route::get('/rd/xml/a/update-count-key-youtube/{key}', 'Admin\ToolCloneController@updateCountKeyYoutube');
Route::get('/rd/xml/a/get-key-youtube', 'Admin\ToolCloneController@getKeyYoutube');
Route::get('/rd/xml/a/get-next-key-youtube/{key}', 'Admin\ToolCloneController@getNextKeyYoutube');
Route::get('/rd/xml/a/get-first-key-youtube', 'Admin\ToolCloneController@getFistKeyYoutube');
Route::get('/rd/xml/a/get-all-key-youtube', 'Admin\ToolCloneController@getAllKeyYoutube');
Route::post('/rd/xml/a/save-key-youtube', 'Admin\ToolCloneController@saveKeyYoutube')->name('save_key_youtube');
Route::get('/rd/xml/a/delete-key-youtube/{id_key_gg}', 'Admin\ToolCloneController@deleteKeyGoogle')->name('delete_key_youtube');
Route::get('/rd/xml/a/reset-all-key-youtube', 'Admin\ToolCloneController@resetAllKeyYoutube');
Route::get('/rd/xml/a/update-vi-tri/{id_key}', 'Admin\ToolCloneController@updateViTri');
Route::get('/rd/xml/a/update-count-request/{count_y}/{count_g}/{id_key}', 'Admin\ToolCloneController@updateCountRequest');
Route::get('/rd/xml/a/get-total-request/{id_cam}', 'Admin\ToolCloneController@getTotalRequest');
//Route::get('/rd/xml/a/save-image-by-id-key/{id_key}', 'Admin\ToolCloneController@saveImgByKey');



// END API

Route::get('/random-pass/', 'PostController@randomkey')->name('random');
Route::get('/huong-dan-lay-pass/', 'PostController@random_bv')->name('random_bv');
Route::get('post/export/','PostController@export');

Route::prefix('/admin')->group(function () {
    Route::get('/trang-chu', 'Admin\BaiVietController@danhSachBaiViet')->name('trang_chu');
    Route::get('/', 'Admin\BaiVietController@danhSachBaiViet')->name('trang_chu');
    Route::get('/them-bai-viet', 'Admin\BaiVietController@themBaiViet')->name('themBV');
    Route::post('/them-bai-viet', 'Admin\BaiVietController@luuBaiViet')->name('luuBV');
    Route::get('/sua-bai-viet/{id}', 'Admin\BaiVietController@suaBaiViet')->name('suaBV');
    Route::post('/sua-bai-viet', 'Admin\BaiVietController@updateBaiViet')->name('updateBV');
    Route::get('xoa-bai-viet/{id}', 'Admin\BaiVietController@xoaBaiViet')->name('xoaBV');
    Route::get('/tim-kiem', 'Admin\BaiVietController@timBaiViet')->name('timkiemBV');
    Route::get('/login', 'Admin\UsersController@view_login')->name('view_login');
    Route::post('/action-login', 'Admin\UsersController@action_login')->name('action_login');
    Route::get('/them-user', 'Admin\UsersController@page_user')->name('page_user');
    Route::post('/insert-user', 'Admin\UsersController@insert_user')->name('insert_user');
    Route::get('/index-user', 'Admin\UsersController@index_user')->name('index_user');
    Route::get('/edit-user/{id}', 'Admin\UsersController@page_edit_user')->name('page_edit_user');
    Route::post('/edit-user', 'Admin\UsersController@edit_user')->name('edit_user');
    Route::get('/delete-user/{id}', 'Admin\UsersController@delete_user')->name('delete_user');
    Route::get('/find-user', 'Admin\UsersController@find_user')->name('find_user');
    Route::get('bai-huong-dan', 'Admin\BaiHDController@danhsachHD')->name('ds_huong_dan');
    Route::get('them-huong-dan', 'Admin\BaiHDController@themHD');
    Route::get('tk-bv-hd', 'Admin\BaiHDController@searchBaiVietHD')->name('tk_huong_dan');
    Route::post('them-huong-dan', 'Admin\BaiHDController@luuHD')->name('luuHD');
    Route::get('sua-huong-dan/{id}', 'Admin\BaiHDController@suaHD')->name('suaHD');
    Route::post('sua-huong-dan', 'Admin\BaiHDController@updateHD')->name('updateHD');
    Route::get('xoa-huong-dan/{id}', 'Admin\BaiHDController@xoaHD')->name('xoaHD');
    Route::get('thong-ke-view-bai-viet-hd', 'Admin\BaiHDController@thongKeView')->name('thongKeViewHD');
    Route::get('danh-sach-key', 'Admin\KeyController@index')->name('ds_key');
    Route::get('them-key', 'Admin\KeyController@themKey');
    Route::post('them-key', 'Admin\KeyController@luuKey')->name('luuKey');
    Route::get('sua-key/{id}', 'Admin\KeyController@suaKey')->name('suaKey');
    Route::post('sua-key', 'Admin\KeyController@updateKey')->name('updateKey');
    Route::get('xoa-key/{id}', 'Admin\KeyController@xoaKey')->name('xoaKey');
    Route::get('tim-kiem-key', 'Admin\KeyController@searchKey')->name('timkiemKey');
    Route::get('thong-ke-view-bai-viet', 'Admin\BaiVietController@thongKeView')->name('thongKeView');
    Route::get('them-banner', 'Admin\BannerController@themBanner');
    Route::post('them-banner', 'Admin\BannerController@luuBanner')->name('luuBanner');


    Route::get('bai-tong-hop', 'Admin\TongHopController@danhsachTongHop')->name('ds_tong_hop');


    //    Route::get('tim-key', 'Admin\TongHopController@tim_key');
//    Route::get('key_post', 'Admin\TongHopController@searchKey')->name('searchkey');
//    Route::get('them-post/{id_key}','Admin\TongHopController@them_post')->name('them_post');
//    Route::post('them-post','Admin\TongHopController@luu_post')->name('luu_post');
});
Route::prefix('/')->group(function () {
    // em chạy vào hàm postshow để lọc ra
    Route::get('{home?}', 'PostController@show')->name('postShow');
    Route::get('category/{slug}/', 'TagController@index')->name('category');
    Route::get('tag/{slug}/', 'TagController@index')->name('tag');
    Route::get('hd/{slug}/', 'PostController@post_huong_dan')->name('post_hd');
    Route::get('{slug}/page/{number}', 'AuthorController@index')->name('author');
    Route::get('/search', 'PostController@search')->name('postSearch');
    Route::get('/top-list/rd/{slug}','TopListController@home_rdone')->name('top_list_rd');
    Route::get('/top-list/wiki/{slug}/{id_key}','TopListController@home_wiki')->name('top_list_wiki');
    Route::get('/top-list/xds/{slug}','TopListController@home_xds')->name('top_list_xds');
});
//Route::get('/rd/xml/a/genrate-sitemap', function () {
//    // genarate site map
//    SitemapGenerator::create('https://rdone.net/')->writeToFile(public_path('sitemap.xml'));
//    echo "<script>window.close();</script>";
//});

Route::get('/rd/xml/a/clear-cache', function () {
    Artisan::call('cache:clear');
    echo "<script>window.close();</script>";
    return "Cache is cleared";
});
Route::get('/rd/xml/a/genrate-sitemap', function () {
    try {
        $site = App::make('sitemap');
        $site->add("https://rdone.net/", date('Y-m-d h:i:s'), 1, 'daily');
        $ds_bai_viet = DB::table('hwp_posts')
            ->select('hwp_posts.post_date', 'hwp_posts.post_name')
            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->join('hwp_users', 'hwp_posts.post_author', '=', 'hwp_users.id')
            ->where('hwp_posts.post_type', '=', 'post')
            ->orderBy('hwp_posts.ID', 'desc')
            ->get()->toArray();
        foreach ($ds_bai_viet as $post) {
            $site->add('https://rdone.net/' . $post->post_name, $post->post_date, 1, 'daily');
        }

        $list_tag = BaseController::getListTag();
        foreach ($list_tag as $tag) {
            $site->add('https://rdone.net/tag/' . $tag->slug, '', 1, 'daily');
        }
        $author = DB::table('hwp_users')
            ->select('user_login', 'user_registered')
            ->get()->toArray();
        foreach ($author as $user) {
            $site->add('https://rdone.net/' . $user->user_login . '/page/0/', $user->user_registered, 1, 'daily');
        }


        $site->store('xml', 'sitemap');
        echo "<script>window.close();</script>";
    } catch (\Exception $e) {
        echo "<script>window.close();</script>";
    }

});



