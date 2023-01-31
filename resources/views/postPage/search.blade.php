@extends('layouts.layout')
@section('meta')
    <title id="titleIndex">Tìm kiếm: {{ $s }}</title>
    <meta name="copyright" content="https://rdone.net/">
    <meta name="keywords"
          content="rdone, autocard, bản vẽ xây dựng, phần mềm xây dựng, xây dựng, excel, hướng dẫn excel">
    <meta name="description"
          content="Với từ khóa {{ $s }} các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến những tài liệu tốt nhất">
    <meta name="”robots”" content="”index,follow”">
    <meta name="author" content="{{$s}}">
    <meta property="og:site_name" content="rdone.net">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Tìm kiếm: {{ $s }}">
    <meta property="og:description"
          content="Với từ khóa {{ $s }}  các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến những tài liệu tốt nhất">
    <meta property="og:image" content="{{empty($item_search[0]->twitter_image)? "": $item_search[0]->twitter_image}}">
    <meta name="copyright" content="Copyright © 2021 RDone">
    <meta property="og:type" content="threed.asset">
    <meta property="og:asset" content="{{Request::url().'/'}}">
    <link rel="canonical" href="{{Request::url().'/'}}">
    <style>
        .entry-excerpt {
            line-height: 1.6rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
        }

        .entry-summary {
            line-height: 1.6rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .dg {
            display: grid;
            width: auto;
            max-width: 100%;
        }
    </style>

@endsection
@section('banner')
    @if(!empty($banner_ngang))
        <img id="banner-ngang" src="{{asset('banners/'.$banner_ngang)}}" alt="">
    @endif
@endsection
@section('content-left')
    <div class="content-left col-xxl-9 col-xl-9 col-lg-2 col-lg-9 col-md-9 col-sm-12 col-xs-12  ">
        @if(!empty($item_search))
            <div class="box-list-post hoverflow">
                <div class="arti-right">
                    <ul class="list-topic p-0 m-0">
                        @foreach ($item_search as $item)
                            <li class="itemtopic pt-3 pb-3"
                                style="border-bottom: 1px dashed rgba(0, 0, 0, 0.158);">
                                <div class="d-flex">
                                    <div class="me-3" style="width: 120px;height: 100px;">
                                        <a href="{{route('postShow',['home'=>$item->post_name]).'/'}}"
                                           style="width: 120px;height: 100px;">
                                            <img class="w-100 " style="width: 120px;height: 100px;"
                                                 src="{{$item->twitter_image}}"
                                                 alt=""/></a>
                                    </div>
                                    <div class="pb-2 dg">
                                        <a href="{{route('postShow',['home'=>$item->post_name]).'/'}}"
                                           class="fs-3 fw-bold">
                                            {{$item->post_title}}
                                        </a>
                                        <div class="entry-excerpt">
                                            {{strip_tags(substr(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $item->description), 0))}}
                                        </div>
                                        <div class="d-flex mt-2 justify-content-end">
                                            <a href="{{route('postShow',['home'=>$item->post_name]).'/'}}"
                                               class="fs-5 text-danger">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <div class="d-flex justify-content-center">
                {{ $item_search->appends(request()->all())->links('vendor.pagination.custom')}}
            </div>
        @else
            <h2 class="notSearch">Không tìm thấy bài viết có "{{$s}}".</h2>
        @endif

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
@section('footer')
    <footer class="container footer mt-4 pt-4">
        <ul class="list-tag pt-4 mt-4" style="border-top: 1px solid red;">
            <li class="d-inline fw-bold ">
                <a href="" class="fs-3">Tag: </a>
            </li>
            @foreach($list_tag as $tags)
                <li class="item-tag">
                    <a href="{{route('tag',['slug'=>$tags->slug]).'/'}}" class="fs-4">{{$tags->name}}</a>
                </li>
            @endforeach
        </ul>
    </footer>
@endsection

