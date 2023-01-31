@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="card" style="max-height: 100vh">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-sm-6 col-md-3 mb-2 mb-sm-0">
                    <form action="{{route('tk_huong_dan')}}" class="" method="GET">
                    @csrf
                    <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch" type="search" class="form-control"
                                   name="s"
                                   value="{{!empty($search_text)?$search_text:""}}"
                                   placeholder="Tìm kiếm bài viết hướng dẫn"
                                   aria-label="Search users">
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
                <div class="col-sm-6 col-md-4 mb-3 mb-sm-0 d-flex justify-content-end">
{{--                    <a onclick="copyToClipbroad('/random-pass/?val=4000')" class="btn url-image"--}}
{{--                       title="Copy to clipboard"><img src="https://img.icons8.com/material-outlined/24/000000/copy.png"></a>--}}
                    <a onclick="copyToClipbroad('/huong-dan-lay-pass/')" class="btn url-image"
                       title="Copy to clipboard"><img src="https://img.icons8.com/material-outlined/24/000000/copy.png"></a>
                    <a href="{{Request::root()."/huong-dan-lay-pass/"}}"
                       class="btn btn-primary pt-1 pb-1 pr-2 pl-2 mr-2">Random key</a>
                    <a href="{{Request::root().'/admin/them-huong-dan'}}" class="btn btn-primary pt-1 pb-1 pr-2 pl-2">Thêm
                        mới</a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->

        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <div id="datatable_wrapper" class="dataTables_wrapper no-footer">

                <table id="datatable"
                       class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer"
                       role="grid" aria-describedby="datatable_info">
                    <thead class="thead-light">
                    <tr role="row">

                        <th class="table-column-pl-0 sorting text-center p-1 align-middle" tabindex="0"
                            aria-controls="datatable" rowspan="1"
                            colspan="1" aria-label="Name: activate to sort column ascending">
                            STT
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Position: activate to sort column ascending">Ảnh
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Country: activate to sort column ascending">Tên bài
                            viết
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Status: activate to sort column ascending">Tác giả
                        </th>

                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                            aria-label="Role: activate to sort column ascending">Ngày tạo
                        </th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label=""
                        >Hành động
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($ds_bai_viet as $item)
                        <tr role="row" class="odd">


                            <td class="table-column-pl-0 text-center">
                                @if(empty(Request::get('page')))
                                    {{$loop->index + 1+ Request::get('page')* count($ds_bai_viet)}}
                                @else
                                    {{$loop->index + 1 + Request::get('page')* count($ds_bai_viet) -15}}
                                @endif
                            </td>
                            <td>
                                {{--                                <img src="{{asset('storage/images/'.$item->twitter_image)}}" alt="" style="width: 80px;height: 80px">--}}
                                {{--                                <img src="{{ $item->twitter_image }}" alt="" style="width: 80px;height: 80px">--}}
                                @if(preg_match_all('((http|https)\:\/\/)',$item->twitter_image))
                                    <img src="{{ $item->twitter_image }}" alt="" style="width: 80px;height: 80px">
                                @else
                                    <img src="{{asset('images/'.$item->twitter_image)}}" alt=""
                                         style="width: 80px;height: 80px">
                                @endif

                            </td>

                            <td><a href="{{route('suaHD',['id'=>$item->ID_HD])}}">{{ $item->postHD_title }}</a></td>

                            <td> {{ $item->display_name }}
                            <td>{{$item->postHD_date}}</td>
                            <td>
                                @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')
                                    <a class="btn btn-sm btn-white"
                                       href="{{Request::root()."/hd/".$item->postHD_name}}">
                                        <i class="tio-edit"></i> View
                                    </a>
                                @endif
                                {{--                                @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')--}}
                                {{--                                    <a class="btn btn-sm btn-white" href="{{Request::root()."/rd/xml/a/random-bai-viet/?id=".$item->ID_HD}}">--}}
                                {{--                                        <i class="tio-pages"></i> Key--}}
                                {{--                                    </a>--}}
                                {{--                                @endif--}}
                                @if(session()->get('role')[0] == 'admin')
                                    <a class="btn btn-sm btn-white" href="{{route('xoaHD',['id'=>$item->ID_HD])}}"
                                       onclick="return confirm('Bạn có chắc không?')">
                                        Delete
                                    </a>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">Showing 1 to 15 of 24
                    entries
                </div>
            </div>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                {{ $ds_bai_viet->appends(request()->all())->links('vendor.pagination.custom')}}
            </div>
            <!-- End Pagination -->
        </div>
        <!-- End Footer -->
    </div>
    <script>
        function copyToClipbroad(text) {
            navigator.clipboard.writeText(window.origin + text).then(function () {
                alert("Đã copy to clipboard")
            }, function (err) {
                alert('Async: Could not copy text: ', err);
            });
        }
    </script>
@endsection
