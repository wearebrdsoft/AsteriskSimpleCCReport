<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class RelatorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $r)
    {
        if($r->xls){
            $di = explode('/',$r->di);
            $df = explode('/',$r->df);
            $di = "$di[2]-$di[1]/$di[0] 00:00:00";
            $df = "$df[2]-$df[1]/$df[0] 23:59:59";
        }else{
            $df = Carbon::parse($r->df)->format('Y-m-d 23:59:59');
            $di = Carbon::parse($r->di)->format('Y-m-d 00:00:00');
        }

        $dados = [];
        if(!isset($df)||!isset($df)||!isset($r->r_tp)){
            return "<h3>Selecione um intervalo de data válidos e/ou um típo de ralatório!</h3>";
        }
        $extra_filter = '';
        switch ($r->r_tp) {
            case 'exs':{
                if($r->tp_extrato == 'abandonada'){
                    $extra_filter = 'and status=\'ABANDON\'';
                }elseif($r->tp_extrato == 'completada'){
                    $extra_filter = 'and status in(\'COMPLETEAGENT\',\'COMPLETECALLER\')';
                }
                $dados['result'] = DB::select("select cs.* from call_status as cs
                where 
                (timestamp between '{$di}' and '{$df}') {$extra_filter} order by callId desc;");
                break;
            }
            case 'tf':{
                $dados['result'] = DB::select("select count(*) as qtd_chamadas, (SUM(holdtime)/COUNT(*)) as
                tempo_espera, (SUM(callduration)/COUNT(*)) as tempo_falado ,
                HOUR(timestamp) as hora from call_status where
                (timestamp between '{$di}' and '{$df}') and
                (status = 'COMPLETEAGENT' or status = 'COMPLETECALLER')
                group by hora asc;");
                break;
            }
            case 'te':{
                $dados['result'] = DB::select("select count(*) as qtd_chamadas, (SUM(holdtime)/COUNT(*)) as
                tempo_espera, HOUR(timestamp) as hora from call_status where
                (timestamp between '{$di}' and '{$df}')
                and (status ='ABANDON') group by hora asc;");
                break;
            }
            case 'vc':{
                $dados['result'] = DB::select("select SUM(1) as total,
                SUM( IF ( (holdtime < 60), 1, 0) ) as count_l60,
                SUM( IF ( (holdtime > 60 and holdtime <90), 1, 0) ) as count_60_90,
                SUM( IF ( (holdtime > 90 and holdtime <120), 1, 0) ) as count_90_120,
                SUM( IF ( (holdtime > 120 ), 1, 0) ) as count_g120 from call_status where
                ( timestamp between '{$di}' and '{$df}' ) and (status =  'COMPLETEAGENT' or status = 'COMPLETECALLER' );");
                break;
            }
            case 'qc':{
                $dados['result'] = DB::select("select SUM(1) as total,
                SUM( IF ( (status = 'ABANDON' ) , 1, 0 ) ) AS qtd_abandon,
                SUM( IF ( (callduration > 0 ), 1, 0 ) ) AS qtd_answer FROM call_status
                where ( timestamp between '{$di}' and '{$df}');");
                break;
            }
            case 'vca':{
                $dados['result'] = DB::select("select SUM(1) as total,
                    SUM( IF ( (holdtime < 60), 1, 0) ) as count_l60,
                    SUM( IF ( (holdtime > 60 and holdtime <90), 1, 0) ) as count_60_90,
                    SUM( IF ( (holdtime > 90 and holdtime <120), 1, 0) ) as count_90_120,
                    SUM( IF ( (holdtime > 120 ), 1, 0) ) as count_g120 from call_status
                    where ( timestamp between '{$di}' and '{$df}') and status = 'ABANDON';");
                break;
            }
            case 'vcap':{
                $dados['result'] = DB::select("select SUM(1) as total,
                    SUM( IF ( (position = 1), 1, 0) ) as count_p1,
                    SUM( IF ( (position > 1 and position <3), 1, 0) ) as count_p1_3,
                    SUM( IF ( (position > 3 and position <5), 1, 0) ) as count_p3_5,
                    SUM( IF ( (position > 5 ), 1, 0) ) as count_pg5 from call_status where
                    ( timestamp between '{$di}' and '{$df}' ) and status = 'ABANDON';");
                break;
            }
            case 'vcta':{
                $dados['result'] = DB::select("select agent,
                    COUNT(*) as QTY_CALLS,
                    SUM(callduration) as CALL_MIN,
                    SUM(callduration) as CALL_SECS,
                    DATE(timestamp) as CALL_DATE from call_status
                     where (timestamp between '{$di}' and '{$df}')
                     and callduration > 0 group by CALL_DATE, AGENT order by CALL_DATE asc;");
                break;
            }
            default:{
                $dados['result'] = '';
                break;
            }
        }
        $view = view('relatorios.'.$r->r_tp, $dados);
        if($r->xls){
            $file_name = str_replace(' ', '', $r->title_rt).'_'.time();
            $file_name = storage_path("app/{$file_name}.xls");
            $file = fopen($file_name, "w");
            fwrite($file, utf8_decode($view));
            fclose($file);
            return response()->download($file_name)->deleteFileAfterSend(true);
        }
        return $view;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cdr(Request $r)
    {
        if($r->xls){
            $di = explode('/',$r->di);
            $df = explode('/',$r->df);
            $di = "$di[2]-$di[1]/$di[0] 00:00:00";
            $df = "$df[2]-$df[1]/$df[0] 23:59:59";
        }else{
            $df = Carbon::parse($r->df)->format('Y-m-d 23:59:59');
            $di = Carbon::parse($r->di)->format('Y-m-d 00:00:00');
        }

        $dados['result'] = DB::select("select * from cdr where calldate between '{$di}' and '{$df}';");

        $view = view('relatorios.cdr', $dados);
        if($r->xls){
            $file_name = str_replace(' ', '', $r->title_rt).'_'.time();
            $file_name = storage_path("app/{$file_name}.xls");
            $file = fopen($file_name, "w");
            fwrite($file, utf8_decode($view));
            fclose($file);
            return response()->download($file_name)->deleteFileAfterSend(true);
        }
        return $view;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
