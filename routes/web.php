<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- Controller Imports ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StaffLoanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\OnlineClassController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\StudyMaterialController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AdmissionInquiryController;

// --- Public Routes ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- Protected Routes (Login Required) ---
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // 1. Main Dashboard (Video 1 @ 02:11)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Student Management (Spec Module 1)
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/store', [StudentController::class, 'store'])->name('store');
        Route::post('/import', [StudentController::class, 'import'])->name('import'); // Bulk Excel
        Route::post('/promote', [StudentController::class, 'promoteStudents'])->name('promote'); // Promotion
        Route::get('/id-cards', [StudentController::class, 'generateIdCards'])->name('id_cards'); // PDF
    });

    // 3. Admission Inquiries (Leads)
    Route::prefix('inquiries')->name('inquiries.')->group(function () {
        Route::get('/', [AdmissionInquiryController::class, 'index'])->name('index');
        Route::post('/store', [AdmissionInquiryController::class, 'store'])->name('store');
        Route::post('/promote/{id}', [AdmissionInquiryController::class, 'promote'])->name('promote'); // Convert to Student
    });

    // 4. Class Management
    Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
    Route::post('/classes/store', [SchoolClassController::class, 'store'])->name('classes.store');
    Route::delete('/classes/{id}', [SchoolClassController::class, 'destroy'])->name('classes.destroy');

    // 5. Fees & Accounts (Spec Module 3)
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/generate', [FeeController::class, 'create'])->name('create');
        Route::post('/store', [FeeController::class, 'store'])->name('store'); // Bulk Generate
        Route::get('/challan/{id}', [FeeController::class, 'downloadChallan'])->name('challan'); // PDF
        Route::get('/family-challan/{parent_id}/{month}', [FeeController::class, 'downloadFamilyChallan'])->name('family_challan'); // Family PDF
        Route::get('/student/{student}/card', [FeeController::class, 'showFeeCard'])->name('card');
        Route::post('/pay-wallet/{invoice}', [FeeController::class, 'payFromWallet'])->name('pay_wallet');
    });

    // 6. Expenses (Daily Operations)
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::post('/store', [ExpenseController::class, 'store'])->name('store');
        Route::post('/category', [ExpenseController::class, 'storeCategory'])->name('category.store');
    });

    // 7. Attendance (Spec Module 2)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/store', [AttendanceController::class, 'store'])->name('store'); // Manual
        // Note: Biometric API is in routes/api.php
    });

    // 8. Examinations (Spec Module 5)
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::post('/store', [ExamController::class, 'store'])->name('store');
        Route::get('/marks-entry', [ExamController::class, 'marksEntry'])->name('marks_entry');
        Route::post('/save-marks', [ExamController::class, 'saveMarks'])->name('save_marks');

        // Reports (PDFs)
        Route::get('/result-card/{exam_id}/{student_id}', [ExamController::class, 'generateResultCard'])->name('result_card');
        Route::get('/tabulation', [ExamController::class, 'tabulationSheet'])->name('tabulation');
        Route::get('/admit-cards', [ExamController::class, 'generateAdmitCards'])->name('admit_cards');
        Route::get('/combined-result', [ExamController::class, 'combinedResult'])->name('combined_result'); // Mid+Final
    });

    // 9. HR & Payroll (Spec Module 4)
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries');
        Route::post('/salaries/generate', [SalaryController::class, 'generate'])->name('salaries.generate');
        Route::post('/salaries/pay/{id}', [SalaryController::class, 'pay'])->name('salaries.pay');

        Route::get('/loans', [StaffLoanController::class, 'index'])->name('loans');
        Route::post('/loans/store', [StaffLoanController::class, 'store'])->name('loans.store');
    });

    // 10. Library (Books)
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::post('/books', [LibraryController::class, 'store'])->name('books.store');
        Route::post('/issue', [LibraryController::class, 'issueBook'])->name('issue');
        Route::post('/return/{id}', [LibraryController::class, 'returnBook'])->name('return');
    });

    // 11. Inventory (Stock)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/item', [InventoryController::class, 'storeItem'])->name('item.store');
        Route::post('/transaction', [InventoryController::class, 'transaction'])->name('transaction');
    });

    // 12. Transport (Routes)
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/', [TransportRouteController::class, 'index'])->name('index');
        Route::post('/store', [TransportRouteController::class, 'store'])->name('store');
        Route::delete('/{id}', [TransportRouteController::class, 'destroy'])->name('destroy');
    });

    // 13. Communication (Diary, SMS, Notices)
    Route::prefix('communication')->name('communication.')->group(function () {
        Route::get('/diary', [DiaryController::class, 'index'])->name('diary.index');
        Route::post('/diary', [DiaryController::class, 'store'])->name('diary.store');

        Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms.index');
        Route::post('/sms-send', [SmsLogController::class, 'sendCustom'])->name('sms.send');

        Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');

        Route::get('/leaves', [LeaveApplicationController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveApplicationController::class, 'store'])->name('leaves.store'); // Apply
        Route::post('/leaves/{id}', [LeaveApplicationController::class, 'updateStatus'])->name('leaves.update'); // Approve/Reject
    });

    // 14. Academic Extras (TimeTable, Online Class, Certificates)
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::get('/timetable', [TimeTableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable', [TimeTableController::class, 'store'])->name('timetable.store');
        Route::delete('/timetable/{id}', [TimeTableController::class, 'destroy'])->name('timetable.destroy');

        Route::get('/online-classes', [OnlineClassController::class, 'index'])->name('online_classes.index');
        Route::post('/online-classes', [OnlineClassController::class, 'store'])->name('online_classes.store');

        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate'); // PDF

        Route::get('/study-material', [StudyMaterialController::class, 'index'])->name('study_material.index');
        Route::post('/study-material', [StudyMaterialController::class, 'store'])->name('study_material.store');
    });

    // 15. Reports (Financial & Ops)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance_sheet');
        Route::get('/transport', [ReportController::class, 'transportReport'])->name('transport');
        Route::get('/defaulters', [ReportController::class, 'feeDefaulters'])->name('defaulters');
        Route::post('/defaulters/sms', [ReportController::class, 'sendDefaulterSMS'])->name('defaulters.sms');
    });

    // 16. Settings & Configuration
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/campuses', [CampusController::class, 'index'])->name('campuses');
        Route::post('/campuses', [CampusController::class, 'store'])->name('campuses.store');

        Route::get('/general', [SettingController::class, 'index'])->name('general');
        Route::post('/general', [SettingController::class, 'update'])->name('general.update');

        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles');
        Route::post('/roles', [RolePermissionController::class, 'update'])->name('roles.update');
    });

});
Route::post('/pay-cash/{invoice}', [FeeController::class, 'payCash'])->name('pay_cash');

// Add this line inside prefix('fees') group
Route::get('/collect/{id}', function ($id) {
    // Invoice load karo aur Student data ke sath bhejo
    $invoice = \App\Models\FeeInvoice::with(['student.schoolClass', 'feeType'])->findOrFail($id);
    return Inertia::render('Fees/Collect', ['invoice' => $invoice]);
})->name('collect');
Route::post('/fees/pay-cash/{id}', [FeeController::class, 'payCash'])->name('fees.pay_cash');