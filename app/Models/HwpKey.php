<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class HwpKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tien_to',
        'ten',
        'hau_to',
        'url_key_cha',
        'id_cam',
        'key_con_1',
        'url_key_con_1',
        'key_con_2',
        'url_key_con_2',
        'key_con_3',
        'url_key_con_3',
        'key_con_4',
        'url_key_con_4',
        'top_view_1',
        'url_top_view_1',
        'top_view_2',
        'url_top_view_2',
        'top_view_3',
        'url_top_view_3',
        'top_view_4',
        'url_top_view_4',
        'top_view_5',
        'url_top_view_5',
        'ky_hieu',
        'id_list_vd'        
    ];
}
