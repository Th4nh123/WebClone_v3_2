@extends('layouts.layout')
@section('meta')
    <title id="titleIndex">{{$post_detail_hd[0]->postHD_title}}</title>
    <meta name="keywords" content="{{$post_detail_hd[0]->primary_focus_keyword}}">
    <meta name="robots" content="{{$post_detail_hd[0]->meta_robot}}" />
    <meta name="description" content="{{$post_detail_hd[0]->description}}">
    <meta property="og:site_name" content="{{Request::url().'/'}}">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:title" content="{{$post_detail_hd[0]->postHD_title}}">
    <meta property="og:description" content="{{$post_detail_hd[0]->description}}">
    <meta property="og:image" content="{{$post_detail_hd[0]->twitter_image}}">
    <meta property="og:asset" content="{{Request::url().'/'}}">
    <link rel="canonical" href="{{Request::url().'/'}}">
@endsection
@section('banner')
    @if(!empty($banner_ngang))
        <img id="banner-ngang" src="{{asset('banners/'.$banner_ngang)}}" alt="">
    @endif
@endsection
@section('content-left')
    <div class="content-left col-xxl-9 col-xl-9 col-lg-2 col-lg-9 col-md-9 col-sm-12 col-xs-12  ">
        <header class="entry-header">
            <h1 class="entry-title">{{$post_detail_hd[0]->postHD_title}}</h1>
            <div class="post-meta">
                <span class="meta-date date updated"><i class="fa fa-calendar"></i>{{$post_detail_hd[0]->postHD_date}}</span>
                <span class="category-post"><i class="fa fa-archive"></i><a
                        href="https://rdone.net/category/{{$hwp_terms[0]->slug.'/'}}"
                        rel="tag">{{$hwp_terms[0]->name}}</a></span>
            </div>
        </header>
        <div class="entry-content">

            {!! html_entity_decode($post_detail_hd[0]->postHD_content) !!}
            @if(!empty($list_bv_tham_khao))
                <section class="related m-b-15" style="margin-top: 30px;">
                    <header>
                        <div class="title">
                            <span class="icon_oneweb"></span>
                        </div>
                    </header>
                    <div id="show_post_related">


                        <div class="row fix-safari" >
                            <div class="member_exps col-xs-12">
                                <h3><span
                                        class=" title_text primary-color text-uppercase font-bold">Bài viết liên quan</span>
                                </h3>
                                <div class="row auto-clear fix-safari" style="margin-top: 30px">

                                    @foreach($list_bv_tham_khao as $item)
                                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 m-b-15" style="border-bottom: 1px solid #3a3a3a33;padding-bottom: 10px;">
                                            <div class="image">
                                                <a href="{{Request::root()."/".$item->post_name.'/'}}"
                                                   title="{{$item->post_title}}" target="_self" class=""><img
                                                        src="{{$item->twitter_image}}" alt="{{$item->post_title}}"
                                                        class="img-responsive" width="332" height="265"/></a></div>
                                            <div style="margin-top: 0px" class="name font-bold text-left m-t-15">
                                                <a href="{{Request::root()."/".$item->post_name.'/'}}"
                                                   title="{{$item->post_title}}" target="_self"
                                                   class="name text-decrip-2">{{$item->post_title}}</a></div>
                                            <span class="text-decrip-2 fs-5" style="color: #646464;font-size: 12px;margin-top: 3px;letter-spacing: 0.5px;line-height: 20px;">
                        {{$item->description}}</span>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </section><!--  end .related -->
            @endif
        </div>

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
                                                 height="265" class=" m-r-15" /></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold" style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
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
                                                 height="265" class=" m-r-15" /></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold" style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
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
                                                 height="265" class=" m-r-15" /></a>
                            </div>
                            <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                               title="{{$list_chu_de_noi_bat[$i]->post_title}}" target="_self"
                               class="name font-bold" style="font-size: 16px ">{{$list_chu_de_noi_bat[$i]->post_title}}</a>
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


