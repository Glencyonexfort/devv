<article>    
    {{-- Start:: Material Issues items --}}                
        <table class="inventory">
            <thead>
                <tr>
                    <th style="width: 50%;"><span >Site Risk Assessment</span></th>
                    <th style="width: 20%;"><span >Y/N/NA</span></th>
                    <th style="width: 30%;"><span>Control Measures</span></th>
                </tr>
            </thead>
            <tbody>
                @if(!$delivery_risk_assessment->isEmpty())
                @foreach($delivery_risk_assessment as $item)
                    <tr>
                        <td>{{ $item->checklist }}</td>
                        <td>{{ $item->risk }}</td>
                        <td>{{ $item->control_measures }}</td>
                    </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="3">No record available !</td>
                    </tr>
                @endif
    
        </tbody>                
        </table>
</article>
    {{-- End:: Material Issues items --}}