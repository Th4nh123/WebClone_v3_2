@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="card" style="max-height: 100vh">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-sm-6 col-md-3 mb-2 mb-sm-0">
                    <form action="{{route('searchkey')}}" class="" method="GET" style="width: 400px">
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
                                   placeholder="Tìm kiếm key bài viết"
                                   aria-label="Search users">
                            <button type="submit" class="btn btn-primary pt-1 pb-1 pr-2 pl-2">Tìm kiếm</button>
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
                            aria-label="Country: activate to sort column ascending">Tên Key
                        </th>

                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label=""
                        >Hành động
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($ds_key))
                        @foreach ($ds_key as $item)
                            <tr role="row" class="odd">


                                <td class="table-column-pl-0 text-center">
                                    @if(empty(Request::get('page')))
                                        {{$loop->index + 1+ Request::get('page')* count($ds_key)}}
                                    @else
                                        {{$loop->index + 1 + Request::get('page')* count($ds_key) -15}}
                                    @endif
                                </td>
                                <td><h5>{{$item->ten}}</h5></td>

                                <td>
                                    @if(session()->get('role')[0] == 'admin' || session()->get('role')[0] == 'nv')
                                        <a class="btn btn-sm btn-white"
                                           href="{{route('them_post',['id_key'=>$item->id])}}">
                                            <i class="tio-edit"></i> Danh sách URL
                                        </a>
                                    @endif

                                </td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                </table>
                <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">Showing 1 to 15 of 24
                    entries
                </div>
            </div>
        </div>
        <!-- End Table -->

        <!-- Footer -->
{{--            <div class="card-footer">--}}
{{--                <!-- Pagination -->--}}
{{--                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">--}}
{{--                    {{ $ds_key->appends(request()->all())->links('vendor.pagination.custom')}}--}}
{{--                    {{ $ds_key->appends(request()->all())->links('vendor.pagination.custom')}}--}}
{{--                </div>--}}
{{--                <!-- End Pagination -->--}}
{{--            </div>--}}
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
