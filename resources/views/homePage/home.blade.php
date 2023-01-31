@extends('layouts.layout')
@section('meta')
    <title id="titleIndex">RDONE - Chia sẻ tài liệu xây dựng</title>
    <meta name="copyright" content="https://rdone.net/">
    <meta name="keywords"
          content="rdone, autocard, bản vẽ xây dựng, phần mềm xây dựng, xây dựng, excel, hướng dẫn excel">
    <meta name="description"
          content="Các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến ...">
    <meta name="author" content="spec.edu.vn">
    <meta property="og:site_name" content="rdone.net">
    <meta property="og:url" content="{{Request::url().'/'}}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Trang chủ">
    <meta property="og:description"
          content="Các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao. Nhưng Rdone của chúng tôi luôn mang đến ...">
    <meta property="og:image" content="https://rdone.net/wp-content/uploads/2020/04/bia-3.png">
    <meta name="copyright" content="Copyright © 2021 SPEC LEARNING">
    <meta property="og:type" content="threed.asset">
    <meta property="og:asset" content="https://rdone.net/">
    <link rel="canonical" href="https://rdone.net/">

@endsection
@section('banner')
    @if(!empty($banner_ngang))
        <img id="banner-ngang" src="{{asset('banners/'.$banner_ngang)}}" alt="">
    @endif
@endsection
@section('content-left')
    <div class="content-left col-xxl-9 col-xl-9 col-lg-2 col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'phan-mem-xay-dung']).'/'}}" class="text-dark">Phần mềm xây
                    dựng</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_7[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_7[0]->twitter_image}}"
                            alt="{{$term_relationship_7[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class="text-dark"
                                   href="{{Request::root()."/".$term_relationship_7[0]->post_name.'/' }}">{{$term_relationship_7[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Các bạn có thể tìm thấy rất nhiều file autocad về chi tiết trần thạch cao, công nghệ thi công với vật liệu thạch cao.
                        Nhưng Rdone của chúng tôi luôn mang đến cho bạn đọc những thư viện cad chất lượng...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_7[$i]->post_name.'/' }}"><img
                                                class="w-100 text-dark"
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_7[$i]->twitter_image}}"
                                                alt="{{$term_relationship_7[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_7[$i]->post_name.'/' }}"
                                           class="fs-4 text-dark"
                                        >{{$term_relationship_7[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'tai-lieu-thi-cong'])}}" class=" text-dark">Tài liệu thi công</a>
            </div>
            <section class="block-arti row m-4">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_5[0]->post_name.'/' }}"><img

                            src="{{$term_relationship_5[0]->twitter_image}}"
                            alt="{{$term_relationship_5[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_5[0]->post_name.'/' }}">{{$term_relationship_5[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Bộ tài liệu dùng để thiết kế thi công thi công khoan lỗ lấp lỗ Sleeves hay nhất chỉ có tại Rdone!
                        Bộ tài liệu được dịch từ tiếng Anh và đi kèm song song với tiếng Việt. Biện pháp thi công khoan lỗ lấp...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_5[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_5[$i]->twitter_image}}"
                                                alt="{{$term_relationship_5[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_5[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_5[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'tai-lieu-thiet-ke'])}}" class=" text-dark">Tài liệu thiết kế</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_6[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_6[0]->twitter_image}}"
                            alt="{{$term_relationship_6[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_6[0]->post_name.'/' }}">{{$term_relationship_6[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Trong quá trình thiết kế thi công nhà, Các bạn có thể tìm thấy rất nhiều tài liệu về thang máy trên mạng hiện nay. Nhưng Rdone luôn mang đến cho bạn những tài liệu đầy đủ và trong tính toán và thiết...Trong quá trình thiết kế thi công nhà, Các bạn có thể tìm thấy rất nhiều tài liệu về thang máy trên mạng hiện nay.
                        Nhưng Rdone luôn mang đến cho bạn những tài liệu đầy đủ và trong tính toán và thiết...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_6[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_6[$i]->twitter_image}}"
                                                alt="{{$term_relationship_6[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_6[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_6[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'tieu-chuan']).'/'}}" class=" text-dark">Tiêu chuẩn</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_17[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_17[0]->twitter_image}}"
                            alt="{{$term_relationship_17[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_17[0]->post_name.'/' }}">{{$term_relationship_17[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Để tìm được những tài liệu hay, chi tiết thì các bạn hay đến với Rdone nhé.
                        Các bạn sẽ tìm thấy một kho tài liệu xây dựng hay, hữu ích nhất đó. Hôm nay để giúp mọi người tìm kiếm tài liệu dễ dàng hơn....
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_17[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_17[$i]->twitter_image}}"
                                                alt="{{$term_relationship_17[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_17[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_17[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'thu-vien']).'/'}}" class=" text-dark">Thư viện</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_8[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_8[0]->twitter_image}}"
                            alt="{{$term_relationship_8[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_8[0]->post_name.'/' }}">{{$term_relationship_8[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Cương lĩnh chính trị đầu tiên của Đảng là một trong những tiểu luận tương đối thú vị trong quá trình học chính trị của các bạn sinh viên Đại học.
                        Bài tiểu luận đòi hỏi sinh viên phải nắm vững được...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_8[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_8[$i]->twitter_image}}"
                                                alt="{{$term_relationship_8[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_8[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_8[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'bang-tinh-excel']).'/'}}" class=" text-dark">Bảng tính excel trong xây
                    dựng</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_21[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_21[0]->twitter_image}}"
                            alt="{{$term_relationship_21[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_21[0]->post_name.'/' }}">{{$term_relationship_21[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Mẫu rút dự toán ngân sách nhà nước – Tài liệu hay nhất gồm cả file docx, excel đều tải về miễn phí Như các bạn cũng đã hiểu
                        rất rõ về Rdone chúng tôi, chúng tôi luôn đem đến cho các bạn tài liệu về...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_21[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_21[$i]->twitter_image}}"
                                                alt="{{$term_relationship_21[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_21[$i]->post_name.'/' }}"
                                           class="fs-4 text-dark"
                                        >{{$term_relationship_21[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'du-toan']).'/'}}" class=" text-dark">Dự toán</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_70[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_70[0]->twitter_image}}"
                            alt="{{$term_relationship_70[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_70[0]->post_name.'/' }}">{{$term_relationship_70[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Với giá vật tư đang tăng cao như hiện nay thì việc ước lượng khoản chi phí cần bỏ ra để xây dựng một căn nhà hoàn chỉnh được rất nhiều người quan tâm.
                        Nhất là đối với những chủ nhà đang có ý định xây...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_70[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_70[$i]->twitter_image}}"
                                                alt="{{$term_relationship_70[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_70[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_70[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'thu-vien-sketchup']).'/'}}" class=" text-dark">Thư viện Sketchup</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_86[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_86[0]->twitter_image}}"
                            alt="{{$term_relationship_86[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_86[0]->post_name.'/'}}">{{$term_relationship_86[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Hiên nay có rất nhiều phần mềm đồ họa 3D phục vụ các kiến trúc sư.
                        Trong đó sketchup là một trong những phần mềm mạnh mẽ và được tin dùng nhiều nhất . Và các thư viện trong sketchup rất nhiều  mà bạn...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_86[0]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_86[$i]->twitter_image}}"
                                                alt="{{$term_relationship_86[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_86[0]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_86[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'thu-vien-revit']).'/'}}" class=" text-dark">Thư viện Revit</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_85[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_85[0]->twitter_image}}"
                            alt="{{$term_relationship_85[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_85[0]->post_name.'/' }}">{{$term_relationship_85[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Để phục vụ cho việc thiết kế bằng revit theo những tiêu chuẩn việt nam.
                        Việc sử dụng cho mình những file template theo tcvn là điều cần thiết với các ky sư. Tài liệu về nó cũng có rất nhiều đặc biệt...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_85[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_85[$i]->twitter_image}}"
                                                alt="{{$term_relationship_85[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_85[$i]->post_name.'/' }}"
                                           class="fs-4 text-dark"
                                        >{{$term_relationship_85[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'thu-vien-3dsmax']).'/'}}" class=" text-dark">Thư viện 3DsMax</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_87[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_87[0]->twitter_image}}"
                            alt="{{$term_relationship_87[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_87[0]->post_name.'/' }}">{{$term_relationship_87[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Với thời đại công nghệ 4.0 hiện nay thì việc vận dụng và áp dụng các phần mềm vào trong thiết kế, xây dựng là một việc rất cần thiết. Nó sẽ giúp tăng hiệu quả công việc, giảm đáng kể thời gian làm...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_87[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_87[$i]->twitter_image}}"
                                                alt="{{$term_relationship_87[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_87[$i]->post_name.'/' }}"
                                           class="fs-4  text-dark"
                                        >{{$term_relationship_87[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
        </div>
        <div class="category-item">
            <div class="title-arti text-uppercase fw-bold fs-4 " style="border-bottom: 1px solid red;">
                <a href="{{route('category',['slug'=>'do-an-xay-dung']).'/'}}" class=" text-dark">Đồ án xây dựng</a>
            </div>
            <section class="block-arti m-4 row">
                <div class="arti-left col-xxl-5 col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <a href="{{Request::root()."/".$term_relationship_47[0]->post_name.'/' }}"><img
                            src="{{$term_relationship_47[0]->twitter_image}}"
                            alt="{{$term_relationship_47[0]->post_title}}"
                        /></a>

                    <h2 class="mb-2 mt-2">
                        <strong><a class=" text-dark"
                                   href="{{Request::root()."/".$term_relationship_47[0]->post_name.'/' }}">{{$term_relationship_47[0]->post_title}}</a></strong>
                    </h2>
                    <span class="text-decrip-4 fs-5">
              Đồ án thoát nước là phần bắt buộc đi kèm với học phần lý thuyết tương ứng trong chương trình đào tạo kỹ sư ngành
                        Công nghệ Kỹ thuật Môi trường của trường Đại học Xây dựng Hà Nội. Đồ án giúp sinh viên...
                </span>
                </div>

                <div class="arti-right col-xxl-7 col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    @for($i=1;$i<=4;$i++)
                        <ul class="list-topic">
                            <li class="itemtopic pt-3 pb-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <a href="{{Request::root()."/".$term_relationship_47[$i]->post_name.'/' }}"><img
                                                class="w-100 "
                                                style="max-width: 70px;height: 70px;"
                                                src="{{$term_relationship_47[$i]->twitter_image}}"
                                                alt="{{$term_relationship_47[$i]->post_title}}"
                                            /></a>
                                    </div>
                                    <div class="col-9 pb-2">
                                        <a href="{{Request::root()."/".$term_relationship_47[$i]->post_name.'/' }}"
                                           class="fs-4 text-dark"
                                        >{{$term_relationship_47[$i]->post_title}}</a
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endfor
                </div>
            </section>
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

