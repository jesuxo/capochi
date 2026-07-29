
    <div class="row">
        <div class="col-xl-12">
                <div class="card overflow-hidden">
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div class="table-responsive table-card ">
                                <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                    <tr bgcolor="#fff">
                                        <td width="50%" height="30"align="left" class="tdlineff" >CLIENTE - {{$codclie}}</td>
                                        <td width="11%" align="center" class="tdlineff" > DOCUMENTO</td>
                                        <td width="11%" align="center" class="tdlineff" > TIPO DOC</td>
                                        <td width="17%" align="center" class="tdlineff" > OBSERVACION</td>
                                        <td width="11%" align="center" class="tdlineff" > MONTO</td>
                                    </tr>

                                    @php
                                        $nn     = 1;
                                        $tmonto = 0;
                                        $tabona = 0;
                                        $tsaldo = 0;



                                                  $sqltarj = "
                                                        WITH CommonConditions AS (
                                        SELECT
                                            a.FechaE AS fecha,
                                            c.CodTarj,
                                            c.Descrip,
                                            (b.Monto * ISNULL(a.signo, 1)) AS suma,
                                            (b.dolares * ISNULL(a.signo, 1)) AS sumadolares,
                                            c.bs,
                                            c.dolares,
                                            c.pesos,
                                            (b.pesos * ISNULL(a.signo, 1)) AS sumapesos,
                                            b.descrip AS notas,
                                            COALESCE(d.Descrip, a.Descrip) AS cliente,
                                            a.numerod AS doc,
                                            COALESCE(a.tipofac, a.tipocxc) AS tipofac
                                        FROM satarj c
                                        INNER JOIN saipavta b ON c.CodTarj = b.CodPago
                                        LEFT JOIN safact a ON b.NumeroD = a.numerod AND a.tipofac = b.tipofac
                                        LEFT JOIN saclie d ON a.codclie = d.codclie
                                        WHERE
                                            c.dolares = 1
                                            AND a.CODSUCU = '00000'
                                            AND a.FechaE BETWEEN '2024-12-19 00:00:00.000' AND '2024-12-19 23:59:59.998'

                                        UNION ALL

                                        SELECT
                                            a.FechaE AS fecha,
                                            c.CodTarj,
                                            c.Descrip,
                                            b.Monto AS suma,
                                            b.dolares AS sumadolares,
                                            c.bs,
                                            c.dolares,
                                            c.pesos,
                                            b.pesos AS sumapesos,
                                            b.descrip AS notas,
                                            d.Descrip AS cliente,
                                            a.numerod AS doc,
                                            a.tipocxc AS tipofac
                                        FROM saacxc a
                                        INNER JOIN satarj c ON c.CodTarj = b.CodPago
                                        INNER JOIN saipacxc b ON a.nrounico = b.NroPpal
                                        INNER JOIN saclie d ON a.codclie = d.codclie
                                        WHERE
                                            c.dolares = 1
                                            AND a.CODSUCU = '00000'
                                            AND a.FechaE BETWEEN '2024-12-19 00:00:00.000' AND '2024-12-19 23:59:59.998'


                                    )
                                    SELECT * FROM CommonConditions
                                    ORDER BY fecha;

                                                          ";

                                        $saldocxc = \Illuminate\Support\Facades\DB::select($sqltarj);

                                        @endphp
                                            @foreach($saldocxc as $index => $res)
                                                @php
                                                 $nn++;
                                                 $tmonto += $res->sumadolares;
                                                @endphp
                                                <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                                    <td  height="30"align="left" class="tdline" >  {{$res->cliente}}  </td>
                                                    <td align="right" class="tdline" > {{$res->doc}}</td>
                                                    <td align="right" class="tdline" >  asd</td>
                                                    <td align="right" class="tdline" > {{$res->numero}}</td>
                                                    <td align="right" class="tdline" > {{($res->sumadolares != 0 )? number_format( $res->sumadolares ,2,',','.').'  ' : ''}}</td>

                                                </tr>

                                            @endforeach

                                    <tr >
                                        <td height="30"align="left"  > </td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                    </tr>
                                    <tr >
                                        <td height="30"align="left" class="tdline " >TOTALES </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  {{($tmonto != 0)? number_format($tmonto ,2,',','.')  : ''}} </td>

                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>


