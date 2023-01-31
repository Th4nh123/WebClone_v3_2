@extends('admin.layout_admin.layout_admin')
@section("main")
    <div class="row justify-content-lg-center">
        <div class="col-lg-8">


            <!-- Content Step Form -->
            <div id="addUserStepFormContent">
                <h2 class="text-center">Thêm Nhân Viên</h2>
                <!-- Card -->
                <form action="{{Request::root().'/admin/insert-user'}}" method="post">
                    @csrf
                    <div id="addUserStepProfile" class="card card-lg active" style="">
                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Tên</label>
                                <div class="col-sm-9">
                                    @if(!empty($err))
                                        <h6 class="text-danger">Lỗi: {{$err}} </h6>
                                    @endif
                                    <input type="text" class="form-control" name="full_name" id="" required
                                           placeholder="Tên user..." aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="emailLabel" value=" "
                                           placeholder="VD: rdone@example.com..." aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group --><!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">User name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" id="" required
                                           placeholder="Tài khoản đăng nhập" aria-label="clarice@example.com">
                                </div>
                            </div>
                            <!-- End Form Group --><!-- Form Group -->
                            <div class="row form-group">
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control"
                                           pattern="(?=.*\d)(?=.*[A-Z]).{8,}"
                                           title="Mật khẩu phải chứa ít nhất 1 số, 1 ký tự viết hoa và không nhỏ hơn 8 ký tự"
                                           name="password" id="password" required
                                           placeholder="Mật khẩu đăng nhập">
                                </div>
                                <label for="emailLabel" class="col-sm-3 col-form-label input-label"></label>
                                <div id="message" class="col-sm-9">
                                    <h6>Mật khẩu phải chứa ít nhất:</h6>
                                    <div class="d-flex">
                                        <div class="mr-2 pr-2">
                                            <p id="capital" class="invalid">1 <b>ký tự viết hoa</b></p>
                                            <p id="number" class="invalid">1 <b>số</b></p>
                                        </div>
                                        <div class="pl-2">
                                            <p id="length" class="invalid">Ít nhất <b>8 ký tự</b></p>
                                        </div>
                                    </div>

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
                                        <select id="inputGroupMergeGenderSelect" name="quyen" class="custom-select"
                                                required>
                                            <option value="nv">Nhân viên</option>
                                            <option value="admin">Admin</option>
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
