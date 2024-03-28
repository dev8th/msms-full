<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\WareController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\WebFormController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DiskonController;
use Illuminate\Support\Facades\Auth;

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
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/auth', [LoginController::class, 'auth']);
Route::get('/login', [LoginController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () {

    Route::controller(AppController::class)->group(function () {
        Route::get('/', 'index')->name('app.index');
        Route::get('/check/getcustlist', 'getCustList')->name('app.getcustlist');
        Route::get('/check/getcustlistdisc', 'getCustListDisc')->name('app.getcustlistdisc');
        Route::get('/check/gettotaldisc', 'getTotalDisc')->name('app.gettotaldisc');
        Route::get('/check/getservlist', 'getServList')->name('app.getservlist');
        Route::get('/check/getservdata', 'getServData')->name('app.getservdata');
        Route::get('/check/getcustdata', 'getCustData')->name('app.getcustdata');
        Route::get('/check/getuomlist', 'getUomList')->name('app.getuomlist');
        Route::get('/check/getshipnum', 'getShipNum')->name('app.getshipnum');
        Route::get('/check/getshipid', 'getShipId')->name('app.getshipid');
        Route::get('/check/checkphoneandemail', 'checkPhoneAndEmail')->name('app.checkphoneandemail');
        Route::get('/check/invoicelinkdoku', 'invoiceLinkDoku')->name('app.checkinvoicelinkdoku');
        Route::get('/check/getshipnumfrominvoice', 'getShipNumFromInvoice')->name('app.getshipnumfrominvoice');
        Route::post('/dashboard', 'dashboard')->name('dashboard.index');
        Route::get('/load/dashboard', 'loadDashboard')->name('app.loadDashboard');
        Route::get('/import/example/invoice', 'importExampleInvoice')->name('app.importExampleInvoice');
        Route::get('/import/example/shipping/{id}', 'importExampleShipping')->name('app.importExampleShipping');
    });

    Route::controller(DiskonController::class)->group(function(){
        Route::post('/diskonlist', 'index')->name('diskon.index');
        Route::get('/diskonlist/table/{custTypeId}', 'table')->name('diskon.table');
    });

    Route::controller(ShipmentController::class)->group(function () {
        Route::post('/newship', 'index')->name('newship.index');
        Route::post('/newship/buat', 'buatOrder')->name('newship.buatorder');
        Route::get('/shiplist/table/order/{custTypeId}', 'tableOrder')->name('shiplist.tableorder');
        Route::post('/shiplist/hapus/order', 'hapusOrder')->name('shiplist.hapusorder');

        Route::post('/shiplist', 'shiplist')->name('shiplist.index');

        Route::post('/shiplist/buat/invoice', 'buatInvoice')->name('shiplist.buatinvoice');
        Route::get('/shiplist/table/invoice/{custTypeId}', 'tableInvoice')->name('shiplist.tableinvoice');
        Route::get('/shiplist/edit/invoice', 'editInvoice')->name('shiplist.editinvoice');
        Route::get('/shiplist/edit/detilcons', 'editDetilCons')->name('shiplist.editdetilcons');
        Route::get('/shiplist/edit/invoice/getdata', 'editInvoiceGetData')->name('shiplist.editinvoicegetdata');
        Route::post('/shiplist/hapus/invoice', 'hapusInvoice')->name('shiplist.hapusinvoice');

        Route::post('/shiplist/buat/resi', 'buatResi')->name('shiplist.buatresi');
        Route::post('/shiplist/edit/resi', 'editResi')->name('shiplist.editresi');
        Route::get('/shiplist/edit/order/status', 'editOrderStatus')->name('shiplist.editorderstatus');
        Route::get('/shiplist/table/tracking/{custTypeId}', 'tableTracking')->name('shiplist.tabletracking');
        Route::post('/check/getdatalist', 'getDataList')->name('shiplist.getdatalist');
        
        Route::get('/printout/invoice/{id}', 'printOutInvoice')->name('shiplist.printoutinvoice');
        Route::get('/printout/invoice/editable/{id}', 'printOutInvoiceEditable')->name('shiplist.printoutinvoiceeditable');
        Route::get('/printout/resi/{id}', 'printOutResi')->name('shiplist.printoutresi');
        Route::post('/check/lastshippingid', 'lastShippingId')->name('shiplist.lastshippingid');
        Route::get('/foreignrate', 'foreignRate')->name('shiplist.foreignrate');
        Route::post('/import', 'import')->name('shiplist.import');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::post('/custlist', 'index')->name('customer.index');
        Route::post('/custlist/submitref', 'submitref')->name('customer.submitref');
        Route::post('/custlist/tambah', 'tambah')->name('customer.tambah');
        Route::post('/custlist/edit', 'edit')->name('customer.edit');
        Route::post('/custlist/hapus', 'hapus')->name('customer.hapus');
        Route::get('/custlist/table/{custTypeId}', 'table')->name('customer.table');
        Route::get('/custlist/export/{custType}', 'export')->name('customer.export');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::post('/servlist', 'index')->name('service.index');
        Route::post('/servlist/tambah', 'tambah')->name('service.tambah');
        Route::post('/servlist/edit', 'edit')->name('service.edit');
        Route::post('/servlist/hapus', 'hapus')->name('service.hapus');
        Route::get('/servlist/table', 'table')->name('service.table');
        Route::get('/servlist/namechecking', 'nameChecking')->name('warehouse.namechecking');
        Route::get('/servlist/export', 'export')->name('service.export');
    });

    Route::controller(WareController::class)->group(function () {
        Route::post('/warehouse', 'index')->name('warehouse.index');
        Route::post('/warehouse/tambah', 'tambah')->name('warehouse.tambah');
        Route::post('/warehouse/edit', 'edit')->name('warehouse.edit');
        Route::post('/warehouse/hapus', 'hapus')->name('warehouse.hapus');
        Route::get('/warehouse/table', 'table')->name('warehouse.table');
        Route::get('/warehouse/inputchecking', 'inputChecking')->name('warehouse.inputchecking');
        Route::get('/warehouse/export', 'export')->name('warehouse.export');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::post('/profile', 'index')->name('profile.index');
        Route::get('/profile/gantipass', 'gantipass')->name('profile.gantipass');
    });

    Route::controller(BackupController::class)->group(function () {
        Route::post('/backup', 'index')->name('backup.index');
        Route::get('/backup/export', 'export')->name('backup.export');
        Route::get('/backup/exportops', 'exportops')->name('backup.exportops');
    });

    Route::controller(HistoryController::class)->group(function () {
        Route::post('/history', 'index')->name('history.index');
        Route::get('/history/list/{id}', 'list')->name('history.list');
        Route::get('/history/table', 'table')->name('history.table');
    });

});

Route::get('/check/session', [AppController::class, 'checkSes'])->name('app.checkses');
Route::get('/encrypt/{pass}', [AppController::class, 'encrypt'])->name('app.encrypt');
Route::get('/p/{invoiceLink}', [ShipmentController::class, 'printOutInvoiceForCustomer'])->domain('print.' . env('APP_URL'))->name('shiplist.printoutinvoice');
Route::controller(WebFormController::class)->group(function () {
    Route::get('/webform', 'index')->name('webform.index');
    Route::get('/webform/input', 'input')->name('webform.input');
});

Route::get('/test', [TestController::class, 'index']);
Route::post('/testing', [TestController::class, 'testing']);