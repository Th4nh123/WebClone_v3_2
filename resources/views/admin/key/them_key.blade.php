@extends('admin.layout_admin.layout_admin')

@section('main')

    <div class="col-lg-8 col-xs-12 " style="margin: auto">
        <!-- Card -->
        <div class="card card-lg mb-3 mb-lg-5">
            <form action="{{route('luuKey')}}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">Thêm Key</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Form Group -->
                    <!-- Input Group -->
                    <div class="form-group">
                        <label for="formControlPilledFullName" class="input-label">Key hướng dẫn</label>

                        <input type="text" name="keyHD" class="form-control form-control-pill" placeholder="Nhập Key"
                               required title="Bạn chưa điền key">
                        @if(!empty($err_key))
                            <span class="text-danger">{{$err_key}}</span>
                        @endif
                    </div>
                    <!-- End Input Group -->

                    <!-- Input Group -->
                    <div class="form-group">
                        <label for="formControlPilledEmail" class="input-label">Value</label>
                        <input type="number" name="valueHD" class="form-control form-control-pill" required
                               placeholder="Nhập Value" title="Bạn chưa điền value">
                        @if(!empty($err_val))
                            <span class="text-danger">{{$err_val}}</span>
                        @endif
                    </div>
                    <!-- End Input Group -->

                    {{--                    <div class="row">--}}
                    {{--                        <div class="col-12">--}}
                    {{--                            <!-- Form Group -->--}}
                    {{--                            <div class="form-group">--}}
                    {{--                                <label for="projectNameProjectSettingsLabel" class="input-label">Chọn các link liên quan<i--}}
                    {{--                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"--}}
                    {{--                                        data-placement="top"--}}
                    {{--                                        title=""--}}
                    {{--                                        data-original-title="Displayed on public forums, such as Front."></i></label>--}}

                    {{--                                <div class="input-group input-group-merge">--}}
                    {{--                                    <select  id="multi-select" class="col-12 m-auto" multiple multiselect-search="true">--}}
                    {{--                                        @foreach($list_link as $post)--}}
                    {{--                                            <option value="{{$post->postHD_title}}">{{$post->postHD_title}}</option>--}}
                    {{--                                        @endforeach--}}
                    {{--                                    </select>--}}
                    {{--                                </div>--}}

                    {{--                                --}}{{--                                    <div id="search_list"></div>--}}
                    {{--                            </div>--}}
                    {{--                            <!-- End Form Group -->--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                </div>
                <!-- End Quill -->

                <!-- End Body -->

                <!-- Footer -->
                <div class="card-footer d-flex justify-content-center align-items-center">
                    {{--                        <button type="button" class="btn btn-white mr-2">Cancel</button>--}}
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                </div>
            </form>
            <!-- End Footer -->
        </div>
        <!-- End Card -->


    </div>


@endsection
