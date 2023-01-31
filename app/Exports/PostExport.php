<?php

namespace App\Exports;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PostExport implements FromArray,WithStrictNullComparison,WithHeadings,WithColumnWidths
{
    /**
    * @return \Illuminate\Database\Eloquent\Builder
     */
    public function array():array
    {
        $post =  DB::table('hwp_posts')
            ->select('hwp_posts.id','hwp_posts.post_date','post_title','hwp_yoast_indexable.description','hwp_posts.post_name',
            'hwp_yoast_indexable.twitter_image','hwp_yoast_indexable.meta_robot','hwp_posts.id_key','hwp_posts.id_url','hwp_url.url')
            ->join('hwp_yoast_indexable', 'hwp_yoast_indexable.object_id', '=', 'hwp_posts.id')
            ->join('hwp_key','hwp_key.id','=','hwp_posts.id_key')
            ->join('hwp_url','hwp_url.id','=','hwp_posts.id_url')
            ->where('hwp_key.check','=',1)->get()->toArray();

        foreach ($post as $p){
            $url = "https://rdone.net/".$p->post_name;
            $p->post_name=$url;
        }
        return $post;
    }


    public function headings(): array
    {
        return [
            "ID",
            'post_date',
            "post_title",
            'description',
            "post_name",
            "image",
            'meta_robot',
            'id_key',
            'id_url',
            'url'
        ];
    }

    public function columnWidths(): array
    {
        return [
            "A"=>10,
            'B'=>15,
            "C"=>50,
            'D'=>20,
            "E"=>50,
            "F"=>30,
            'G'=>10,
            'H'=>5,
            'I'=>5,
            'K'=>50
        ];
    }
}
