<?php

use Api\XuatDataController;
use Api\ChienDichController;
use Api\BlackListController;
use Api\KeyController;
use Api\KeyGoogleController;
use Api\KeyYoutubeController;
use Api\PostController;
use Api\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/rd/xml/a/export/{from}/{to}/{id_cam}','Admin\ToolCloneController@export_js');
Route::get('/rd/xml/a/export_txt/{from}/{to}/{id_cam}','Admin\ToolCloneController@export_txt_rd');
Route::get('/rd/xml/a/export_txt_wiki/{from}/{to}/{id_cam}','Admin\ToolCloneController@export_txt_wiki');

Route::get('/rd/xml/a/createTH_rd/{from}/{to}/{id_cam}','Admin\ToolCloneController@createTH_rd');
Route::get('/rd/xml/a/createTH_wiki/{from}/{to}/{id_cam}','Admin\ToolCloneController@createTH_wiki');
Route::get('/rd/xml/a/createTH_wiki_new/{from}/{to}/{id_cam}/{chon}','Admin\ToolCloneController@createTH_wiki_new');
Route::get('/rd/xml/a/createTH_xds/{from}/{to}/{id_cam}','Admin\ToolCloneController@createTH_xds');


Route::controller(ChienDichController::class)->group(function () {
    // Chiến dịch
    Route::get('/rd/xml/a/get-cam', 'getCam');
    Route::post('/rd/xml/a/save-cam', 'saveCam');
    Route::post('/rd/xml/a/delete-campaign', 'deleteCam');
    Route::post('/rd/xml/a/delete-all-campaign', 'deleteAllCam');
    Route::post('/rd/xml/a/update-cam/{id_campaign}', 'updateStatusCam');
    Route::post('/rd/xml/a/reset-cam/{id_campaign}', 'resetStatusCam');
});

Route::controller(KeyController::class)->group(function () {
    Route::get('/rd/xml/a/get-key', 'getKey');
    Route::get('/rd/xml/a/get-key-by-id-cam/{id_cam}','getKeyByIdCam');
    Route::get('/rd/xml/a/get-data-id-have-video/{id_key}','getDataIdHaveVideo');
    Route::get('/rd/xml/a/get-data-id-have-url-google/{id_key}','getDataIdHaveUrlGoogle');
    Route::get('/rd/xml/a/find-key/{name}', 'findLikeKey');
    Route::get('/rd/xml/a/get-ky-hieu/{key}','getKyHieu');
    Route::post('/rd/xml/a/save-key', 'saveKey');
    Route::post('/rd/xml/a/save-key-by-id-cam/{id_cam}', 'saveKeyByIdCam');
    Route::post('/rd/xml/a/reset-key/{key_word}', 'resetKey');
    Route::post('/rd/xml/a/update-key/{key_word}', 'updateKey');
    Route::post('/rd/xml/a/delete-key', 'xoaKey');
    Route::post('/rd/xml/a/delete-all-key/{id_cam}', 'xoaAllKeyByIdCam');
    Route::get('/rd/xml/a/get-id-key/{id_cam}', 'getIdKey');
    Route::get('/rd/xml/a/get-key-none-url/{id_cam}','getKeyNoneUrl');
});

Route::controller(UrlController::class)->group(function () {
    Route::post('/rd/xml/a/save-url/{id_key}', 'saveUrl');
    Route::get('/rd/xml/a/delete-url/{url}', 'xoaURL');
    Route::get('/rd/xml/a/get-url/{id_key}', 'getUrl');
    Route::get('/rd/xml/a/get-url-by-id/{id_url}', 'getUrlById');
    Route::get('/rd/xml/a/get-url-by-id-key/{id_key}', 'getUrlByIdKey');
    Route::get('/rd/xml/a/get-url-by-id-key2/{id_key}', 'getUrlByIdKey2');
    Route::get('/rd/xml/a/reset-url/{id_key}', 'resetUrl');
    Route::post('/rd/xml/a/update-vi-tri/{key_word}', 'updateViTri');
    Route::post('/rd/xml/a/save-video', 'saveVideo');
    Route::post('/rd/xml/a/save-web', 'saveWeb');
    Route::post('/rd/xml/a/save-file', 'saveFileType');
    Route::post('/rd/xml/a/delete-url-by-id-key/{id_key}', 'xoaURLByIdKey');
});


Route::controller(BlackListController::class)->group(function () {
    Route::get('/rd/xml/a/get-black-list-by-id-cam/{id_cam}', 'getBlackListByIdCam');
    Route::post('/rd/xml/a/save-black-list-by-id-cam/{id_cam}', 'saveBlackListByIdCam');
    Route::post('/rd/xml/a/delete-black-list', 'deleteBlackKey');
    Route::post('/rd/xml/a/delete-all-black-list/{id_cam}', 'deleteAllBlackKeyByIdCam');
});

Route::controller(PostController::class)->group(function () {
    Route::post('/rd/xml/a/tool-clone', 'parseURL');
    Route::get('/rd/xml/a/delete-post-by-id-key/{id_key}', 'xoaPostByIdKey');
    Route::get('/rd/xml/a/check-video/{id_key}', 'checkVideo');
    Route::get('/rd/xml/a/save-image-by-id-key/{id_key}', 'saveImgByKey');
    Route::get('/rd/xml/a/get-bai-viet-all', 'getBaiVietAll');
    Route::get('/rd/xml/a/get-detail-post/{id_post}', 'getDetailPost');
    Route::get('/rd/xml/a/find-like-url/{name}', 'findLikeUrl');
});

// Route::get('/rd/xml/a/create_toplist/{id}', 'taoBaiTH');


// Route::get('/rd/xml/a/get-key-none-url/{id_cam}','getKeyNoneUrl');
// Route::post('/rd/xml/a/save-key-by-id-cam/{id_cam}', 'saveKeyByIdCam');

// Route::get('/rd/xml/a/get-count-check-url-by-id-cam/{id_cam}','getCountCheckURLByIdCam');

// // Api post tool v2


Route::controller(KeyGoogleController::class)->group(function () {
    Route::get('/rd/xml/a/get-all-key-google', 'getAllKeyGoogle');
    Route::get('/rd/xml/a/get-key-google', 'getKeyGoogle');
    Route::get('/rd/xml/a/get-first-key-google', 'getFirstKeyGoogle');
    Route::post('/rd/xml/a/get-next-key-google/{key}', 'getNextKeyGoogle');
    Route::post('/rd/xml/a/save-key-google', 'saveKeyGoogle');
    Route::post('/rd/xml/a/delete-key-google', 'deleteKeyGoogle');
    Route::post('/rd/xml/a/delete-all-key-google', 'deleteAllKeyGoogle');
    Route::post('/rd/xml/a/update-count-key-google/{key_gg}', 'updateCountKeyGoogle');
    Route::post('/rd/xml/a/reset-all-key-google', 'resetAllKeyGoogle');
});
// Route::get('/rd/xml/a/update-count-key-google/{key}', 'updateCountKeyGoogle');
// Route::get('/rd/xml/a/get-key-google', 'getKeyGoogle');
// Route::get('/rd/xml/a/get-next-key-google/{key}', 'getNextKeyGoogle');
// Route::get('/rd/xml/a/get-first-key-google', 'getFistKeyGoogle');
// Route::get('/rd/xml/a/get-all-key-google', 'getAllKeyGoogle');
// Route::post('/rd/xml/a/save-key-google', 'saveKeyGoogle');
// Route::get('/rd/xml/a/delete-key-google/{id_key_gg}', 'deleteKeyGoogle');

Route::controller(KeyYoutubeController::class)->group(function () {
    Route::get('/rd/xml/a/get-key-youtube', 'getKeyYoutube');
    Route::get('/rd/xml/a/get-all-key-youtube', 'getAllKeyYoutube');
    Route::get('/rd/xml/a/get-first-key-youtube', 'getFirstKeyYoutube');
    Route::post('/rd/xml/a/save-key-youtube', 'saveKeyYoutube');
    Route::post('/rd/xml/a/delete-key-youtube', 'deleteKeyYoutube');
    Route::post('/rd/xml/a/delete-all-key-youtube', 'deleteAllKeyYoutube');
    Route::post('/rd/xml/a/update-count-key-youtube/{key_yt}', 'updateCountKeyYoutube');
    Route::post('/rd/xml/a/get-next-key-youtube/{key}', 'getNextKeyYoutube');
    Route::post('/rd/xml/a/reset-all-key-youtube', 'resetAllKeyYoutube');
    Route::post('/rd/xml/a/update-count-request/{count_y}/{count_g}/{id_key}', 'updateCountRequest');
    Route::get('/rd/xml/a/get-total-request/{id_cam}', 'getTotalRequest');
});


Route::controller(XuatDataController::class)->group(function () {
    Route::get('/rd/xml/a/export/{from}/{to}/{id_cam}','export_js');
    Route::get('/rd/xml/a/export_txt_wiki/{from}/{to}/{id_cam}','export_txt_wiki');

    Route::get('/rd/xml/a/createTH_wiki_new/{from}/{to}/{id_cam}/{chon}','createTH_wiki_new');
    Route::get('/rd/xml/a/createTH_xds/{from}/{to}/{id_cam}','createTH_xds');

});