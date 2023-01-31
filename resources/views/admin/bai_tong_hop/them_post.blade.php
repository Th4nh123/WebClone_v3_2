@extends('admin.layout_admin.layout_admin')

@section('main')

    <div class="col-lg-12">
        <!-- Card -->
        <div class="card card-lg mb-3 mb-lg-5">
            <form action="{{route('luu_post')}}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">Thêm bài viết vào bài tổng hợp có key là <u style="font-size: 18px">{{$key->ten}}</u></h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Form Group -->



                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-one-eg1" role="tabpanel"
                             aria-labelledby="nav-one-eg1-tab">
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="id_key" value="{{$key->id}}" hidden="">
                                    @if(!empty($post_detail))
                                        @foreach($post_detail as $post)
                                    <div class="form-group">
                                        <!-- Checkbox -->
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="{{$post->ID}}" class="custom-control-input" name="selectPost[]" value="{{$post->ID}}" checked>
                                            <label class="custom-control-label" for="{{$post->ID}}">{{$post->post_title}}</label>
                                        </div>
                                        <!-- End Checkbox -->
                                    </div>
                                        @endforeach
                                    @endif
                                </div>


                            </div>


                        </div>


                    </div>


                    <!-- Quill -->
                    <div class="card-footer d-flex justify-content-end align-items-center">
                        {{--                        <button type="button" class="btn btn-white mr-2">Cancel</button>--}}
                        <button type="submit" class="btn btn-primary">Tạo bài tổng hợp</button>
                    </div>
                </div>
                <!-- End Quill -->

                <!-- End Body -->

                <!-- Footer -->

            </form>
            <!-- End Footer -->
        </div>
        <!-- End Card -->


    </div>



@endsection
