<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Get the wedding info associated with the user
        $wedding = \App\Models\Wedding::where('user_id', Auth::id())->first();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'wedding' => $wedding,
        ]);
    }

    /**
     * Update the user's wedding information.
     */
    public function updateWedding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_cpw' => 'required|string|max:255',
            'nama_cpp' => 'required|string|max:255',
            'tanggal_nikah' => 'required|date',
            'lokasi_akad' => 'nullable|string|max:255',
            'lokasi_resepsi' => 'nullable|string|max:255',
        ]);

        $wedding = \App\Models\Wedding::where('user_id', Auth::id())->first();
        if ($wedding) {
            $wedding->update($validated);
        }

        return Redirect::route('profile.edit')->with('status', 'wedding-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
