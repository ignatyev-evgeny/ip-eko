<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IP-EKO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="{{ asset('assets/dist/css/bootstrap.min.css')  }}" rel="stylesheet">
    <link href="{{ asset('assets/dist/css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/dist/js/bootstrap.bundle.min.js') }}"></script>
    @include('chunk.datatable')
    @if($iframe)
        <script src="//api.bitrix24.com/api/v1/"></script>
    @endif
    <script src="https://kit.fontawesome.com/581b7721a1.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</head>