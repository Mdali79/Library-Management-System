<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\AutherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');
Route::post('/', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.store');

// OTP Verification routes
Route::get('/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'showOtpVerification'])->name('verify.otp');
Route::post('/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/resend-otp', [App\Http\Controllers\Auth\RegisterController::class, 'resendOtp'])->name('resend.otp');

// Old verification route (kept for backward compatibility)
Route::get('/verify', [App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('verify');
Route::post('/verify', [App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('verify.store');


Route::middleware(['auth', 'verified.user'])->group(function () {
    Route::get('change-password', [dashboardController::class, 'change_password_view'])->name('change_password_view');
    Route::post('change-password', [dashboardController::class, 'change_password'])->name('change_password');
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');

    // author CRUD
    Route::get('/authors', [AutherController::class, 'index'])->name('authors');
    Route::get('/authors/create', [AutherController::class, 'create'])->name('authors.create');
    Route::get('/authors/edit/{auther}', [AutherController::class, 'edit'])->name('authors.edit');
    Route::post('/authors/update/{id}', [AutherController::class, 'update'])->name('authors.update');
    Route::post('/authors/delete/{id}', [AutherController::class, 'destroy'])->name('authors.destroy');
    Route::post('/authors/create', [AutherController::class, 'store'])->name('authors.store');

    // publisher crud
    Route::get('/publishers', [PublisherController::class, 'index'])->name('publishers');
    Route::get('/publisher/create', [PublisherController::class, 'create'])->name('publisher.create');
    Route::get('/publisher/edit/{publisher}', [PublisherController::class, 'edit'])->name('publisher.edit');
    Route::post('/publisher/update/{id}', [PublisherController::class, 'update'])->name('publisher.update');
    Route::post('/publisher/delete/{id}', [PublisherController::class, 'destroy'])->name('publisher.destroy');
    Route::post('/publisher/create', [PublisherController::class, 'store'])->name('publisher.store');

    // Category CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::get('/category/edit/{category}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::post('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::post('/category/create', [CategoryController::class, 'store'])->name('category.store');




    // books CRUD
    Route::get('/books', [BookController::class, 'index'])->name('books');
    Route::get('/book/suggestions', [BookController::class, 'getSuggestions'])->name('book.suggestions');
    Route::get('/book/create', [BookController::class, 'create'])->name('book.create');
    Route::get('/book/edit/{book}', [BookController::class, 'edit'])->name('book.edit');
    Route::post('/book/update/{id}', [BookController::class, 'update'])->name('book.update');
    Route::post('/book/delete/{id}', [BookController::class, 'destroy'])->name('book.destroy');
    Route::post('/book/create', [BookController::class, 'store'])->name('book.store');

    // students CRUD
    Route::get('/students', [StudentController::class, 'index'])->name('students');
    Route::get('/student/create', [StudentController::class, 'create'])->name('student.create');
    Route::get('/student/edit/{student}', [StudentController::class, 'edit'])->name('student.edit');
    Route::post('/student/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::post('/student/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
    Route::post('/student/create', [StudentController::class, 'store'])->name('student.store');
    Route::get('/student/show/{id}', [StudentController::class, 'show'])->name('student.show');



    Route::get('/book_issue', [BookIssueController::class, 'index'])->name('book_issued');
    Route::get('/book-issue/create', [BookIssueController::class, 'create'])->name('book_issue.create');
    Route::get('/book-issue/edit/{id}', [BookIssueController::class, 'edit'])->name('book_issue.edit');
    Route::post('/book-issue/update/{id}', [BookIssueController::class, 'update'])->name('book_issue.update');
    Route::post('/book-issue/delete/{id}', [BookIssueController::class, 'destroy'])->name('book_issue.destroy');
    Route::post('/book-issue/create', [BookIssueController::class, 'store'])->name('book_issue.store');

    // Pending requests and approval (Librarian only)
    Route::get('/book-issue/pending', [BookIssueController::class, 'pendingRequests'])->name('book_issue.pending');
    Route::post('/book-issue/approve/{id}', [BookIssueController::class, 'approveRequest'])->name('book_issue.approve');
    Route::post('/book-issue/reject/{id}', [BookIssueController::class, 'rejectRequest'])->name('book_issue.reject');

    // Reports routes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/Date-Wise', [ReportsController::class, 'date_wise'])->name('reports.date_wise');
    Route::post('/reports/Date-Wise', [ReportsController::class, 'generate_date_wise_report'])->name('reports.date_wise_generate');
    Route::get('/reports/monthly-Wise', [ReportsController::class, 'month_wise'])->name('reports.month_wise');
    Route::post('/reports/monthly-Wise', [ReportsController::class, 'generate_month_wise_report'])->name('reports.month_wise_generate');
    Route::get('/reports/not-returned', [ReportsController::class, 'not_returned'])->name('reports.not_returned');
    Route::get('/reports/book-report', [ReportsController::class, 'bookReport'])->name('reports.book');
    Route::get('/reports/member-report', [ReportsController::class, 'memberReport'])->name('reports.member');
    Route::get('/reports/return-report', [ReportsController::class, 'returnReport'])->name('reports.return');
    Route::get('/reports/overdue-report', [ReportsController::class, 'overdueReport'])->name('reports.overdue');
    Route::get('/reports/fine-collection-report', [ReportsController::class, 'fineCollectionReport'])->name('reports.fine_collection');
    Route::get('/reports/category-statistics', [ReportsController::class, 'categoryStatistics'])->name('reports.category_statistics');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // User Registration Approval routes (Admin/Librarian only)
    Route::get('/registrations/pending', [App\Http\Controllers\UserRegistrationController::class, 'index'])->name('registrations.pending');
    Route::post('/registrations/approve/{id}', [App\Http\Controllers\UserRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/reject/{id}', [App\Http\Controllers\UserRegistrationController::class, 'reject'])->name('registrations.reject');

    // Fine Management routes
    Route::get('/fines', [App\Http\Controllers\FineController::class, 'index'])->name('fines.index');
    Route::get('/fines/pending/{studentId}', [App\Http\Controllers\FineController::class, 'pending'])->name('fines.pending');
    Route::get('/fines/history', [App\Http\Controllers\FineController::class, 'history'])->name('fines.history');
    Route::post('/fines/pay/{id}', [App\Http\Controllers\FineController::class, 'pay'])->name('fines.pay');
    Route::post('/fines/waive/{id}', [App\Http\Controllers\FineController::class, 'waive'])->name('fines.waive');
    Route::post('/fines/calculate-overdue', [App\Http\Controllers\FineController::class, 'calculateOverdueFines'])->name('fines.calculate_overdue');

    // Book Reservation routes
    Route::get('/reservations', [App\Http\Controllers\BookReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/reserve', [App\Http\Controllers\BookReservationController::class, 'reserve'])->name('reservations.reserve');
    Route::post('/reservations/cancel/{id}', [App\Http\Controllers\BookReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/notify/{bookId}', [App\Http\Controllers\BookReservationController::class, 'notifyAvailable'])->name('reservations.notify');
    Route::post('/reservations/mark-issued/{id}', [App\Http\Controllers\BookReservationController::class, 'markAsIssued'])->name('reservations.mark_issued');

    // Chatbot routes (Student only)
    Route::get('/chatbot', [App\Http\Controllers\ChatBotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/chat', [App\Http\Controllers\ChatBotController::class, 'chat'])->name('chatbot.chat');

    // Book Reading routes (Student only)
    Route::get('/reading', [App\Http\Controllers\BookReadingController::class, 'index'])->name('reading.index');
    Route::get('/reading/{book}', [App\Http\Controllers\BookReadingController::class, 'show'])->name('reading.show');
    Route::get('/reading/{book}/pdf', [App\Http\Controllers\BookReadingController::class, 'getPdfPreview'])->name('reading.pdf');

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
