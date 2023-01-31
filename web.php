<?php

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

Route::get('/random-pass/', 'PostController@randomkey')->name('random');
Route::get('/huong-dan-lay-pass/', 'PostController@random_bv')->name('random_bv');

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
    Route::get('/edit-user', 'Admin\UsersController@page_edit_user')->name('page_edit_user');
    Route::post('/edit-user', 'Admin\UsersController@edit_user')->name('edit_user');
    Route::get('/delete-user', 'Admin\UsersController@delete_user')->name('delete_user');
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
    Route::get('xoa-key/{id}', 'Admin\KeyController@xoaKey')->name('xoaKey_v2');
    Route::get('tim-kiem-key', 'Admin\KeyController@searchKey')->name('timkiemKey');
    Route::get('thong-ke-view-bai-viet', 'Admin\BaiVietController@thongKeView')->name('thongKeView');
    Route::get('them-banner', 'Admin\BannerController@themBanner');
    Route::post('them-banner', 'Admin\BannerController@luuBanner')->name('luuBanner');

    Route::get('/sitemap',function (){
        $site = App::make('sitemap');
        $site->add("https://rdone.net/",date('Y-m-d h:i:s'),1,'daily');
        $ds_bai_viet = DB::table('hwp_posts')
            ->select('hwp_posts.post_date', 'hwp_posts.post_name')
            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->join('hwp_users', 'hwp_posts.post_author', '=', 'hwp_users.id')
            ->where('hwp_posts.post_type', '=', 'post')
            ->orderBy('hwp_posts.ID', 'desc')
            ->get()->toArray();
        foreach ($ds_bai_viet as $post){
            $site->add('https://rdone.net/'.$post->post_name,$post->post_date,1,'daily');
        }

        $list_tag = BaseController::getListTag();
        foreach ($list_tag as $tag){
            $site->add('https://rdone.net/category/'.$tag->slug,'',1,'daily');
        }
        $author = DB::table('hwp_users')
            ->select('user_login','user_registered')
            ->get()->toArray();
        foreach ($author as $user){
            $site->add('https://rdone.net/'.$user->user_login.'/page/0/',$user->user_registered,1,'daily');
        }


        $site->store('xml', 'sitemap');

        echo "<script>window.close();</script>";
    });

});
Route::prefix('/')->group(function () {
    // em chạy vào hàm postshow để lọc ra
    Route::get('{home?}', 'PostController@show')->name('postShow');
    Route::get('category/{slug}/', 'TagController@index')->name('category');
    Route::get('tag/{slug}/', 'TagController@index')->name('tag');
    Route::get('hd/{slug}/', 'PostController@post_huong_dan')->name('post_hd');
    Route::get('{slug}/page/{number}', 'AuthorController@index')->name('author');
    Route::get('/search', 'PostController@search')->name('postSearch');
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



