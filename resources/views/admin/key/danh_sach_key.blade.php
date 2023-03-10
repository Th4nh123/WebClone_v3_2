@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="card" style="max-height: 100vh">
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-sm-6 col-md-4 mb-3 mb-sm-0">
                    <form action="{{route('timkiemKey')}}" method="GET">
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch" type="search" class="form-control"
                                   name="s"
                                   placeholder="Tìm kiếm Key"
                                   aria-label="Search users">
                            <button type="submit" class="btn btn-primary pt-1 pb-1 pr-2 pl-2">Tìm kiếm</button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
                <div class="col-sm-6 col-md-4 mb-3 mb-sm-0 d-flex justify-content-end">
                    <a href="{{Request::root().'/admin/them-key'}}"
                       class="btn btn-primary pt-1 pb-1 pr-2 pl-2">Thêm mới</a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->

        <!-- Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-header-title">Users</h4>
            </div>

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
                                aria-label="Country: activate to sort column ascending">Key
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"
                                aria-label="Status: activate to sort column ascending">Value
                            </th>
                            <th class="sorting_disabled" rowspan="1" colspan="1" aria-label=""
                            >Hành động
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($key as $item)
                            <tr role="row" class="odd">


                                <td class="table-column-pl-0 text-center">
                                    @if(empty(Request::get('page')))
                                        {{$loop->index + 1+ Request::get('page')* count($key)}}
                                    @else
                                        {{$loop->index + 1 + Request::get('page')* count($key) -15}}
                                    @endif
                                </td>
                                <td><a
                                        href="{{route('suaKey',['id'=>$item->id])}}">{{$item->keyHD}}  </a></td>

                                <td>{{$item->valueHD}}</td>
                                <td>
                                    @if(session()->get('role')[0] == 'admin')
                                        <a class="btn btn-sm btn-white" href="{{route('xoaKey',['id'=>$item->id])}}"
                                           onclick="return confirm('Bạn có chắc không?')">
                                            Delete
                                        </a>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                    <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">Showing 1 to 15 of
                        24
                        entries
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->

        <!-- Footer -->
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                {{ $key->appends(request()->all())->links('vendor.pagination.custom')}}
            </div>
            <!-- End Pagination -->
        </div>
        <!-- End Footer -->
    </div>

@endsection
