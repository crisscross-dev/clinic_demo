<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_id' => $admin->id,
                'admin_role' => $admin->role,
                'admin_firstname' => $admin->firstname,
                'admin_lastname' => $admin->lastname,
                'admin_username' => $admin->username,
                'is_demo' => strtolower($admin->username) === 'demo@gmail.com'
            ]);
            if (in_array($admin->role, ['admin', 'medical'])) {
                return redirect()->route('admin.dashboard');
            } elseif ($admin->role === 'staff') {
                return redirect()->route('inventory.index');
            }
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $request->session()->flush(); // Remove all session data
        return redirect()->route('admin.login'); // Redirect to login page
    }
}
