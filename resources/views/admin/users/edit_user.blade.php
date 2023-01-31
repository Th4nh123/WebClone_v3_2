@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="row justify-content-lg-center">
        <div class="col-lg-8">


            <!-- Content Step Form -->
            <div id="addUserStepFormContent">
                <h2 class="text-center">Thêm Nhân Viên</h2>

                <!-- Card -->
                <form action="{{route("edit_user")}}" method="post">
                    @csrf
                    <div id="addUserStepProfile" class="card card-lg active" style="">
                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form Group -->
                            <div class="row form-group">
                                <div class="col-sm-9">
                                    <input type="number" class="form-control"
                                           name="id" id="emailLabel" value="{{$user->ID}}"
                                           placeholder="VD: rdone@example.com..." aria-label="clarice@example.com" hidden="">
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Tên</label>
                                <div class="col-sm-9">
                                    @if(!empty($err))
                                        <h6 class="text-danger">Lỗi: {{$err}} </h6>
                                    @endif
                                    <input type="text" class="form-control" value="{{$user->display_name}}"
                                           name="full_name" id="" required
                                           placeholder="Tên user..." aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control"
                                           name="email" id="emailLabel" value="{{$user->user_email}}"
                                           placeholder="VD: rdone@example.com..." aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group --><!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">User name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username"
                                           id="" required value="{{$user->user_login}}"
                                           placeholder="Tài khoản đăng nhập" aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group --><!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control"
                                           name="password" id="" required value="{{$user->user_pass}}"
                                           placeholder="Mật khẩu đăng nhập" aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group -->
                            <div class="form-group row">
                                <label for="inputGroupMergeGenderSelect" class=" col-sm-3  input-label">Phân
                                    quyền</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">
                                          <i class="tio-user-outlined"></i>
                                        </span>
                                        </div>
                                        <select id="inputGroupMergeGenderSelect"
                                                name="quyen" class="custom-select"
                                                required>
                                            @if($user->role == "admin")
                                                <option value="nv">Nhân viên</option>
                                                <option value="admin" selected>Admin</option>
                                            @else
                                                <option value="nv" selected>Nhân viên</option>
                                                <option value="admin">Admin</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- End Body -->

                        <!-- Footer -->
                        <div class="card-footer d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-primary">
                                Thêm <i class="tio-chevron-right"></i>
                            </button>
                        </div>
                        <!-- End Footer -->
                    </div>
                    <!-- End Card -->

                </form>
            </div>
            <!-- End Content Step Form -->


        </div>
    </div>
@endsection
