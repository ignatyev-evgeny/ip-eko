
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<body>

<style>

    .contractInput {
        background-color: #f8d7da;
    }

    .contractInput:focus {
        background-color: #f8d7da;
    }

    th {
        white-space: nowrap;
    }

</style>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="{{ route('index') }}" class="nav-link">Главная</a></li>
            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link">Поставщики</a></li>
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Клиенты</a></li>
            <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
            <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
        </ul>
    </header>
</div>
<div class="container-fluid py-3">
    <main class="m-auto">
        <table id="clients" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Bitrix ID</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Город</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr>
                        <td>{{ $client->bitrix_id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>{{ $client->city }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </main>
</div>

<script>

    $(document).ready( function () {

        BX24.init(function(){
            console.log('Инициализация завершена!', BX24.isAdmin());
        });


        const table = $('#clients').DataTable({
            scrollX: true,
            pageLength: 100,
            // ordering: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            }
        });

        table.on('mouseenter', 'td', function () {
            let colIdx = table.cell(this).index().column;

            table
                .cells()
                .nodes()
                .each((el) => el.classList.remove('highlight'));

            table
                .column(colIdx)
                .nodes()
                .each((el) => el.classList.add('highlight'));
        });

        {{--$('#example').DataTable({--}}
        {{--    ajax: '/product-item/{{ $integration->id }}?dealId={{ $dealId }}',--}}
        {{--    columns: [--}}
        {{--        { data: 'deal', sortable: false, className: 'align-middle', width: "100px" },--}}
        {{--        { data: 'productName', sortable: false, className: 'dt-left align-middle' },--}}
        {{--        { data: 'article', sortable: false, className: 'dt-left align-middle' },--}}
        {{--        { data: 'analogs', sortable: false, className: 'dt-left align-middle' },--}}
        {{--        { data: 'amount', className: 'align-middle', sortable: false, width: "150px" },--}}
        {{--        { data: 'quantity', className: 'align-middle', sortable: false, width: "100px" },--}}
        {{--        { data: 'discount', className: 'align-middle', sortable: false },--}}
        {{--        { data: 'tax', className: 'align-middle', sortable: false },--}}
        {{--        { data: 'total', className: 'align-middle', sortable: false, width: "150px" },--}}
        {{--        { data: 'action', sortable: false, className: 'align-middle', width: "70px" },--}}
        {{--    ],--}}
        {{--    processing: false,--}}
        {{--    serverSide: true,--}}
        {{--    pageLength: 100,--}}
        {{--    language: {--}}
        {{--        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"--}}
        {{--    },--}}
        {{--    initComplete: function () {--}}
        {{--        BX24.fitWindow();--}}
        {{--    }--}}
        {{--});--}}


    });

</script>

</body>
</html>
