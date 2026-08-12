<?php

namespace App\Http\Controllers;

use App\Models\Saeprd;
use App\Models\Saprod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaeprdController extends Controller
{
    /**
     * Recibe y sincroniza los datos de SAEPRD desde la sucursal
     */
    public function sync(Request $request)
    {
        try {
            // Validar que lleguen los datos
            $request->validate([
                'saeprd_data' => 'required|array',
                'dato'        => 'required|string',
                'sucursal'    => 'required|string'
            ]);

            // Verificar token de seguridad
            if ($request->dato !== '$xjsSwN5xEX4nY2U@') {
                return response()->json([
                    'success' => false,
                    'message' => 'xxxxxxx'
                ], 401);
            }

            $saeprdData = $request->saeprd_data;
            $sucursal   = $request->sucursal;

            // Extraer el ID de la sucursal (eliminar el prefijo "300")
            $fk_sucursal = intval(str_replace("300", "", $sucursal));

            $updated = 0;
            $failed  = 0;
            $errors  = [];

            // Iniciar transacción
            DB::beginTransaction();

            try {
                foreach ($saeprdData as $data) {
                    // Verificar si el producto existe en saprod
                    $producto = Saprod::where('codprod', $data['CodProd'])->first();

                    if (!$producto) {
                        $failed++;
                        $errors[] = "Producto {$data['CodProd']} no existe en el sistema";
                        continue;
                    }

                    // Crear o actualizar el registro
                    $saeprd = Saeprd::updateOrCreate(
                        [
                            'CodProd'     => $data['CodProd'],
                            'Periodo'     => $data['Periodo'],
                            'fk_sucursal' => $fk_sucursal
                        ],
                        [
                            'ExInicial'   => $data['ExInicial'] ?? 0,
                            'ExFinal'     => $data['ExFinal'] ?? 0,
                            'preciodi'    => $data['preciodi'] ?? null,
                            'preciod2i'   => $data['preciod2i'] ?? null
                        ]
                    );

                    if ($saeprd->wasRecentlyCreated || $saeprd->wasChanged()) {
                        $updated++;
                    }
                }

                DB::commit();

                return response()->json([
                    'success'  => 'success',
                    'message'  => 'Datos de SAEPRD sincronizados correctamente',
                    'updated'  => $updated,
                    'failed'   => $failed,
                    'errors'   => $errors,
                    'sucursal' => $sucursal,
                    'fk_sucursal' => $fk_sucursal
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en sync SAEPRD: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar los datos: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error en SAEPRD sync: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la sincronización: ' . $e->getMessage()
            ], 500);
        }
    }

}
