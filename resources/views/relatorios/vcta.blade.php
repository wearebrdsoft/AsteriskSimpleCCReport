<table class="table table-striped">
    <thead>
        <tr>
            <th>Agente</th>
            <th>Quant. Chamadas</th>
            <th>Tempo em minutos</th>
            <!-- <th>Tempo em segundos</th> -->
            <th>Dia</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="5" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ utf8_decode($r->agent) }}</td>
            <td>{{ $r->QTY_CALLS }}</td>
            <td>{{ formattime($r->CALL_MIN) }}</td>
            <!-- <td>{{ $r->CALL_SECS }}</td> -->
            <td>{{  date('d/m/Y',strtotime($r->CALL_DATE)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
