<table class="table table-striped">
    <thead>
        <tr>
            <!-- <th>ID</th> -->
            <th>Número</th>
            <th>Status</th>
            <th>Data & Hora</th>
            <th>Fila</th>
            <th>Agente</th>
            <th>Posição</th>
            <th>Posição Original</th>
            <th>Tempo de Espera (hh:mm:ss)</th>
            <th>Duração da Chamada (hh:mm:ss)</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="10" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <!-- <td>{{ $r->callId }}</td> -->
            <td>{{ $r->callerId }}</td>
            <td>{{ statusPhone($r->status) }}</td>
            <td>{{ date('d/m/Y H:i:s',strtotime($r->timestamp)) }}</td>
            <td>{{ preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('pj','',$r->queue)) }} - {{ $r->queue }} </td>
            <td>{{ utf8_decode($r->agent) }}</td>
            <td>{{ $r->position!=''?$r->position:'1' }}</td>
            <td>{{ $r->originalPosition!=''?$r->originalPosition:'1' }}</td>
            <td>{{ formattime($r->holdtime) }}</td>
            <td>{{ formattime($r->callduration) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
