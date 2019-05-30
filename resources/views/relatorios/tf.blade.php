<table class="table table-striped">
    <thead>
        <tr>
            <th>Hora</th>
            <th>Quantidade de Chamadas</th>
            <th>Tempo em espera (hh:mm:ss)</th>
            <th>Tempo falado (hh:mm:ss)</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="4" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ $r->hora }}</td>
            <td>{{ $r->qtd_chamadas }}</td>
            <td>{{ formattime(round($r->tempo_espera)) }}</td>
            <td>{{ formattime($r->tempo_falado) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
