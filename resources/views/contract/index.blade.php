
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
            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link" >Клиенты</a></li>
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Договоры</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
            <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
        </ul>
    </header>
</div>
<div class="container-fluid py-3">
    <main class="m-auto">
        <table id="contracts" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>Название</th>
                <th>Баланс</th>
                <th>Рекомендуемый платеж</th>
                <th>Сумма отгрузки за предыдущий период</th>
                <th>Тип договора</th>
                <th>Номер</th>
                <th>Дата</th>
                <th>Телефон</th>
                <th>Создать сделки</th>
                <th>Дата начала вывоза</th>
                <th>Дни недели вывоза</th>
                <th>Периодичность вывоза</th>
                <th>Сумма платежа</th>
                <th>Тип платежа</th>
                <th>Вывезено кг (Для сообщения)</th>
                <th>Текущая дата доверенности</th>
                <th>Цена</th>
                <th>Фрукты овощи</th>
                <th>Хлебобулочные изделия</th>
                <th>Молочная гастрономия</th>
                <th>Отработанное растительное масло</th>
                <th>Бакалея</th>
                <th>Пищевой отход</th>
                <th>Иное</th>
                <th>Город</th>
                <th>Статус договора</th>
                <th>№ отгрузки</th>
                <th>ID Процесса
                </th>
                <th>Учтен в поставщике?</th>
                <th>Ритейлер</th>
                <th>Регион</th>
                <th>Статус баланса</th>
                <th>Источник</th>
            </tr>
            </thead>
            <tbody>
            @foreach($contracts as $contract)
                <tr>
                    <td><a target="_blank" href="https://ip-eko.bitrix24.ru/page/kontragenty/dogovory_s_klientom/type/172/details/{{ $contract->bitrix_id }}/">{{ $contract->title }}</a></td>
                    <td>{{ $contract->balance }}</td>
                    <td>{{ $contract->recommended_payment }}</td>
                    <td>{{ $contract->previous_period_amount }}</td>
                    <td>{{ $contract->type }}</td>
                    <td>{{ $contract->number }}</td>
                    <td>{{ $contract->date }}</td>
                    <td>{{ $contract->phone }}</td>
                    <td>{{ $contract->create_deals }}</td>
                    <td>{{ $contract->export_start_date }}</td>
                    <td>{{ is_array($contract->export_week_days) ? implode(', ', $contract->export_week_days) : '' }}</td>
                    <td>{{ $contract->export_frequency }}</td>
                    <td>{{ $contract->payment_total }}</td>
                    <td>{{ $contract->payment_type }}</td>
                    <td>{{ $contract->export_total_count }}</td>
                    <td>{{ $contract->attorney_date }}</td>
                    <td>{{ $contract->price }}</td>
                    <td>{{ $contract->price_fruits_vegetables }}</td>
                    <td>{{ $contract->price_bakery }}</td>
                    <td>{{ $contract->price_dairy }}</td>
                    <td>{{ $contract->price_used_oil }}</td>
                    <td>{{ $contract->price_grocery }}</td>
                    <td>{{ $contract->price_waste }}</td>
                    <td>{{ $contract->other }}</td>
                    <td>{{ $contract->city }}</td>
                    <td>{{ $contract->status }}</td>
                    <td>{{ $contract->shipment }}</td>
                    <td>{{ $contract->process }}</td>
                    <td>{{ $contract->supplier_registered }}</td>
                    <td>{{ $contract->retailer }}</td>
                    <td>{{ $contract->region }}</td>
                    <td>{{ $contract->balance_status }}</td>
                    <td>{{ $contract->source }}</td>
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


        const table = $('#contracts').DataTable({
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
