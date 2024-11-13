
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

    .w-500px {
        width: 500px !important;
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
                <th>ID</th>
                <th>ВН</th>
                <th>Статус договора</th>
                <th>Локальный баланс</th>
                <th></th>
                <th>Ритейлер</th>
                <th>Название</th>
                <th>Дни недели вывоза</th>
                <th>Цена</th>
                <th>Фрукты овощи</th>
                <th>Хлебобулочные изделия</th>
                <th>Молочная гастрономия</th>
                <th>Отработанное растительное масло</th>
                <th>Бакалея</th>
                <th>Пищевой отход</th>
                <th>Иное</th>
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
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Все',

                    action: function () {
                        table.columns(1).search('').draw();
                    }
                },
                {
                    text: 'Активные',
                    className: 'btn btn-success',
                    action: function () {
                        table.columns(1).search('Активный').draw();
                    }
                },
                {
                    text: 'Закрытые',
                    className: 'btn btn-danger',
                    action: function () {
                        table.columns(1).search('Закрыт').draw();
                    }
                },
                {
                    text: 'Приостановленные',
                    className: 'btn btn-warning',
                    action: function () {
                        table.columns(1).search('Приостановлен').draw();
                    }
                },
                {
                    text: 'Согласование',
                    className: 'btn btn-secondary',
                    action: function () {
                        table.columns(1).search('Согласование').draw();
                    }
                }
            ],
            columns: [
                { data: 'id', className: 'text-center align-middle'},
                { data: 'shop', className: 'text-center align-middle'},
                { data: 'status', className: 'text-center align-middle'},
                { data: 'local_balance', className: 'text-center align-middle'},
                { data: 'balance_history', className: 'text-center align-middle', sortable: false},
                { data: 'retailer', className: 'text-center align-middle'},
                { data: 'title', className: 'text-center align-middle'},
                { data: 'export_week_days', className: 'text-center align-middle'},
                { data: 'price', className: 'text-center align-middle'},
                { data: 'price_fruits_vegetables', className: 'text-center align-middle'},
                { data: 'price_bakery', className: 'text-center align-middle'},
                { data: 'price_dairy', className: 'text-center align-middle'},
                { data: 'price_used_oil', className: 'text-center align-middle'},
                { data: 'price_grocery', className: 'text-center align-middle'},
                { data: 'price_waste', className: 'text-center align-middle'},
                { data: 'other', className: 'text-center align-middle'},
            ],
            createdRow: function(row, data, dataIndex) {
                //$(row).find('td:eq(2)').attr('contenteditable', 'true');  // Делаем редактируемой только 2-ю колонку (local_balance)
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            },
            initComplete: function() {
                // Создаем контейнер для выпадающего списка рядом с кнопками
                const retailerFilter = `<select id="retailerFilter" class="form-control ml-2 mt-2"><option value="">Без сортировки</option></select>`;

                // Вставляем выпадающий список рядом с кнопками
                $('.dt-buttons').append(retailerFilter);

                // Динамическое заполнение выпадающего списка
                $.ajax({
                    url: '{{ route('contract.get.retailers') }}',
                    method: 'GET',
                    success: function(data) {
                        data.forEach(function(retailer) {
                            $('#retailerFilter').append(new Option(retailer, retailer));
                        });
                    }
                });

                // Поле для поиска по второму столбцу
                const shopFilter = `<input type="text" id="shopFilter" class="form-control ml-2 mt-2" placeholder="Поиск по ВН">`;

// Вставляем текстовое поле рядом с кнопками
                $('.dt-buttons').append(shopFilter);

// Обработчик изменения значения для фильтрации второго столбца
                $('#shopFilter').on('keyup', function () {
                    const shop = $(this).val();
                    table.columns(1).search(shop).draw();
                });

                // Обработчик изменения значения для фильтрации
                $('#retailerFilter').on('change', function () {
                    const retailer = $(this).val();
                    table.columns(5).search(retailer).draw();
                });
            }
        });

        // Очистка текста при начале редактирования
        $('#contracts tbody').on('focus', 'td[contenteditable="true"]', function() {
            var cellText = $(this).text();
            var cleanedText = cellText.replace(/[^0-9.-]/g, '');
            $(this).text(cleanedText);
        });

        // Ограничение ввода только числами, точкой и минусом
        $('#contracts tbody').on('keypress', 'td[contenteditable="true"]', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;

            // Разрешаем: цифры (0-9), точку (.), минус (-)
            if (
                (charCode < 48 || charCode > 57) &&  // Не цифры
                charCode !== 46 &&  // Не точка
                charCode !== 45     // Не минус
            ) {
                return false;
            }

            // Запрещаем ввод больше одной точки
            if (charCode === 46 && $(this).text().includes('.')) {
                return false;
            }

            // Запрещаем ввод больше одного знака минус
            if (charCode === 45 && $(this).text().includes('-')) {
                return false;
            }

            // Минус можно вводить только в начале
            if (charCode === 45 && $(this).text().length > 0) {
                return false;
            }

            return true;
        });

        // Сохранение изменений при потере фокуса
        $('#contracts tbody').on('blur', 'td[contenteditable="true"]', function() {
            var cellData = $(this).text().trim();  // Получаем новое значение и обрезаем пробелы
            var rowIndex = table.row($(this).closest('tr')).index();  // Индекс строки
            var columnIndex = table.cell(this).index().column;  // Индекс колонки

            // Получаем всю строку данных
            var rowData = table.row(rowIndex).data();

            // Отправляем данные на сервер для сохранения
            $.ajax({
                url: '{{ route('contract.changeBalance') }}',  // URL для сохранения данных
                method: 'POST',
                data: {
                    row: rowData.id,  // Уникальный ID строки
                    column: columnIndex,
                    value: cellData,
                    _token: $('meta[name="csrf-token"]').attr('content')  // CSRF-токен для Laravel
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message);
                    table.ajax.reload();
                }
            });

        });

        var urlParams = new URLSearchParams(window.location.search);
        var searchTerm = urlParams.get('search');
        if (searchTerm) {
            table.search(searchTerm).draw();
        }

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
