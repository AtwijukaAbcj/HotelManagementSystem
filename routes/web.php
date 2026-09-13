<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CalanderController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\App;

Route::get('/',[HomeController::class,'index']);


// this will directly open login page in our website
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {

Route::middleware('admin')->group(function () {
    Route::get('/role-management', [RoleController::class, 'index'])->name('admin.roles');
    Route::post('/role-management', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/user-management', [RoleController::class, 'users'])->name('admin.users');
    Route::put('/user-management/{user}/role', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::get('/role-management/permissions', [RoleController::class, 'permissions'])->name('admin.roles.permissions');
    Route::put('/role-management/permissions/{role}', [RoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update');

    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/dashboard', [PropertyController::class, 'dashboard'])->name('properties.dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'send'])->name('notifications.send');

    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
});

Route::middleware('access.control')->prefix('access-control')->name('access-control.')->group(function () {
    Route::get('/', [AccessControlController::class, 'index'])->name('index');
    Route::get('/guest-cards', [AccessControlController::class, 'guestCards'])->middleware('access.control:guest-cards')->name('guest-cards.index');
    Route::get('/guest-cards/create', [AccessControlController::class, 'createGuestCard'])->middleware('access.control:guest-cards')->name('guest-cards.create');
    Route::post('/guest-cards', [AccessControlController::class, 'issueGuestCard'])->middleware('access.control:guest-cards')->name('guest-cards.store');
    Route::post('/cards/{card}/activate', [AccessControlController::class, 'activateCard'])->middleware('access.control:manage')->name('cards.activate');
    Route::post('/cards/{card}/suspend', [AccessControlController::class, 'suspendCard'])->middleware('access.control:manage')->name('cards.suspend');
    Route::post('/cards/{card}/revoke', [AccessControlController::class, 'revokeCard'])->middleware('access.control:manage')->name('cards.revoke');
    Route::post('/cards/{card}/replace', [AccessControlController::class, 'replaceCard'])->middleware('access.control:manage')->name('cards.replace');
    Route::get('/employee-cards', [AccessControlController::class, 'employeeCards'])->middleware('access.control:employee-cards')->name('employee-cards.index');
    Route::get('/employee-cards/create', [AccessControlController::class, 'createEmployeeCard'])->middleware('access.control:employee-cards')->name('employee-cards.create');
    Route::post('/employee-cards', [AccessControlController::class, 'issueEmployeeCard'])->middleware('access.control:employee-cards')->name('employee-cards.store');
    Route::get('/events', [AccessControlController::class, 'events'])->middleware('access.control:events')->name('events.index');
    Route::get('/access-points', [AccessControlController::class, 'accessPoints'])->middleware('access.control:manage')->name('access-points.index');
    Route::post('/access-points', [AccessControlController::class, 'storeAccessPoint'])->middleware('access.control:manage')->name('access-points.store');
    Route::get('/restricted-areas', [AccessControlController::class, 'restrictedAreas'])->middleware('access.control:manage')->name('restricted-areas.index');
    Route::post('/restricted-areas', [AccessControlController::class, 'storeRestrictedArea'])->middleware('access.control:manage')->name('restricted-areas.store');
    Route::get('/reports', [AccessControlController::class, 'reports'])->middleware('access.control:reports')->name('reports.index');
    Route::get('/settings', [AccessControlController::class, 'settings'])->middleware('access.control:manage')->name('settings.index');
    Route::get('/alerts', [AccessControlController::class, 'alerts'])->middleware('access.control:events')->name('alerts.index');
    Route::post('/alerts/{alert}', [AccessControlController::class, 'updateAlert'])->middleware('access.control:manage')->name('alerts.update');
});

Route::get('/redirect',[HomeController::class,'redirect']);


Route::get('/view_profile',[AdminController::class,'view_profile']);
Route::get('/edit_profile',[AdminController::class,'edit_profile']);


Route::get('form/allbooking',[BookingController::class,'allbooking']);
Route::get('form/addbooking',[BookingController::class,'addbooking']);
Route::post('form/savebooking',[BookingController::class,'saveRecord']);
Route::get('/delete_record/{id}',[BookingController::class,'deleterecord'])->name('delete_record');
Route::get('/update_record/{id}', [BookingController::class, 'updaterecord'])->name('update_record');
Route::put('/update_data_confirm/{id}', [BookingController::class, 'update_data_confirm'])->name('update_data_confirm');

Route::post('/update-status/{id}', [BookingController::class, 'updateStatus'])->name('update-status');


Route::get('form/customers',[BookingController::class,'customers']);



Route::get('/all_rooms',[RoomController::class,'allrooms']);
Route::get('/form/allrooms',[RoomController::class,'allrooms']);
Route::get('/edit_rooms',[RoomController::class,'editrooms']);
Route::get('/add_rooms',[RoomController::class,'addrooms']);
Route::post('/save_rooms',[RoomController::class,'saveRoom']);
Route::get('/delete_record1/{id}',[RoomController::class,'deleterecord1']);
Route::get('/room-types', [RoomController::class, 'roomTypes'])->name('room-types');

Route::post('/update-roomstatus/{id}', [RoomController::class, 'updateRoomStatus'])->name('update-roomstatus');
Route::get('/room-status-board', [RoomController::class, 'statusBoard'])->name('room-status-board');
Route::post('/rooms/{id}/status', [RoomController::class, 'setOperationalStatus'])->name('rooms.operational-status');
Route::post('/stays/{id}/check-in', [RoomController::class, 'checkIn'])->name('stays.check-in');
Route::post('/stays/{id}/check-out', [RoomController::class, 'checkOut'])->name('stays.check-out');

Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
Route::get('/guests/create', [GuestController::class, 'create'])->name('guests.create');
Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
Route::get('/inventory/{item}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::put('/inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/stock-movements', [InventoryController::class, 'movements'])->name('stock-movements.index');
Route::get('/purchases', [InventoryController::class, 'purchases'])->name('purchases.index');
Route::get('/purchases/create', [InventoryController::class, 'createPurchase'])->name('purchases.create');
Route::post('/purchases', [InventoryController::class, 'storePurchase'])->name('purchases.store');
Route::post('/purchases/{purchase}/approve', [InventoryController::class, 'approve'])->name('purchases.approve');
Route::post('/purchases/{purchase}/reject', [InventoryController::class, 'reject'])->name('purchases.reject');
Route::get('/purchases/{purchase}/history', [InventoryController::class, 'history'])->name('purchases.history');
Route::get('/purchases/{purchase}/receive', [InventoryController::class, 'receiveForm'])->name('purchases.receive');
Route::post('/purchases/{purchase}/receive', [InventoryController::class, 'receive'])->name('purchases.receive.store');

Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
Route::get('/payments', [FinanceController::class, 'payments'])->name('payments.index');
Route::post('/payments', [FinanceController::class, 'storePayment'])->name('payments.store');
Route::get('/invoices', [FinanceController::class, 'invoices'])->name('invoices.index');
Route::get('/invoices/create', [FinanceController::class, 'createInvoice'])->name('invoices.create');
Route::get('/invoices/{invoice}/print', [FinanceController::class, 'printInvoice'])->name('invoices.print');
Route::post('/invoices', [FinanceController::class, 'storeInvoice'])->name('invoices.store');
Route::get('/expenses', [FinanceController::class, 'expenses'])->name('expenses.index');
Route::get('/expenses/create', [FinanceController::class, 'createExpense'])->name('expenses.create');
Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store');

Route::get('/housekeeping', [OperationsController::class, 'housekeeping'])->name('housekeeping.index');
Route::post('/housekeeping', [OperationsController::class, 'storeHousekeeping'])->name('housekeeping.store');
Route::get('/maintenance', [OperationsController::class, 'maintenance'])->name('maintenance.index');
Route::post('/maintenance', [OperationsController::class, 'storeMaintenance'])->name('maintenance.store');

Route::get('employee/list',[EmployeeController::class,'listemployees']);
Route::get('employee/leave',[EmployeeController::class,'leave']);
Route::get('form/addemployee',[EmployeeController::class,'addemployee']);
Route::get('form/addleave',[EmployeeController::class,'addleave']);
Route::post('form/saveemployee',[EmployeeController::class,'saveEmployee']);
Route::get('/delete_emp/{id}',[EmployeeController::class,'deleteEmp']);
Route::get('/update_emp/{id}', [EmployeeController::class, 'updateemp'])->name('update_emp');
Route::put('/update_emp_confirm/{id}', [EmployeeController::class, 'update_emp_confirm'])->name('update_emp_confirm');



Route::get('/calander',[CalanderController::class,'calander']);
Route::post('/add-event', [CalanderController::class, 'createEvent']);


Route::get('/check',[CheckController::class,'index']);
Route::get('/sheet-report',[CheckController::class,'sheetReport']);
Route::post('/check_store',[CheckController::class,'CheckStore']);


Route::get('/billing',[BillingController::class,'billing']);
Route::post('/savebill',[BillingController::class,'savebill']);
Route::get('/billing_report',[BillingController::class,'billing_report']);
Route::get('/delete_billrecord/{id}',[BillingController::class,'deletebillrecord']);
Route::get('/update_billrecord/{id}', [BillingController::class, 'updatebillrecord'])->name('update_billrecord');
Route::put('/update_billdata_confirm/{id}', [BillingController::class, 'update_billdata_confirm'])->name('update_billdata_confirm');

});

