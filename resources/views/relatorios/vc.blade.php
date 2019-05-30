<table class="table table-striped">
    <thead>
        <tr>
            <th>Chamdas < 60 segundos</th>
            <th>Chamdas > 60 < 90 segundos</th>
            <th>Chamdas > 90 < 120 segundos</th>
            <th>Chamdas > 120 segundos</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="5" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ $r->count_l60 }}</td>
            <td>{{ $r->count_60_90 }}</td>
            <td>{{ $r->count_90_120 }}</td>
            <td>{{ $r->count_g120 }}</td>
            <td>{{ $r->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
