
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
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Поставщики</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Клиенты</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Договора</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Списания</a></li>
        </ul>
    </header>
</div>
<div class="container-fluid py-3">
    <main class="m-auto">
        <table id="supplier" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Bitrix ID</th>
                    <th>Название</th>
                    <th>Email</th>
                    <th>Ретейлер</th>
                    <th>Адрес</th>
                    <th>Внутренний номер</th>
                    <th>Внешний номер</th>
                    <th>Регион</th>
                    <th>Город</th>
                    <th>Направление</th>
                    <th>Тип точки</th>
                    <th>Тип оплаты</th>
                    <th>Цена</th>
                    <th>Контракт с</th>
                    <th>ЮР имя</th>
                    <th>Проблемный</th>
                    <th>Частота выемки</th>
                    <th>Дни выемки</th>
                    <th>Статус поставщика</th>
                    <th>Тех статус поставщика</th>
                    <th>Свободные дни</th>
                    <th>Graph ID</th>
                    <th>Цена фильтра</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->bitrix_id }}</td>
                        <td>{{ $supplier->title }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->retailer }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td>{{ $supplier->internal_number }}</td>
                        <td>{{ $supplier->external_number }}</td>
                        <td>{{ $supplier->region }}</td>
                        <td>{{ $supplier->cities }}</td>
                        <td>{{ $supplier->direction }}</td>
                        <td>{{ $supplier->type_point }}</td>
                        <td>{{ $supplier->type_payment }}</td>
                        <td>{{ $supplier->price }}</td>
                        <td>{{ $supplier->contract_with }}</td>
                        <td>{{ $supplier->legal_name }}</td>
                        <td>{{ $supplier->problem }}</td>
                        <td>{{ $supplier->export_frequency }}</td>
                        <td>{{ !empty($supplier->export_days) ? implode(', ', $supplier->export_days) : '' }}</td>
                        <td>{{ $supplier->supplier_status }}</td>
                        <td>{{ $supplier->supplier_tech_status }}</td>
                        <td>{{ !empty($supplier->supplier_free_days) ? implode(', ', $supplier->supplier_free_days) : '' }}</td>
                        <td>{{ $supplier->graph_id }}</td>
                        <td>{{ $supplier->price_filter }}</td>
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


        const table = $('#supplier').DataTable({
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
