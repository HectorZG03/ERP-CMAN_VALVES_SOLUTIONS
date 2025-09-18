<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\SolicitudMaterial;
use App\Models\Requisicion;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $data = [
            'totalInventario' => Inventario::sum('existencia'),
            'solicitudesPendientes' => SolicitudMaterial::where('estatus', 'pendiente')->count(),
            'requisicionesPendientes' => Requisicion::where('estatus', 'pendiente')->count(),
        ];

        return view('dashboard.index', compact('data', 'user'));
    }
}