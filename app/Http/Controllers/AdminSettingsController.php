<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated admin session can access
        // $this->middleware('admin.session'); // Commented out due to missing method
    }

    public function show(Request $request)
    {
        $adminId = session('admin_id');
        $admin = Admin::findOrFail($adminId);
        return view('partials.settings', compact('admin'));
    }

    public function update(Request $request)
    {
        $adminId = session('admin_id');
        $admin = Admin::findOrFail($adminId);

        $validated = $request->validate([
            'firstname'        => ['required', 'string', 'max:100'],
            'lastname'         => ['required', 'string', 'max:100'],
            'username'         => ['required', 'string', 'max:100', 'unique:admin,username,' . $admin->id],
            'current_password' => ['required_with:password'],
            'password'         => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            // 🔹 Custom error messages
            'firstname.required' => 'First name cannot be empty.',
            'lastname.required'  => 'Last name cannot be empty.',
            'password.confirmed' => 'New password and confirm password do not match.',
            'password.min' => 'Password must be at least 6 characters.',
            'current_password.required_with' => 'Please enter your current password to set a new one.',
            'username.unique' => 'This username is already taken.',
        ]);

        if (!empty($validated['firstname'])) {
            $admin->firstname = $validated['firstname'];
        }

        if (!empty($validated['lastname'])) {
            $admin->lastname = $validated['lastname'];
        }

        if (!empty($validated['username'])) {
            $admin->username = $validated['username'];
        }

        if (!empty($validated['password'])) {
            // Verify old password first
            if (!Hash::check($validated['current_password'], $admin->password)) {
                return redirect()->back()->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ]);
            }

            // Save new password
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Settings updated');
    }

    /**
     * Show form to create a new admin
     */
    public function create()
    {
        $admins = Admin::orderBy('lastname')->get();
        return view('admin.admin_accounts', compact('admins'));
    }

    /**
     * Store a newly created admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname'  => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:100', 'unique:admin,username'],
            'password'  => ['required', 'string', 'min:6'],
            'role'      => ['nullable', 'string', 'max:50'],
        ]);

        $admin = new Admin();
        $admin->firstname = $validated['firstname'];
        $admin->lastname  = $validated['lastname'];
        $admin->username  = $validated['username'];
        $admin->password  = Hash::make($validated['password']);
        $admin->role      = $validated['role'] ?? 'admin';

        // Save first to get ID for file storage
        $admin->save();
        return redirect()->route('admin.settings.show')->with('status', 'New admin created');
    }

    /**
     * Update admin role (AJAX friendly)
     */
    public function updateRole(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $validated = $request->validate([
            'role' => ['required', 'in:admin,nurse,staff'],
        ]);

        $admin->role = $validated['role'];
        $admin->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'role' => $admin->role]);
        }

        return redirect()->back()->with('status', 'Role updated');
    }
}
