<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\SolicitudMaterialController;
use App\Http\Controllers\RequisicionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrestamoMaterialController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Ruta de debug ampliada
    Route::get('/debug-role', function() {
        $user = auth()->user();
        return response()->json([
            'user' => $user->name,
            'role' => '"' . $user->role . '"',
            'role_length' => strlen($user->role),
            'canManageInventory' => $user->canManageInventory(),
            'canManageInventoryadmin' => $user->canManageInventoryadmin(),
            'canManageUsers' => $user->canManageUsers(),
            'canApproveRequests' => $user->canApproveRequests(),
            'all_roles_in_db' => \App\Models\User::pluck('role')->unique()->values(),
        ]);
    })->name('debug-role');
    
    // Inventario - Almacén, Auxiliar Almacén, Dirección y TI
    Route::middleware(['inventory.access'])->group(function () {
        Route::resource('inventario', InventarioController::class);
        Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
        Route::resource('clientes', ClienteController::class);



        // Rutas para entradas y salidas


        // Rutas para entradas
        Route::resource('entradas', EntradaController::class)->only(['index', 'create', 'store', 'show']);

        // Nuevas rutas para PDF de entradas
        Route::get('entradas/{entrada}/pdf', [EntradaController::class, 'generatePDF'])->name('entradas.pdf');
        Route::get('entradas/{entrada}/view-pdf', [EntradaController::class, 'viewPDF'])->name('entradas.view-pdf');


        // Rutas para salidas
        Route::resource('salidas', SalidaController::class)->only(['index', 'create', 'store', 'show']);
    
        // Nuevas rutas para PDF
        Route::get('salidas/{salida}/pdf', [SalidaController::class, 'generatePDF'])->name('salidas.pdf');
        Route::get('salidas/{salida}/view-pdf', [SalidaController::class, 'viewPDF'])->name('salidas.view-pdf');
    });
    
    // Solicitudes y requisiciones (todos los roles)
    Route::get('/solicitudes', [SolicitudMaterialController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/create', [SolicitudMaterialController::class, 'create'])->name('solicitudes.create');
    Route::post('/solicitudes', [SolicitudMaterialController::class, 'store'])->name('solicitudes.store');

    // Las rutas específicas deben ir antes de la ruta con parámetro {solicitud}
    Route::get('/solicitudes/buscar-productos', [SolicitudMaterialController::class, 'buscarProductos'])->name('solicitudes.buscar-productos');
    Route::get('/solicitudes/producto/{id}', [SolicitudMaterialController::class, 'obtenerProducto'])->name('solicitudes.obtener-producto');

    Route::get('/solicitudes/{solicitud}', [SolicitudMaterialController::class, 'show'])->name('solicitudes.show');


    // Requisicion

    Route::get('/requisiciones', [RequisicionController::class, 'index'])->name('requisiciones.index');
    Route::get('/requisiciones/create', [RequisicionController::class, 'create'])->name('requisiciones.create');
    Route::post('/requisiciones', [RequisicionController::class, 'store'])->name('requisiciones.store');
    Route::get('/requisiciones/{requisicion}', [RequisicionController::class, 'show'])->name('requisiciones.show');
    
    
    
    // Aprobación de solicitudes y requisiciones - Solo TI y Dirección
    Route::put('solicitudes/{solicitud}/estatus', [SolicitudMaterialController::class, 'updateEstatus'])->name('solicitudes.updateEstatus');
    Route::put('requisiciones/{requisicion}/estatus', [RequisicionController::class, 'updateEstatus'])->name('requisiciones.updateEstatus');



    // Prestamos de materiales
Route::prefix('prestamos')->name('prestamos.')->group(function () {
    // CRUD básico con resource (index, create, store, show)
    Route::resource('/', PrestamoMaterialController::class)->only([
        'index', 'create', 'store', 'show'
    ])->parameters(['' => 'prestamo'])->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'show' => 'show',
    ]);

    // Dashboard de préstamos
    Route::get('/dashboard/overview', [PrestamoMaterialController::class, 'dashboard'])
        ->name('dashboard');

    // Búsqueda de productos (AJAX)
    Route::get('/buscar-productos/search', [PrestamoMaterialController::class, 'buscarProductos'])
        ->name('buscar-productos');
    Route::get('/producto/{id}', [PrestamoMaterialController::class, 'obtenerProducto'])
        ->name('obtener-producto');

    // Aprobar / denegar préstamo (solo aprobadores) - SIN middleware
    Route::put('/{prestamo}/estatus', [PrestamoMaterialController::class, 'updateEstatus'])
        ->name('updateEstatus');

    // Devoluciones (solo inventario) - SIN middleware
    Route::get('/{prestamo}/devolucion', [PrestamoMaterialController::class, 'devolucion'])
        ->name('devolucion');
    Route::post('/{prestamo}/devolucion', [PrestamoMaterialController::class, 'procesarDevolucion'])
        ->name('procesarDevolucion');
});



    // Usuarios - Solo TI y Dirección
    Route::middleware(['user.access'])->group(function () {
        Route::resource('users', UserController::class);
    });





    // ✅ NUEVA RUTA: Para que finanzas apruebe/deniegue
Route::patch('/requisiciones/{requisicion}/estatus-finanzas', [RequisicionController::class, 'updateEstatusFinanzas'])
    ->name('requisiciones.updateEstatusFinanzas')
    ->middleware('auth');





    // parte del inventario
    
   // Rutas de inventario
    Route::prefix('inventario')->name('inventario.')->group(function () {
    Route::get('/', [InventarioController::class, 'index'])->name('index');
    Route::post('/search-ajax', [InventarioController::class, 'searchAjax'])->name('search.ajax');
    Route::get('/create', [InventarioController::class, 'create'])->name('create');
    Route::post('/', [InventarioController::class, 'store'])->name('store');
    Route::get('/{inventario}', [InventarioController::class, 'show'])->name('show');
    Route::get('/{inventario}/edit', [InventarioController::class, 'edit'])->name('edit');
    Route::put('/{inventario}', [InventarioController::class, 'update'])->name('update');
    Route::delete('/{inventario}', [InventarioController::class, 'destroy'])->name('destroy');
    
    // Rutas de exportación
    Route::get('/export/pdf', [InventarioController::class, 'exportPDF'])->name('export.pdf');
    Route::get('/export/excel', [InventarioController::class, 'exportExcel'])->name('export.excel');
    Route::get('/view/pdf', [InventarioController::class, 'viewPDF'])->name('view.pdf');
    
    // Ruta para API si la necesitas
    Route::get('/api/all', [InventarioController::class, 'getAll'])->name('api.all');
});


    // PARTE DEL BOTON DEL EXCEL SOLICITUD DE MATERIAL
    Route::get('/solicitudes/{solicitud}/excel', [SolicitudMaterialController::class, 'exportExcel'])
    ->name('solicitudes.exportExcel');

    // PARTE DEL BOTON DEL EXCEL PRESTAMO DE MATERIAL
    Route::get('/prestamos/{prestamo}/excel', [PrestamoMaterialController::class, 'exportExcel'])
    ->name('prestamos.exportExcel');

    // PARTE DEL BOTON DEL EXCEL REQUISICION DE MATERIAL
    Route::get('/requisiciones/{requisicion}/excel', [RequisicionController::class, 'exportExcel'])
    ->name('requisiciones.exportExcel');

});