
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/datepicker-ru.min.js"></script>


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

    .ui-autocomplete {
        z-index: 1056 !important; /* Ensure this is higher than the modal's z-index */
    }

    #filters-container {
        flex-wrap: wrap;
    }

    #status-filter-buttons button {
        margin-right: 5px; /* Отступ между кнопками статусов */
    }

    #date-filter input {
        width: auto; /* Настройка ширины полей даты */
    }

    #reset-filters {
        margin-left: 5px; /* Перемещение кнопки сброса вправо */
    }

    #button-actions {
        display: flex;
        gap: 5px; /* Отступ между кнопками Copy и Excel */
        margin-left: auto; /* Перемещение блока кнопок вправо */
    }

    .table-row-new {
        background-color: #fff3cd !important; /* Желтый цвет для статуса "Новое" */
    }

    .table-row-passed {
        background-color: #d4edda !important; /* Зеленый цвет для статуса "Отработано" */
    }

</style>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="{{ route('index') }}" class="nav-link">Главная</a></li>
{{--            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link" >Поставщики</a></li>--}}
{{--            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link">Клиенты</a></li>--}}
            <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
            <li class="nav-item"><a href="{{ route('invoices.list') }}" class="nav-link">Счета</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link active" aria-current="page">Поступления</a></li>
            <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
            <li class="nav-item"><a href="{{ route('entry.bank', ['date' => date('Y-m-d')]) }}" target="_blank" class="nav-link">Выписка с банка</a></li>
        </ul>
    </header>
</div>

@if(empty($type))
    <div class="container-fluid py-3">
        <main class="m-auto">
{{--            <p class="d-inline-flex gap-1 w-100">--}}
{{--                <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">--}}
{{--                    Добавить поступление--}}
{{--                </button>--}}
{{--            </p>--}}
{{--            <div class="collapse mb-5" id="collapseExample">--}}
{{--                <form id="entryForm">--}}
{{--                    <div class="card card-body">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-4">--}}
{{--                                <label for="contract" class="form-label">Договор</label>--}}
{{--                                <input type="text" class="form-control contractInput" name="contract" id="contract" aria-describedby="contractInput">--}}
{{--                                <div class="mb-3"></div>--}}
{{--                            </div>--}}
{{--                            <div class="col-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="counteragent" class="form-label">Контрагент (Клиент)</label>--}}
{{--                                    <input type="text" class="form-control" name="counteragent" id="counteragent">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="counteragent_bank_account" class="form-label">Контрагент (Клиента) р/с</label>--}}
{{--                                    <input type="text" class="form-control" name="counteragent_bank_account" id="counteragent_bank_account">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-2">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="amount" class="form-label">Поступило</label>--}}
{{--                                    <div class="input-group">--}}
{{--                                        <input type="text" class="form-control" name="amount" id="amount">--}}
{{--                                        <span class="input-group-text">₽</span>--}}
{{--                                        <span class="input-group-text">0.00</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-2">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="datetime" class="form-label">Дата платежа</label>--}}
{{--                                    <input type="datetime-local" name="datetime" class="form-control" id="datetime">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-2">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="number" class="form-label">Номер</label>--}}
{{--                                    <input type="text" class="form-control" name="number" id="number">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="payment_purpose" class="form-label">Назначение платежа</label>--}}
{{--                                    <input type="text" class="form-control" name="payment_purpose" id="payment_purpose">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-2">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label for="operation_type" class="form-label">Вид операции</label>--}}
{{--                                    <input type="text" class="form-control" name="operation_type" id="operation_type">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-12">--}}
{{--                                <button type="submit" class="btn btn-success w-100">Добавить новое поступление</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}

            <div id="columnVisibilityControls" class="mb-3 text-center">
                <label><input type="checkbox" data-column="0" checked> Кнопки</label>
                <label class="ms-3"><input type="checkbox" data-column="1" checked> Статус</label>
                <label class="ms-3"><input type="checkbox" data-column="2" checked> Дата платежа</label>
                <label class="ms-3"><input type="checkbox" data-column="3" checked> Номер</label>
                <label class="ms-3"><input type="checkbox" data-column="4" checked> Поступление</label>
                <label class="ms-3"><input type="checkbox" data-column="5" checked> Контрагент</label>
                <label class="ms-3"><input type="checkbox" data-column="6" checked> Контрагент р/с</label>
                <label class="ms-3"><input type="checkbox" data-column="7" checked> Договор</label>
                <label class="ms-3"><input type="checkbox" data-column="8" checked> Назначение</label>
            </div>

            <div id="filters-container" class="d-flex align-items-center mb-3">
                <div id="status-filter-buttons" class="d-flex me-3">
                    <!-- Кнопки фильтрации по статусам будут добавлены сюда -->
                </div>
                <div id="date-filter" class="d-flex align-items-center me-3">
                    <input type="text" id="min-date" class="date-range-filter form-control me-1" placeholder="Начальная дата">
                    <label for="max-date" class="me-1">-</label>
                    <input type="text" id="max-date" class="date-range-filter form-control me-1" placeholder="Конечная дата">
                </div>

                <div id="button-actions" class="d-flex ms-auto"></div>
                <button id="reset-filters" class="btn btn-warning">Сбросить фильтры</button>
                <a href="/entry/list/ignored"><button id="ignore-list" class="btn btn-danger ms-1">Игнорируется</button></a>


            </div>

            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th style="min-width: 75px"></th>
                    <th>Статус</th>
                    <th>Дата платежа</th>
                    <th>Номер</th>
                    <th>Поступление</th>
                    <th>Контрагент</th>
                    <th>Контрагент р/с</th>
                    <th>Договор</th>
                    <th>Назначение</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </main>
        <div class="modal fade" id="confirmPassedModal" tabindex="-1" aria-labelledby="confirmPassedModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmPassedModalLabel">Подтверждение отработки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите отработать выбранные строки?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" id="confirmPassed" class="btn btn-success">Отработать</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmIgnoreModal" tabindex="-1" aria-labelledby="confirmIgnoreModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmIgnoreModalLabel">Подтверждение добавления в список игнорирования</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите добавить данного контрагента в список игнорирования? В дальнейшем платежи от данного контрагента не будут фиксироваться.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" id="confirmIgnore" class="btn btn-warning">Игнорировать</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Подтверждение удаления</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите удалить выбранные строки?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" id="confirmDelete" class="btn btn-danger">Удалить</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Редактирование</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            @csrf
                            <input type="hidden" id="editId" name="id">
                            <div class="mb-3">
                                <label for="editContract" class="form-label">Договор</label>
                                <textarea type="text" class="form-control contractInput" style="height: 150px;" name="contract" id="editContract" aria-describedby="contractInput"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editCounteragent" class="form-label">Контрагент (Клиент)</label>
                                <textarea type="text" class="form-control" style="height: 100px;" name="counteragent" id="editCounteragent" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editPayment_purpose" class="form-label">Назначение платежа</label>
                                <textarea type="text" class="form-control" style="height: 150px;" name="payment_purpose" id="editPayment_purpose" disabled></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">Просмотр</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="viewForm">
                            @csrf
                            <input type="hidden" id="viewId" name="id">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="viewContract" class="form-label">Договор</label>
                                        <textarea type="text" class="form-control contractInput" style="height: 150px" name="contract" id="viewContract" aria-describedby="contractInput" readonly></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewCounteragent" class="form-label">Контрагент (Клиент)</label>
                                        <textarea type="text" class="form-control" name="counteragent" style="height: 100px" id="viewCounteragent" readonly></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="viewAmount" class="form-label">Поступило</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="amount" id="viewAmount" readonly>
                                            <span class="input-group-text">₽</span>
                                            <span class="input-group-text">0.00</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewDatetime" class="form-label">Дата платежа</label>
                                        <input type="datetime-local" name="datetime" class="form-control" id="viewDatetime" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewNumber" class="form-label">Номер</label>
                                        <input type="text" class="form-control" name="number" id="viewNumber" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="viewCounteragent_bank_account" class="form-label">Контрагент (Клиента) р/с</label>
                                        <input type="text" class="form-control" name="counteragent_bank_account" id="viewCounteragent_bank_account" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="viewPayment_purpose" class="form-label">Назначение платежа</label>
                                        <textarea type="text" class="form-control" style="height: 150px" name="payment_purpose" id="viewPayment_purpose" readonly></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newContract" class="form-label">Новый договор</label>
                                        <textarea type="text" class="form-control contractInput" style="height: 150px;" name="new_contract" id="newContract" aria-describedby="newContract"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Перенести платеж</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready( function () {

            $('#min-date, #max-date').datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1, // Первый день недели — понедельник
                regional: 'ru' // Устанавливаем русскую локализацию
            });

            @if($iframe)
            BX24.init(function(){
                console.log('Инициализация завершена!', BX24.isAdmin());
            });
            BX24.fitWindow()
            @endif

            const table = $('#example').DataTable({
                scrollX: true,
                stateSave: true,
                colReorder: true,
                pageLength: 100,
                ajax: '{{ route('entry.data') }}',
                processing: true,
                order: [[2, 'desc']],
                select: {
                    style: 'multi'
                },
                layout: {
                    topStart: {
                        buttons: [
                            {
                                text: 'Выделение',
                                action: function (e, dt, button, config) {
                                    var allSelected = table.rows({ page: 'current' }).nodes().to$().hasClass('selected');

                                    if (allSelected) {
                                        table.rows({ page: 'current' }).deselect();
                                        button.text('Выделение');
                                    } else {
                                        table.rows({ page: 'current' }).select();
                                        button.text('Выделение');
                                    }
                                }
                            },
                            {
                                text: 'Провести',
                                className: 'btn btn-success',
                                action: function ( e, dt, node, config ) {
                                    var selectedRows = dt.rows({ selected: true }).count();
                                    if (selectedRows > 0) {
                                        $('#confirmPassedModal').modal('show');
                                    } else {
                                        toastr.error('Выберите хотя бы одну строку для проведения.');
                                    }
                                }
                            },
                            {
                                text: 'Игнорировать',
                                className: 'btn btn-warning',
                                action: function ( e, dt, node, config ) {
                                    var selectedRows = dt.rows({ selected: true }).count();
                                    if (selectedRows > 0) {
                                        $('#confirmIgnoreModal').modal('show');
                                    } else {
                                        toastr.error('Выберите хотя бы одну строку для добавления в список игнорирования.');
                                    }
                                }
                            },
                            {
                                text: 'Удалить',
                                className: 'btn btn-danger',
                                action: function ( e, dt, node, config ) {
                                    var selectedRows = dt.rows({ selected: true }).count();
                                    if (selectedRows > 0) {
                                        $('#confirmModal').modal('show');
                                    } else {
                                        toastr.error('Выберите хотя бы одну строку для удаления.');
                                    }
                                }
                            },
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
                    { data: 'actions', className: 'align-middle', sortable: false },
                    { data: 'status', className: 'align-middle' },
                    { data: 'datetime', className: 'align-middle' },
                    { data: 'number', className: 'align-middle' },
                    { data: 'amount', className: 'align-middle' },
                    { data: 'counteragent', className: 'align-middle' },
                    { data: 'counteragent_bank_account', className: 'align-middle' },
                    { data: 'contract', className: 'align-middle' },
                    { data: 'payment_purpose', className: 'align-middle' },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
                },
                rowCallback: function(row, data) {
                    $(row).removeClass('table-row-new table-row-passed');

                    if (data.status === 'Новое') {
                        $(row).addClass('table-row-new');
                    } else if (data.status === 'Проведено') {
                        $(row).addClass('table-row-passed');
                    } else if (data.status === 'Найден') {
                        $(row).addClass('table-row-contract');
                    }
                },
                initComplete: function() {

                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            const min = $('#min-date').datepicker("getDate");
                            const max = $('#max-date').datepicker("getDate");
                            const date = new Date(data[2]);

                            // Если даты не выбраны
                            if (!min && !max) {
                                return true;
                            }

                            // Сравнение только по дате (без учета времени)
                            const minDate = min ? new Date(min.setHours(0, 0, 0, 0)) : null;
                            const maxDate = max ? new Date(max.setHours(23, 59, 59, 999)) : null;
                            const selectedDate = new Date(date.setHours(0, 0, 0, 0));

                            if ((minDate === null && maxDate === null) ||
                                (minDate === null && selectedDate <= maxDate) ||
                                (minDate <= selectedDate && maxDate === null) ||
                                (minDate <= selectedDate && selectedDate <= maxDate)) {
                                return true;
                            }

                            return false;
                        }
                    );

                    // Триггер фильтрации при изменении даты
                    $('#min-date, #max-date').change(function() {
                        table.draw();
                    });

                    $('.dt-buttons').find('.buttons-copy, .buttons-excel').appendTo('#button-actions'); // Переносим только Copy и Excel

                    updateColumnVisibilityControls(table);
                    updateStatusButtons();

                    @if($iframe)
                    BX24.fitWindow()
                    @endif

                }
            });

            // Обработка изменения видимости колонок при изменении состояния чекбоксов
            $('#columnVisibilityControls input[type="checkbox"]').on('change', function() {
                var columnIndex = $(this).attr('data-column');
                var currentOrderIndex = table.colReorder.order().indexOf(parseInt(columnIndex)); // Получаем текущий индекс
                var column = table.column(currentOrderIndex);
                column.visible($(this).is(':checked'));
                table.state.save(); // Сохранение состояния таблицы
                updateStatusButtons();
            });

            // Обновление индексов и состояния чекбоксов после перемещения колонок
            table.on('column-reorder', function(e, settings, details) {
                updateColumnVisibilityControls(table);
                updateStatusButtons();
            });

            function updateStatusButtons() {
                let statusCounts = {};
                let statusColumnIndex = table.colReorder.transpose(1); // Используем transpose для получения правильного индекса

                table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    let status = table.cell(rowIdx, statusColumnIndex).data(); // Получаем данные статуса из нужного столбца
                    if (!statusCounts[status]) {
                        statusCounts[status] = 0;
                    }
                    statusCounts[status]++;
                });

                let buttonsContainer = $('#status-filter-buttons');
                buttonsContainer.empty();

                for (let status in statusCounts) {
                    let buttonClass = 'btn btn-secondary';

                    if (status === 'Проведено') {
                        buttonClass = 'btn btn-success';
                    }

                    if (status === 'Найден') {
                        buttonClass = 'btn btn-primary';
                    }

                    if(status !== 'Проведено') {
                        buttonsContainer.append(`<button class="${buttonClass} status-filter me-2" data-status="${status}">${status} (${statusCounts[status]})</button>`);
                    } else {
                        buttonsContainer.append(`<button class="${buttonClass} status-filter me-2" data-status="${status}">${status}</button>`);
                    }
                }

                $('.status-filter').on('click', function() {
                    const status = $(this).data('status');
                    table.column(statusColumnIndex).search(status).draw(); // Используем правильный индекс столбца для фильтрации
                });
            }

            function updateColumnVisibilityControls(table) {
                var currentOrder = table.colReorder.order(); // Получаем текущий порядок колонок
                table.columns().every(function(index) {
                    var column = this;
                    var isVisible = column.visible();
                    var originalIndex = currentOrder.indexOf(index); // Получаем оригинальный индекс колонки
                    $('#columnVisibilityControls input[data-column="' + originalIndex + '"]').prop('checked', isVisible);
                });
            }

            updateStatusButtons();

            table.on('draw.dt', function() {
                updateStatusButtons();
            });

            $('#reset-filters').on('click', function() {
                $('#min-date').val('');
                $('#max-date').val('');
                table.columns().every(function() {
                    this.visible(true);
                });
                $('#columnVisibilityControls input[type="checkbox"]').prop('checked', true);
                table.state.save();
                table.search('').columns().search('').draw(); // Сброс фильтров DataTables
                table.state.save();
                updateStatusButtons();
            });

            $('#example tbody').on('click', 'button.changeRow', function(e) {
                e.stopPropagation();
                var button = this;
                var entryID = $(button).attr('data-entry-id');
                var url = '{{ route("entry.detail", ":entryID") }}';
                url = url.replace(':entryID', entryID);

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('#editId').val(response.entry.id);
                        $('#editStatus').val(response.entry.status);
                        $('#editAmount').val(response.entry.amount);
                        $('#editContract').val(response.entry.contract);
                        $('#editCounteragent').val(response.entry.counteragent);
                        $('#editCounteragent_bank_account').val(response.entry.counteragent_bank_account);
                        $('#editDatetime').val(response.entry.datetime);
                        $('#editNumber').val(response.entry.number);
                        $('#editOperation_type').val(response.entry.operation_type);
                        $('#editPayment_purpose').val(response.entry.payment_purpose);
                        $('#editModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });

            $('#example tbody').on('click', 'button.viewRow', function(e) {
                e.stopPropagation();
                var button = this;
                var entryID = $(button).attr('data-entry-id');
                var url = '{{ route("entry.detail", ":entryID") }}';
                url = url.replace(':entryID', entryID);

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('#viewId').val(response.entry.id);
                        $('#viewStatus').val(response.entry.status);
                        $('#viewAmount').val(response.entry.amount);
                        $('#viewContract').val(response.entry.contract);
                        $('#viewCounteragent').val(response.entry.counteragent);
                        $('#viewCounteragent_bank_account').val(response.entry.counteragent_bank_account);
                        $('#viewDatetime').val(response.entry.datetime);
                        $('#viewNumber').val(response.entry.number);
                        $('#viewOperation_type').val(response.entry.operation_type);
                        $('#viewPayment_purpose').val(response.entry.payment_purpose);
                        $('#newContract').val('');
                        $('#viewModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var entryID = $('#editId').val();
                var url = '{{ route("entry.update", ":entryID") }}';
                url = url.replace(':entryID', entryID);

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                        $('#editModal').modal('hide');
                    }
                });

            });

            $('#viewForm').on('submit', function(e) {
                e.preventDefault();
                var entryID = $('#viewId').val();
                var url = '{{ route("entry.transfer", ":entryID") }}';
                url = url.replace(':entryID', entryID);

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#viewModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                        $('#viewModal').modal('hide');
                    }
                });

            });

            {{--$('#entryForm').on('submit', function(event) {--}}
            {{--    event.preventDefault(); // Предотвращаем стандартное поведение формы--}}

            {{--    var formData = $(this).serialize();--}}

            {{--    $.ajax({--}}
            {{--        url: '{{ route('entry.store') }}',--}}
            {{--        method: 'POST',--}}
            {{--        data: formData,--}}
            {{--        success: function(response) {--}}
            {{--            toastr.success('Данные успешно отправлены!');--}}
            {{--            table.ajax.reload();--}}
            {{--        },--}}
            {{--        error: function(xhr) {--}}
            {{--            toastr.error('Произошла ошибка при отправке данных.');--}}
            {{--            console.error(xhr.responseText);--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            $("#contract").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '{{ route('contract.getNames') }}',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            /** Преобразуем данные так, чтобы каждый элемент содержал `label` (для отображения) и `value` (для использования) */
                            const results = data.map(item => ({
                                label: item.title, // Отображаемое значение в списке
                                value: item.title, // Значение, устанавливаемое в поле при выборе
                                data: item // Сохраняем весь объект для использования в `select`
                            }));
                            response(results);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                },
                minLength: 2, // Минимальное количество символов для начала поиска
                select: function(event, ui) {
                    $("#contract").val(ui.item.value);
                    return false;
                }
            });

            $("#editContract").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '{{ route('contract.getNames') }}',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            /** Преобразуем данные так, чтобы каждый элемент содержал `label` (для отображения) и `value` (для использования) */
                            const results = data.map(item => ({
                                label: item.title, // Отображаемое значение в списке
                                value: item.title, // Значение, устанавливаемое в поле при выборе
                                data: item // Сохраняем весь объект для использования в `select`
                            }));
                            response(results);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                },
                minLength: 2, // Минимальное количество символов для начала поиска
                select: function(event, ui) {
                    $("#editContract").val(ui.item.value);
                    return false;
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1056);
                    $(".ui-autocomplete").css("position", "absolute");
                }
            });

            $("#newContract").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '{{ route('contract.getNames') }}',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            /** Преобразуем данные так, чтобы каждый элемент содержал `label` (для отображения) и `value` (для использования) */
                            const results = data.map(item => ({
                                label: item.title, // Отображаемое значение в списке
                                value: item.title, // Значение, устанавливаемое в поле при выборе
                                data: item // Сохраняем весь объект для использования в `select`
                            }));
                            response(results);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                },
                minLength: 2, // Минимальное количество символов для начала поиска
                select: function(event, ui) {
                    $("#newContract").val(ui.item.value);
                    return false;
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1056);
                    $(".ui-autocomplete").css("position", "absolute");
                }
            });


            $('#confirmDelete').click(function() {
                $(this).prop('disabled', true);
                var selectedRows = table.rows({ selected: true }).data();
                var entries = $.map(selectedRows, function(row) {
                    return row.id;
                });

                $.ajax({
                    url: '{{ route('entry.delete') }}',
                    method: 'DELETE',
                    data: {
                        entries: entries
                    },
                    success: function(response) {
                        table.rows({ selected: true }).remove().draw();
                        $('#confirmModal').modal('hide');
                        $('#confirmDelete').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#confirmModal').modal('hide');
                        toastr.error(xhr.responseJSON.message);
                        $('#confirmDelete').prop('disabled', false);
                    }
                });

            });

            $('#confirmPassed').click(function() {
                $(this).prop('disabled', true);
                var selectedRows = table.rows({ selected: true }).data();
                var entries = $.map(selectedRows, function(row) {
                    return row.id;
                });

                $.ajax({
                    url: '{{ route('entry.passed') }}',
                    method: 'PATCH',
                    data: {
                        entries: entries
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $('#confirmPassedModal').modal('hide');
                        $('#confirmPassed').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#confirmPassedModal').modal('hide');
                        toastr.error(xhr.responseJSON.message);
                        $('#confirmPassed').prop('disabled', false);
                    }
                });
            });

            $('#confirmIgnore').click(function() {
                $(this).prop('disabled', true);
                var selectedRows = table.rows({ selected: true }).data();
                var entries = $.map(selectedRows, function(row) {
                    return row.id;
                });

                $.ajax({
                    url: '/entry/ignore/true',
                    method: 'PATCH',
                    data: {
                        entries: entries
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $('#confirmIgnoreModal').modal('hide');
                        $('#confirmIgnore').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#confirmIgnoreModal').modal('hide');
                        toastr.error(xhr.responseJSON.message);
                        $('#confirmIgnore').prop('disabled', false);
                    }
                });

            });

            $.ui.autocomplete.prototype._renderMenu = function(ul, items) {
                var self = this;
                ul.css("z-index", 1056);
                ul.css("position", "absolute");
                $.each(items, function(index, item) {
                    self._renderItemData(ul, item);
                });
            };

        });

    </script>
@endif

@if(!empty($type) && $type == "ignored")
    <div class="container-fluid py-3">
        <main class="m-auto">

            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th class="text-center align-middle">Контрагент</th>
                    <th class="text-center align-middle">Контрагент р/с</th>
                    <th class="text-center align-middle">Действия</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td class="text-center align-middle">{{ $entry->counteragent }}</td>
                            <td class="text-center align-middle">{{ $entry->counteragent_bank_account }}</td>
                            <td class="text-center align-middle"><button class="btn btn-success removeFromIgnoreList" data-counteragent-bank-account="{{ $entry->counteragent_bank_account }}">Перестать игнорировать</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Подтверждение удаления</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Вы уверены, что хотите удалить из списка игнорирования?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                            <button type="button" data-counteragent-bank-account="" id="confirmRemoveFromIgnoreList" class="btn btn-danger">Удалить</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready( function () {
                    $('.removeFromIgnoreList').on('click', function () {
                        var counteragentBankAccount = $(this).attr('data-counteragent-bank-account');
                        $('#confirmModal').modal('show');
                        $('#confirmRemoveFromIgnoreList').attr('data-counteragent-bank-account', counteragentBankAccount);
                    })

                    $('#confirmRemoveFromIgnoreList').click(function() {
                        var counteragentBankAccount = $(this).attr('data-counteragent-bank-account');

                        $.ajax({
                            url: '/entry/ignore/false',
                            method: 'PATCH',
                            data: {
                                counteragentBankAccount: counteragentBankAccount
                            },
                            success: function(response) {
                                $('#confirmModal').modal('hide');
                                location.reload();
                            },
                            error: function(xhr) {
                                $('#confirmModal').modal('hide');
                                toastr.error(xhr.responseJSON.message);
                            }
                        });
                    });

                });
            </script>
        </main>
    </div>
@endif

</body>
</html>
