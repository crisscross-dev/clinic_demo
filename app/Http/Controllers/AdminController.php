<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    // List all admins
    public function index()
    {
        $admins = Admin::orderBy('lastname')->get();
        return view('admins.index', compact('admins'));
    }

    // Show create form
    public function create()
    {
        return view('admins.create');
    }

    // Store new admin
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstname' => ['required', 'string', 'max:100'],
                'lastname'  => ['required', 'string', 'max:100'],
                'username'  => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:admin,username', // ✅ check uniqueness on username
                    'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i', // ✅ Gmail only
                ],
                'role'      => ['required', 'in:admin,medical,staff'],
            ], [
                'username.regex' => 'The username must be a valid Gmail address (e.g., example@gmail.com).',
            ]);

            $admin = new Admin();
            $admin->firstname = $validated['firstname'];
            $admin->lastname  = $validated['lastname'];

            // ✅ Use Gmail username for both username & email
            $admin->username  = $validated['username'];
            $admin->email     = $validated['username'];

            // ✅ Set default password (user can change it later in settings)
            $defaultPassword = 'Samuelclinic_2012'; // You can change this default password
            $admin->password  = Hash::make($defaultPassword);
            $admin->role      = $validated['role'];
            $admin->save();

            // Check if request is AJAX
            if ($request->expectsJson()) {
                // Also flash a session message so a client-side redirect to the index
                // create blade to index success message
                session()->flash('success', 'Admin created successfully with default password. They can change it in their settings.');
                return response()->json([
                    'success' => true,
                    'message' => 'Admin created successfully with default password.',
                    'admin'   => $admin
                ]);
            }

            return redirect()->route('admins.index')->with('success', 'Admin created successfully with default password. They can change it in their settings.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $e->validator->errors()->toArray()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the admin.'
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while creating the admin.');
        }
    }



    // Show specific admin
    public function show(Admin $admin)
    {
        return view('admins.show', compact('admin'));
    }

    // Show edit form
    public function edit(Admin $admin)
    {
        return view('admins.edit', compact('admin'));
    }

    // Update admin
    public function update(Request $request)
    {
        $adminId = session('admin_id');
        $admin = Admin::findOrFail($adminId);

        // Block updates for demo account
        if (strtolower($admin->username) === 'demo@gmail.com' || session('is_demo', false)) {
            return redirect()->back()->withErrors([
                'demo' => 'This is a demo account. Changes are not allowed.',
            ]);
        }

        $validated = $request->validate([
            'firstname'        => ['required', 'string', 'max:100'],
            'lastname'         => ['required', 'string', 'max:100'],
            'middlename'       => ['nullable', 'string', 'max:100'],
            'prefix'           => ['nullable', 'string', 'max:100'],
            'username'         => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i', // ✅ Gmail-only username
                'unique:admin,username,' . $admin->id,
            ],
            'current_password' => ['required_with:password'],
            'password'         => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/'
            ],
        ], [
            'username.regex'   => 'Username must be a valid Gmail address.',
            'username.unique'  => 'This username is already taken.',
            'password.confirmed' => 'New password and confirm password do not match.',
            'password.min'     => 'Password must be at least 8 characters long.',
            'password.regex'   => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*).',
            'current_password.required_with' => 'Please enter your current password to set a new one.',
        ]);

        // ✅ Normalize prefix: trim whitespace + add "." if missing
        $prefix = $validated['prefix'] ?? null;
        if ($prefix) {
            $prefix = trim($prefix); // remove spaces at start/end
            $prefix = preg_replace('/\s+/', ' ', $prefix); // collapse multiple spaces
            $prefix = preg_replace('/\s*\.\s*$/', '.', $prefix); // clean spaces before/after .
            if (!str_ends_with($prefix, '.')) {
                $prefix .= '.';
            }
        }


        // ✅ Always sync username and email
        $admin->firstname  = $validated['firstname'];
        $admin->lastname   = $validated['lastname'];
        $admin->middlename = $validated['middlename'] ?? null;
        $admin->prefix     = $prefix;
        $admin->username   = $validated['username'];
        $admin->email      = $validated['username'];

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $admin->password)) {
                return redirect()->back()->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ]);
            }
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Settings updated');
    }



    // Delete admin
    public function destroy(Admin $admin)
    {
        // Prevent deleting the last admin or current user
        if (Admin::count() <= 1) {
            return redirect()->route('admins.index')->with('error', 'Cannot delete the last admin.');
        }

        if ($admin->id == session('admin_id')) {
            return redirect()->route('admins.index')->with('error', 'Cannot delete yourself.');
        }

        $admin->delete();
        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully.');
    }

    // Update role via AJAX
    public function updateRole(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,medical,staff',
        ]);

        $admin->update(['role' => $validated['role']]);

        // Set flash message for the redirect
        session()->flash('success', 'Role updated successfully for ' . $admin->firstname . ' ' . $admin->lastname);

        return response()->json([
            'success' => true,
            'role' => $admin->role
        ]);
    }
}
