<?php

namespace App\Imports;

use App\Models\Saeprdday;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SaprodUpdateSaexis implements ToCollection, WithHeadingRow
{
    private $sucursalid;
    private $fechaInventario;

    public function __construct($sucursalid, $fechaInventario = null)
    {
        $this->sucursalid = $sucursalid;
        $this->fechaInventario = $fechaInventario ?? Carbon::now()->format('Y-m-d');
    }

    public function collection(Collection $rows)
    {

        $fecha      = $this->fechaInventario;
        $sucursalid = $this->sucursalid;

        $this->procesarInventario($rows, $fecha, $sucursalid);

    }

    private function procesarInventario($rows, $fecha, $sucursalid)
    {
        $sucursal  = Sasucursal::find($sucursalid);
        $comercial = $sucursal->fk_comercial;

        foreach ($rows as $row) {
            if(isset($row['codprod']) and $row['codprod'] != '' and isset($row['existen'])) {

                $codprod = trim($row['codprod']);
                $costod  = (isset($row['costod']) ) ? floatval($row['costod'])  : 0;
                $preciod = (isset($row['preciod'])) ? floatval($row['preciod']) : 0;
                $existen = floatval($row['existen']);

                if($costod == 0 and $existen > 0) {
                    $producto = Saprod::where('codprod', $codprod)->where('comercial', $comercial)->first();
                    $costod   = $producto->costod + 0;
                    $preciod  = $producto->preciod+ 0;
                }

                // Buscar si ya existe un registro para ese producto, fecha y sucursal
                $record = Saeprdday::where([
                    'codprod'    => $codprod,
                    'fecha'      => $fecha,
                    'fksucursal' => $sucursalid
                ])->first();

                if(!$record) {
                    $record = new Saeprdday();
                    $record->fecha      = $fecha;
                    $record->fksucursal = $sucursalid;
                    $record->codprod    = $codprod;
                }

                $record->costod  = $costod;
                $record->preciod = $preciod;
                $record->existen = $existen;
                $record->save();
            }
        }
    }
}
