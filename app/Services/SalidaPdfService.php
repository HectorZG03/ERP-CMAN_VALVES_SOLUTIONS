<?php

namespace App\Services;

use App\Models\Salida;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class SalidaPdfService
{
    /**
     * Descargar el PDF de una salida.
     */
    public function download(Salida $salida)
    {
        $salida->load(
            $this->relacionesParaPdf()
        );

        $datosFirmas = $this->obtenerDatosFirmas(
            $salida
        );

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper(
            'letter',
            'portrait'
        );

        $folio =
            $salida->numero_factura ??
            'SAL-' . str_pad(
                (string) $salida->id,
                6,
                '0',
                STR_PAD_LEFT
            );

        $nombreArchivo = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '-',
            $folio
        );

        return $pdf->download(
            'vale-salida-' .
            $nombreArchivo .
            '.pdf'
        );
    }

    /**
     * Visualizar el PDF de una salida.
     */
    public function stream(Salida $salida)
    {
        $salida->load(
            $this->relacionesParaPdf()
        );

        $datosFirmas = $this->obtenerDatosFirmas(
            $salida
        );

        $pdf = Pdf::loadView(
            'salidas.pdf',
            array_merge(
                ['salida' => $salida],
                $datosFirmas
            )
        )->setPaper(
            'letter',
            'portrait'
        );

        return $pdf->stream(
            'vale-salida.pdf'
        );
    }

    /**
     * Relaciones necesarias para generar el PDF.
     */
    private function relacionesParaPdf(): array
    {
        return [
            /*
             * Cliente se conserva para PDFs
             * de salidas históricas.
             */
            'cliente',
            'user',
            'detalles.inventario',
            'solicitudMaterial.user',
            'solicitudMaterial.operadorPersonal',
        ];
    }

    /**
     * Convertir una firma en Base64 compatible con DomPDF.
     */
    private function obtenerFirmaDataUri(
        ?User $usuario
    ): ?string {
        if (
            !$usuario ||
            !$usuario->signature
        ) {
            return null;
        }

        $rutaFirma = storage_path(
            'app/public/' .
            ltrim(
                $usuario->signature,
                '/\\'
            )
        );

        if (
            !is_file($rutaFirma) ||
            !is_readable($rutaFirma)
        ) {
            return null;
        }

        $mimeType =
            mime_content_type(
                $rutaFirma
            ) ?: 'image/png';

        $formatosPermitidos = [
            'image/png',
            'image/jpeg',
            'image/gif',
        ];

        if (
            !in_array(
                $mimeType,
                $formatosPermitidos,
                true
            )
        ) {
            return null;
        }

        $contenidoFirma =
            file_get_contents(
                $rutaFirma
            );

        if ($contenidoFirma === false) {
            return null;
        }

        return 'data:' .
            $mimeType .
            ';base64,' .
            base64_encode(
                $contenidoFirma
            );
    }

    /**
     * Obtener los firmantes del vale de salida.
     */
    private function obtenerDatosFirmas(
        Salida $salida
    ): array {
        $firmanteAutorizador =
            User::query()
                ->where(
                    'email',
                    'direccion@cman.com'
                )
                ->first();

        $firmanteAlmacen =
            User::query()
                ->where(
                    'email',
                    'almacen@cman.com'
                )
                ->first();

        $firmanteSolicitante =
            $salida
                ->solicitudMaterial
                ?->user;

        return [
            'firmanteAutorizador' =>
                $firmanteAutorizador,

            'firmanteAlmacen' =>
                $firmanteAlmacen,

            'firmaAutorizador' =>
                $this->obtenerFirmaDataUri(
                    $firmanteAutorizador
                ),

            'firmaAlmacen' =>
                $this->obtenerFirmaDataUri(
                    $firmanteAlmacen
                ),

            'firmaSolicitante' =>
                $this->obtenerFirmaDataUri(
                    $firmanteSolicitante
                ),
        ];
    }
}