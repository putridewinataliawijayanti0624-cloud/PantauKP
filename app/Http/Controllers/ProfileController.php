<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load('studentProfile', 'lecturerProfile');

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        if ($user->isMahasiswa() && $user->studentProfile) {
            $user->studentProfile->update(['no_hp' => $data['no_hp'] ?? null]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
