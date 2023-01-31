<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
    <meta name="Language" content="Vietnamese">
    <meta name="Designer" content="RDSIC">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-language" content="vi">
    <meta name="copyright" content="https://rdone.net/">
    <meta name="”robots”" content="”index,follow”">
    <meta property="og:type" content="article">
    <meta name="copyright" content="Copyright © RDone">
    <meta property="og:type" content="threed.asset">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="https://rdone.net/sitemap.xml">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3063449043875072"
            crossorigin="anonymous"></script>
    <link href="{{asset("css/css_client/global.css")}}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

@yield('meta')
@include('layouts.lib_bs5')
@include('layouts.lib_jquery')

<!-- Global site tag (gtag.js) - Google Analytics -->

    <script>

        function loadGGtag(_time) {
            setTimeout(function () {
                (function () {
                    var s1 = document.createElement("script"),
                        s0 = document.getElementsByTagName("script")[0];
                    s1.async = true;
                    s1.src = "https://www.googletagmanager.com/gtag/js?id=UA-32341979-18";
                    s0.parentNode.insertBefore(s1, s0);

                    gtag('event', 'page_view', {
                        'send_to': 'AW-753090115',
                        'value': 'replace with value',
                        'items': [{
                            'id': 'replace with value',
                            'location_id': 'replace with value',
                            'google_business_vertical': 'custom'
                        }]
                    });


                })();
            }, _time);
        }

        loadGGtag(5000);
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-32341979-18');

    </script>


    <!-- Facebook Pixel Code -->
    <script>
        function loadFacebookPixel(_time) {
            setTimeout(function () {
                (function () {
                    !function (f, b, e, v, n, t, s) {
                        if (f.fbq) return;
                        n = f.fbq = function () {
                            n.callMethod ?
                                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                        };
                        if (!f._fbq) f._fbq = n;
                        n.push = n;
                        n.loaded = !0;
                        n.version = '2.0';
                        n.queue = [];
                        t = b.createElement(e);
                        t.async = !0;
                        t.src = v;
                        s = b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t, s)
                    }(window, document, 'script',
                        'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '277357285800457');
                    fbq('track', 'PageView');
                })();
            }, _time);
        }

        loadFacebookPixel(5000);

    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=277357285800457&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Facebook Pixel Code -->
    <!-- Facebook Pixel Code -->

    <script>
        function loadFacebookPixelCtyXDS(_time) {
            setTimeout(function () {
                (function () {
                    !function (f, b, e, v, n, t, s) {
                        if (f.fbq) return;
                        n = f.fbq = function () {
                            n.callMethod ?
                                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                        };
                        if (!f._fbq) f._fbq = n;
                        n.push = n;
                        n.loaded = !0;
                        n.version = '2.0';
                        n.queue = [];
                        t = b.createElement(e);
                        t.async = !0;
                        t.src = v;
                        s = b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t, s)
                    }(window, document, 'script',
                        'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '1152898655226527');
                    fbq('track', 'PageView');
                })();
            }, _time);

        }

        loadFacebookPixelCtyXDS(5000);
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1152898655226527&ev=PageView&noscript=1"/></noscript>

    <style>

        .entry-content h2,
        .entry-content h3{
            font-weight: 700;
        }
        .post_title{
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .entry-content li{
            list-style-type: decimal;
        }
        .non_toc #toc_container{
            display: none;
        }
        #toc_container {
            background: #f9f9f9 none repeat scroll 0 0;
            border: 1px solid #aaa;
            display: block;
            font-size: 95%;
            margin-bottom: 1em;
            padding: 20px;
            width: auto;
        }

        .toc_title {
            font-weight: 700;
            text-align: center;
        }

        #toc_container li, #toc_container ul, #toc_container ul li{
            list-style-type: decimal;
        }
        #toc_container ul{
            padding-left: 30px;
        }
        #banner-ngang{
            display: block;
            object-fit: cover;
            height: 90px;
            width: 728px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 10px;
        }
        #banner-doc{
            display: block;
            object-fit: cover;
            height: 300px;
            width: 250px;
            margin-left: auto;
            margin-right: auto;
        }
        .title_text{
            font-size: 2rem;
            font-weight: 700;
        }

        .img-responsive{
            max-width: 100%;
            height: 142px;
            vertical-align: super;
            object-fit: cover;
        }
        .notSearch{
            font-size: 3rem;
            margin-top: 50px;
        }

        .inner{
            padding-left: 15px;
            padding-right: 15px;
        }
        .pull-left img {
            display: block;
            margin-right: auto;
            margin-left:auto;
            width: 100%;
            height: 290px;
            object-fit: fill;
        }
        .pull-left{
            margin-top: 40px;
        }
        .flex-direc{
            flex-direction: column;
        }

        .post_img{
            width: auto;
            height:auto;
        }
        @media (min-width: 560px){

            .pull-left img {
                width: 85%;
                height: 300px;
                margin-left: 0;
                object-fit: fill;
            }
        }
        @media (min-width: 640px){

            .pull-left img {
                width: 90%;
                height: 350px;
                margin-left: 0;
                object-fit: fill;
            }
            .flex-direc{
                flex-direction: row-reverse;
            }
            .post_img{
                width: 130px;
                height:130px;
            }
        }
        @media (min-width: 768px){
            .pull-left{
                margin:0;
            }
            .pull-left img{
                width: 100%;
                height: 130px;
                object-fit: fill;
            }
            .inner{
                padding-left: 15px;
                padding-right: 50px;
            }
        }
        @media (min-width: 992px){
            .pull-left img{
                width: 100%;
                height: 170px;
                object-fit: fill;
            }
        }

    </style>
</head>
<body class="">

<nav class="navbar block-nav  navbar-expand-lg navbar-light bg-light" style="position: sticky;top: 0;z-index: 1000;">
    <div class="container">
        <a href="{{Request::root().'/'}}" @class('fw-bold fs-1 text-danger title-mobile')>RDONE</a>
        <button class="navbar-toggler" id="shownav" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="list-menu navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item item-menu">
                    <a @class('nav-link') href="{{route('category',['slug'=>'tai-lieu-thi-cong']).'/'}}">Tài liệu thi
                        công</a>
                </li>
                <li class="nav-item item-menu">
                    <a @class('nav-link')  href="{{route('category',['slug'=>'tai-lieu-thiet-ke']).'/'}}">Tài liệu thiết
                        kế </a>

                </li>
                <li class="nav-item item-menu">
                    <a @class('nav-link')  href="{{route('category',['slug'=>'phan-mem-xay-dung']).'/'}}">Phần mềm xây
                        dựng</a>

                </li>
                <li class="nav-item item-menu">
                    <a @class('nav-link') href="{{route('category',['slug'=>'thu-vien']).'/'}}">Thư viện</a>

                </li>
                <li class="nav-item item-menu">
                    <a @class('nav-link') href="https://xaydungso.vn/thau-xay-dung/nganh-nghe-sua-chua-cai-tao-nha">Sửa
                        chữa nhà</a>

                </li>
                <li class="nav-item item-menu">
                    <a @class('nav-link') href="https://autocad123.vn/">Autocad</a>
                </li>
            </ul>

            <form method="get" class="d-flex" action="{{route('postSearch')}}">
                <input
                    type="text"
                    class="form-control fs-5  me-2"
                    placeholder="Tìm kiếm bài viết"
                    aria-label="Username"
                    aria-describedby="basic-addon1"
                    value=""
                    name="s"
                />
                <input type="submit" class="search-submit" value="Tìm kiếm">
            </form>

        </div>
    </div>
</nav>
<div class="content container mb-4">

    <a href="{{Request::root().'/'}}" class="ms-4 mt-3 title" style="font-size: 36px; color: red;margin-bottom: 30px;">
        RDONE
    </a>

    <div class="row mt-4">
        @yield('banner')
        @yield('content-left')
        @yield('content-right')
    </div>


</div>

<script>
    $(document).ready(function () {
        $('#shownav').click(function () {
            console.log("jdjdjdjd")
            $('#navbarSupportedContent').toggleClass('d-inline')
        })
    })
</script>
<script>
    $("#toc_container").on('click','a', function(event){
        event.preventDefault();
        var o =  $( $(this).attr("href") ).offset();
        var sT = o.top - $(".navbar").outerHeight(true)-30;
        window.scrollTo(0,sT);
    });
</script>
{{--@include('layouts.libs_js_bs5')--}}
</body>
</html>

