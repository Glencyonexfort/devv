<article>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th><span >Quantity</span></th>
            </tr>
        </thead>
        <tbody>
            @if($extras)
            @foreach($extras as $item)                
                <tr>
                    <td>
                        {{ $item->item_summary }}
                    </td>
                    <td>
                        {{ $item->quantity }}
                    </td>
                </tr>
                @endforeach
            @endif
    </tbody>                
    </table>
</article>  