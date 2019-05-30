<table class="table table-striped">
    <thead>
        <tr>
            <th>Chamadas Abandonadas</th>
            <th>Chamadas Atendidas</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="3" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ $r->qtd_abandon }}</td>
            <td>{{ $r->qtd_answer }}</td>
            <td>{{ $r->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
