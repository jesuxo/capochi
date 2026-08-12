<?php

use App\Http\Controllers\TonerController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\UserSucursalController;
use App\Http\Controllers\SaacxcController;
use App\Http\Controllers\InventarioHistoricoController;

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

/* Auth */

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

\Illuminate\Support\Facades\Auth::routes();

// Route::post('login', 'Auth\LoginController@login')->name('login');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('register', 'Auth\RegisterController@register')->name('register');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

\Illuminate\Support\Facades\Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});



use App\Http\Controllers\ComercialDashboardController;

// Rutas para el dashboard del comercial
Route::middleware(['auth', 'redirect.to.comercial'])->group(function () {
    Route::get('/comercial/{comercialId}/dashboard', [ComercialDashboardController::class, 'index'])
        ->name('comercial.dashboard');

    Route::post('/comercial/cambiar/{comercialId}', [ComercialDashboardController::class, 'cambiarComercial'])
        ->name('comercial.cambiar');

    // Ruta para usuarios sin asignación
    Route::get('/sin-asignacion', function () {
        return view('errors.sin-asignacion');
    })->name('sin.asignacion');
});


Route::middleware(['auth'])->group(function () {
    // Ruta para cambiar de comercial
    Route::get('/cambiarcomercial/{comercialId}', [ComercialDashboardController::class, 'cambiarComercial'])
        ->name('comercial.cambiar');

    // Ruta para obtener comerciales disponibles (API)
    Route::get('/comerciales/disponibles', [ComercialDashboardController::class, 'getComercialesDisponibles'])
        ->name('comerciales.disponibles');
});


Route::middleware(['auth'])->group(function () {

    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');



    // Dentro de Route::middleware(['auth'])->group(function () {
    Route::prefix('inventario')->group(function() {
        Route::post('/categorias-tree', [InventarioHistoricoController::class, 'getCategoriasTree']);
        Route::post('/sincronizacion-por-fecha', [InventarioHistoricoController::class, 'getSincronizacionPorFecha']);
        Route::get('/historico', [InventarioHistoricoController::class, 'index'])->name('inventario.historico');
        Route::post('/matriz', [InventarioHistoricoController::class, 'getInventarioMatriz']);
        Route::post('/evolucion', [InventarioHistoricoController::class, 'getEvolucionTemporal']);

        Route::get('/', [InventarioHistoricoController::class, 'index']);
        Route::get('/dashboard', [InventarioHistoricoController::class, 'dashboard'])->name('inventario.dashboard');

        // Nuevas rutas para los requisitos

        // Rutas existentes
        Route::post('/por-fecha', [InventarioHistoricoController::class, 'getInventarioPorFecha']);
        Route::post('/comparar', [InventarioHistoricoController::class, 'compararInventarios']);
        Route::post('/evolucion-producto', [InventarioHistoricoController::class, 'evolucionProducto']);
        Route::get('/ultima-fecha/{sucursalId}', function($sucursalId) {
            $ultima = \App\Models\Saeprdday::where('fksucursal', $sucursalId)
                ->orderBy('fecha', 'desc')
                ->first();
            $sucursal = \App\Models\Sasucursal::find($sucursalId);
            return response()->json([
                'fecha' => $ultima->fecha ?? null,
                'sucursal' => $sucursal->descrip ?? null
            ]);
        });
        Route::get('/calendario-sincronizacion', [InventarioHistoricoController::class, 'calendarioSincronizacion'])
            ->name('inventario.calendario');
        Route::post('/calendario-sincronizacion/data', [InventarioHistoricoController::class, 'getCalendarioSincronizacionData'])
            ->name('inventario.calendario.data');
    });
// }

    Route::prefix('usersucursal')->group(function () {
        Route::get('/', [UserSucursalController::class, 'index'])->name('usersucursal.index');
        Route::get('/usuarios', [UserSucursalController::class, 'getUsersConSucursales'])->name('usersucursal.usuarios');
        Route::get('/sucursales', [UserSucursalController::class, 'getAllSucursales'])->name('usersucursal.sucursales');
        Route::get('/sucursales-asignadas/{userId}', [UserSucursalController::class, 'getSucursalesAsignadasPorUsuario']);
        Route::get('/usuarios-por-sucursal/{sucursalId}', [UserSucursalController::class, 'getUsuariosPorSucursal']);
        Route::post('/asignar', [UserSucursalController::class, 'asignarSucursal'])->name('usersucursal.asignar');
        Route::post('/quitar', [UserSucursalController::class, 'quitarSucursal'])->name('usersucursal.quitar');
    });

    Route::match(['get','post'],'/resumenVentas', [App\Http\Controllers\HomeController::class, 'resumenVentas'])->name('resumenVentas');

    Route::get('signup', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');

    Route::get('revisarcosas', [App\Http\Controllers\HomeController::class, 'revisarcosas']);

    Route::post('saprod/update', [App\Http\Controllers\SaprodController::class, 'updateSaprodData']);
    Route::post('saprod/saexis', [App\Http\Controllers\SaprodController::class, 'updateSaexisData']);
    Route::get('saprod/export/{codalte}', [App\Http\Controllers\SaprodController::class, 'saprodexport']);

    Route::resource('vendedores', \App\Http\Controllers\SavendController::class);
    Route::controller(\App\Http\Controllers\SavendController::class)->group(function () {
        Route::get('savend/json', 'json');
    });

    Route::resource('instancias', \App\Http\Controllers\SainstaController::class);
    Route::controller(\App\Http\Controllers\SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json');
        Route::post('sainsta/check/lastprod/{codinst}', 'lastprod');
    });

    Route::resource('cliente', \App\Http\Controllers\SaclieController::class);
    Route::controller(\App\Http\Controllers\SaclieController::class)->group(function () {
        Route::match(['get','post'],'/clientes/{codclie?}/{tab?}', 'index')->name('buscarclientes');
        Route::post('updatecliente', 'updatecliente')->name('updatecliente');
    });

    // routes/web.php - dentro del grupo auth
    Route::prefix('cxcweb')->name('cxcweb.')->group(function () {
        Route::get('/instrumentos', [SaacxcController::class, 'getInstrumentosPago'])->name('instrumentos');
        Route::post('/procesar-pago-web', [SaacxcController::class, 'procesarPagoWeb'])->name('procesar.pago.web');
    });

    Route::controller(SaacxcController::class)->group(function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxc')->name('saacxc');
        Route::post('/cxclist', 'cxclist');
        Route::post('/cxcabonarweb', 'cxcabonarweb');
        Route::post('/cxc/clientes-por-sucursal', 'clientesPorSucursal');
        Route::post('/cxcdescuento',  'aplicarDescuento')->name('cxcdescuento');
    });
    Route::controller(\App\Http\Controllers\SafactController::class)->group(function () {
        Route::get('doc/{tipofac}/{numerod}/{fksucu}', 'documentoSafact');
        Route::post('openDoc', 'documentoAjax');
    });

    Route::resource('proveedores', \App\Http\Controllers\SaprovController::class);
    Route::controller(\App\Http\Controllers\SaprovController::class)->group(function () {
        Route::get('saprov/json', 'json');
    });

    Route::resource('productos', \App\Http\Controllers\SaprodController::class);
    Route::controller(\App\Http\Controllers\SaprodController::class)->group(function () {
        Route::post('saprod/listprodubiccompany', 'listprodubiccompany')->name('saprod.listprodubiccompany');
        Route::get('saprod/json', 'json');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::match(['get','post'],'ventas/resultado', 'resultadosucursales');
        Route::match(['get','post'],'operaciones/productos/sucursales', 'operacionessucursales');
        Route::match(['get','post'],'operaciones/detallado/sucursal', 'operacionessucursal');
        Route::match(['get','post'],'mermas/sucursales', 'mermassucursales');

        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
    });

    Route::resource('depositos', \App\Http\Controllers\SadepoController::class);
    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::resource('instpago', \App\Http\Controllers\SatarjController::class);
    Route::controller(\App\Http\Controllers\SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
        Route::get('/tarjetas', 'tarjetas');
        Route::post('/tarjetas/content/ubicado', 'contentubicado');
        Route::post('/tarjetas/ubicados', 'ubicados');
        Route::post('/tarjetas/noubicado', 'noubicado');
        Route::post('/tarjetas/quitarubicado', 'quitarubicado');
    });

    Route::match(['get','post'],'/reporte/inventarios',     [App\Http\Controllers\SaprodController::class, 'inventarios'])->name('inventarios');

    Route::post( '/reporte/detinstpagodolares', [App\Http\Controllers\SatarjController::class, 'detinstpagodolares'])->name('detinstpagodolares');

    Route::match(['get','post'],'/reporte/venta', [App\Http\Controllers\HomeController::class, 'reporteventa'])->name('reporteventa');
    Route::post('/reporte/venta/sucu', [App\Http\Controllers\HomeController::class, 'reporteventasucu'])->name('reporteventasucu');

    Route::match(['get','post'],'/{unidades?}/{instancia?}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::match(['get','post'],'/index/{unidades?}/{instancia?}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::match(['get','post'],'/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::match(['get','post'],'/index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    // routes/web.php

});
