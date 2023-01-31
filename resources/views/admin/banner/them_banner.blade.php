@extends('admin.layout_admin.layout_admin')

@section('main')

    <div class="col-lg-12">
        <!-- Card -->
        <div class="card card-lg mb-3 mb-lg-5">
            <form action="{{route('luuBanner')}}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">Thêm Banner</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Form Group -->
                    <div class="form-group">
                        <label class="input-label">Chọn vị trí</label>
                        <!-- Form Check -->
                        <div class="form-check form-check-inline">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="banner-ngang" value="banner-ngang" class="custom-control-input"
                                       name="vi_tri">
                                <label class="custom-control-label" for="banner-ngang"><b>Ngang</b> (Chính)</label>
                            </div>
                        </div>
                        <!-- End Form Check -->
                        <!-- Form Check -->
                        <div class="form-check form-check-inline">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="banner-doc" value="banner-doc" class="custom-control-input"
                                       name="vi_tri">
                                <label class="custom-control-label" for="banner-doc"><b>Dọc</b></label>
                            </div>
                        </div>
                        <!-- End Form Check -->
                    </div>


                    <div class="form-group">
                        <label class="input-label" for="avatarUploader">Banner</label>
                        <div class="d-flex align-items-center position-relative">
                            <!-- Avatar -->
                            <label class="avatar avatar-xl avatar-circle avatar-uploader mr-5" for="avatarUploader">
                                <img id="output" class="avatar-img shadow-soft" style="padding: 20px"
                                     src="{{!empty($banner_ngang) ? asset('banners/'.$banner_ngang) : "https://rdone.net/images/icons8-upload-96.png"}}" alt="Image Description">

                                <span class="avatar-uploader-trigger">
                                    <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                                    </span>
                            </label>
                            <input type="file" class="js-file-attach avatar-uploader-input form-control"
                                   id="avatarUploader"
                                   name="image_upload" required
                                   accept="image/*"
                                   onchange="loadFile(this)">
                            <!-- End Avatar -->

                            <button type="button" id="deleteImage" onclick="deleteImg(this)"
                                    class="js-file-attach-reset-img btn btn-white">Delete
                            </button>
                        </div>
                    </div>
                    <!-- End Form Group -->


                </div>
                <!-- End Quill -->

                <!-- End Body -->

                <!-- Footer -->
                <div class="card-footer d-flex justify-content-end align-items-center">
                    {{--                        <button type="button" class="btn btn-white mr-2">Cancel</button>--}}
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
            <!-- End Footer -->
        </div>
        <!-- End Card -->


    </div>
@endsection
