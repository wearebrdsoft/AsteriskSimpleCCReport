<table class="table table-striped">
    <thead>
        <tr>
            <th>1ª Posição</th>
            <th>Entre a 1ª e 3ª</th>
            <th>Entre a 3ª e 5ª</th>
            <th>5ª ou maior</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="5" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ $r->count_p1 }}</td>
            <td>{{ $r->count_p1_3 }}</td>
            <td>{{ $r->count_p3_5 }}</td>
            <td>{{ $r->count_pg5 }}</td>
            <td>{{ $r->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
