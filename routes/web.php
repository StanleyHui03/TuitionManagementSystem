<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LessonController; // ★ Lessons
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TutorLessonController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Home → Login
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/login'));

/*
|--------------------------------------------------------------------------
| Dashboard 跳转（按角色）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'notBanned'])->get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin')   return redirect()->route('admin.dashboard');
    if ($user->role === 'tutor')   return redirect()->route('tutor.dashboard');
    return redirect()->route('student.dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Account（个人账户）+ 邮箱验证相关
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'notBanned'])->group(function () {
    // 账户编辑/更新/删除
    Route::get('/account',  [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account',[AccountController::class, 'update'])->name('account.update');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    Route::delete('/account',[AccountController::class, 'destroy'])->name('account.destroy');

    // 重新发送验证邮件
    Route::post('/email/verification-notification', [AccountController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // 验证提示页
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    // 验证回调
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('account.edit')->with('status', 'email-verified');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

/*
|--------------------------------------------------------------------------
| Admin routes（/admin 前缀 + auth + isAdmin + notBanned）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isAdmin', 'notBanned'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Users 管理
        Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');

        // id 仅允许数字，避免和静态路径冲突
        Route::get   ('/users/create',     [AdminController::class, 'createUserForm'])->name('users.create');
        Route::post  ('/users/create',     [AdminController::class, 'createUser'])->middleware('throttle:10,1');
        Route::get   ('/users/{id}/edit',  [AdminController::class, 'editUserForm'])->whereNumber('id')->name('users.edit');
        Route::put   ('/users/{id}',       [AdminController::class, 'updateUser'])->whereNumber('id')->middleware('throttle:20,1')->name('users.update');
        Route::delete('/users/{id}',       [AdminController::class, 'deleteUser'])->whereNumber('id')->middleware('throttle:20,1')->name('users.delete');

        // 导出 CSV（带上当前筛选条件：type/search）+ 轻度限流
        Route::get('/users/export', [AdminController::class, 'export'])
            ->middleware('throttle:3,1')
            ->name('users.export');

        // 封禁 / 解封
        Route::patch('/users/{user}/ban',   [AdminController::class, 'ban'])->name('users.ban');
        Route::patch('/users/{user}/unban', [AdminController::class, 'unban'])->name('users.unban');

        // 重置用户密码
        Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])
            ->whereNumber('id')
            ->middleware('throttle:10,1')
            ->name('users.resetPassword');

        // Subjects CRUD（基于主键 subject_id 进行模型绑定）
        Route::get   ('/subjects',                             [SubjectController::class, 'index'])->name('subjects.index');
        Route::get   ('/subjects/create',                      [SubjectController::class, 'create'])->name('subjects.create');
        Route::post  ('/subjects',                             [SubjectController::class, 'store'])->middleware('throttle:10,1')->name('subjects.store');

        /* ✅ 新增导出路由：务必放在参数路由之前，避免被当成 subject_id 匹配 */
        Route::get   ('/subjects/export',                      [SubjectController::class, 'export'])->middleware('throttle:3,1')->name('subjects.export');

        Route::get   ('/subjects/{subject:subject_id}',        [SubjectController::class, 'show'])->name('subjects.show');
        Route::get   ('/subjects/{subject:subject_id}/edit',   [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put   ('/subjects/{subject:subject_id}',        [SubjectController::class, 'update'])->middleware('throttle:20,1')->name('subjects.update');
        Route::delete('/subjects/{subject:subject_id}',        [SubjectController::class, 'destroy'])->middleware('throttle:20,1')->name('subjects.destroy');
     
        // Lessons CRUD（基于主键 lesson_id 进行模型绑定）
        Route::get   ('/lessons',                        [LessonController::class, 'index'])->name('lessons.index');
        Route::get   ('/lessons/create',                 [LessonController::class, 'create'])->name('lessons.create');

        // 先放「静态」或工具路由，避免与 {lesson} 冲突
        Route::get   ('/lessons/export',                 [LessonController::class, 'export'])
            ->middleware('throttle:3,1')
            ->name('lessons.export');

        Route::get   ('/lessons/check-conflicts',        [LessonController::class, 'checkConflicts'])
            ->middleware('throttle:20,1') // 可按需调整
            ->name('lessons.checkConflicts');

        Route::post  ('/lessons',                        [LessonController::class, 'store'])->middleware('throttle:10,1')->name('lessons.store');
        Route::get   ('/lessons/{lesson:lesson_id}',     [LessonController::class, 'show'])->name('lessons.show');
        Route::get   ('/lessons/{lesson:lesson_id}/edit',[LessonController::class, 'edit'])->name('lessons.edit');
        Route::put   ('/lessons/{lesson:lesson_id}',     [LessonController::class, 'update'])->middleware('throttle:20,1')->name('lessons.update');
        Route::delete('/lessons/{lesson:lesson_id}',     [LessonController::class, 'destroy'])->middleware('throttle:20,1')->name('lessons.destroy');

        Route::get('/subjects/{subject:subject_id}/analytics', [AnalyticsController::class, 'showSubjectAnalytics'])
            ->name('subjects.analytics');
    });


    
    Route::get('/test-analytics', function () {
        // 模拟数据传递给视图
        $subject = (object)[
            'subject_id' => 'SU0001',
            'subject_Name' => 'Test Subject'
        ];

        $metrics = [
            'total_lessons' => 10,
            'active_students' => 50,
            'avg_attendance' => 85.5
        ];

        return view('admin.subjects.analytics', [
            'subject' => $subject,
            'metrics' => $metrics
        ]);
    });

/*
|--------------------------------------------------------------------------
| Tutor & Student dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'notBanned'])->group(function () {
    Route::get('/tutor/dashboard', fn () => view('tutor.dashboard'))->name('tutor.dashboard');
    Route::get('/student/dashboard', fn () => view('student.dashboard'))->name('student.dashboard');
});

Route::name('materials.')->group(function () {
    Route::get('/materials', [MaterialController::class, 'index'])->name('index');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('show');

    // Secure streaming endpoints (GET only; no CSRF needed)
    Route::get('/materials/{material}/preview', [MaterialController::class, 'preview'])->name('preview');
    Route::get('/materials/{material}/download', [MaterialController::class, 'download'])->name('download');

    // Mutations (need CSRF + session → keep under web + add @csrf in forms)
    Route::post('/materials', [MaterialController::class, 'store'])
        ->middleware(['auth','verified'])->name('store');

    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])
        ->middleware(['auth','verified'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    
    // List lessons for logged-in tutor
    Route::get('/tutor/lessons', [TutorLessonController::class, 'index'])
        ->name('tutor.lessons');

    
    // Show attendance form for a lesson
    Route::get('/lessons/{lesson:lesson_id}/attendance', [AttendanceController::class, 'create'])
        ->name('attendance.create');

    // Save attendance for a lesson
    Route::post('/lessons/{lesson:lesson_id}/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

        // Student views own attendance
    Route::get('/student/attendance', [AttendanceController::class, 'studentAttendance'])
        ->name('student.attendance');

});

// 示例页
Route::get('/test', fn () => view('test'));

// 邮件连通性测试（开发用途）
Route::get('/_mail_test', function () {
    try {
        Mail::raw('This is a raw test email body.', function ($message) {
            $message->to('你的接收邮箱@example.com')->subject('SMTP Test from Laravel');
        });
        return 'Mail sent OK. Check your inbox (and spam).';
    } catch (\Throwable $e) {
        Log::error('Mail test failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return 'Mail failed: '.$e->getMessage();
    }
});

// API 文档页（仅登录用户可看）
Route::middleware(['auth'])->get('/api-docs', fn () => view('api.docs'))->name('api.docs');

// 生成个人访问令牌（仅开发演示用途）
Route::middleware(['auth'])->post('/api-docs/token', function (\Illuminate\Http\Request $request) {
    $token = $request->user()->createToken('demo-token')->plainTextToken;
    return back()->with('api_token', $token);
})->name('api.docs.token');

// Breeze/Fortify/Auth 路由
require __DIR__ . '/auth.php';
