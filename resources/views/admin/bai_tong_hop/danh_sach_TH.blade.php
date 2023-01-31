@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="card" style="max-height: 100vh">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-sm-6 col-md-3 mb-2 mb-sm-0">
                    <form action="" class="" method="GET">
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
                                   placeholder="Tìm kiếm bài tổng hợp"
                                   aria-label="Search users">
                        </div>
                        <!-- End Search -->
                    </form>
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
                            aria-label="Country: activate to sort column ascending">Tên bài
                            viết
                        </th>

                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label=""
                        >Hành động
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($ds_bai_viet_TH as $item)
                        <tr role="row" class="odd">


                            <td class="table-column-pl-0 text-center">
                                @if(empty(Request::get('page')))
                                    {{$loop->index + 1+ Request::get('page')* count($ds_bai_viet_TH)}}
                                @else
                                    {{$loop->index + 1 + Request::get('page')* count($ds_bai_viet_TH) -15}}
                                @endif
                            </td>
                            <td><h4 href="#">{{$item->tien_to}} {{$item->ten}} {{$item->hau_to}}</h4></td>

                            <td>
                                @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')
                                    <a class="btn btn-sm btn-white"
                                       href="{{Request::root()."/top_list/".$item->url_key_cha}}">
                                        <i class="tio-edit"></i> View
                                    </a>
                                @endif
                                {{--                                @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')--}}
                                {{--                                    <a class="btn btn-sm btn-white" href="{{Request::root()."/rd/xml/a/random-bai-viet/?id=".$item->ID_HD}}">--}}
                                {{--                                        <i class="tio-pages"></i> Key--}}
                                {{--                                    </a>--}}
                                {{--                                @endif--}}
{{--                                @if(session()->get('role')[0] == 'admin')--}}
{{--                                    <a class="btn btn-sm btn-white" href="{{route('xoaHD',['id'=>$item->ID_HD])}}"--}}
{{--                                       onclick="return confirm('Bạn có chắc không?')">--}}
{{--                                        Delete--}}
{{--                                    </a>--}}
{{--                                @endif--}}
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
                {{ $ds_bai_viet_TH->appends(request()->all())->links('vendor.pagination.custom')}}
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
