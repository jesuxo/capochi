<style>
    .tdline{
        border:1px solid #f95a02 !important;

    }
    .tdlineff{
        border-left:1px solid #fff !important;

        color: white !important;
        background-color: #f95a02 !important;
    }
</style>

<div class="row">

    <div class=" col-lg-9 ">
        <div class=" row ">
            <div class="card card-height-100">
                <div class="card-header align-items-center text-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1"> REPORTE DE VENTAS -  <small> DESDE  {{$fecha1}} HASTA {{$fecha2}}</small></h4>
                </div>
                <div class="card-body" data-simplebar style="max-height: 490px;">
                    <table width="100%" border="0" class="ocultar" >
                        <tr>
                    <td width="30%" valign="top">
                        <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                            <tr bgcolor="#fff">
                                <td width="14%" height="30"align="center" class="tdline" >VENTAS  </td>
                                <td width="3%" align="center" class="tdlineff" > DOC</td>
                                <td width="6%" align="center" class="tdlineff" > BS</td>
                                <td width="5%" align="center" class="tdlineff" > BS.T</td>
                                <td width="6%" align="center" class="tdlineff" > USD</td>
                                <td width="5%" align="center" class="tdlineff" > USD.T</td>
                                <td width="6%" align="center" class="tdlineff ocultarcop" > COP</td>
                                <td width="6%" align="center" class="tdlineff ocultarcopt" > COP.T</td>
                                <td width="5%" align="center" class="tdlineff ocultareur" > EUR</td>
                                <td width="5%" align="center" class="tdlineff" > ANTICIPOS </td>
                                <td width="5%" align="center" class="tdlineff" > CREDITO </td>
                                <td width="6%" align="center" class="tdlineff" > TOTAL USD</td>
                            </tr>

                                @php $n=0;
                                        $tcancele    =0;
                                        $tcancelt    =0;
                                        $tdolares    =0;
                                        $ttransf     =0;
                                        $tpesos      =0;
                                        $tpeso_tranf =0;
                                        $teuros      =0;
                                        $tcredito    =0;
                                        $tcancelaUSD =0;
                                        $ttotalventa =0;
                                @endphp
                                @foreach($listado as $nrounico => $sucursal)
                                    @php
                                        $tcancele    += $listado[$nrounico]['cancele'];
                                        $tcancelt    += $listado[$nrounico]['cancelt'];
                                        $tdolares    += $listado[$nrounico]['dolares'];
                                        $ttransf     += $listado[$nrounico]['transf'];
                                        $tpesos      += $listado[$nrounico]['pesos'];
                                        $tpeso_tranf += $listado[$nrounico]['peso_tranf'];
                                        $teuros      += $listado[$nrounico]['euros'];
                                        $tcredito    += $listado[$nrounico]['credito'];
                                        $tcancelaUSD += $listado[$nrounico]['cancelaUSD'];
                                        $ttotalventa += $listado[$nrounico]['totalventa'];
                                    @endphp
                                    <tr @php if(($n%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                        <td height="30"align="left" class="tdline" >
                                          <div style=" max-width: 99%;  width: 100%; height: 20px; overflow: hidden; font-size: 12px">
                                              <a target="_blank" href="/clientes/{{ (isset($listado[$nrounico]['codclie']))? $listado[$nrounico]['codclie']:''  }}">
                                                  {{(isset($listado[$nrounico]['cliente']))? $listado[$nrounico]['cliente']:''}}
                                              </a>
                                          </div>
                                        </td>
                                        <td align="right" class="tdline" >
                                            <a href="/doc/{{ (isset($listado[$nrounico]['tipofac']))? $listado[$nrounico]['tipofac']:'' }}/{{ $listado[$nrounico]['numerod'] }}/{{$listado[$nrounico]['fksucu']}}" target="_blank">
                                                {{($listado[$nrounico]['numerod'] !='')? $listado[$nrounico]['numerod']: ''}}
                                            </a>
                                        </td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['cancele']!=0)?number_format($listado[$nrounico]['cancele'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['cancelt']!=0)?number_format($listado[$nrounico]['cancelt'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['dolares']!=0)?number_format($listado[$nrounico]['dolares'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['transf'] !=0)?number_format($listado[$nrounico]['transf'] ,2,',','.') : ''}}</td>
                                        <td align="right" class="tdline ocultarcop" >  {{($listado[$nrounico]['pesos']  !=0)?number_format($listado[$nrounico]['pesos']  ,2,',','.') : ''}}</td>
                                        <td align="right" class="tdline ocultarcopt" >  {{($listado[$nrounico]['peso_tranf']!=0)?number_format($listado[$nrounico]['peso_tranf'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline ocultareur" >  {{($listado[$nrounico]['euros']!=0)?number_format($listado[$nrounico]['euros'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline " >  {{($listado[$nrounico]['cancelaUSD']!=0)?number_format($listado[$nrounico]['cancelaUSD'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['credito']!=0)?number_format($listado[$nrounico]['credito'],2,',','.') : ''}}</td>
                                        <td align="right" class="tdline" >  {{($listado[$nrounico]['totalventa']!=0)?number_format($listado[$nrounico]['totalventa'],2,',','.') : ''}}</td>
                                    </tr>
                                    @php $n++; @endphp
                                @endforeach

                            <tr >
                                <td height="30"align="left" class=" " > </td>
                                <td align="center" class=" " >&nbsp;  </td>
                                <td align="center" class=" " >&nbsp;  </td>
                                <td align="center" class=" " >  </td>
                                <td align="center" class="" >  </td>
                                <td align="center" class="" >  </td>
                                <td align="center" class="ocultarcop" >  </td>
                                <td align="center" class="ocultarcopt" >  </td>
                                <td align="center" class="ocultareur" >  </td>
                                <td align="center" class="" >  </td>
                                <td align="center" class="" >  </td>
                                <td align="center" class="" >  </td>
                            </tr>
                            <tr bgcolor="#eee">
                                <td height="30"align="left" class="tdline" > TOTALES</td>
                                <td align="left" class="tdline" ></td>
                                <td align="right" class="tdline" >{{ ($tcancele!=0)? number_format($tcancele,2,',','.') : ''}}     </td>
                                <td align="right" class="tdline" >{{ ($tcancelt!=0)?number_format($tcancelt,2,',','.'): ''}}        </td>
                                <td align="right" class="tdline" >{{ ($tdolares!=0)?number_format($tdolares,2,',','.'): ''}}       </td>
                                <td align="right" class="tdline" >{{ ($ttransf!=0)?number_format($ttransf,2,',','.'): ''}}         </td>
                                <td align="right" class="tdline ocultarcop" >{{ ($tpesos!=0)?number_format($tpesos,2,',','.'): ''}}           </td>
                                <td align="right" class="tdline ocultarcopt" >{{ ($tpeso_tranf!=0)?number_format($tpeso_tranf,2,',','.'): ''}} </td>
                                <td align="right" class="tdline ocultareur" >{{ ($teuros!=0)?number_format($teuros,2,',','.'): ''}}           </td>
                                <td align="right" class="tdline" >{{ ($tcancelaUSD!=0)?number_format($tcancelaUSD,2,',','.'): ''}}       </td>
                                <td align="right" class="tdline" >{{ ($tcredito!=0)?number_format($tcredito,2,',','.'): ''}}       </td>
                                <td align="right" class="tdline" >{{ ($ttotalventa!=0)?number_format($ttotalventa,2,',','.'): ''}} </td>
                            </tr>

                        <tr >
                            <td height="30"align="left" class=" " > </td>
                            <td align="center" class=" " >&nbsp;  </td>
                            <td align="center" class=" " >&nbsp;  </td>
                            <td align="center" class=" " >  </td>
                            <td align="center" class="" >  </td>
                            <td align="center" class="" >  </td>
                            <td align="center" class="ocultarcop" >  </td>
                            <td align="center" class="ocultarcopt" >  </td>
                            <td align="center" class="ocultareur" >  </td>
                            <td align="center" class="" >  </td>
                            <td align="center" class="" >  </td>
                            <td align="center" class="" >  </td>
                        </tr>


                                    <tr bgcolor="#fff">
                                        <td   height="30"align="center" class="tdline" >COBRANZAS</td>
                                        <td   height="30"align="center" class="tdlineff" >DOC</td>
                                        <td   align="center" class="tdlineff" > BS</td>
                                        <td   align="center" class="tdlineff" > BS.T</td>
                                        <td   align="center" class="tdlineff" > USD</td>
                                        <td   align="center" class="tdlineff" > USD.T</td>
                                        <td   align="center" class="tdlineff ocultarcop" > COP</td>
                                        <td   align="center" class="tdlineff ocultarcopt" > COP.T</td>
                                        <td   align="center" class="tdlineff ocultareur" > EUR</td>
                                        <td   align="center" class="tdlineff" > ANTICIPOS </td>
                                        <td   align="center" class="tdlineff" >  ...  </td>
                                        <td   align="center" class="tdlineff" > TOTAL USD</td>
                                    </tr>

                                    @php $n=0;
                                        $tcancelec    =0;
                                        $tcanceltc    =0;
                                        $tdolaresc    =0;
                                        $ttransfc     =0;
                                        $tpesosc      =0;
                                        $tpeso_tranfc =0;
                                        $teurosc      =0;
                                        $tcancelausdc =0;
                                        $ttotalventac =0;
                                    @endphp
                                    @foreach($listadoc as $nrounico => $sucursal)
                                        @php
                                            $tcancelec    += $listadoc[$nrounico]['cancele'];
                                            $tcanceltc    += $listadoc[$nrounico]['cancelt'];
                                            $tdolaresc    += $listadoc[$nrounico]['dolares'];
                                            $ttransfc     += $listadoc[$nrounico]['transf'];
                                            $tpesosc      += $listadoc[$nrounico]['pesos'];
                                            $tpeso_tranfc += $listadoc[$nrounico]['peso_tranf'];
                                            $teurosc      += $listadoc[$nrounico]['euros'];
                                            $tcancelausdc += $listadoc[$nrounico]['cancelausd'];
                                            $ttotalventac += $listadoc[$nrounico]['totalcobranza'];
                                        @endphp
                                        <tr @php if(($n%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td height="30"align="left" class="tdline" >
                                                <div style=" max-width: 99%;  width: 100%; height: 20px; overflow: hidden; font-size: 12px"> {{$listadoc[$nrounico]['cliente']}}</div>
                                            </td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['numerod']!='')? $listadoc[$nrounico]['numerod']                        : ''}}</td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['cancele']!=0)?number_format($listadoc[$nrounico]['cancele'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['cancelt']!=0)?number_format($listadoc[$nrounico]['cancelt'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['dolares']!=0)?number_format($listadoc[$nrounico]['dolares'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['transf'] !=0)?number_format($listadoc[$nrounico]['transf'] ,2,',','.') : ''}}</td>
                                            <td align="right" class="tdline ocultarcop" >  {{($listadoc[$nrounico]['pesos']  !=0)?number_format($listadoc[$nrounico]['pesos']  ,2,',','.') : ''}}</td>
                                            <td align="right" class="tdline ocultarcopt" >  {{($listadoc[$nrounico]['peso_tranf']!=0)?number_format($listadoc[$nrounico]['peso_tranf'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline ocultareur" >  {{($listadoc[$nrounico]['euros']!=0)?number_format($listadoc[$nrounico]['euros'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['cancelausd']!=0)?number_format($listadoc[$nrounico]['cancelausd'],2,',','.') : ''}}</td>
                                            <td align="right" class="tdline" >   </td>
                                            <td align="right" class="tdline" >  {{($listadoc[$nrounico]['totalcobranza']!=0)?number_format($listadoc[$nrounico]['totalcobranza'],2,',','.') : ''}}</td>
                                        </tr>
                                        @php $n++; @endphp
                                    @endforeach

                                    <tr >
                                        <td height="30"align="left" class=" " > </td>
                                        <td align="center" class=" " >&nbsp;  </td>
                                        <td align="center" class=" " >&nbsp;  </td>
                                        <td align="center" class=" " >  </td>
                                        <td align="center" class="" >  </td>
                                        <td align="center" class="" >  </td>
                                        <td align="center" class="ocultarcop" >  </td>
                                        <td align="center" class="ocultarcopt" >  </td>
                                        <td align="center" class="ocultareur" >  </td>
                                        <td align="center" class="" >  </td>
                                        <td align="center" class="" >  </td>
                                        <td align="center" class="" >  </td>
                                    </tr>
                                    <tr bgcolor="#eee">
                                        <td width="" height="30"align="left" class="tdline" >TOTALES </td>
                                        <td width=""  align="left" class="tdline" ></td>
                                        <td align="right" class="tdline" >{{ ($tcancelec!=0)? number_format($tcancelec,2,',','.') : ''}}     </td>
                                        <td align="right" class="tdline" >{{ ($tcanceltc!=0)?number_format($tcanceltc,2,',','.'): ''}}        </td>
                                        <td align="right" class="tdline" >{{ ($tdolaresc!=0)?number_format($tdolaresc,2,',','.'): ''}}       </td>
                                        <td align="right" class="tdline" >{{ ($ttransfc!=0)?number_format($ttransfc,2,',','.'): ''}}         </td>
                                        <td align="right" class="tdline ocultarcop" >{{ ($tpesos!=0)?number_format($tpesosc,2,',','.'): ''}}           </td>
                                        <td align="right" class="tdline ocultarcopt" >{{ ($tpeso_tranfc!=0)?number_format($tpeso_tranfc,2,',','.'): ''}} </td>
                                        <td align="right" class="tdline ocultareur" >{{ ($teurosc!=0)?number_format($teurosc,2,',','.'): ''}}           </td>
                                        <td align="right" class="tdline" >{{ ($tcancelausdc!=0)?number_format($tcancelausdc,2,',','.'): ''}}       </td>
                                        <td align="right" class="tdline" >   </td>
                                        <td align="right" class="tdline" >{{ ($ttotalventac!=0)?number_format($ttotalventac,2,',','.'): ''}} </td>
                                    </tr>

                                </table>
                                <script>
                                    @php if($tpesos == 0 and $tpesosc == 0){ @endphp
                                    $('.ocultarcop').hide();
                                    @php }
                                 if($tpeso_tranf == 0 and $tpeso_tranfc == 0){ @endphp
                                    $('.ocultarcopt').hide();
                                    @php }
                                if($teuros == 0 and $teurosc == 0){ @endphp
                                    $('.ocultareur').hide();
                                    @php }  @endphp
                                </script>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class=" col-lg-3 ">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Productos Vendidos </h4>
                <small>en el periodo seleccionado</small>
            </div>

            <div class="card-body" data-simplebar style="max-height: 490px;">
                @php
                    $porc      = 0;
                    $tantosprd = 0;
                    if(isset($topprod)){
                        foreach ($topprod as $top){
                            $tantosprd += $top->salidas;
                        }
                    }
                @endphp
                @if(isset($topprod))
                    @foreach($topprod as $top)

                        <div style="height: 18px; overflow:hidden; width: 100%;" >
                            <span class="badge badge-soft-dark float-end">{{ $top->salidas }}</span>
                            <span  style="font-size: 12px"> {{(isset($top->producto) and isset($top->producto->Descrip))? $top->producto->Descrip: $top}}</span>
                        </div>
                    @endforeach
                @endif
                <script> $('#unidadesvendidas').html('{{$tantosprd}}')</script>
            </div>
        </div>
    </div>

</div>


