<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Manage Users (admin only): add, edit, deactivate/activate and delete accounts.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = new User(['name' => $data['name'], 'email' => $data['email']]);
        $user->password = Hash::make($data['password']);
        $user->is_admin = $request->boolean('is_admin');   // set in code, not mass-assigned
        $user->is_active = true;
        $user->save();

        return redirect()->route('users.index')->with('success', "Account for {$user->name} was created.");
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    /**
     * Edit name, email and admin rights. A new password is optional (admin reset).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $makeAdmin = $request->boolean('is_admin');
        if ($user->is_admin && !$makeAdmin && $this->isLastActiveAdmin($user)) {
            return back()->withInput()->withErrors('There must be at least one active admin.');
        }
        if ($user->id === $request->user()->id && !$makeAdmin) {
            return back()->withInput()->withErrors('You cannot remove your own admin rights.');
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        $user->is_admin = $makeAdmin;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('users.index')->with('success', "Account of {$user->name} was updated.");
    }

    /**
     * Deactivate / activate. A deactivated account cannot sign in.
     */
    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors('You cannot deactivate your own account.');
        }
        if ($user->is_active && $user->is_admin && $this->isLastActiveAdmin($user)) {
            return back()->withErrors('There must be at least one active admin.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', $user->name . ($user->is_active ? ' can sign in again.' : ' can no longer sign in.'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors('You cannot delete your own account.');
        }
        if ($user->is_admin && $this->isLastActiveAdmin($user)) {
            return back()->withErrors('There must be at least one active admin.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', "Account of {$user->name} was deleted.");
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return !User::where('is_admin', true)->where('is_active', true)->where('id', '!=', $user->id)->exists();
    }
}
