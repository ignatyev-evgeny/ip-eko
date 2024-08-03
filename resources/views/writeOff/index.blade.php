
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
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link" >Поступления</a></li>
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Списания</a></li>
        </ul>
    </header>
</div>
<div class="container-fluid py-3">
    <main class="m-auto">

        <p class="d-inline-flex gap-1 w-100">
            <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Добавить списание
            </button>
        </p>

        <div class="collapse mb-5" id="collapseExample">
            <form id="writeOffForm">
                <div class="card card-body">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <div class="mb-3">
                                <label for="contract" class="form-label">Договор</label>
                                <input type="text" class="form-control contractInput" name="contract" id="contract" aria-describedby="contractInput">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="mb-3">
                                <label for="retailer" class="form-label">Ретейлер</label>
                                <input type="text" class="form-control" name="retailer" id="retailer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="external" class="form-label">Номер магазина</label>
                                <input type="text" class="form-control" name="external" id="external">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="store" class="form-label">Адрес магазина</label>
                                <input type="text" class="form-control" name="store" id="store">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="counteragent" class="form-label">Контрагент (Клиент)</label>
                                <input type="text" class="form-control" name="counteragent" id="counteragent">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="date" class="form-label">Дата</label>
                                <input type="date" name="date" class="form-control" id="date">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">

                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="total_weight" class="form-label">Отгружено</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="detailToggle">
                                        <label class="form-check-label" for="detailToggle">Детальный вид</label>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="total_weight" id="total_weight">
                                    <span class="input-group-text">кг.</span>
                                    <span class="input-group-text">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Сумма</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="total_amount" id="total_amount">
                                    <span class="input-group-text">₽</span>
                                    <span class="input-group-text">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="additionalFields"></div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Добавить новое списание</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <table id="example" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th></th>
                <th>Внешний №</th>
                <th>Дата</th>
                <th>Магазин</th>
                <th>Отгружено</th>
                <th>Сумма</th>
                <th>Контрагент</th>
                <th>Договор</th>
                <th>Ретейлер</th>
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

    <div id="uploadModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Загрузить файл</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm">
                        <div class="form-group mb-3">
                            <select id="supplierType" class="form-control" required>
                                <option value="">Выберите тип</option>
                                <option value="VCR">ВЦР</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fileInput">Выберите файл</label>
                            <input type="file" id="fileInput" class="form-control-file" accept=".xlsx" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" id="uploadButton" class="btn btn-primary">Загрузить</button>
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
                            <label for="contract" class="form-label">Договор</label>
                            <input type="text" class="form-control contractInput" name="contract" id="editContract" aria-describedby="contractInput">
                        </div>
                        <div class="mb-3">
                            <label for="retailer" class="form-label">Ретейлер</label>
                            <input type="text" class="form-control" name="retailer" id="editRetailer">
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Дата</label>
                            <input type="datetime-local" name="date" class="form-control" id="editDate">
                        </div>
                        <div class="mb-3">
                            <label for="total_weight" class="form-label">Отгружено</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="total_weight" id="editTotal_weight">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_amount" class="form-label">Сумма</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="total_amount" id="editTotal_amount">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="external" class="form-label">Номер магазина</label>
                            <input type="text" class="form-control" name="external" id="editExternal">
                        </div>
                        <div class="mb-3">
                            <label for="store" class="form-label">Адрес магазина</label>
                            <input type="text" class="form-control" name="store" id="editStore">
                        </div>
                        <div class="mb-3">
                            <label for="counteragent" class="form-label">Контрагент (Клиент)</label>
                            <input type="text" class="form-control" name="counteragent" id="editCounteragent">
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

        $('#detailToggle').change(function() {
            const additionalFields = $('.additionalFields');
            if ($(this).is(':checked')) {

                $('#total_weight').attr('disabled', true);
                $('#total_amount').attr('disabled', true);

                additionalFields.append(`
                <div class="row">
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="fruits_amount" id="fruits_amount" placeholder="Фрукты овощи">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="fruits_weight" id="fruits_weight" placeholder="Фрукты овощи">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="bread_amount" id="bread_amount" placeholder="Хлебобулочные изделия">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="bread_weight" id="bread_weight" placeholder="Хлебобулочные изделия">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="milk_amount" id="milk_amount" placeholder="Молочная гастрономия">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="milk_weight" id="milk_weight" placeholder="Молочная гастрономия">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="food_waste_amount" id="food_waste_amount" placeholder="Пищевые отходы">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="food_waste_weight" id="food_waste_weight" placeholder="Пищевые отходы">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="used_vegetable_oil_amount" id="used_vegetable_oil_amount" placeholder="Отработанное растительное масло">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="used_vegetable_oil_weight" id="used_vegetable_oil_weight" placeholder="Отработанное растительное масло">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="groceries_amount" id="groceries_amount" placeholder="Бакалея">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="groceries_weight" id="groceries_weight" placeholder="Бакалея">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="other_amount" id="other_amount" placeholder="Иное">
                                <span class="input-group-text">₽</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="other_weight" id="other_weight" placeholder="Иное">
                                <span class="input-group-text">кг.</span>
                                <span class="input-group-text">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            } else {
                $('#total_weight').attr('disabled', false);
                $('#total_amount').attr('disabled', false);
                additionalFields.empty();
            }
        });

        const table = $('#example').DataTable({
            scrollX: true,
            stateSave: true,
            pageLength: 100,
            processing: true,
            ajax: '{{ route('write-off.data') }}',
            columns: [
                { data: 'actions' },
                { data: 'external' },
                { data: 'date' },
                { data: 'store' },
                { data: 'total_weight' },
                { data: 'total_amount' },
                { data: 'counteragent' },
                { data: 'contract' },
                { data: 'retailer' },
            ],
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
                        'copy', 'excel',
                        {
                            text: 'Загрузить файл',
                            className: 'btn btn-warning',
                            action: function ( e, dt, node, config ) {
                                $('#uploadModal').modal('show');
                            }
                        }
                    ]
                },
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            }
        });

        $('#example tbody').on('click', 'button.changeRow', function(e) {
            e.stopPropagation();
            var button = this;
            var writeoff = $(button).attr('data-write-off-id');
            var url = '{{ route("write-off.detail", ":writeoff") }}';
            url = url.replace(':writeoff', writeoff);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#editId').val(response.writeOff.id);
                    $('#editContract').val(response.writeOff.contract);
                    $('#editRetailer').val(response.writeOff.retailer);
                    $('#editDate').val(response.writeOff.date);
                    $('#editTotal_weight').val(response.writeOff.total_weight);
                    $('#editTotal_amount').val(response.writeOff.total_amount);
                    $('#editExternal').val(response.writeOff.external);
                    $('#editStore').val(response.writeOff.store);
                    $('#editCounteragent').val(response.writeOff.counteragent);
                    $('#editModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });

        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var writeoffID = $('#editId').val();
            var url = '{{ route("write-off.update", ":editId") }}';
            url = url.replace(':editId', writeoffID);

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

        $('#writeOffForm').on('submit', function(event) {
            event.preventDefault(); // Предотвращаем стандартное поведение формы

            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route('write-off.store') }}',
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

        $('#confirmDelete').click(function() {
            var selectedRows = table.rows({ selected: true }).data();
            var writeoffs = $.map(selectedRows, function(row) {
                return row.id;
            });

            $.ajax({
                url: '{{ route('write-off.delete') }}',
                method: 'DELETE',
                data: {
                    writeoffs: writeoffs
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

        $('#uploadButton').on('click', function() {
            var supplierType = $('#supplierType').val();
            var fileInput = $('#fileInput')[0].files[0];

            if (!supplierType || !fileInput) {
                alert('Пожалуйста, выберите тип поставщика и файл для загрузки.');
                return;
            }

            var formData = new FormData();
            formData.append('supplierType', supplierType);
            formData.append('file', fileInput);

            $.ajax({
                url: '{{ route('write-off.upload') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#uploadModal').modal('hide');
                    alert(response.message);
                },
                error: function(xhr, status, error) {
                    alert('Произошла ошибка при загрузке файла: ' + error);
                }
            });
        });

        $.ui.autocomplete.prototype._renderMenu = function(ul, items) {
            var self = this;
            ul.css("z-index", 1056); // Ensure z-index is higher than modal
            ul.css("position", "absolute");
            $.each(items, function(index, item) {
                self._renderItemData(ul, item);
            });
        };

    });

</script>

</body>
</html>
