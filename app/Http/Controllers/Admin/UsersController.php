<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController
{
    public function view_login(Request $request)
    {
        try {
            return view('admin.users.login');
        } catch (\Exception $e) {
            return view('errors.404');
        }
    }

    public function action_login(Request $request)
    {
        $err = '';
        if (!empty($request->username) && !empty($request->password)) {
            $user = DB::table('hwp_users')
                ->where('user_login', '=', $request->username)
                ->where('user_pass', '=', $request->password)
                ->get()->toArray();

            if (count($user) == 1) {
                $request->session()->push('tk_user', $request->username);
                $request->session()->push('role', $user[0]->role);
                return redirect('/admin/trang-chu');
            } else {
                $err = "Sai tài khoản hoặc mật khẩu";
                return view('admin.users.login', compact('err'));
            }
        }
    }

    public function index_user(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $ds_user = DB::table('hwp_users')
                    ->orderBy('hwp_users.ID', 'desc')
                    ->paginate(15);
                return view('admin.users.list_user', compact('ds_user'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function page_user()
    {
        return view('admin.users.them_user');
    }

    public function page_edit_user(Request $request)
    {
        $user = DB::table('hwp_users')
            ->where("hwp_users.ID", '=', $request->id)
            ->get()->toArray()[0];
        return view('admin.users.edit_user', compact('user'));
    }

    public function edit_user(Request $request)
    {
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses) && ($request->session()->get('role')[0] == 'admin')) {

                $rs = DB::table('hwp_users')
                    ->where('hwp_users.ID', '=', $request->id)
                    ->update([
                        'user_login' => $request->username,
                        'user_pass' => $request->password,
                        'user_nicename' => $request->full_name,
                        'user_email' => $request->email == null ? "" : $request->email,
                        'user_url' => '',
                        'user_registered' => date('y-m-d h:i:s'),
                        'user_activation_key' => '',
                        'user_status' => 0,
                        'display_name' => $request->full_name,
                        'role' => $request->quyen,
                    ]);
                return redirect()->route('index_user');
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function delete_user(Request $request)
    {

        $ses = $request->session()->get('tk_user');
        if (isset($ses) && $request->session()->get('role')[0] == 'admin') {
            DB::table('hwp_users')->where('hwp_users.ID', '=', $request->id)->delete();
        }
        return redirect()->back();
    }

    public function insert_user(Request $request)
    {

        $rs = DB::table('hwp_users')->insert([
            'user_login' => $request->username,
            'user_pass' => $request->password,
            'user_nicename' => $request->full_name,
            'user_email' => $request->email == null ? "" : $request->email,
            'user_url' => '',
            'user_registered' => date('y-m-d h:i:s'),
            'user_activation_key' => '',
            'user_status' => 0,
            'display_name' => $request->full_name,
            'role' => $request->quyen,
        ]);
        if ($rs == true) {
            return redirect('/admin/index-user');
        } else {
            $err = 'Vui lòng kiểm tra lại thông tin';
            return view('admin.users.them_user', compact('err'));
        }
    }
    public function find_user(Request $request){
        try {
            $ses = $request->session()->get('tk_user');
            if (isset($ses)) {
                $index = 1;
                if (isset($request->s) && strlen($request->s) >= 1) {
                    $search_text = $request->s;
                    $ds_user = DB::table('hwp_users')
                        ->where('hwp_users.display_name', 'like', '%' . $search_text . '%')
                        ->orderBy('hwp_users.ID', 'desc')
                        ->paginate(15);
                    return view('admin.users.list_user', compact('ds_user'));
                }
            } else {
                return redirect('/admin/login');

            }

        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
