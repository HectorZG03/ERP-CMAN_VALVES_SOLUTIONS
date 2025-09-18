<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventario;
use App\Models\Proveedor;
use App\Models\Cliente;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear proveedores de ejemplo
        $proveedores = [
            ['proveedor' => 'Ferretería El Martillo', 'direccion' => 'Av. Industrial #123, Col. Centro'],
            ['proveedor' => 'Suministros Marítimos SA', 'direccion' => 'Puerto Industrial, Muelle 5'],
            ['proveedor' => 'Tecnología y Equipos', 'direccion' => 'Zona Comercial, Local 45'],
            ['proveedor' => 'Materiales del Golfo', 'direccion' => 'Carretera Costera Km 15'],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }

        // Crear clientes de ejemplo
        $clientes = [
            ['area' => 'Operaciones', 'cedula' => 'TAB123456', 'nombre' => 'Juan Pérez', 'email' => 'juan.perez@cman.com'],
            ['area' => 'Mantenimiento', 'cedula' => 'TAB789012', 'nombre' => 'María González', 'email' => 'maria.gonzalez@cman.com'],
            ['area' => 'Seguridad', 'cedula' => 'TAB345678', 'nombre' => 'Carlos Rodríguez', 'email' => 'carlos.rodriguez@cman.com'],
            ['area' => 'Logística', 'cedula' => 'TAB901234', 'nombre' => 'Ana López', 'email' => 'ana.lopez@cman.com'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }

        // Crear productos de inventario de ejemplo
        $productos = [
            ['categoria' => 'Herramientas', 'nombre_producto' => 'Martillo Industrial', 'medida' => 'Piezas'],
            ['categoria' => 'Herramientas', 'nombre_producto' => 'Destornillador Phillips', 'medida' => 'Piezas'],
            ['categoria' => 'Materiales', 'nombre_producto' => 'Tornillos Inoxidables', 'medida' => 'Kg'],
            ['categoria' => 'Materiales', 'nombre_producto' => 'Cable Eléctrico', 'medida' => 'Metros'],
            ['categoria' => 'Seguridad', 'nombre_producto' => 'Cascos de Seguridad', 'medida' => 'Piezas'],
            ['categoria' => 'Seguridad', 'nombre_producto' => 'Chalecos Reflectivos', 'medida' => 'Piezas'],
            ['categoria' => 'Lubricantes', 'nombre_producto' => 'Aceite Motor', 'medida' => 'Litros'],
            ['categoria' => 'Lubricantes', 'nombre_producto' => 'Grasa Industrial', 'medida' => 'Kg'],
            ['categoria' => 'Electrónicos', 'nombre_producto' => 'Multímetro Digital', 'medida' => 'Piezas'],
            ['categoria' => 'Electrónicos', 'nombre_producto' => 'Lámpara LED', 'medida' => 'Piezas'],
        ];

        foreach ($productos as $producto) {
            Inventario::create($producto);
        }
    }
}