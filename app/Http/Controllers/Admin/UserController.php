<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, fn ($query, $role) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
        $user->email_verified_at = now();
        $user->save();

        ActivityLogger::log(
            'create_user',
            "Menambahkan user {$user->name} ({$user->email}) dengan role {$user->role}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Admin tidak boleh mengubah role akunnya sendiri
        if ($user->is($request->user())) {
            $data['role'] = $user->role;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        $roleInfo = $user->wasChanged('role') ? " (role menjadi {$user->role})" : '';

        ActivityLogger::log(
            'update_user',
            "Mengubah data user {$user->name} ({$user->email}){$roleInfo}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($user->loans()->withTrashed()->exists() || $user->activityLogs()->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena sudah memiliki riwayat peminjaman atau aktivitas.');
        }

        $name = $user->name;
        $email = $user->email;
        $user->delete();

        ActivityLogger::log('delete_user', "Menghapus user {$name} ({$email})");

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}