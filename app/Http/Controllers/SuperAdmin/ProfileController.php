<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the super admin profile
     */
    public function index()
    {
        $superAdmin = auth()->guard('superadmin')->user();
        return view('superadmin.profile.index', compact('superAdmin'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $superAdmin = auth()->guard('superadmin')->user();
        return view('superadmin.profile.edit', compact('superAdmin'));
    }

    /**
     * Update the super admin profile
     */
    public function update(Request $request)
    {
        $superAdmin = auth()->guard('superadmin')->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:super_admins,email,' . $superAdmin->super_admin_id . ',super_admin_id'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()],
            'new_password_confirmation' => ['nullable', 'required_with:new_password', 'same:new_password'],
        ]);

        // Update basic info
        $superAdmin->full_name = $validated['full_name'];
        $superAdmin->email = $validated['email'];

        // Update password if provided
        if ($request->filled('new_password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $superAdmin->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }

            $superAdmin->password = Hash::make($validated['new_password']);
        }

        $superAdmin->save();

        return redirect()->route('superadmin.profile.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
