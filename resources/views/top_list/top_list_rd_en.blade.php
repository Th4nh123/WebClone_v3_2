@extends('layouts.layout')
@section('meta')
    <title id="titleIndex">{{$key->tien_to}} {{$key->ten}} {{$key->hau_to}}</title>
    <meta name="keywords" content="{{$post_detail[0]->primary_focus_keyword}}">
    <meta name="robots" content="{{$post_detail[0]->meta_robot}}"/>
    <meta name="description" content="Chủ đề cập nhật mẫu {{$key->ten}}được các kiến trúc sư và chủ đầu
          tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển
          chọn trong bài viết này.">
    <meta property="og:site_name" content="{{Request::url().'/'}}">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:title" content="{{$key->ten}}">
    <meta property="og:description" content="Chủ đề cập nhật mẫu {{$key->ten}}được các kiến trúc sư và chủ đầu
                    tư đang quan tâm đến. Tham khảo toplist các mẫu thiết kế nổi bật, xu hướng kiến trúc mới được tuyển
                    chọn trong bài viết này.">
    <meta property="og:image" content="{{$post_detail[0]->twitter_image}}">
    <meta property="og:asset" content="{{Request::url().'/'}}">
    <link rel="canonical" href="{{Request::url().'/'}}">
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
                <p>Updated topic <b><a href="{{route('top_list_rd',['slug'=>$key->url_key_cha])}}">{{$key->ten}}</a></b> by architects and owners
                    are interested in. Refer to the toplist of outstanding designs, newly recruited architectural trends
                    selected in this article.</p>
                @php
                    if (count($post_detail) <= 10){
                        $count_page = count($post_detail);
                    }
                    else if(count($post_detail) <= 20 && count($post_detail) > 10){
                        $count_page = 10;
                        $count_bvlq = count($post_detail);
                    }
                    else if(count($post_detail) > 20){
                        $count_page = 10;
                        $count_bvlq = 20;
                    }


                @endphp
                <div id="toc_container"><p class="toc_title">TABLE OF CONTENT</p><ul>
                        @for($i =0;$i<$count_page;$i++)
                            <li>
                                <a href="#{{$post_detail[$i]->post_name}}">{{$post_detail[$i]->post_title}}</a>
                            </li>
                            @php
                                $tocGenerator = new \TOC\TocGenerator();
                                $htmlOut =  $tocGenerator->getHtmlMenu($post_detail[$i]->post_content,2,2);
                                echo $htmlOut;
                            @endphp
                        @endfor

                    </ul>
                </div>
                @for($i =0;$i<$count_page;$i++)
                    <h2 id="{{$post_detail[$i]->post_name}}" class="post_title">
                        {{$post_detail[$i]->post_title}}
                    </h2>
                    <div class="non_toc" style="background: #eee; border: 1px solid #ccc; padding: 5px 10px;">
                        @php
                            $last = strpos($post_detail[$i]->url,"/",8);
                            $domain = substr($post_detail[$i]->url,0,$last);
                            $random = rand(1000,5000);
                        @endphp
                        <p>Author: {{$domain}}</p>
                        <p>Rate: 5 ⭐ ({{$random}} reviews)</p>
                        <p>Tóm tắt: {{$post_detail[$i]->description}}}</p>
                        {!! html_entity_decode($post_detail[$i]->post_content) !!}
                    </div>

                @endfor
                @if(count($post_detail)>10)
                    <section class="related m-b-15" style="margin-top: 30px;">
                        <header>
                            <div class="title">
                                <span class="icon_oneweb"></span>
                            </div>
                        </header>
                        <div id="show_post_related">


                            <div class="row fix-safari">
                                <div class="member_exps col-xs-12">
                                    <h3><span
                                            class=" title_text primary-color text-uppercase font-bold">Related Searches</span>
                                    </h3>
                                    <div class="row auto-clear fix-safari" style="margin-top: 30px">

                                        @for($i =$count_page;$i<$count_bvlq;$i++)
                                            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 m-b-15"
                                                 style="border-bottom: 1px solid #3a3a3a33;padding-bottom: 10px;">
                                                <div class="image">
                                                    <a href="{{Request::root()."/".$post_detail[$i]->post_name.'/'}}"
                                                       title="{{$post_detail[$i]->post_title}}" target="_self" class=""><img
                                                            src="{{$post_detail[$i]->twitter_image}}"
                                                            alt="{{$post_detail[$i]->post_title}}"
                                                            class="img-responsive" width="332" height="265"/></a></div>
                                                <div style="margin-top: 0px" class="name font-bold text-left m-t-15">
                                                    <a href="{{Request::root()."/".$post_detail[$i]->post_name.'/'}}"
                                                       title="{{$post_detail[$i]->post_title}}" target="_self"
                                                       class="name ">{{$post_detail[$i]->post_title}}</a></div>
                                                <span class="text-decrip-2 fs-5"
                                                      style="color: #646464;font-size: 12px;margin-top: 3px;letter-spacing: 0.5px;line-height: 20px;">
                        {{$post_detail[$i]->description}}</span>
                                            </div>
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



