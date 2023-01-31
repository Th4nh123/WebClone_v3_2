@extends('admin.layout_admin.layout_admin')

@section('main')

    <div class="col-lg-12">
        <!-- Card -->
        <div class="card card-lg mb-3 mb-lg-5">
            <form action="{{route('luuHD')}}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">Thêm bài viết</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Form Group -->

                    <div class="d-flex justify-content-between">
                        <div class="form-group">
                            <label class="input-label" for="avatarUploader">Ảnh Bài Viết</label>
                            <div class="d-flex align-items-center position-relative">
                                <!-- Avatar -->
                                <label class="avatar avatar-xl avatar-circle avatar-uploader mr-5" for="avatarUploader">
                                    <img id="output" class="avatar-img shadow-soft" style="padding: 20px"
                                         src="https://rdone.net/images/icons8-upload-96.png" alt="Image Description">

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
                        <!-- Nav -->
                        <div class="text-left">
                            <ul class="nav nav-segment  mb-7" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="nav-one-eg1-tab" data-toggle="pill"
                                       href="#nav-one-eg1"
                                       role="tab" aria-controls="nav-one-eg1" aria-selected="true">Thông tin</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="nav-two-eg1-tab" data-toggle="pill" href="#nav-two-eg1"
                                       role="tab" aria-controls="nav-two-eg1" aria-selected="false">SEO</a>
                                </li>

                            </ul>
                        </div>
                        <!-- End Nav -->
                    </div>


                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-one-eg1" role="tabpanel"
                             aria-labelledby="nav-one-eg1-tab">
                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="tieu_de" class="input-label">Tiêu đề bài
                                            viết
                                            <b>(<=60)</b>
                                            <span>Có:</span>
                                            <b><span id="count"></span></b>
                                        </label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="tieu_de"
                                                   id="tieu_de" placeholder="Tiêu đề"
                                                   aria-label="Enter project name here"
                                                   onchange="onChangeInput(this,'tieu_de')"
                                                   pattern=".{1,60}"
                                                   title="Tiêu đề có độ dài không quá 60 ký tự" required>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>

                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Slug <i
                                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="Displayed on public forums, such as Front."></i></label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="post_name"
                                                   id="slug" placeholder="VD: file-cad"
                                                   aria-label="Enter project name here"
                                                   title="Không được để trống" required>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Tác giả<i
                                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="Displayed on public forums, such as Front."></i></label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <select name="tac_gia" id="tac_gia"
                                                    class="form-control">
                                                @foreach($users as $user)
                                                    @if(session()->get('tk_user')[0] == $user->user_login)
                                                        <option value="{{$user->ID}}"
                                                                selected>{{$user->user_login}}</option>
                                                    @else
                                                        <option value="{{$user->ID}}">{{$user->user_login}}</option>
                                                    @endif
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>

                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Mô tả
                                            <b>(<140)</b>
                                            <span>Có:</span>
                                            <b><span id="count1"></span></b>
                                        </label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="mo_ta"
                                                   id="projectNameProjectSettingsLabel" placeholder="Tóm tắt nội dung"
                                                   aria-label="Enter project name here" pattern=".{1,140}"
                                                   title="Mô tả có độ dài không quá 140 ký tự" required>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Thể loại<i
                                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="Displayed on public forums, such as Front."></i></label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <select name="the_loai" id="projectNameProjectSettingsLabel"
                                                    class="form-control">

                                                @foreach($categories as $category)
                                                    <option
                                                        value="{{$category->term_taxonomy_id}}">{{$category->name}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>

                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Key<i
                                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="Displayed on public forums, such as Front."></i></label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <select required name="key" id="projectNameProjectSettingsLabel"
                                                    class="form-control">
                                                <option disabled selected value> -- Select an option --</option>
                                                @foreach($keys as $key)
                                                    <option value="{{$key->id}}">{{$key->keyHD}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{--                                    <div id="search_list"></div>--}}
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Bài viết liên
                                            quan<i
                                                class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                                data-placement="top"
                                                title=""
                                                data-original-title="Displayed on public forums, such as Front."></i></label>

                                        <div class="input-group input-group-merge">

                                            {{--                                        <input type="text" class="form-control" name="search"--}}
                                            {{--                                               id="search" placeholder="Bài viết liên quan"--}}
                                            {{--                                               aria-label="Enter project name here" pattern=".{1,}"--}}
                                            {{--                                               title="Không được để trống" required>--}}

                                            <select  id="multi-select" multiple multiselect-search="true">
                                                @foreach($posts as $post)
                                                    <option value="{{$post->post_title}}">{{$post->post_title}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{--                                    <div id="search_list"></div>--}}
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="nav-two-eg1" role="tabpanel" aria-labelledby="nav-two-eg1-tab">
                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Meta
                                            title </label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control"
                                                   id="metaTitle" placeholder="Meta title"
                                                   aria-label="Enter project name here"
                                                   title="Không được để trống">
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Meta
                                            keyword </label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="meta_key"
                                                   id="projectNameProjectSettingsLabel" placeholder="Meta keyword"
                                                   aria-label="Enter project name here"
                                                   title="Không được để trống">
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <!-- Form Group -->
                                    <div class="form-group">
                                        <label for="projectNameProjectSettingsLabel" class="input-label">Meta
                                            robot</label>

                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-briefcase-outlined"></i>
                                                </div>
                                            </div>
                                            <select name="meta_robot" id="projectNameProjectSettingsLabel"
                                                    class="form-control">
                                                <option value="index,follow">index,follow</option>
                                                <option value="noindex,nofollow" selected >noindex,nofollow</option>
                                                <option value="index,nofollow">index,nofollow</option>
                                                <option value="noindex,follow">noindex,follow</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- End Form Group -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Quill -->
                    <label class="input-label">Nội dung bài viết </label>

                    <div class="quill-custom ql-toolbar-bottom">
                        <input name="noi_dung" id="mytextarea" placeholder="Nhập nội dung bài viết">

                    </div>
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

    {{--        <section class="content">--}}
    {{--            <div class="col-6">--}}
    {{--                <form action="{{route('themBV')}}" method="post">--}}
    {{--                    @csrf--}}
    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">Tiêu đề</label>--}}
    {{--                        <input type="text" name="tieu_de" class="form-control" placeholder="Tiêu đề">--}}
    {{--                    </div>--}}
    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">Nội dung</label>--}}
    {{--                        <input type="text" id="noi_dung" name="noi_dung" placeholder="Nhập nội dung bài viết"> </input>--}}
    {{--                    </div>--}}
    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">Tác giả </label>--}}
    {{--                        <input type="number" name="name" class="form-control" placeholder="Tác giả">--}}
    {{--                    </div>--}}
    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">Date </label>--}}
    {{--                        <input type="datetime-local" name="date" class="form-control" placeholder="Date">--}}
    {{--                    </div>--}}

    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">modified </label>--}}
    {{--                        <input type="datetime-local" name="modified" class="form-control" placeholder="Date">--}}
    {{--                    </div>--}}
    {{--                    <div class="form-group">--}}
    {{--                        <label for="exampleInputEmail1">name</label>--}}
    {{--                        <input type="text" name="name" class="form-control" placeholder="name">--}}
    {{--                    </div>--}}
    {{--                    <div class="col-md-12 text-center ">--}}
    {{--                        <button type="submit" class=" btn btn-block mybtn btn-primary tx-tfm">Enter</button>--}}
    {{--                    </div>--}}
    {{--                </form>--}}
    {{--            </div>--}}

    {{--        </section>--}}



@endsection
