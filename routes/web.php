<?php

use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BajaColaboradorController;
use App\Http\Controllers\CatalogoRhController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\PrestamoMaterialController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RequisicionController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\SolicitudMaterialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValeppController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas generales
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

        Route::view('/vacaciones', 'rh.vacaciones.index')
        ->name('vacaciones.index');

    // Ruta de depuración de roles
    Route::get('/debug-role', function () {
        $user = auth()->user();

        return response()->json([
            'user' => $user->name,
            'role' => '"' . $user->role . '"',
            'role_length' => strlen($user->role),
            'canManageInventory' => $user->canManageInventory(),
            'canManageInventoryadmin' => $user->canManageInventoryadmin(),
            'canManageUsers' => $user->canManageUsers(),
            'canManageValeEPP' => $user->canManageValeEPP(),
            'canManagePersonal' => $user->canManagePersonal(),
            'canApproveRequests' => $user->canApproveRequests(),
            'all_roles_in_db' => \App\Models\User::pluck('role')
                ->unique()
                ->values(),
        ]);
    })->name('debug-role');

    // Inventario y almacén
    Route::middleware(['inventory.access'])->group(function () {
        Route::post(
            'inventario/search-ajax',
            [InventarioController::class, 'searchAjax']
        )->name('inventario.search.ajax');

        Route::get(
            'inventario/export/pdf',
            [InventarioController::class, 'exportPDF']
        )->name('inventario.export.pdf');

        Route::get(
            'inventario/export/excel',
            [InventarioController::class, 'exportExcel']
        )->name('inventario.export.excel');

        Route::get(
            'inventario/view/pdf',
            [InventarioController::class, 'viewPDF']
        )->name('inventario.view.pdf');

        Route::get(
            'inventario/api/all',
            [InventarioController::class, 'getAll']
        )->name('inventario.api.all');

        Route::get(
            'inventario/ajustes',
            [AjusteInventarioController::class, 'index']
        )->name('inventario.ajustes.index');

        Route::middleware('can:manage-inventory')->group(function () {
            Route::resource('inventario', InventarioController::class)
                ->only(['create', 'store', 'edit', 'update', 'destroy']);

            Route::post(
                'inventario/{inventario}/ajustes',
                [AjusteInventarioController::class, 'store']
            )->name('inventario.ajustes.store');
        });

        Route::resource('inventario', InventarioController::class)
            ->only(['index', 'show']);

        Route::resource('proveedores', ProveedorController::class)
            ->parameters(['proveedores' => 'proveedor']);

        Route::resource('clientes', ClienteController::class);

        // Entradas
        Route::get(
            'entradas/buscar-proveedores',
            [EntradaController::class, 'buscarProveedores']
        )->name('entradas.buscar-proveedores');

        Route::get(
            'entradas/buscar-productos',
            [EntradaController::class, 'buscarProductos']
        )->name('entradas.buscar-productos');

        Route::resource('entradas', EntradaController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::get(
            'entradas/{entrada}/pdf',
            [EntradaController::class, 'generatePDF']
        )->name('entradas.pdf');

        Route::get(
            'entradas/{entrada}/view-pdf',
            [EntradaController::class, 'viewPDF']
        )->name('entradas.view-pdf');

        // Búsqueda y consulta para salidas
        Route::get(
            'salidas/buscar-solicitudes',
            [SalidaController::class, 'buscarSolicitudes']
        )->name('salidas.buscar-solicitudes');

        Route::get(
            'salidas/solicitudes/{solicitud}',
            [SalidaController::class, 'obtenerSolicitud']
        )->name('salidas.solicitudes.show');

        Route::resource('salidas', SalidaController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::get(
            'salidas/{salida}/pdf',
            [SalidaController::class, 'generatePDF']
        )->name('salidas.pdf');

        Route::get(
            'salidas/{salida}/view-pdf',
            [SalidaController::class, 'viewPDF']
        )->name('salidas.view-pdf');
    });

    // Solicitudes de material
    Route::get(
        '/solicitudes',
        [SolicitudMaterialController::class, 'index']
    )->name('solicitudes.index');

    Route::get(
        '/solicitudes/create',
        [SolicitudMaterialController::class, 'create']
    )->name('solicitudes.create');

    Route::post(
        '/solicitudes',
        [SolicitudMaterialController::class, 'store']
    )->name('solicitudes.store');

    Route::get(
        '/solicitudes/buscar-productos',
        [SolicitudMaterialController::class, 'buscarProductos']
    )->name('solicitudes.buscar-productos');

    Route::get(
        '/solicitudes/producto/{id}',
        [SolicitudMaterialController::class, 'obtenerProducto']
    )->name('solicitudes.obtener-producto');

    Route::get(
        '/solicitudes/{solicitud}',
        [SolicitudMaterialController::class, 'show']
    )->name('solicitudes.show');

    Route::get(
        'solicitudes/{solicitud}/pdf',
        [SolicitudMaterialController::class, 'pdf']
    )->name('solicitudes.pdf');

    // Requisiciones
    Route::get(
        '/requisiciones',
        [RequisicionController::class, 'index']
    )->name('requisiciones.index');

    Route::get(
        '/requisiciones/create',
        [RequisicionController::class, 'create']
    )->name('requisiciones.create');

    Route::post(
        '/requisiciones',
        [RequisicionController::class, 'store']
    )->name('requisiciones.store');

    Route::get(
        '/requisiciones/{requisicion}',
        [RequisicionController::class, 'show']
    )->name('requisiciones.show');

    Route::get(
        'requisiciones/{requisicion}/pdf',
        [RequisicionController::class, 'pdf']
    )->name('requisiciones.pdf');

    // Aprobación de solicitudes y requisiciones
    Route::put(
        'solicitudes/{solicitud}/estatus',
        [SolicitudMaterialController::class, 'updateEstatus']
    )->name('solicitudes.updateEstatus');

    Route::put(
        'requisiciones/{requisicion}/estatus',
        [RequisicionController::class, 'updateEstatus']
    )->name('requisiciones.updateEstatus');

    // Préstamos de materiales
    Route::prefix('prestamos')->name('prestamos.')->group(function () {
        Route::resource('/', PrestamoMaterialController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->parameters(['' => 'prestamo'])
            ->names([
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'show' => 'show',
            ]);

        Route::get(
            '/dashboard/overview',
            [PrestamoMaterialController::class, 'dashboard']
        )->name('dashboard');

        Route::get(
            '/buscar-productos/search',
            [PrestamoMaterialController::class, 'buscarProductos']
        )->name('buscar-productos');

        Route::get(
            '/producto/{id}',
            [PrestamoMaterialController::class, 'obtenerProducto']
        )->name('obtener-producto');

        Route::put(
            '/{prestamo}/estatus',
            [PrestamoMaterialController::class, 'updateEstatus']
        )->name('updateEstatus');

        Route::get(
            '/{prestamo}/devolucion',
            [PrestamoMaterialController::class, 'devolucion']
        )->name('devolucion');

        Route::post(
            '/{prestamo}/devolucion',
            [PrestamoMaterialController::class, 'procesarDevolucion']
        )->name('procesarDevolucion');
    });

    // Usuarios
    Route::middleware(['user.access'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Aprobación de requisiciones por Finanzas
    Route::patch(
        '/requisiciones/{requisicion}/estatus-finanzas',
        [RequisicionController::class, 'updateEstatusFinanzas']
    )->name('requisiciones.updateEstatusFinanzas');

    // Exportaciones
    Route::get(
        '/prestamos/{prestamo}/excel',
        [PrestamoMaterialController::class, 'exportExcel']
    )->name('prestamos.exportExcel');

    Route::get(
        '/requisiciones/{requisicion}/excel',
        [RequisicionController::class, 'exportExcel']
    )->name('requisiciones.exportExcel');
});

// Rutas de Recursos Humanos
Route::middleware(['auth', 'rh.access'])->group(function () {
    Route::resource('personal', PersonalController::class);

    Route::get(
        'catalogos-rh',
        [CatalogoRhController::class, 'index']
    )->name('catalogos-rh.index');

    Route::post(
        'catalogos-rh/areas',
        [CatalogoRhController::class, 'storeArea']
    )->name('catalogos-rh.areas.store');

    Route::put(
        'catalogos-rh/areas/{area}',
        [CatalogoRhController::class, 'updateArea']
    )->name('catalogos-rh.areas.update');

    Route::delete(
        'catalogos-rh/areas/{area}',
        [CatalogoRhController::class, 'destroyArea']
    )->name('catalogos-rh.areas.destroy');

    Route::post(
        'catalogos-rh/departamentos',
        [CatalogoRhController::class, 'storeDepartamento']
    )->name('catalogos-rh.departamentos.store');

    Route::put(
        'catalogos-rh/departamentos/{departamento}',
        [CatalogoRhController::class, 'updateDepartamento']
    )->name('catalogos-rh.departamentos.update');

    Route::delete(
        'catalogos-rh/departamentos/{departamento}',
        [CatalogoRhController::class, 'destroyDepartamento']
    )->name('catalogos-rh.departamentos.destroy');

    Route::post(
        'catalogos-rh/puestos',
        [CatalogoRhController::class, 'storePuesto']
    )->name('catalogos-rh.puestos.store');

    Route::put(
        'catalogos-rh/puestos/{puesto}',
        [CatalogoRhController::class, 'updatePuesto']
    )->name('catalogos-rh.puestos.update');

    Route::delete(
        'catalogos-rh/puestos/{puesto}',
        [CatalogoRhController::class, 'destroyPuesto']
    )->name('catalogos-rh.puestos.destroy');

    Route::get(
        'personal/buscar/ajax',
        [PersonalController::class, 'buscar']
    )->name('personal.buscar');

    Route::get(
        'personal/datos/{id}',
        [PersonalController::class, 'getDatos']
    )->name('personal.getDatos');

    Route::get(
        'personal/export/pdf',
        [PersonalController::class, 'exportPDF']
    )->name('personal.exportPDF');

    // Bajas de personal
    Route::get(
        'bajas',
        [BajaColaboradorController::class, 'index']
    )->name('bajas.index');

    Route::post(
        'bajas',
        [BajaColaboradorController::class, 'store']
    )->name('bajas.store');

    Route::patch(
        'bajas/{baja}/reactivar',
        [BajaColaboradorController::class, 'reactivar']
    )->name('bajas.reactivar');
});

// Rutas de HSE
Route::middleware(['auth', 'hse.access'])->group(function () {
    Route::get(
        'valepp/solicitudes-epp/buscar',
        [ValeppController::class, 'buscarSolicitudesEpp']
    )->name('valepp.solicitudes-epp.buscar');

    Route::get(
        'valepp/solicitudes-epp/{solicitud}',
        [ValeppController::class, 'obtenerSolicitudEpp']
    )->name('valepp.solicitudes-epp.show');

    Route::get(
        'valepp/{valepp}/pdf',
        [ValeppController::class, 'exportPDF']
    )->name('valepp.exportPDF');

    Route::resource('valepp', ValeppController::class)
        ->only(['index', 'create', 'store', 'show']);
});

// Órdenes de compra
Route::middleware(['auth', 'finanzas.access'])->group(function () {
    Route::get(
        '/orden-compra/buscar-productos',
        [OrdenCompraController::class, 'buscarProductos']
    )->name('OrdenCompra.buscar-productos');

    Route::resource('orden-compra', OrdenCompraController::class)
        ->parameters([
            'orden-compra' => 'ordenCompra',
        ]);
});

// PDF de orden de compra
Route::get(
    'orden-compra/{ordenCompra}/pdf',
    [OrdenCompraController::class, 'pdf']
)->name('orden-compra.pdf');