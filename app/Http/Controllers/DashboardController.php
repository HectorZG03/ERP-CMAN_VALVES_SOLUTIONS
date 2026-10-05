<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\SolicitudMaterial;
use App\Models\Requisicion;
use App\Models\User;
use App\Models\Personal;
use App\Models\Valepp;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Identificación del usuario
        |--------------------------------------------------------------------------
        */

        $rol = $user->role;


        /*
        |--------------------------------------------------------------------------
        | Estructura base del Dashboard
        |--------------------------------------------------------------------------
        |
        | El Dashboard se construye dependiendo del rol del usuario.
        | No se cargan datos innecesarios para otros departamentos.
        |
        */

        $dashboard = [
            'rol' => $rol,
            'resumen' => [],
        ];


        /*
        |--------------------------------------------------------------------------
        | DIRECCIÓN
        |--------------------------------------------------------------------------
        |
        | Dirección se enfoca principalmente en:
        | - Solicitudes pendientes
        | - Requisiciones pendientes de aprobación
        |
        */

        if ($user->canApproveRequests()) {

            $solicitudesPendientes = SolicitudMaterial::where(
                'estatus',
                'pendiente'
            )->count();


            $requisicionesPendientes = Requisicion::where(
                'estatus_finanzas',
                'aprobado'
            )
                ->where('estatus', 'pendiente')
                ->count();


            $dashboard['resumen'] = [

                'solicitudes' => $solicitudesPendientes,

                'requisiciones' => $requisicionesPendientes,

                'pendientes' => $solicitudesPendientes + $requisicionesPendientes,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | FINANZAS
        |--------------------------------------------------------------------------
        |
        | Finanzas trabaja principalmente con las requisiciones.
        |
        */

        elseif ($user->canApproveFinanzas()) {

            $requisicionesPendientes = Requisicion::where(
                'estatus_finanzas',
                'pendiente'
            )->count();


            $requisicionesAprobadas = Requisicion::where(
                'estatus_finanzas',
                'aprobado'
            )->count();


            $requisicionesDenegadas = Requisicion::where(
                'estatus_finanzas',
                'denegado'
            )->count();


            $dashboard['resumen'] = [

                'requisiciones' => Requisicion::count(),

                'pendientes' => $requisicionesPendientes,

                'aprobadas' => $requisicionesAprobadas,

                'denegadas' => $requisicionesDenegadas,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ALMACÉN
        |--------------------------------------------------------------------------
        |
        | Almacén se enfoca en:
        | - Inventario
        | - Bajo stock
        | - Productos agotados
        | - Solicitudes pendientes
        |
        */

        elseif ($user->canManageInventory()) {

            $totalInventario = Inventario::sum('existencia');


            $productosAgotados = Inventario::where(
                'existencia',
                0
            )->count();


            $productosBajoStock = Inventario::whereBetween(
                'existencia',
                [1, 5]
            )->count();


            $solicitudesPendientes = SolicitudMaterial::where(
                'estatus',
                'pendiente'
            )->count();


            $dashboard['resumen'] = [

                'inventario' => $totalInventario,

                'agotados' => $productosAgotados,

                'bajoStock' => $productosBajoStock,

                'solicitudes' => $solicitudesPendientes,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RH
        |--------------------------------------------------------------------------
        |
        | RH se enfoca en el personal.
        |
        */

        elseif ($user->canManagePersonal()) {

            $personalActivo = Personal::where(
                'estatus',
                'activo'
            )->count();


            $personalBaja = Personal::where(
                'estatus',
                'baja'
            )->count();


            $dashboard['resumen'] = [

                'personalActivo' => $personalActivo,

                'personalBaja' => $personalBaja,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | HSE
        |--------------------------------------------------------------------------
        |
        | HSE trabaja con los Vales EPP.
        |
        */

        elseif ($user->canManageValeEPP()) {

            $valesPendientes = Valepp::where(
                'estatus',
                'pendiente'
            )->count();


            $valesAprobados = Valepp::where(
                'estatus',
                'aprobado'
            )->count();


            $valesEntregados = Valepp::where(
                'estatus',
                'entregado'
            )->count();


            $dashboard['resumen'] = [

                'valesPendientes' => $valesPendientes,

                'valesAprobados' => $valesAprobados,

                'valesEntregados' => $valesEntregados,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | TI
        |--------------------------------------------------------------------------
        |
        | TI se enfoca en administración y supervisión general.
        |
        */

        elseif ($user->canManageUsers()) {

            $usuarios = User::count();


            $solicitudesPendientes = SolicitudMaterial::where(
                'estatus',
                'pendiente'
            )->count();


            $requisicionesPendientes = Requisicion::where(
                'estatus',
                'pendiente'
            )->count();


            $dashboard['resumen'] = [

                'usuarios' => $usuarios,

                'solicitudes' => $solicitudesPendientes,

                'requisiciones' => $requisicionesPendientes,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | USUARIO NORMAL
        |--------------------------------------------------------------------------
        |
        | Un usuario normal solamente necesita ver información relacionada
        | con sus propias solicitudes y requisiciones.
        |
        */

        else {

            $misSolicitudesPendientes = SolicitudMaterial::where(
                'user_id',
                $user->id
            )
                ->where('estatus', 'pendiente')
                ->count();


            $misSolicitudesAprobadas = SolicitudMaterial::where(
                'user_id',
                $user->id
            )
                ->where('estatus', 'aprobado')
                ->count();


            $misRequisicionesPendientes = Requisicion::where(
                'user_id',
                $user->id
            )
                ->where('estatus', 'pendiente')
                ->count();


            $misRequisicionesAprobadas = Requisicion::where(
                'user_id',
                $user->id
            )
                ->where('estatus', 'aprobado')
                ->count();


            $dashboard['resumen'] = [

                'solicitudesPendientes' => $misSolicitudesPendientes,

                'solicitudesAprobadas' => $misSolicitudesAprobadas,

                'requisicionesPendientes' => $misRequisicionesPendientes,

                'requisicionesAprobadas' => $misRequisicionesAprobadas,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Datos generales para la vista
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.index',
            compact(
                'user',
                'dashboard'
            )
        );
    }
}