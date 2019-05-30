<table class="table table-striped">
    <thead>
        <tr>
            <th>Ramal</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Data & Hora</th>
            <th>Status</th>
            <th>Gravação</th>
        </tr>
    </thead>
    <tbody>
        @if(count($result)<1)
            <tr><td colspan="10" >Sem resultados encontrados...</td><tr>
        @endif
        @foreach($result as $r)
        <tr>
            <td>{{ $r->clid }}</td>
            <td>{{ $r->src }}</td>
            <td>{{ $r->dst }}</td>
            <td>{{ date('d/m/Y h:i:s',strtotime($r->calldate)) }}</td>
            <td>{{ $r->disposition }}</td>
            <td>
                @if($r->recordingfile!="")
                <a href="http://192.168.0.249/download.php?url={{ base64_encode(date('Y/m/d',strtotime($r->calldate))."/".$r->recordingfile) }}">Download</a>
                @else
                Não disponível
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
