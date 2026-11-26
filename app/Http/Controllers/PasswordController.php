<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Change Password for the signed-in user. The current password is asked first.
 */
class PasswordController extends Controller
{
    public function edit()
    {
        return view('account.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'Your current password is wrong.',
            'password.different'                 => 'The new password must be different from the current one.',
        ]);

        $request->user()->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return back()->with('success', 'Your password was changed.');
    }
}
