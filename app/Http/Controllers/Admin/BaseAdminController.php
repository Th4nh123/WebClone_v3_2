<?php

namespace App\Http\Controllers\Admin;



class BaseAdminController
{
    public function set_session($name,$value) {
        $_SESSION[$name] = $value;
    }
}
