
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
{{--            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link">Поставщики</a></li>--}}
{{--            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link" >Клиенты</a></li>--}}
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
            </tbody>
        </table>

    </main>
</div>

<script>

    $(document).ready( function () {

        @if($iframe)
        BX24.init(function(){
            console.log('Инициализация завершена!', BX24.isAdmin());
        });
        BX24.fitWindow()
        @endif


        const table = $('#contracts').DataTable({
            scrollX: true,
            stateSave: true,
            pageLength: 100,
            processing: true,
            ajax: '{{ route('contract.data') }}',
            layout: {
                topStart: {
                    buttons: [
                        'copy', 'excel'
                    ]
                },
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            },
            columns: [
                { data: 'title' },
                { data: 'balance' },
                { data: 'recommended_payment' },
                { data: 'previous_period_amount' },
                { data: 'type' },
                { data: 'number' },
                { data: 'date' },
                { data: 'phone' },
                { data: 'create_deals' },
                { data: 'export_start_date' },
                { data: 'export_week_days' },
                { data: 'export_frequency' },
                { data: 'payment_total' },
                { data: 'payment_type' },
                { data: 'export_total_count' },
                { data: 'attorney_date' },
                { data: 'price' },
                { data: 'price_fruits_vegetables' },
                { data: 'price_bakery' },
                { data: 'price_dairy' },
                { data: 'price_used_oil' },
                { data: 'price_grocery' },
                { data: 'price_waste' },
                { data: 'other' },
                { data: 'city' },
                { data: 'status' },
                { data: 'shipment' },
                { data: 'process' },
                { data: 'supplier_registered' },
                { data: 'retailer' },
                { data: 'region' },
                { data: 'balance_status' },
                { data: 'source' },
            ],
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

    });

</script>

</body>
</html>
