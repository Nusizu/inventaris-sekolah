<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $role));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $this->guardAdminRole($request->user(), $data['role']);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'user', // disesuaikan otomatis dari peran di bawah
            'password' => Hash::make($data['password']),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $user->syncRoles([$data['role']]);
        $user->syncRoleColumn();

        ActivityLogger::log(
            'create_user',
            "Menambahkan user {$user->name} ({$user->email}) dengan peran {$data['role']}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user)
    {
        $this->guardAdminAccount($request->user(), $user);

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $this->guardAdminAccount($request->user(), $user);

        $data = $request->validated();
        $newRole = $data['role'];
        unset($data['role']);

        // Role akun yang sedang dipakai tidak boleh diubah sendiri
        $isSelf = $user->is($request->user());

        if (! $isSelf) {
            $this->guardAdminRole($request->user(), $newRole);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        $roleInfo = '';

        if (! $isSelf && ! $user->hasRole($newRole)) {
            $user->syncRoles([$newRole]);
            $user->syncRoleColumn();
            $roleInfo = " (peran menjadi {$newRole})";
        }

        ActivityLogger::log(
            'update_user',
            "Mengubah data user {$user->name} ({$user->email}){$roleInfo}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->guardAdminAccount($request->user(), $user);

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

    /** Hanya Admin yang boleh menetapkan peran Admin ke akun lain. */
    private function guardAdminRole(User $actor, string $role): void
    {
        abort_if($role === 'admin' && ! $actor->hasRole('admin'), 403);
    }

    /** Hanya Admin yang boleh mengubah atau menghapus akun Admin. */
    private function guardAdminAccount(User $actor, User $target): void
    {
        abort_if($target->hasRole('admin') && ! $actor->hasRole('admin'), 403);
    }
}