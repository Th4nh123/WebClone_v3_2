@extends('layouts.layout')

@section('meta')

    <title id="titleIndex">Tác giả: {{$author[0]->display_name}}</title>
    <meta name="copyright" content="https://rdone.net/">
    <meta name="keywords"
          content="rdone, autocard, bản vẽ xây dựng, phần mềm xây dựng, xây dựng, excel, hướng dẫn excel">
    <meta name="description"
          content="Các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến những tài liệu tốt nhất">
    <meta name="”robots”" content="”index,follow”">
    <meta name="author" content="{{$author[0]->display_name}}">
    <meta property="og:site_name" content="rdone.net">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Tác giả: {{$author[0]->display_name}}">
    <meta property="og:description"
          content="Các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến những tài liệu tốt nhất">
    <meta property="og:image" content="{{$list_post_lien_quan[0]->twitter_image}}">
    <meta name="copyright" content="Copyright © RDone">
    <meta property="og:type" content="threed.asset">
    <meta property="og:asset" content="{{Request::url().'/'}}">
    <link rel="canonical" href="{{Request::url().'/'}}">

@endsection
<style>
    .entry-summary {
        line-height: 1.6rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .entry-excerpt {
        line-height: 1.6rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        font-size: 1.4rem;
        -webkit-box-orient: vertical;
    }
</style>
<div id="page" class="hfeed site" style="height: auto !important;">
    <div class="site-inner" style="height: auto !important;">
        <div id="content" class="site-content clearfix" style="height: auto !important;">
            <div class="content-archive site-main wrap container wrapper" style="height: auto !important;">
                @section('content-left')
                    <div class="content-left col-xxl-9 col-xl-9 col-lg-2 col-lg-9 col-md-9 col-sm-12 col-xs-12  ">
                        <div class="header-archive mb-4"
                             style="background: #eee none repeat scroll 0 0; padding: 20px 30px ; border-left: 5px solid red">
                            <p class="htitle" style="font-size: 32px">Tác giả: {{$author[0]->display_name}}</p>
                        </div>
                        <div class="box-list-post hoverflow">
                            <div class="arti-right">
                                <ul class="list-topic p-0 m-0">
                                    @foreach ($list_post_lien_quan as $value)
                                        <li class="itemtopic pt-3 pb-3"
                                            style="border-bottom: 1px dashed rgba(0, 0, 0, 0.158);">
                                            <div class="d-flex">
                                                <div class="me-3 col-2"
                                                     style="max-width: 120px;display: block;min-width:80px;height: 100px;">
                                                    <a href="{{Request::root()."/".$value->post_name.'/'}}"
                                                       style="width: 120px;height: 100px;">
                                                        <img class="w-100 " style="width: 120px;height: 100px;"
                                                             src="{{$value->twitter_image}}"
                                                             alt=""/></a>
                                                </div>
                                                <div class="pb-2">
                                                    <a href="{{Request::root()."/".$value->post_name.'/'}}"
                                                       class="fs-3 fw-bold text-dark">
                                                        {{$value->post_title}}
                                                    </a>
                                                    <div class="entry-excerpt">
                                                        {{strip_tags(substr(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value->post_content), 0))}}
                                                    </div>
                                                    <div class="d-flex mt-2 justify-content-end">
                                                        <a href="{{Request::root()."/".$value->post_name.'/'}}"
                                                           class="fs-5 text-danger">Xem chi tiết</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @include('vendor.pagination.pagi-author')
                        {{--                        <div class="d-flex justify-content-center">--}}
                        {{--                            {{ $list_post_lien_quan->appends(request()->query())->links('vendor.pagination.custom')}}--}}
                        {{--                        </div>--}}
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
                            <div class="title-theme fs-3 mb-3 pb-3"
                                 style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                                <strong>Chủ đề nổi bật</strong>
                            </div>
                            @for($i=0;$i<8; $i++)
                                @if(!empty($list_chu_de_noi_bat[$i]))
                                    <div class="arti-right">
                                        <ul class="list-topic p-0 m-0">
                                            <li class="itemtopic pt-3 pb-3"
                                                style="border-bottom: 1px dashed rgba(0, 0, 0, 0.158);">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"><img
                                                                class="w-100 "
                                                                style="max-width: 60px;height: 60px;"
                                                                src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                                alt=""/></a>
                                                    </div>
                                                    <div class="col-9 pb-2">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                                           class="fs-5 fw-bold text-dark">
                                                            {{$list_chu_de_noi_bat[$i]->post_title}}
                                                        </a>
                                                        <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            @endfor
                        </div>
                        <div class="block-theme pt-3 pb-2">
                            <div class="title-theme fs-3 mb-3 pb-3"
                                 style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                                <strong>Chủ đề hot nhất</strong>
                            </div>
                            @for($i=8;$i<16; $i++)
                                @if(!empty($list_chu_de_noi_bat[$i]))
                                    <div class="arti-right">
                                        <ul class="list-topic p-0 m-0">
                                            <li class="itemtopic pt-3 pb-3"
                                                style="border-bottom: 1px dashed rgba(0, 0, 0, 0.158);">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"><img
                                                                class="w-100 "
                                                                style="max-width: 60px;height: 60px;"
                                                                src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                                alt=""/></a>
                                                    </div>
                                                    <div class="col-9 pb-2">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                                           class="fs-5 fw-bold text-dark">
                                                            {{$list_chu_de_noi_bat[$i]->post_title}}
                                                        </a>
                                                        <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endif

                            @endfor
                        </div>
                        <div class="block-theme pt-3 pb-2">
                            <div class="title-theme fs-3 mb-3 pb-3"
                                 style="border-bottom: 1px solid rgba(138, 137, 137, 0.212);">
                                <strong>Được xem nhiều nhất</strong>
                            </div>
                            @for($i=0;$i<6; $i++)
                                @if(!empty($list_chu_de_noi_bat[$i]))
                                    <div class="arti-right">
                                        <ul class="list-topic p-0 m-0">
                                            <li class="itemtopic pt-3 pb-3"
                                                style="border-bottom: 1px dashed rgba(0, 0, 0, 0.158);">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"><img
                                                                class="w-100 "
                                                                style="max-width: 60px;height: 60px;"
                                                                src="{{$list_chu_de_noi_bat[$i]->twitter_image}}"
                                                                alt=""/></a>
                                                    </div>
                                                    <div class="col-9 pb-2">
                                                        <a href="{{Request::root()."/".$list_chu_de_noi_bat[$i]->post_name.'/'}}"
                                                           class="fs-5 fw-bold text-dark">
                                                            {{$list_chu_de_noi_bat[$i]->post_title}}
                                                        </a>
                                                        <span class="text-decrip-2 fs-5">
                        {{$list_chu_de_noi_bat[$i]->description}}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endsection
            </div>
        </div><!-- .site-content -->
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

    </div><!-- .site-inner -->
</div><!-- .site -->

