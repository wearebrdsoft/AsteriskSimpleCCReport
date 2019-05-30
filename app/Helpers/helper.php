<?php
    function statusPhone($status){
        switch ($status) {
            case 'ABANDON':
                $msg = 'Abandonada';
                break;
            case 'COMPLETEAGENT':
                $msg = 'Finalizada pelo Agente';
                break;
            case 'COMPLETECALLER':
                $msg = 'Finalizada pelo Cliente';
                break;
            case 'CONNECT':
                $msg = 'Atendido';
                break;
            case 'EXITWITHTIMEOUT':
                $msg = 'Desistência';
                break;
            default:
                $msg = $status;
                break;
    }
    return $msg;
}
    function formattime($seconds) {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }