@extends('admin.layout_admin.layout_admin')

@section('main')

    <div class="col-lg-8 col-xs-12 m-auto">
        <!-- Card -->
        <div class="card card-lg mb-3 mb-lg-5">
            <form action="{{route('updateKey')}}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">Sửa key</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Form Group -->
                    <!-- Input Group -->
                    <div class="form-group">
                        <label for="formControlPilledFullName" class="input-label" hidden="">ID</label>

                        <input type="text" name="id" value="{{$key_HD->id}}" class="form-control form-control-pill"
                               placeholder="" hidden="">
                    </div>
                    <!-- End Input Group -->
                    <!-- Input Group -->
                    <div class="form-group">
                        <label for="formControlPilledFullName" class="input-label">Key hướng dẫn</label>

                        <input type="text" name="keyHD" value="{{$key_HD->keyHD}}"
                               class="form-control form-control-pill" placeholder="Nhập Key">
                    </div>
                    <!-- End Input Group -->

                    <!-- Input Group -->
                    <div class="form-group">
                        <label for="formControlPilledEmail" class="input-label">Value</label>

                        <input type="text" name="valueHD" value="{{$key_HD->valueHD}}"
                               class="form-control form-control-pill"
                               placeholder="Nhập Value">
                    </div>
                    <!-- End Input Group -->

                    <div class="form-group">
                        <label for="projectNameProjectSettingsLabel" class="input-label">Chọn các link liên quan<i
                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                data-placement="top"
                                title=""
                                data-original-title="Displayed on public forums, such as Front."></i></label>

                        <div class="input-group input-group-merge">
                            <select id="multi-select" class="col-12 m-auto"  multiple multiselect-search="true">
                                @if($ds_lienquan == null)
                                    @foreach($ds_post as $post)
                                        <option value="{{$post->ID_HD}}"
                                        >{{$post->postHD_title}}</option>
                                    @endforeach
                                @else
                                    @foreach($ds_lienquan as $post_lq)
                                        <option value="{{$post_lq->ID_HD}}"
                                                selected>{{$post_lq->postHD_title}}</option>
                                    @endforeach
                                    @foreach($ds_post as $post)
                                        <option value="{{$post->ID_HD}}"
                                        >{{$post->postHD_title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{--                                    <div id="search_list"></div>--}}
                    </div>
                    <!-- End Form Group -->

                </div>
                <!-- End Quill -->

                <!-- End Body -->

                <!-- Footer -->
                <div class="card-footer d-flex justify-content-center align-items-center">
                    {{--                        <button type="button" class="btn btn-white mr-2">Cancel</button>--}}
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
            <!-- End Footer -->
        </div>
        <!-- End Card -->


    </div>


@endsection
