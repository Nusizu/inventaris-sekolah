<?php

namespace App\Support;

class Permissions
{
    /** Peran bawaan yang tidak boleh diganti nama atau dihapus. */
    public const PROTECTED_ROLES = ['admin', 'user'];

    /** @return array<string, array{group: string, label: string}> */
    public static function all(): array
    {
        return [
            'admin.access' => ['group' => 'Akses Area', 'label' => 'Area pengelola (Admin dan Petugas)'],
            'user.access' => ['group' => 'Akses Area', 'label' => 'Area peminjam (User)'],

            'categories.manage' => ['group' => 'Pengelolaan', 'label' => 'Kelola kategori'],
            'items.manage' => ['group' => 'Pengelolaan', 'label' => 'Kelola barang dan label QR'],
            'loans.manage' => ['group' => 'Pengelolaan', 'label' => 'Kelola peminjaman (tambah, setujui, tolak, hapus)'],
            'returns.manage' => ['group' => 'Pengelolaan', 'label' => 'Proses pengembalian dan lihat riwayatnya'],

            'users.manage' => ['group' => 'Administrasi', 'label' => 'Kelola akun user'],
            'roles.manage' => ['group' => 'Administrasi', 'label' => 'Kelola peran dan izin'],
            'activity-logs.view' => ['group' => 'Administrasi', 'label' => 'Lihat Activity Log'],

            'reports.view' => ['group' => 'Laporan dan Alat', 'label' => 'Lihat dan unduh laporan peminjaman'],
            'scan.use' => ['group' => 'Laporan dan Alat', 'label' => 'Gunakan pemindai QR Code'],

            'items.browse' => ['group' => 'Fitur Peminjam', 'label' => 'Melihat daftar barang'],
            'loans.request' => ['group' => 'Fitur Peminjam', 'label' => 'Mengajukan dan membatalkan peminjaman, serta melihat riwayat sendiri'],
        ];
    }

    /** @return array<string, array<string, string>> grup => [nama izin => label] */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::all() as $name => $info) {
            $groups[$info['group']][$name] = $info['label'];
        }

        return $groups;
    }

    /** Izin bawaan untuk setiap peran. */
    public static function forRole(string $role): array
    {
        return match ($role) {
            'admin' => array_values(array_diff(
                array_keys(self::all()),
                ['user.access', 'items.browse', 'loans.request']
            )),
            'petugas' => ['admin.access', 'items.manage', 'loans.manage', 'returns.manage', 'reports.view', 'scan.use'],
            'user' => ['user.access', 'items.browse', 'loans.request', 'scan.use'],
            default => [],
        };
    }
}