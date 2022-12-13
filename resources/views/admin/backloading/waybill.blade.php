<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/pdf.css') }}">
    {{-- <link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css"> --}}
  </head>
  <body>
    <header class="clearfix">
      <table style="width: 100%">
        <tr>
          <td>
            @if($company_logo_exists==true)
            <div id="logo">
                <img src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$companies->logo }}">
            </div>
        @endif
          </td>
          <td>
            <div id="company" class="clearfix">
              <div><b>{{ $companies->company_name}}</b></div>
              <div>ABN: {{ $companies->abn}}</div>
              <div>{{ $companies->address}}</div>
              <div>Phone: {{ $companies->phone}}</div>
              <div><a href="mailto:{{ $companies->email}}">{{ $companies->email}}</a></div>
            </div>
          </td>
        </tr>
      </table>
    </header>
    <h1>WAYBILL: <?=$trip->waybill_number?></h1>

    <script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 7;
        $font = $fontMetrics->getFont("Calibri");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

  </body>
</html>