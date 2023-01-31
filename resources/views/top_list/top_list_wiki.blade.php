@extends('layouts.layout')
@section('meta')
    <title id="titleIndex">{{$key->tien_to}} {{$key->ten}} {{$key->hau_to}}</title>
    <meta name="keywords" content="{{$post_detail[0]->primary_focus_keyword}}">
    <meta name="robots" content="{{$post_detail[0]->meta_robot}}"/>
    <meta name="description" content="Cập nhật thông tin và kiến thức về {{$key->ten}}
        chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên Thoitrangwiki.">
    <meta property="og:site_name" content="{{Request::url().'/'}}">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:title" content="{{$key->ten}}">
    <meta property="og:description" content="Cập nhật thông tin và kiến thức về {{$key->ten}}
        chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên Thoitrangwiki.">
    <meta property="og:image" content="{{$post_detail[0]->twitter_image}}">
    <meta property="og:asset" content="{{Request::url().'/'}}">
    <link rel="canonical" href="{{Request::url().'/'}}">
    <style>
        .ifream {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
        }

        /* Then style the iframe to fit in the container div with full height and width */
        .responsive-iframe {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
        }
    </style>
    @if(!empty($post_detail))
        @for($i =0;$i<count($post_detail);$i++)
            <link rel="dns-prefetch" href="{{$post_detail[$i]->twitter_image}}"/>
            <link rel="preconnect" href="https://web1"/>
        @endfor
    @endif
@endsection

@section('content-left')
    <div class="content-left col-xxl-9 col-xl-9 col-lg-2 col-lg-9 col-md-9 col-sm-12 col-xs-12  ">
        <article id="post-9450"
                 class="loop-single clearfix post-9450 post type-post status-publish format-standard has-post-thumbnail hentry category-mau-nha-dep hocwp-post entry">
            <header class="entry-header">
                <h1 class="entry-title">{{$key->tien_to}} {{$key->ten}} {{$key->hau_to}}</h1>
                <div class="post-meta">
                    <span class="meta-date date updated"><i class="fa fa-calendar"></i>Post on 20/08/2022</span>
                </div>
            </header>
            <div class="entry-content">
                <p>Cập nhật thông tin và kiến thức về <b><a
                            href="https://thoitrangwiki.com/{{$key->url_key_cha}}.html">{{$key->ten}}</a></b>
                    chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội
                    ngũ biên tập viên Thoitrangwiki.</p>

                <div id="toc_container"><p class="toc_title">MỤC LỤC</p>
                    <ul>
                        @for($i =0;$i<count($post_detail);$i++)
                            <li>
                                <a href="#{{$post_detail[$i]->post_name}}">{{$post_detail[$i]->post_title}}</a>
                            </li>
                            @if($i==4 && !empty($video))
                                <li>
                                    <a href="#{{$i}}"><b>YOUTUBE: </b> {{$video->video_title}}</a>
                                </li>
                            @endif
                        @endfor
                        @if(count($post_detail)<4)
                                <li>
                                    <a href="#video"><b>YOUTUBE: </b> {{$video->video_title}}</a>
                                </li>
                        @endif
                    </ul>
                </div>
                <p style="font-size: 25px">Kết quả tìm kiếm Google: <b>{{$key->ten}}</b></p>
                <img src="{{$post_detail[0]->twitter_image}}"
                     alt="{{$post_detail[0]->post_title}}"
                     style="width: 100%;">
                @for($i =0;$i<count($post_detail);$i++)
                    <blockquote
                        style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                        <h2 id="{{$post_detail[$i]->post_name}}" class="post_title">
                            {{$post_detail[$i]->post_title}}
                        </h2>
                        <div>
                            @php
                                $last = strpos($post_detail[$i]->url,"/",8);
                                $domain = substr($post_detail[$i]->url,0,$last);
                                $random = rand(1000,5000);
                            @endphp
                            <p>Tác giả: {{$domain}}</p>
                            <p>Đánh giá: 5 ⭐ ({{$random}} lượt đánh giá)</p>
                        </div>
                        <div class="clearfix"><img src="{{$post_detail[$i]->twitter_image}}"
                                                   alt="{{$post_detail[$i]->post_title}}"
                                                   style="width: 130px;height:130px; object-fit:cover; margin-right: 20px;float: left">
                            <p class="text-decrip-4" style="line-height: 25px;">{{$post_detail[$i]->description}}
                                ...<strong></strong></p>
                            <a href="{{$post_detail[$i]->url}}"
                               target="_blank"
                               style="float: right; font-size: 20px; margin-right: 30px"
                               rel="noopener,nofollow"><b style="margin-left: 25px">Xem ngay</b></a>
                        </div>

                    </blockquote>
                    @if($i==4 && !empty($video))
                        <blockquote
                            style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                            <h2 id="{{$i}}" class="post_title">
                                {{$video->video_title}}
                            </h2>
                            <p>{{$video->video_description}}</p>
                            <div class="ifream">
                                <iframe class="responsive-iframe" width="100%" height=""
                                        src="{{$video->link}}">
                                </iframe>
                            </div>

                        </blockquote>
                    @endif
                @endfor
                @if(count($post_detail) <4)
                    <blockquote
                        style="-webkit-text-stroke-width: 0px; background: white; border: 1px solid black; border-radius: 7px; box-sizing: border-box; clear: right; color: #181818; font-family:Gotham SSm A,Gotham SSm B, Gotham, sans-serif; font-style: normal; font-variant-caps: normal; font-variant-ligatures: normal; font-weight: 300; letter-spacing: normal; line-height: 1.6em; margin: 1.5em 0px; orphans: 2; padding: 1.6em; text-align: start; text-decoration-color: initial; text-decoration-style: initial; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;">
                        <h2 id="video" class="post_title">
                            {{$video->video_title}}
                        </h2>
                        <p>{{$video->video_description}}</p>
                        <div class="ifream">
                            <iframe class="responsive-iframe" width="100%" height=""
                                    src="{{$video->link}}">
                            </iframe>
                        </div>

                    </blockquote>
                @endif
                @if($count > 1)
                    <section class="related m-b-15" style="margin-top: 30px;">
                        <header>
                            <div class="title">
                                <span class="icon_oneweb"></span>
                            </div>
                        </header>
                        <div id="show_post_related">


                            <div class="row fix-safari">
                                <div class="member_exps col-xs-12">
                                    <span class=" title_text primary-color text-uppercase font-bold">Tìm kiếm có liên quan: {{$key->ten}}</span>
                                    <div class="row auto-clear fix-safari" style="margin-top: 30px">
                                        @php
                                            $index=0;
                                        @endphp
                                        @for($i =$count-2;$i>=0;$i--)
                                            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 m-b-15"
                                                 style="border-bottom: 1px solid #3a3a3a33;padding-bottom: 10px;">
                                                <div class="image">
                                                    <a href="{{Request::root()."/top-list/wiki/".$list_key[$i]->url_key_cha.'/'}}"
                                                       title="{{$list_key[$i]->ten}}" target="_self" class=""><img
                                                            src="{{$list_img[$i]}}"
                                                            alt="{{$list_key[$i]->ten}}"
                                                            class="img-responsive" width="332" height="265"/></a></div>
                                                <h3 style="margin-top: 0px" class="name font-bold text-left m-t-15">
                                                    <a href="{{Request::root()."/top-list/wiki/".$list_key[$i]->url_key_cha.'/'}}"
                                                       title="{{$list_key[$i]->ten}}" target="_self"
                                                       class="name ">{{$list_key[$i]->tien_to}} {{$list_key[$i]->ten}} {{$list_key[$i]->hau_to}}</a>
                                                </h3>
                                                <span class="text-decrip-2 fs-5"
                                                      style="color: #646464;font-size: 12px;margin-top: 3px;letter-spacing: 0.5px;line-height: 20px;">
                        Cập nhật thông tin và kiến thức về {{$list_key[$i]->ten}}
                    chi tiết và đầy đủ nhất, bài viết này đang là chủ đề đang được nhiều quan tâm được tổng hợp bởi đội ngũ biên tập viên Thoitrangwiki.</span>
                                            </div>
                                            @php
                                                $index++;
                                                if ($index==15){
                                                    break;
                                                }
                                            @endphp
                                        @endfor

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section><!--  end .related -->
                @endif
            </div>

        </article>
    </div>
@endsection

@section('content-right')
    <div class="content-right  col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 ">
        <form method="get" class="input-group" action="{{route('postSearch')}}">
            <input
                type="text"
                class="form-control fs-4 search-field"
                placeholder="Tìm kiếm bài viết"
                aria-label="Username"
                aria-describedby="basic-addon2"
                value=""
                name="s"
            />
            <input type="submit" class="search-submit" value="Tìm kiếm">
        </form>
        <div class="block-theme pt-3 pb-2">
            <div class="title-theme fs-3 mb-3 pb-3" style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                <strong>Chủ đề nổi bật</strong>
            </div>

            <div class="inner">
                @for($i=0;$i<5; $i++)
                    @if(!empty($list_chu_de_noi_bat[$i]))
                        <div class="pull-left">
                            <div style="width: 100%;">
                                <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                   title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                                   class=""><img src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                 alt="{{$list_chu_de_noi_bat[$i]->post_title}}" width="332"
                                                 height="265" class=" m-r-15"/></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold"
                               style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
                            <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                            <hr>
                        </div>
                    @endif
                @endfor
            </div>


        </div>
        <div class="block-theme pt-3 pb-2">
            <div class="title-theme fs-3 mb-3 pb-3" style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                <strong>Chủ đề hot nhất</strong>
            </div>
            <div class="inner">
                @for($i=5;$i<10; $i++)
                    @if(!empty($list_chu_de_noi_bat[$i]))
                        <div class="pull-left">
                            <div style="width: 100%;">
                                <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                   title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                                   class=""><img src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                 alt="{{$list_chu_de_noi_bat[$i]->post_title}}" width="332"
                                                 height="265" class=" m-r-15"/></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold"
                               style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
                            <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                            <hr>
                        </div>
                    @endif
                @endfor
            </div>

        </div>
        @if(!empty($banner_doc))
            <img id="banner-doc" src="{{asset('banners/'.$banner_doc)}}" alt=""
                 style="height: 420px; object-fit: cover">
        @endif
        <div class="block-theme pt-3 pb-2">
            <div class="title-theme fs-3 mb-3 pb-3" style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                <strong>Được xem nhiều nhất</strong>
            </div>
            <div class="inner">
                @for($i=0;$i<5; $i++)
                    @if(!empty($list_chu_de_noi_bat[$i]))
                        <div class="pull-left">
                            <div style="width: 100%;">
                                <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                   title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                                   class=""><img src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                 alt="{{$list_chu_de_noi_bat[$i]->post_title}}" width="332"
                                                 height="265" class=" m-r-15"/></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold"
                               style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
                            <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                            <hr>
                        </div>
                    @endif
                @endfor
            </div>

        </div>
    </div>
@endsection



