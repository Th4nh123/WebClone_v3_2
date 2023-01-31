<?php

namespace App\Console\Commands;

use App\Http\Controllers\BaseController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CreateSiteMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
            $site->add('https://rdone.net/tag/'.$tag->slug,'',1,'daily');
        }
        $author = DB::table('hwp_users')
            ->select('user_login','user_registered')
            ->get()->toArray();
        foreach ($author as $user){
            $site->add('https://rdone.net/'.$user->user_login.'/page/0/',$user->user_registered,1,'daily');
        }


        $site->store('xml', 'sitemap');
        echo "<script>window.close();</script>";
        return "Site map";
    }
}
