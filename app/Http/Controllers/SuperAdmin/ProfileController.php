<?php

/**
 * Company: CETAM
 * Project: FF
 * File: ProfileController.php (SuperAdmin)
 * Created on: 20/11/2025
 * Created by: Alan Jesus Garcia Nava
 * Approved by: Alan Jesus Garcia Nava
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Alan Jesus Garcia Nava |
 *   Description: Refactored Profile Controller |
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the super admin profile
     */
    public function index(): View
    {
        $superAdmin = auth()->guard('superadmin')->user();

        return view('superadmin.profile.index', compact('superAdmin'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit(): View
    {
        $superAdmin = auth()->guard('superadmin')->user();

        return view('superadmin.profile.edit', compact('superAdmin'));
    }

    /**
     * Update the super admin profile
     */
    public function update(Request $request): RedirectResponse
    {
        $superAdmin = auth()->guard('superadmin')->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:super_admins,email,' . $superAdmin->super_admin_id . ',super_admin_id'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', Password::min(8)->max(12)->mixedCase()->numbers()],
            'new_password_confirmation' => ['nullable', 'required_with:new_password', 'same:new_password'],
        ]);

        //  Extract password update to private method
        $this->updateBasicInfo($superAdmin, $validated);

        //  Early Return - Invalid current password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $superAdmin->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }

            $this->updatePassword($superAdmin, $validated['new_password']);
        }

        $superAdmin->save();

        return redirect()->route('superadmin.profile.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Update basic profile information
     * Private helper method following SRP
     */
    private function updateBasicInfo($superAdmin, array $validated): void
    {
        $superAdmin->full_name = $validated['full_name'];
        $superAdmin->email = $validated['email'];
    }

    /**
     * Update password
     *Private helper method following SRP
     */
    private function updatePassword($superAdmin, string $newPassword): void
    {
        $superAdmin->password = Hash::make($newPassword);
    }
}
