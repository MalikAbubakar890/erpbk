<?php

use App\Http\Controllers\BikesController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierInvoicesController;
use App\Http\Controllers\UploadFilesController;
use App\Http\Controllers\VouchersController;
use App\Http\Controllers\VisaexpenseController;
use App\Http\Controllers\SalikController;
use App\Http\Controllers\riderhiringController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
/* Route::any('/register', function () {
  return view('auth.register');
}); */
// Main Page Route

// pages
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

Route::middleware(['auth', 'web'])->group(function () {

  Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home-dashboard');

  Route::resource('items', App\Http\Controllers\ItemsController::class);

  Route::resource('users', App\Http\Controllers\UserController::class);
  Route::any('/user/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
  Route::any('/user/services/{id}', [App\Http\Controllers\UserController::class, 'services'])->name('user_services');
  Route::resource('permissions', App\Http\Controllers\PermissionsController::class);
  Route::resource('roles', App\Http\Controllers\RolesController::class);

  Route::resource('bikes', App\Http\Controllers\BikesController::class);
  Route::any('bikes/assign_rider/{id?}', [BikesController::class, 'assign_rider'])->name('bikes.assign_rider');
  Route::any('bikes/assignrider/{id?}', [BikesController::class, 'assignrider'])->name('bikes.assignrider');
  Route::get('bikes/contract/{id?}', [\App\Http\Controllers\BikesController::class, 'contract'])->name('bike.contract');
  Route::any('bikes/contract_upload/{id?}', [\App\Http\Controllers\BikesController::class, 'contract_upload'])->name('bike_contract_upload');
  Route::get('bikes/delete/{id}', [\App\Http\Controllers\BikesController::class, 'destroy'])->name('bikes.delete');

  Route::resource('customers', App\Http\Controllers\CustomersController::class);
  Route::get('customer/ledger/{id}', [\App\Http\Controllers\CustomersController::class, 'ledger'])->name('customer.ledger');
  Route::get('customer/files/{id}', [\App\Http\Controllers\CustomersController::class, 'files'])->name('customer.files');
  Route::get('customers/delete/{id}', [\App\Http\Controllers\CustomersController::class, 'destroy'])->name('customers.delete');


  Route::resource('rtaFines', App\Http\Controllers\RtaFinesController::class);
  Route::post('rtaFines/store', [\App\Http\Controllers\RtaFinesController::class, 'store'])->name('rtaFines.store');
  Route::get('rtaFines/edit/{id}', [\App\Http\Controllers\RtaFinesController::class, 'edit'])->name('rtaFines.edit');
  Route::post('rtaFines/update', [\App\Http\Controllers\RtaFinesController::class, 'update'])->name('rtaFines.update');
  Route::get('rtaFines/create/{id}', [\App\Http\Controllers\RtaFinesController::class, 'create'])->name('rtaFines.create');
  Route::any('rtaFines/attach_file/{id}', [\App\Http\Controllers\RtaFinesController::class, 'fileUpload'])->name('rtaFines.fileupload');
  Route::get('rtaFines/delete/{id}', [\App\Http\Controllers\RtaFinesController::class, 'destroy'])->name('rtaFines.delete');

  Route::post('rtaFines/accountcreate', [\App\Http\Controllers\RtaFinesController::class, 'accountcreate'])->name('rtaFines.accountcreate');
  Route::post('rtaFines/editaccount', [\App\Http\Controllers\RtaFinesController::class, 'editaccount'])->name('rtaFines.editaccount');
  Route::get('rtaFines/deleteaccount/{id}', [\App\Http\Controllers\RtaFinesController::class, 'deleteaccount'])->name('rtaFines.deleteaccount');
  Route::get('rtaFines/tickets/{id}', [\App\Http\Controllers\RtaFinesController::class, 'tickets'])->name('rtaFines.tickets');
  Route::post('rtaFines/payfine', [\App\Http\Controllers\RtaFinesController::class, 'payfine'])->name('rtaFines.payfine');
  Route::get('rtaFines/viewvoucher/{id}', [\App\Http\Controllers\RtaFinesController::class, 'viewvoucher'])->name('rtaFines.viewvoucher');
  Route::get('rtaFines/getrider/{id}', [\App\Http\Controllers\RtaFinesController::class, 'getrider']);






  Route::resource('VisaExpense', App\Http\Controllers\VisaexpenseController::class);
  Route::post('VisaExpense/store', [\App\Http\Controllers\VisaexpenseController::class, 'store'])->name('VisaExpense.store');
  Route::get('VisaExpense/edit/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'edit'])->name('VisaExpense.edit');
  Route::post('VisaExpense/update', [\App\Http\Controllers\VisaexpenseController::class, 'update'])->name('VisaExpense.update');
  Route::get('VisaExpense/create/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'create'])->name('VisaExpense.create');
  Route::any('VisaExpense/attach_file/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'fileUpload'])->name('VisaExpense.fileupload');
  Route::get('VisaExpense/delete/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'destroy'])->name('VisaExpense.delete');
  Route::get('VisaExpense/installmentPlan/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'installmentPlan'])->name('VisaExpense.installmentPlan');

  // Simple Installment Plan Routes
  Route::get('VisaExpense/createInstallmentPlanForm/{riderId}', [\App\Http\Controllers\VisaexpenseController::class, 'createInstallmentPlanForm'])->name('VisaExpense.createInstallmentPlanForm');
  Route::post('VisaExpense/createInstallmentPlan', [\App\Http\Controllers\VisaexpenseController::class, 'createInstallmentPlan'])->name('VisaExpense.createInstallmentPlan');
  Route::post('VisaExpense/payInstallment', [\App\Http\Controllers\VisaexpenseController::class, 'payInstallment'])->name('VisaExpense.payInstallment');
  Route::post('VisaExpense/updateInstallmentField', [\App\Http\Controllers\VisaexpenseController::class, 'updateInstallmentField'])->name('VisaExpense.updateInstallmentField');
  Route::get('VisaExpense/deleteInstallment/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'deleteInstallment'])->name('VisaExpense.deleteInstallment');
  Route::get('VisaExpense/generateInstallmentInvoice/{riderId}', [\App\Http\Controllers\VisaexpenseController::class, 'generateInstallmentInvoice'])->name('VisaExpense.generateInstallmentInvoice');
  Route::get('VisaExpense/autoMarkInstallments/{riderId?}', [\App\Http\Controllers\VisaexpenseController::class, 'autoMarkInstallmentsAsPaid'])->name('VisaExpense.autoMarkInstallments');
  Route::post('VisaExpense/recalculateInstallments', [\App\Http\Controllers\VisaexpenseController::class, 'recalculateInstallments'])->name('VisaExpense.recalculateInstallments');

  Route::post('accountcreate', [\App\Http\Controllers\VisaexpenseController::class, 'accountcreate'])->name('VisaExpense.accountcreate');
  Route::post('editaccount', [\App\Http\Controllers\VisaexpenseController::class, 'editaccount'])->name('VisaExpense.editaccount');
  Route::get('VisaExpense/deleteaccount/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'deleteaccount'])->name('VisaExpense.deleteaccount');
  Route::get('VisaExpense/generatentries/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'generatentries'])->name('VisaExpense.generatentries');
  Route::post('VisaExpense/payfine', [\App\Http\Controllers\VisaexpenseController::class, 'payfine'])->name('VisaExpense.payfine');
  Route::get('VisaExpense/viewvoucher/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'viewvoucher'])->name('VisaExpense.viewvoucher');
  Route::get('VisaExpense/getrider/{id}', [\App\Http\Controllers\VisaexpenseController::class, 'getrider']);





  Route::resource('sims', App\Http\Controllers\SimsController::class);
  Route::get('sims/delete/{id}', [\App\Http\Controllers\SimsController::class, 'destroy'])->name('sims.delete');
  /* Rider section starts from here */

  Route::resource('riders', App\Http\Controllers\RidersController::class);
  Route::any('riders/job_status/{id?}', [\App\Http\Controllers\RidersController::class, 'job_status'])->name('rider.job_status');


  Route::get('riders/timeline/{id?}', [\App\Http\Controllers\RidersController::class, 'timeline'])->name('rider.timeline');
  Route::get('riders/contract/{id?}', [\App\Http\Controllers\RidersController::class, 'contract'])->name('rider.contract');
  Route::any('riders/contract_upload/{id?}', [\App\Http\Controllers\RidersController::class, 'contract_upload'])->name('rider_contract_upload');
  Route::any('riders/picture_upload/{id?}', [\App\Http\Controllers\RidersController::class, 'picture_upload'])->name('rider_picture_upload');
  Route::any('riders/rider-document/{id}', [\App\Http\Controllers\RidersController::class, 'document'])->name('rider.document');
  Route::get('rider/updateRider', [\App\Http\Controllers\RidersController::class, 'updateRider'])->name('rider.updateRider');
  Route::get('rider/delete/{id}', [\App\Http\Controllers\RidersController::class, 'destroy'])->name('rider.delete');
  Route::get('riders/ledger/{id}', [\App\Http\Controllers\RidersController::class, 'ledger'])->name('rider.ledger');
  Route::get('riders/attendance/{id}', [\App\Http\Controllers\RidersController::class, 'attendance'])->name('rider.attendance');
  Route::get('riders/activities/{id}', [\App\Http\Controllers\RidersController::class, 'activities'])->name('rider.activities');
  Route::get('riders/invoices/{id}', [\App\Http\Controllers\RidersController::class, 'invoices'])->name('rider.invoices');
  Route::any('riders/sendemail/{id}', [\App\Http\Controllers\RidersController::class, 'sendEmail'])->name('rider.sendemail');
  Route::get('riders/emails/{id}', [\App\Http\Controllers\RidersController::class, 'emails'])->name('rider.emails');
  Route::get('rider/exportRiders', [\App\Http\Controllers\RidersController::class, 'exportRiders'])->name('rider.exportRiders');
  Route::get('rider/exportCustomizableRiders', [\App\Http\Controllers\RidersController::class, 'exportCustomizableRiders'])->name('rider.exportCustomizableRiders');

  // User Table Settings Routes
  Route::prefix('user-table-settings')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserTableSettingsController::class, 'getSettings'])->name('user-table-settings.get');
    Route::post('/', [\App\Http\Controllers\UserTableSettingsController::class, 'saveSettings'])->name('user-table-settings.save');
    Route::delete('/', [\App\Http\Controllers\UserTableSettingsController::class, 'resetSettings'])->name('user-table-settings.reset');
    Route::get('/all', [\App\Http\Controllers\UserTableSettingsController::class, 'getAllSettings'])->name('user-table-settings.all');
  });
  Route::get('riders/files/{id}', [\App\Http\Controllers\RidersController::class, 'files'])->name('rider.files');
  Route::get('riders/items/{id}', [\App\Http\Controllers\RidersController::class, 'items'])->name('rider.items');
  Route::get('riders/additems/{id}', [\App\Http\Controllers\RidersController::class, 'additems'])->name('riders.additems');
  Route::get('riders/createitems/{id}', [\App\Http\Controllers\RidersController::class, 'createitems'])->name('riders.createitems');
  Route::get('riders/visaloan/{id}', [\App\Http\Controllers\RidersController::class, 'visaloan'])->name('riders.visaloan');
  Route::get('riders/advanceloan/{id}', [\App\Http\Controllers\RidersController::class, 'advanceloan'])->name('riders.advanceloan');
  Route::post('riders/storevisaloan', [\App\Http\Controllers\RidersController::class, 'storevisaloan'])->name('riders.storevisaloan');
  Route::post('riders/storeadvanceloan', [\App\Http\Controllers\RidersController::class, 'storeadvanceloan'])->name('riders.storeadvanceloan');
  Route::post('riders/update-section/{id}', [\App\Http\Controllers\RidersController::class, 'updateSection'])->name('riders.updateSection');
  Route::post('riders/toggle-absconder/{id}', [\App\Http\Controllers\RidersController::class, 'toggleAbsconder'])->name('riders.toggleAbsconder');
  Route::post('riders/toggle-flowup/{id}', [\App\Http\Controllers\RidersController::class, 'toggleFlowup'])->name('riders.toggleFlowup');



  Route::resource('riderleads', App\Http\Controllers\riderhiringController::class);














  Route::get('riders/file-manager', function () {
    return view('riders.file-manager');
  })->name('rider.file-manager');

  Route::resource('riderEmails', App\Http\Controllers\RiderEmailsController::class);


  Route::resource('riderInvoices', App\Http\Controllers\RiderInvoicesController::class);
  Route::any('rider/invoice-import', [\App\Http\Controllers\RiderInvoicesController::class, 'import'])->name('rider.invoice_import');
  Route::get('search_item_price/{RID}/{itemID}', [\App\Http\Controllers\ItemsController::class, 'search_item_price']);
  Route::get('riderInvoices/delete/{id}', [\App\Http\Controllers\RiderInvoicesController::class, 'destroy'])->name('riderInvoices.delete');

  Route::resource('riderAttendances', App\Http\Controllers\RiderAttendanceController::class);
  Route::any('rider/attendance-import', [\App\Http\Controllers\RiderAttendanceController::class, 'import'])->name('rider.attendance_import');

  Route::resource('riderActivities', App\Http\Controllers\RiderActivitiesController::class);
  Route::any('rider/activities-import', [\App\Http\Controllers\RiderActivitiesController::class, 'import'])->name('rider.activities_import');

  /* Rider section end here */


  Route::resource('riderActivities', App\Http\Controllers\RiderActivitiesController::class);

  Route::resource('supplier_invoices', SupplierInvoicesController::class);
  Route::get('supplierInvoices/delete/{id}', [\App\Http\Controllers\SupplierInvoicesController::class, 'destroy'])->name('supplierInvoices.delete');

  Route::get('/item/{id}/price', [ItemsController::class, 'getPrice'])->name('item.price');

  Route::get('/get-item-price/{id}', [ItemsController::class, 'getItemPrice'])->name('item.getPrice');
  Route::get('items/delete/{id}', [\App\Http\Controllers\ItemsController::class, 'destroy'])->name('items.delete');

  Route::resource('files', FilesController::class);
  Route::resource('files', FilesController::class);

  Route::resource('vendors', App\Http\Controllers\VendorsController::class);

  Route::get('vendors/delete/{id}', [\App\Http\Controllers\VendorsController::class, 'destroy'])->name('vendors.delete');

  Route::resource('bikeHistories', App\Http\Controllers\BikeHistoryController::class);

  Route::resource('leasingCompanies', App\Http\Controllers\LeasingCompaniesController::class);
  Route::get('leasingCompanies/delete/{id}', [\App\Http\Controllers\LeasingCompaniesController::class, 'destroy'])->name('leasingCompanies.delete');
  Route::resource('garages', App\Http\Controllers\GaragesController::class);
  Route::get('garages/delete/{id}', [\App\Http\Controllers\GaragesController::class, 'destroy'])->name('garages.delete');
  Route::resource('banks', App\Http\Controllers\BanksController::class);
  Route::get('bank/ledger/{id}', [\App\Http\Controllers\BanksController::class, 'ledger'])->name('bank.ledger');
  Route::get('bank/files/{id}', [\App\Http\Controllers\BanksController::class, 'files'])->name('bank.files');
  Route::get('bank/delete/{id}', [\App\Http\Controllers\BanksController::class, 'destroy'])->name('bank.delete');
  Route::get('banks/receipts', [\App\Http\Controllers\BanksController::class, 'receipts'])->name('banks.receipts');
  Route::get('banks/payments', [\App\Http\Controllers\BanksController::class, 'payments'])->name('banks.payments');
  Route::resource('vouchers', \App\Http\Controllers\VouchersController::class);
  Route::any('voucher/import', [\App\Http\Controllers\VouchersController::class, 'import'])->name('voucher.import');
  Route::get('get_invoice_balance', [\App\Http\Controllers\VouchersController::class, 'GetInvoiceBalance'])->name('get_invoice_balance');
  Route::get('fetch_invoices/{id}/{vt}', [\App\Http\Controllers\VouchersController::class, 'fetch_invoices']);
  /*   Route::any('attach_file/{id}', 'VouchersController@fileUpload'); */
  Route::any('voucher/attach_file/{id}', [\App\Http\Controllers\VouchersController::class, 'fileUpload'])->name('voucher.fileupload');


  Route::prefix('settings')->group(function () {

    Route::any('/company', [HomeController::class, 'settings'])->name('settings');
    Route::resource('departments', App\Http\Controllers\DepartmentsController::class);
    Route::resource('dropdowns', App\Http\Controllers\DropdownsController::class);
  });
  Route::prefix('reports')->group(function () {
    Route::get('/rider_report', [ReportController::class, 'rider_report'])->name('reports.rider_report');
    Route::post('/rider_report_data', [ReportController::class, 'rider_report_data'])->name('reports.rider_report_data');
  });



  Route::get('/itmeslist', function () {
    return App\Helpers\General::dropdownitems();
  });

  Route::prefix('accounts')->group(function () {

    Route::resource('accounts', App\Http\Controllers\AccountsController::class);
    Route::get('tree', [\App\Http\Controllers\AccountsController::class, 'tree'])->name('accounts.tree');

    Route::get('/ledgerreport', [LedgerController::class, 'ledger'])->name('accounts.ledgerreport');
    Route::get('/ledger', [LedgerController::class, 'index'])->name('accounts.ledger');
    Route::get('/ledger/data', [LedgerController::class, 'getLedgerData'])->name('ledger.data');
    Route::post('accounts/{id}/toggle-lock', [App\Http\Controllers\AccountsController::class, 'toggleLock'])->name('accounts.toggleLock');
  });

  // Activity Logs Routes
  Route::prefix('activity-logs')->group(function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/api/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
  });
});
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
  \UniSharp\LaravelFilemanager\Lfm::routes();
});
/* Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
  Lfm::routes();
}); */

Route::get('/storage/{folder}/{filename}', [FileController::class, 'show'])->where('filename', '.*');
Route::get('/storage2/{folder}/{filename}', [FileController::class, 'root'])->where('filename', '.*');


Route::get('/artisan-cache', function () {
  Artisan::call('cache:clear');
  return 'cache cleared';
});
Route::get('/artisan-route', function () {
  Artisan::call('route:clear');
  return 'ruote cleared';
});

Route::get('/artisan-optimize', function () {
  Artisan::call('optimize');
  return 'optimized';
});
Route::get('/artisan-optimize-clear', function () {
  Artisan::call('optimize:clear');
  return 'optimized';
});
Route::get('/artisan-storage-link', function () {
  Artisan::call('storage:link');
  return 'storage link';
});

Route::get('/artisan-storage-unlink', function () {
  Artisan::call('storage:unlink');
  return 'storage unlink';
});

/* Route::resource('calculations', App\Http\Controllers\CalculationsController::class)
    ->names([
        'index' => 'calculations.index',
        'store' => 'calculations.store',  
        'show' => 'calculations.show',
        'update' => 'calculations.update',
        'destroy' => 'calculations.destroy',
        'create' => 'calculations.create',
        'edit' => 'calculations.edit'
    ]); */


/* Settings section end here */
/* Settings section start here */
Route::prefix('settings')->group(function () {

  Route::any('/company', [HomeController::class, 'settings'])->name('settings');
  Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
  Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.updateLogo');
  Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
  Route::post('settings/update-favicon', [SettingsController::class, 'updateFavicon'])->name('settings.updateFavicon');
  Route::resource('departments', App\Http\Controllers\DepartmentsController::class);
  Route::resource('dropdowns', App\Http\Controllers\DropdownsController::class);
});


/* Suppliers section start here */
Route::middleware(['auth'])->group(function () {
  // Suppliers
  Route::resource('suppliers', SupplierController::class);
  Route::get('/suppliers/show/{id}', [SupplierController::class, 'show'])->name('suppliers.show');
  Route::get('/suppliers/ledger/{id}', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
  Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');
  Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
  Route::get('suppliers/delete/{id}', [\App\Http\Controllers\GaragesController::class, 'destroy'])->name('suppliers.delete');

  // Suppliers
  Route::resource('suppliers', SupplierController::class);
  Route::get('suppliers/datatable', [SupplierController::class, 'datatable'])->name('suppliers.datatable');
  Route::get('suppliers/document/{id}', [SupplierController::class, 'document'])->name('suppliers.document');
  Route::get('suppliers/files/{id}', [SupplierController::class, 'files'])->name('suppliers.files');

  // Supplier invoices
  Route::resource('supplierInvoices', SupplierInvoicesController::class);
  Route::any('/supplier_invoices/import', [SupplierInvoicesController::class, 'import'])->name('supplier_invoices.import');
  Route::post('/supplier/invoice/import', [SupplierInvoicesController::class, 'import'])->name('supplier.invoice_import');
  Route::get('/supplier/ledger', [SupplierInvoicesController::class, 'ledger'])->name('supplier.ledger');
  Route::post('/supplier_invoices/send-email/{id}', [SupplierInvoicesController::class, 'sendEmail'])->name('supplier_invoices.send_email');
  Route::put('/supplierInvoices/{id}', [SupplierInvoicesController::class, 'update'])->name('supplierInvoices.update');
  // Route::get('/supplier_invoices/{id}',[SupplierInvoicesController::class, 'edit'])->name('supplier_invoices.edit');
  Route::get('supplierInvoices/edit/{id}', [\App\Http\Controllers\SupplierInvoicesController::class, 'edit'])->name('supplierInvoices.edit');
  Route::post('/supplierInvoices/{id}', [SupplierInvoicesController::class, 'update'])->name('supplierInvoices.update');
  Route::get('/supplier_invoices/{id}', [SupplierInvoicesController::class, 'show'])->name('supplierInvoices.show');
  Route::get('/supplierInvoices/create', [SupplierInvoicesController::class, 'create'])->name('supplierInvoices.create');
  Route::post('supplierInvoices', [SupplierInvoicesController::class, 'store'])->name('supplierInvoices.store');
});

/* Suppliers section end here */
Route::middleware('auth')->group(function () {
  Route::resource('upload_files', UploadFilesController::class);
  Route::get('/upload_files', [UploadFilesController::class, 'index'])->name('upload_files.index');
  Route::get('/upload_files/create', [UploadFilesController::class, 'create'])->name('upload_files.create');
  Route::post('/upload_files', [UploadFilesController::class, 'store'])->name('upload_files.store');
  Route::get('/upload_files/{id}', [UploadFilesController::class, 'show'])->name('upload_files.show');
  Route::get('/upload_files/{id}/edit', [UploadFilesController::class, 'edit'])->name('upload_files.edit');
  Route::put('/upload_files/{id}', [UploadFilesController::class, 'update'])->name('upload_files.update');
  Route::delete('/upload_files/{id}', [UploadFilesController::class, 'destroy'])->name('upload_files.destroy');
});

Route::resource('payments', App\Http\Controllers\PaymentController::class);
Route::resource('receipts', App\Http\Controllers\ReceiptController::class);
Route::get('payments/byparent/{id}', [\App\Http\Controllers\PaymentController::class, 'byparent']);
Route::get('payments/headbytype/{id}', [\App\Http\Controllers\PaymentController::class, 'headbytype']);
Route::get('receipts/byparent/{id}', [\App\Http\Controllers\ReceiptController::class, 'byparent']);
Route::get('receipts/headbytype/{id}', [\App\Http\Controllers\ReceiptController::class, 'headbytype']);



// Specific Salik routes (must come before resource route)
Route::get('salik/missing-records', [\App\Http\Controllers\SalikController::class, 'showMissingRecords'])->name('salik.missing.records');
Route::get('salik/export-missing-records', [\App\Http\Controllers\SalikController::class, 'exportMissingRecords'])->name('salik.export.missing.records');
Route::post('salik/analyze-excel', [\App\Http\Controllers\SalikController::class, 'analyzeExcelFile'])->name('salik.analyze.excel');
Route::post('salik/clear-failed-imports', [\App\Http\Controllers\SalikController::class, 'clearFailedImports'])->name('salik.clear.failed.imports');
Route::get('salik/import/{salik_account_id}', [\App\Http\Controllers\SalikController::class, 'importForm'])->name('salik.import.form');
Route::post('salik/import', [\App\Http\Controllers\SalikController::class, 'import'])->name('salik.import');
Route::post('salik/test-import', [\App\Http\Controllers\SalikController::class, 'testImport'])->name('salik.test.import');

// Salik resource routes
Route::resource('salik', App\Http\Controllers\SalikController::class);
Route::post('salik/store', [\App\Http\Controllers\SalikController::class, 'store'])->name('salik.store');
Route::get('salik/edit/{id}', [\App\Http\Controllers\SalikController::class, 'edit'])->name('salik.edit');
Route::post('/salik/{id}/update', [SalikController::class, 'update'])->name('salik.update');
Route::get('salik/create/{id}', [\App\Http\Controllers\SalikController::class, 'create'])->name('salik.create');
Route::any('salik/attach_file/{id}', [\App\Http\Controllers\SalikController::class, 'fileUpload'])->name('salik.fileupload');
Route::get('salik/delete/{id}', [\App\Http\Controllers\SalikController::class, 'destroy'])->name('salik.delete');

Route::post('salik/accountcreate', [\App\Http\Controllers\SalikController::class, 'accountcreate'])->name('salik.accountcreate');
Route::post('salik/editaccount', [\App\Http\Controllers\SalikController::class, 'editaccount'])->name('salik.editaccount');
Route::get('salik/deleteaccount/{id}', [\App\Http\Controllers\SalikController::class, 'deleteaccount'])->name('salik.deleteaccount');
Route::get('salik/tickets/{id}', [\App\Http\Controllers\SalikController::class, 'tickets'])->name('salik.tickets');
Route::get('salik/viewvoucher/{id}', [\App\Http\Controllers\SalikController::class, 'viewvoucher'])->name('salik.viewvoucher');
Route::post('salik/getriderbybikedate', [SalikController::class, 'getriderbybikedate'])->name('salik.getriderbybikedate');

Route::get('penalties/index/{rider_id?}', [\App\Http\Controllers\PenaltiesController::class, 'index'])->name('penalties.index');
Route::resource('penalties', App\Http\Controllers\PenaltiesController::class, ['except' => ['index']]);
Route::get('penalties/delete/{id}', [\App\Http\Controllers\PenaltiesController::class, 'destroy'])->name('penalties.delete');
Route::get('penalties/rider/{rider_id}', [\App\Http\Controllers\PenaltiesController::class, 'riderPenalties'])->name('penalties.rider');
Route::get('penalties/viewvoucher/{id}', [\App\Http\Controllers\PenaltiesController::class, 'viewVoucher'])->name('penalties.viewvoucher');
Route::post('penalties/mark-paid/{id}', [\App\Http\Controllers\PenaltiesController::class, 'markAsPaid'])->name('penalties.markpaid');
Route::get('penalties/statistics', [\App\Http\Controllers\PenaltiesController::class, 'getStatistics'])->name('penalties.statistics');
Route::post('penalties/import', [\App\Http\Controllers\PenaltiesController::class, 'import'])->name('penalties.import');
Route::get('penalties/viewvoucher/{id}', [\App\Http\Controllers\PenaltiesController::class, 'viewvoucher'])->name('penalties.viewvoucher');
Route::get('penalties/getrider/{id}', [\App\Http\Controllers\PenaltiesController::class, 'getrider']);


Route::get('cod/index/{rider_id?}', [\App\Http\Controllers\CodController::class, 'index'])->name('cod.index');
Route::resource('cod', App\Http\Controllers\CodController::class, ['except' => ['index']]);
Route::get('cod/delete/{id}', [\App\Http\Controllers\CodController::class, 'destroy'])->name('cod.delete');
Route::get('cod/rider/{rider_id}', [\App\Http\Controllers\CodController::class, 'riderCod'])->name('cod.rider');
Route::get('cod/viewvoucher/{id}', [\App\Http\Controllers\CodController::class, 'viewVoucher'])->name('cod.viewvoucher');
Route::post('cod/mark-paid/{id}', [\App\Http\Controllers\CodController::class, 'markAsPaid'])->name('cod.markpaid');
Route::get('cod/statistics', [\App\Http\Controllers\CodController::class, 'getStatistics'])->name('cod.statistics');
