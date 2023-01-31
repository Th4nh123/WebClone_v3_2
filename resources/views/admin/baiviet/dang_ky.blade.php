<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    {{--    <link rel="icon" href="%PUBLIC_URL%/favicon.ico"/>--}}
    <link href="https://rdsic.edu.vn/webroot/favicon.ico" type="image/x-icon" rel="icon">
    <link href="https://rdsic.edu.vn/webroot/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#000000"/>
    <meta
        name="description"
        content="Web site created using create-react-app"
    />

    <link rel="apple-touch-icon" href="%PUBLIC_URL%/logo192.png"/>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap"
        rel="stylesheet"
    />
    <link rel="manifest" href="%PUBLIC_URL%/manifest.json"/>

    <link
        rel="stylesheet"
        href="{{Request::root().'/css/css_admin/theme.min.css'}}"
    />
    <link
        rel="stylesheet"
        href="{{Request::root().'/css/css_admin/vendor.min.css'}}"
    />
    <link
        rel="stylesheet"
        href="{{Request::root().'/js/libs_admin/icon-set/style.css'}}"/>
    <title>React App</title>
    <script src="https://cdn.tiny.cloud/1/0cvgyq8htwfa5cldcb7inwo3d6meev709oz5fuwnlml6q7iz/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <link rel="stylesheet" type="text/css" id="mce-u0" referrerpolicy="origin"
          href="https://cdn.tiny.cloud/1/0cvgyq8htwfa5cldcb7inwo3d6meev709oz5fuwnlml6q7iz/tinymce/5.10.4-130/skins/ui/oxide/skin.min.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero" style="height: 32rem; background-image: url({{ asset('assets/components/abstract-bg-4.svg') }});">
        <!-- SVG Bottom Shape -->
        <figure class="position-absolute right-0 bottom-0 left-0">
            <svg preserveaspectratio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewbox="0 0 1921 273">
                <polygon fill="#fff" points="0,273 1921,273 1921,0 "></polygon>
            </svg>
        </figure>
        <!-- End SVG Bottom Shape -->
        <!-- End SVG Shapes -->
    </div>

    <!-- Content -->
    <div class="container py-5 py-sm-7">
        <a class="d-flex justify-content-center mb-5" href="index.html">
            <img class="z-index-2" src="{{asset('assets/components/logo-rdsic.png')}}" alt="Image Description" style="width: 20rem;">
        </a>

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <!-- Card -->
                <div class="card card-lg mb-5">
                    <div class="card-body">
                        <!-- Form -->
                        <form class="js-validate" action="{{route('luuBV')}}" method="post">
                            <div class="text-center">
                                <div class="mb-5">
                                    <h1 class="display-4">Create your account</h1>
                                    <p>Already have an account? <a href="authentication-signin-basic.html">Sign in here</a></p>
                                </div>
                            </div>
                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrEmail">Full Name</label>

                                <input type="text" class="form-control form-control-lg" name="fullname" id="signupSrEmail" placeholder="Mark William" aria-label="Markwilliams@example.com" required="" data-msg="Please enter a valid email address.">
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrEmail">Your email</label>

                                <input type="email" class="form-control form-control-lg" name="email" id="signupSrEmail" placeholder="Markwilliams@example.com" aria-label="Markwilliams@example.com" required="" data-msg="Please enter a valid email address.">
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrPassword">Password</label>

                                <div class="input-group input-group-merge">
                                    <input type="password" class="js-toggle-password form-control form-control-lg" name="password" id="signupSrPassword" placeholder="8+ characters required" aria-label="8+ characters required" required="" data-msg="Your password is invalid. Please try again." data-hs-toggle-password-options='{
                               "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                               "defaultClass": "tio-hidden-outlined",
                               "showClass": "tio-visible-outlined",
                               "classChangeTarget": ".js-toggle-passowrd-show-icon-1"
                             }'>
                                    <div class="js-toggle-password-target-1 input-group-append">
                                        <a class="input-group-text" href="javascript:;">
                                            <i class="js-toggle-passowrd-show-icon-1 tio-visible-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrConfirmPassword">Confirm password</label>

                                <div class="input-group input-group-merge">
                                    <input type="password" class="js-toggle-password form-control form-control-lg" name="confirmPassword" id="signupSrConfirmPassword" placeholder="8+ characters required" aria-label="8+ characters required" required="" data-msg="Password does not match the confirm password." data-hs-toggle-password-options='{
                               "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                               "defaultClass": "tio-hidden-outlined",
                               "showClass": "tio-visible-outlined",
                               "classChangeTarget": ".js-toggle-passowrd-show-icon-2"
                             }'>
                                    <div class="js-toggle-password-target-2 input-group-append">
                                        <a class="input-group-text" href="javascript:;">
                                            <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Checkbox -->
                            <div class="js-form-message form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="termsCheckbox" required="" data-msg="Please accept our Terms and Conditions.">
                                    <label class="custom-control-label text-muted" for="termsCheckbox"> I accept the <a href="#">Terms and Conditions</a></label>
                                </div>
                            </div>
                            <!-- End Checkbox -->

                            <button type="submit" class="btn btn-lg btn-block btn-primary mb-2">Create an account</button>

                        </form>
                        <!-- End Form -->
                    </div>
                </div>
                <!-- End Card -->


            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- ========== END SECONDARY CONTENTS ========== -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- JS Implementing Plugins -->
<script src="{{Request::root().'/js/js_admin/vendor.min.js'}}"></script>
<script src="{{Request::root().'/js/js_admin/theme.min.js'}}"></script>

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });


        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function() {
            $.HSCore.components.HSValidation.init($(this), {
                rules: {
                    confirmPassword: {
                        equalTo: '#signupSrPassword'
                    }
                }
            });
        });
    });
</script>

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="./assets/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
