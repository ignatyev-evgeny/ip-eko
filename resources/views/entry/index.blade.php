
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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

</style>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="{{ route('index') }}" class="nav-link">Главная</a></li>
{{--            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link" >Поставщики</a></li>--}}
{{--            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link">Клиенты</a></li>--}}
            <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Поступления</a></li>
            <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
        </ul>
    </header>
</div>
<div class="container-fluid py-3">
    <main class="m-auto">

        <p class="d-inline-flex gap-1 w-100">
            <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Добавить поступление
            </button>
        </p>
        <div class="collapse mb-5" id="collapseExample">
            <form id="entryForm">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-4">
                            <label for="contract" class="form-label">Договор</label>
                            <input type="text" class="form-control contractInput" name="contract" id="contract" aria-describedby="contractInput">
                            <div class="mb-3"></div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="counteragent" class="form-label">Контрагент (Клиент)</label>
                                <input type="text" class="form-control" name="counteragent" id="counteragent">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="counteragent_bank_account" class="form-label">Контрагент (Клиента) р/с</label>
                                <input type="text" class="form-control" name="counteragent_bank_account" id="counteragent_bank_account">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Поступило</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="amount" id="amount">
                                    <span class="input-group-text">₽</span>
                                    <span class="input-group-text">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="mb-3">
                                <label for="datetime" class="form-label">Дата платежа</label>
                                <input type="datetime-local" name="datetime" class="form-control" id="datetime">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="mb-3">
                                <label for="number" class="form-label">Номер</label>
                                <input type="text" class="form-control" name="number" id="number">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="payment_purpose" class="form-label">Назначение платежа</label>
                                <input type="text" class="form-control" name="payment_purpose" id="payment_purpose">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="mb-3">
                                <label for="operation_type" class="form-label">Вид операции</label>
                                <input type="text" class="form-control" name="operation_type" id="operation_type">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Добавить новое поступление</button>
                        </div>
                    </div>
                </div>
            </form>
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
                <th>Операция</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </main>

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
                                <input type="text" class="form-control contractInput" name="contract" id="editContract" aria-describedby="contractInput">
                            </div>
                            <div class="mb-3">
                                <label for="editCounteragent" class="form-label">Контрагент (Клиент)</label>
                                <input type="text" class="form-control" name="counteragent" id="editCounteragent">
                            </div>
                            <div class="mb-3">
                                <label for="editCounteragent_bank_account" class="form-label">Контрагент (Клиента) р/с</label>
                                <input type="text" class="form-control" name="counteragent_bank_account" id="editCounteragent_bank_account">
                            </div>
                            <div class="mb-3">
                                <label for="editAmount" class="form-label">Поступило</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="amount" id="editAmount">
                                    <span class="input-group-text">₽</span>
                                    <span class="input-group-text">0.00</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editDatetime" class="form-label">Дата платежа</label>
                                <input type="datetime-local" name="datetime" class="form-control" id="editDatetime">
                            </div>
                            <div class="mb-3">
                                <label for="editNumber" class="form-label">Номер</label>
                                <input type="text" class="form-control" name="number" id="editNumber">
                            </div>
                            <div class="mb-3">
                                <label for="editPayment_purpose" class="form-label">Назначение платежа</label>
                                <input type="text" class="form-control" name="payment_purpose" id="editPayment_purpose">
                            </div>
                            <div class="mb-3">
                                <label for="editOperation_type" class="form-label">Вид операции</label>
                                <input type="text" class="form-control" name="operation_type" id="editOperation_type">
                            </div>
                        <button type="submit" class="btn btn-success w-100">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>

    $(document).ready( function () {

        @if($iframe)
            BX24.init(function(){
                console.log('Инициализация завершена!', BX24.isAdmin());
            });
            BX24.fitWindow()
        @endif

        const table = $('#example').DataTable({
            scrollX: true,
            stateSave: true,
            pageLength: 100,
            ajax: '{{ route('entry.data') }}',
            processing: true,
            select: {
                style: 'multi'
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: 'Удалить выбранные',
                            className: 'btn btn-danger',
                            action: function ( e, dt, node, config ) {
                                var selectedRows = dt.rows({ selected: true }).count();
                                if (selectedRows > 0) {
                                    $('#confirmModal').modal('show');
                                } else {
                                    alert('Выберите хотя бы одну строку для удаления.');
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
                { data: 'actions', sortable: false },
                { data: 'status' },
                { data: 'datetime' },
                { data: 'number' },
                { data: 'amount' },
                { data: 'counteragent' },
                { data: 'counteragent_bank_account' },
                { data: 'contract' },
                { data: 'payment_purpose' },
                { data: 'operation_type' }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            }
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
                    alert('Произошла ошибка при отправке данных.');
                    $('#editModal').modal('hide');
                    console.error(xhr.responseText);
                }
            });

        });

        $('#entryForm').on('submit', function(event) {
            event.preventDefault(); // Предотвращаем стандартное поведение формы

            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route('entry.store') }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert('Данные успешно отправлены!');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    alert('Произошла ошибка при отправке данных.');
                    console.error(xhr.responseText);
                }
            });
        });

        $("#contract").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '{{ route('contract.getNames') }}',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
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
                        response(data);
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

        $.ui.autocomplete.prototype._renderMenu = function(ul, items) {
            var self = this;
            ul.css("z-index", 1056); // Ensure z-index is higher than modal
            ul.css("position", "absolute");
            $.each(items, function(index, item) {
                self._renderItemData(ul, item);
            });
        };

        $('#confirmDelete').click(function() {
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
                },
                error: function(xhr) {
                    $('#confirmModal').modal('hide');
                    alert(xhr.responseJSON.message);
                }
            });
        });

    });

</script>

</body>
</html>
