@if ($group)
    <article>       
        <h3 class="col-lg-10" style="font-size: 20px;font-family: 'Poppins', sans-serif;">{{ $group->checklist_group }}</h3>          
        <table class="inventory">
            <thead>
                <tr>
                    <th style="width: 70%;"><span >Check</span></th>
                    <th style="width: 30%;"><span>Status</span></th>
                </tr>
            </thead>
            <tbody>
                @if (count($finalData))
                    @foreach ($finalData as $data)
                        @if ($data->checklist_group == $group->checklist_group)
                            <tr>
                                <td>{{ $data->checklist }}</td>
                                <td>{{ $data->status }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>                
        </table>
    </article>
    <br>
    <br>
@endif