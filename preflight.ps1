# preflight.ps1 - pemeriksaan otomatis sebelum pengujian
#
# Letakkan file ini di folder utama project (sejajar dengan file artisan), lalu jalankan:
#   powershell -ExecutionPolicy Bypass -File .\preflight.ps1
#
# Pastikan MySQL sudah menyala sebelum menjalankan skrip ini.

if (-not (Test-Path 'artisan')) {
    Write-Host 'Skrip ini harus dijalankan dari folder utama project (yang berisi file artisan).' -ForegroundColor Red
    Write-Host ('Folder sekarang: ' + (Get-Location).Path) -ForegroundColor Red
    exit 1
}

$problems = @()

# 1. File wajib
Write-Host '1/8 Memeriksa file wajib...' -ForegroundColor Cyan
$files = @(
    'app\Http\Controllers\ProfileController.php',
    'app\Http\Controllers\Admin\ActivityLogController.php',
    'app\Http\Controllers\Admin\CategoryController.php',
    'app\Http\Controllers\Admin\DashboardController.php',
    'app\Http\Controllers\Admin\ItemController.php',
    'app\Http\Controllers\Admin\LoanController.php',
    'app\Http\Controllers\Admin\ReturnLogController.php',
    'app\Http\Controllers\Admin\UserController.php',
    'app\Http\Controllers\User\DashboardController.php',
    'app\Http\Controllers\User\ItemController.php',
    'app\Http\Controllers\User\LoanController.php',
    'app\Http\Controllers\Auth\AuthenticatedSessionController.php',
    'app\Http\Controllers\Auth\PasswordController.php',
    'app\Http\Controllers\Auth\RegisteredUserController.php',
    'app\Http\Middleware\RoleMiddleware.php',
    'app\Http\Middleware\SecurityHeaders.php',
    'app\Http\Requests\CategoryRequest.php',
    'app\Http\Requests\ItemRequest.php',
    'app\Http\Requests\LoanRequest.php',
    'app\Http\Requests\ReturnLogRequest.php',
    'app\Http\Requests\UserRequest.php',
    'app\Http\Requests\ProfileUpdateRequest.php',
    'app\Http\Requests\Auth\LoginRequest.php',
    'app\Http\Requests\Auth\RegisterRequest.php',
    'app\Http\Requests\Auth\UpdatePasswordRequest.php',
    'app\Models\ActivityLog.php',
    'app\Models\Category.php',
    'app\Models\Item.php',
    'app\Models\Loan.php',
    'app\Models\ReturnLog.php',
    'app\Models\User.php',
    'app\Services\LoanService.php',
    'app\Services\ActivityLogger.php',
    'app\Providers\AppServiceProvider.php',
    'database\seeders\DatabaseSeeder.php',
    'database\seeders\UserSeeder.php',
    'database\seeders\CategorySeeder.php',
    'database\seeders\ItemSeeder.php',
    'database\seeders\LoanSeeder.php',
    'database\seeders\ReturnLogSeeder.php',
    'database\seeders\ActivityLogSeeder.php',
    'lang\id.json',
    'lang\id\validation.php',
    'lang\id\auth.php',
    'lang\id\pagination.php',
    'resources\js\app.js',
    'resources\css\app.css',
    'resources\views\layouts\app.blade.php',
    'resources\views\layouts\guest.blade.php',
    'resources\views\layouts\navigation.blade.php',
    'resources\views\components\breadcrumb.blade.php',
    'resources\views\components\flash-message.blade.php',
    'resources\views\components\loan-status-badge.blade.php',
    'resources\views\components\stat-card.blade.php',
    'resources\views\auth\login.blade.php',
    'resources\views\auth\register.blade.php',
    'resources\views\profile\edit.blade.php',
    'resources\views\profile\partials\update-profile-information-form.blade.php',
    'resources\views\profile\partials\update-password-form.blade.php',
    'resources\views\errors\layout.blade.php',
    'resources\views\errors\403.blade.php',
    'resources\views\errors\404.blade.php',
    'resources\views\errors\419.blade.php',
    'resources\views\errors\500.blade.php',
    'resources\views\loans\_form.blade.php',
    'resources\views\admin\dashboard.blade.php',
    'resources\views\admin\activity-logs\index.blade.php',
    'resources\views\admin\categories\index.blade.php',
    'resources\views\admin\categories\create.blade.php',
    'resources\views\admin\categories\edit.blade.php',
    'resources\views\admin\categories\_form.blade.php',
    'resources\views\admin\items\index.blade.php',
    'resources\views\admin\items\create.blade.php',
    'resources\views\admin\items\edit.blade.php',
    'resources\views\admin\items\_form.blade.php',
    'resources\views\admin\loans\index.blade.php',
    'resources\views\admin\loans\create.blade.php',
    'resources\views\admin\return-logs\index.blade.php',
    'resources\views\admin\return-logs\create.blade.php',
    'resources\views\admin\users\index.blade.php',
    'resources\views\admin\users\create.blade.php',
    'resources\views\admin\users\edit.blade.php',
    'resources\views\admin\users\_form.blade.php',
    'resources\views\user\dashboard.blade.php',
    'resources\views\user\items\index.blade.php',
    'resources\views\user\loans\index.blade.php',
    'resources\views\user\loans\create.blade.php'
)
foreach ($f in $files) {
    if (-not (Test-Path $f)) { $problems += "File tidak ada: $f" }
}

if (-not (Get-ChildItem database\migrations -Filter '*add_indexes_for_performance.php' -ErrorAction SilentlyContinue)) {
    $problems += 'Migration index belum ada: database\migrations\*add_indexes_for_performance.php'
}

# 2. Sintaks PHP
Write-Host '2/8 Memeriksa sintaks PHP...' -ForegroundColor Cyan
Get-ChildItem app, routes, database, config -Recurse -Filter *.php | ForEach-Object {
    $out = (php -l $_.FullName 2>&1 | Out-String)
    if ($out -notmatch 'No syntax errors') { $problems += "Sintaks PHP bermasalah: $($_.FullName)" }
}

# 3. Nama view yang dipanggil controller
Write-Host '3/8 Memeriksa nama view di controller...' -ForegroundColor Cyan
Get-ChildItem app -Recurse -Filter *.php |
    Select-String -Pattern "view\('([a-z0-9._-]+)'" -AllMatches |
    ForEach-Object { $_.Matches } |
    ForEach-Object { $_.Groups[1].Value } |
    Sort-Object -Unique |
    Where-Object { $_ -notmatch '[-.]$' } |
    ForEach-Object {
        $p = 'resources\views\' + ($_ -replace '\.', '\') + '.blade.php'
        if (-not (Test-Path $p)) { $problems += "View dipanggil controller tetapi file tidak ada: $_" }
    }

# View Livewire disusun dinamis (item-list-{scope}), jadi diperiksa terpisah
foreach ($scope in 'admin', 'user') {
    if (-not (Test-Path "resources\views\livewire\item-list-$scope.blade.php")) {
        $problems += "View Livewire tidak ada: livewire.item-list-$scope"
    }
}

# 4. Nama route yang dipanggil
Write-Host '4/8 Memeriksa nama route...' -ForegroundColor Cyan
$routes = php artisan route:list --json | ConvertFrom-Json | Where-Object { $_.name } | ForEach-Object { $_.name }
if (-not $routes) {
    $problems += 'Daftar route gagal dibaca (jalankan php artisan route:list untuk melihat errornya)'
} else {
    Get-ChildItem app, resources\views, routes -Recurse -Filter *.php |
        Select-String -Pattern '(?<!\$this->)route\(\s*''([a-z0-9.\-]+)''' -AllMatches |
        ForEach-Object { $_.Matches } |
        ForEach-Object { $_.Groups[1].Value } |
        Sort-Object -Unique |
        Where-Object { $_ -notin $routes } |
        ForEach-Object { $problems += "Nama route dipanggil tetapi tidak terdaftar: $_" }
}

# 5. Komponen, include, dan extends Blade
Write-Host '5/8 Memeriksa komponen dan include Blade...' -ForegroundColor Cyan
$bladeFiles = Get-ChildItem resources\views -Recurse -Filter *.blade.php

$bladeFiles |
    Select-String -Pattern '<x-([a-z0-9\-\.]+)' -AllMatches |
    ForEach-Object { $_.Matches } |
    ForEach-Object { $_.Groups[1].Value } |
    Sort-Object -Unique |
    Where-Object { $_ -notin @('app-layout', 'guest-layout', 'slot') } |
    ForEach-Object {
        $p = 'resources\views\components\' + ($_ -replace '\.', '\') + '.blade.php'
        if (-not (Test-Path $p)) { $problems += "Komponen tidak ditemukan: x-$_" }
    }

$bladeFiles |
    Select-String -Pattern "@(include|extends)\('([a-z0-9._-]+)'" -AllMatches |
    ForEach-Object { $_.Matches } |
    ForEach-Object { $_.Groups[2].Value } |
    Sort-Object -Unique |
    ForEach-Object {
        $p = 'resources\views\' + ($_ -replace '\.', '\') + '.blade.php'
        if (-not (Test-Path $p)) { $problems += "View include/extends tidak ditemukan: $_" }
    }

# 6. Label Activity Log untuk semua aksi yang dicatat
Write-Host '6/8 Memeriksa label Activity Log...' -ForegroundColor Cyan
$labelsFile = Get-Content app\Models\ActivityLog.php -Raw
Get-ChildItem app -Recurse -Filter *.php | ForEach-Object {
    [regex]::Matches((Get-Content $_.FullName -Raw), "ActivityLogger::log\(\s*'([a-z_]+)'") |
        ForEach-Object { $_.Groups[1].Value }
} | Sort-Object -Unique | ForEach-Object {
    if ($labelsFile -notmatch "'$_'\s*=>") { $problems += "Label aksi belum ada di ActivityLog::ACTION_LABELS: $_" }
}

# 7. Isi file kunci
Write-Host '7/8 Memeriksa isi file kunci...' -ForegroundColor Cyan
$harusAda = @(
    @('resources\js\app.js', 'flatpickr'),
    @('resources\js\app.js', 'pageshow'),
    @('app\Providers\AppServiceProvider.php', 'Carbon::setLocale'),
    @('app\Providers\AppServiceProvider.php', 'preventLazyLoading'),
    @('app\Models\Item.php', 'SoftDeletes'),
    @('app\Models\Loan.php', 'SoftDeletes'),
    @('app\Models\ReturnLog.php', 'SoftDeletes'),
    @('app\Models\Loan.php', 'withTrashed'),
    @('app\Models\ActivityLog.php', 'ACTION_LABELS'),
    @('bootstrap\app.php', 'RoleMiddleware'),
    @('bootstrap\app.php', 'SecurityHeaders'),
    @('routes\web.php', 'loans.return.create'),
    @('routes\web.php', 'UserItemController'),
    @('app\Http\Controllers\Admin\ReturnLogController.php', "view('admin.return-logs.create'"),
    @('resources\views\admin\loans\index.blade.php', 'admin.loans.return.create'),
    @('resources\views\admin\return-logs\create.blade.php', 'admin.loans.return.store'),
    @('resources\views\admin\return-logs\create.blade.php', 'js-date'),
    @('resources\views\loans\_form.blade.php', 'js-date'),
    @('resources\views\layouts\app.blade.php', 'px-4 sm:px-0'),
    @('resources\views\layouts\navigation.blade.php', 'user.items.index'),
    @('lang\id\validation.php', 'attributes')
)
foreach ($c in $harusAda) {
    if (-not (Test-Path $c[0])) { continue }
    if (-not (Select-String -Path $c[0] -Pattern $c[1] -SimpleMatch -Quiet)) {
        $problems += "Isi belum sesuai: $($c[0]) harus memuat '$($c[1])'"
    }
}

$tidakBoleh = @(
    @('app\Http\Controllers\Admin\ReturnLogController.php', "view('admin.loans.return"),
    @('resources\views\layouts\app.blade.php', 'fonts.bunny.net'),
    @('resources\views\layouts\guest.blade.php', 'fonts.bunny.net'),
    @('resources\views\auth\login.blade.php', 'password.request'),
    @('routes\web.php', "'verified'"),
    @('resources\views\admin\return-logs\create.blade.php', 'type="date"')
)
foreach ($c in $tidakBoleh) {
    if (-not (Test-Path $c[0])) { continue }
    if (Select-String -Path $c[0] -Pattern $c[1] -SimpleMatch -Quiet) {
        $problems += "Isi tidak seharusnya ada: $($c[0]) masih memuat '$($c[1])'"
    }
}

# 8. Konfigurasi, database, dan kompilasi view
Write-Host '8/8 Memeriksa konfigurasi, database, dan kompilasi view...' -ForegroundColor Cyan
if (-not (Select-String -Path config\app.php -Pattern "'timezone' => 'Asia/Jakarta'" -SimpleMatch -Quiet)) {
    $problems += "config\app.php: timezone belum 'Asia/Jakarta'"
}
if (-not (Select-String -Path .env -Pattern 'APP_LOCALE=id' -SimpleMatch -Quiet)) {
    $problems += '.env: APP_LOCALE belum id'
}

php artisan migrate:status | Out-Null
if ($LASTEXITCODE -ne 0) { $problems += 'Database tidak terjangkau atau migrasi bermasalah (pastikan MySQL menyala)' }

$viewOut = php artisan view:cache 2>&1 | Out-String
if ($LASTEXITCODE -ne 0) { $problems += "Kompilasi view gagal: $viewOut" }
php artisan view:clear | Out-Null

# Ringkasan
Write-Host ''
if ($problems.Count -eq 0) {
    Write-Host 'SEMUA PEMERIKSAAN LULUS' -ForegroundColor Green
} else {
    Write-Host "DITEMUKAN $($problems.Count) MASALAH:" -ForegroundColor Red
    $problems | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
}
