
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
        </div>


        <table id="example" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th></th>
                <th>Статус</th>
{{--                <th>Внешний №</th>--}}
                <th>Дата</th>
                <th>Комментарий</th>
                <th>Магазин</th>
                <th>Отгружено</th>
                <th>Сумма</th>
{{--                <th>Контрагент</th>--}}
                <th>Договор</th>
{{--                <th>Ретейлер</th>--}}
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

    <div class="modal fade" id="confirmCanceledModal" tabindex="-1" aria-labelledby="confirmCanceledModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCanceledModalLabel">Подтверждение аннулирования</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите аннулировать выбранные строки?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" id="confirmCanceled" class="btn btn-danger">Аннулировать</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmFreeModal" tabindex="-1" aria-labelledby="confirmFreeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmFreeModalLabel">Подтверждение перевода статуса</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите перевести в статус "Бесплатно" выбранные строки?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" id="confirmFree" class="btn btn-danger">Сделать Бесплатным</button>
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
                                <option value="CROSSROAD">Перекресток</option>
                                <option value="BIO">БИО</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fileInput">Выберите файл</label>
                            <input type="file" id="fileInput" class="form-control-file" accept=".csv, .xls, .xlsx" required>
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

    <div class="modal fade " id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered3 modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Редактирование</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form id="editForm">
                            @csrf
                            <input type="hidden" id="editId" name="id">

                            <div class="row">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="contract" class="form-label">Договор</label>
                                            <div class="form-check form-switch" >
                                                <input class="form-check-input" type="checkbox" name="take_contract" id="detailTakeContract" >
                                                <label class="form-check-label" for="detailTakeContract">Учитывать договор</label>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control contractInput" name="contract" id="editContract" aria-describedby="contractInput">
                                    </div>
                                </div>
                                <div class="col-4 ms-auto">
                                    <div class="mb-3">
                                        <label for="retailer" class="form-label">Ретейлер</label>
                                        <input type="text" class="form-control" name="retailer" id="editRetailer">
                                    </div>
                                </div>
                                <div class="col-4 ms-auto">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Дата</label>
                                        <input type="date" name="date" class="form-control" id="editDate">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 ms-auto">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="total_weight" class="form-label">Отгружено</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="detail_view" type="checkbox" id="detailEditToggle">
                                                <label class="form-check-label" for="detailEditToggle">Детальный вид</label>
                                            </div>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="total_weight" id="editTotal_weight">
                                            <span class="input-group-text">кг.</span>
                                            <span class="input-group-text">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 ms-auto">
                                    <div class="mb-3">
                                        <label for="total_weight" class="form-label">Сумма</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="total_amount" id="editTotal_amount">
                                            <span class="input-group-text">₽</span>
                                            <span class="input-group-text">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row additionalEditFields d-none">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="fruits_price_edit" class="form-label">Фрукты и овощи</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="fruits_price" id="fruits_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="fruits_weight" id="fruits_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="bread_price_edit" class="form-label">Хлебобулочные изделия</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="bread_price" id="bread_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="bread_weight" id="bread_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="milk_price_edit" class="form-label">Молочная гастрономия</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="milk_price" id="milk_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="milk_weight" id="milk_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="food_waste_price_edit" class="form-label">Пищевые отходы</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="food_waste_price" id="food_waste_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="food_waste_weight" id="food_waste_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="used_vegetable_oil_weight_edit" class="form-label">Отработанное растительное масло</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="used_vegetable_oil_price" id="used_vegetable_oil_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="used_vegetable_oil_weight" id="used_vegetable_oil_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="groceries_weight_edit" class="form-label">Бакалея</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="groceries_price" id="groceries_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="groceries_weight" id="groceries_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="other_weight_edit" class="form-label">Иное</label>
                                        <div class="d-flex">
                                            <div class="input-group me-2">
                                                <input type="text" class="form-control" name="other_price" id="other_price_edit" placeholder="Цена за единицу">
                                                <span class="input-group-text">₽</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="other_weight" id="other_weight_edit" placeholder="Введите вес">
                                                <span class="input-group-text">кг.</span>
                                                <span class="input-group-text">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3 ms-auto">
                                    <div class="mb-3">
                                        <label for="external" class="form-label">Внешний номер</label>
                                        <input type="text" class="form-control" name="external" id="editExternal">
                                    </div>
                                </div>
                                <div class="col-3 ms-auto">
                                    <div class="mb-3">
                                        <label for="storeNumber" class="form-label">Номер магазина</label>
                                        <input type="text" class="form-control" name="store_number" id="editStoreNumber">
                                    </div>
                                </div>
                                <div class="col-3 ms-auto">
                                    <div class="mb-3">
                                        <label for="store" class="form-label">Адрес магазина</label>
                                        <input type="text" class="form-control" name="store" id="editStore">
                                    </div>
                                </div>
                                <div class="col-3 ms-auto">
                                    <div class="mb-3">
                                        <label for="counteragent" class="form-label">Контрагент (Клиент)</label>
                                        <input type="text" class="form-control" name="counteragent" id="editCounteragent">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Сохранить</button>
                        </form>
                    </div>
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
                                <input type="text" class="form-control" name="fruits_price" id="fruits_price" placeholder="Фрукты овощи">
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
                                <input type="text" class="form-control" name="bread_price" id="bread_price" placeholder="Хлебобулочные изделия">
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
                                <input type="text" class="form-control" name="milk_price" id="milk_price" placeholder="Молочная гастрономия">
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
                                <input type="text" class="form-control" name="food_waste_price" id="food_waste_price" placeholder="Пищевые отходы">
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
                                <input type="text" class="form-control" name="used_vegetable_oil_price" id="used_vegetable_oil_price" placeholder="Отработанное растительное масло">
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
                                <input type="text" class="form-control" name="groceries_price" id="groceries_price" placeholder="Бакалея">
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
                                <input type="text" class="form-control" name="other_price" id="other_price" placeholder="Иное">
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

        $('#detailEditToggle').change(function() {
            if ($(this).is(':checked')) {

                $('#editTotal_weight').attr('disabled', true);
                $('#editTotal_amount').attr('disabled', true);
                $('.additionalEditFields').removeClass('d-none');

            } else {
                $('#editTotal_weight').attr('disabled', false);
                $('#editTotal_amount').attr('disabled', false);
                $('.additionalEditFields').addClass('d-none');
            }
        });

        const table = $('#example').DataTable({
            scrollX: true,
            stateSave: true,
            pageLength: 100,
            processing: true,
            ajax: '{{ route('write-off.data') }}',
            columns: [
                { data: 'id', sortable: false, className: 'text-center align-middle' },
                { data: 'actions', sortable: false, className: 'text-center align-middle' },
                { data: 'status', sortable: false, className: 'text-center align-middle' },
                // { data: 'external', sortable: false, visible: false, className: 'text-center align-middle' },
                { data: 'date', className: 'text-center align-middle' },
                { data: 'comment', className: 'text-center align-middle', defaultContent: '' },
                { data: 'store', sortable: false, className: 'text-center align-middle' },
                { data: 'total_weight', sortable: false, className: 'text-center align-middle' },
                { data: 'total_amount', sortable: false, className: 'text-center align-middle' },
                // { data: 'counteragent', sortable: false, visible: false, className: 'text-center align-middle' },
                { data: 'contract', sortable: false, className: 'text-center align-middle' },
                // { data: 'retailer', sortable: false, visible: false, className: 'text-center align-middle' },
            ],
            select: {
                style: 'multi'
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: 'Выбрать все',
                            action: function (e, dt, button, config) {
                                var allSelected = table.rows({ page: 'current' }).nodes().to$().hasClass('selected');

                                if (allSelected) {
                                    table.rows({ page: 'current' }).deselect();
                                    button.text('Выбрать все');
                                } else {
                                    table.rows({ page: 'current' }).select();
                                    button.text('Снять выделение');
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
                            text: 'Аннулировать',
                            className: 'btn btn-danger',
                            action: function ( e, dt, node, config ) {
                                var selectedRows = dt.rows({ selected: true }).count();
                                if (selectedRows > 0) {
                                    $('#confirmCanceledModal').modal('show');
                                } else {
                                    toastr.error('Выберите хотя бы одну строку для проведения.');
                                }
                            }
                        },
                        {
                            text: 'Сделать бесплатным',
                            className: 'btn btn-primary',
                            action: function ( e, dt, node, config ) {
                                var selectedRows = dt.rows({ selected: true }).count();
                                if (selectedRows > 0) {
                                    $('#confirmFreeModal').modal('show');
                                } else {
                                    toastr.error('Выберите хотя бы одну строку для проведения.');
                                }
                            }
                        },
                        {
                            text: 'Загрузить файл',
                            className: 'btn btn-warning',
                            action: function ( e, dt, node, config ) {
                                $('#uploadModal').modal('show');
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
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(4)').attr('contenteditable', 'true');  // Делаем редактируемой только 2-ю колонку (local_balance)
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            },
            rowCallback: function(row, data) {
                $(row).removeClass('table-row-new table-row-passed');

                if (data.status === 'Новое') {
                    console.log('table-row-new')
                    $(row).addClass('table-row-new');
                } else if (data.status === 'Проведено') {
                    console.log('table-row-passed')
                    $(row).addClass('table-row-passed');
                }
            },
            initComplete: function() {

                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const min = $('#min-date').datepicker("getDate");
                        const max = $('#max-date').datepicker("getDate");
                        const date = new Date(data[3]);

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

                @if($iframe)
                BX24.fitWindow()
                @endif

            }
        });

        // Очистка текста при начале редактирования
        $('#example tbody').on('focus', 'td[contenteditable="true"]', function() {
            $(this).text($(this).text());
        });

        // Сохранение изменений при потере фокуса
        $('#example tbody').on('blur', 'td[contenteditable="true"]', function() {
            var cellData = $(this).text().trim();  // Получаем новое значение и обрезаем пробелы
            var rowIndex = table.row($(this).closest('tr')).index();  // Индекс строки
            var columnIndex = table.cell(this).index().column;  // Индекс колонки

            // Получаем всю строку данных
            var rowData = table.row(rowIndex).data();

            var url = '{{ route("write-off.comment", ":writeOffID") }}';
            url = url.replace(':writeOffID', rowData.id);

            // Отправляем данные на сервер для сохранения
            $.ajax({
                url: url,  // URL для сохранения данных
                method: 'PATCH',
                data: {
                    row: rowData.id,  // Уникальный ID строки
                    column: columnIndex,
                    comment: cellData,
                    _token: $('meta[name="csrf-token"]').attr('content')  // CSRF-токен для Laravel
                },
                success: function(response) {
                    toastr.success(response.message);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message);
                }
            });

        });

        function updateStatusButtons() {
            let statusCounts = {};

            table.rows().data().each(function(row) {
                const status = row.status;
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

                buttonsContainer.append(`<button class="${buttonClass} status-filter me-2" data-status="${status}">${status} (${statusCounts[status]})</button>`);
            }

            $('.status-filter').on('click', function() {
                const status = $(this).data('status');
                table.column(2).search(status).draw();
            });
        }

        updateStatusButtons();

        table.on('draw.dt', function() {
            updateStatusButtons();
        });

        $('#reset-filters').on('click', function() {
            $('#min-date').val('');
            $('#max-date').val('');
            table.search('').columns().search('').draw(); // Сброс фильтров DataTables
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
                    $('#editStoreNumber').val(response.writeOff.store_number);
                    $('#editStore').val(response.writeOff.store);
                    $('#editCounteragent').val(response.writeOff.counteragent);

                    if (response.writeOff.total_detail) {
                        if (response.writeOff.total_detail.price.fruits !== undefined && response.writeOff.total_detail.price.fruits !== 0) {
                            $('#fruits_price_edit').val(response.writeOff.total_detail.price.fruits);
                        }
                        if (response.writeOff.total_detail.weight.fruits !== undefined && response.writeOff.total_detail.weight.fruits !== 0) {
                            $('#fruits_weight_edit').val(response.writeOff.total_detail.weight.fruits);
                        }
                        if (response.writeOff.total_detail.price.bread !== undefined && response.writeOff.total_detail.price.bread !== 0) {
                            $('#bread_price_edit').val(response.writeOff.total_detail.price.bread);
                        }
                        if (response.writeOff.total_detail.weight.bread !== undefined && response.writeOff.total_detail.weight.bread !== 0) {
                            $('#bread_weight_edit').val(response.writeOff.total_detail.weight.bread);
                        }
                        if (response.writeOff.total_detail.price.milk !== undefined && response.writeOff.total_detail.price.milk !== 0) {
                            $('#milk_price_edit').val(response.writeOff.total_detail.price.milk);
                        }
                        if (response.writeOff.total_detail.weight.milk !== undefined && response.writeOff.total_detail.weight.milk !== 0) {
                            $('#milk_weight_edit').val(response.writeOff.total_detail.weight.milk);
                        }
                        if (response.writeOff.total_detail.price.food_waste !== undefined && response.writeOff.total_detail.price.food_waste !== 0) {
                            $('#food_waste_price_edit').val(response.writeOff.total_detail.price.food_waste);
                        }
                        if (response.writeOff.total_detail.weight.food_waste !== undefined && response.writeOff.total_detail.weight.food_waste !== 0) {
                            $('#food_waste_weight_edit').val(response.writeOff.total_detail.weight.food_waste);
                        }
                        if (response.writeOff.total_detail.price.used_vegetable_oil !== undefined && response.writeOff.total_detail.price.used_vegetable_oil !== 0) {
                            $('#used_vegetable_oil_price_edit').val(response.writeOff.total_detail.price.used_vegetable_oil);
                        }
                        if (response.writeOff.total_detail.weight.used_vegetable_oil !== undefined && response.writeOff.total_detail.weight.used_vegetable_oil !== 0) {
                            $('#used_vegetable_oil_weight_edit').val(response.writeOff.total_detail.weight.used_vegetable_oil);
                        }
                        if (response.writeOff.total_detail.price.groceries !== undefined && response.writeOff.total_detail.price.groceries !== 0) {
                            $('#groceries_price_edit').val(response.writeOff.total_detail.price.groceries);
                        }
                        if (response.writeOff.total_detail.weight.groceries !== undefined && response.writeOff.total_detail.weight.groceries !== 0) {
                            $('#groceries_weight_edit').val(response.writeOff.total_detail.weight.groceries);
                        }
                        if (response.writeOff.total_detail.price.other !== undefined && response.writeOff.total_detail.price.other !== 0) {
                            $('#other_price_edit').val(response.writeOff.total_detail.price.other);
                        }
                        if (response.writeOff.total_detail.weight.other !== undefined && response.writeOff.total_detail.weight.other !== 0) {
                            $('#other_weight_edit').val(response.writeOff.total_detail.weight.other);
                        }
                    }

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
                    toastr.error(xhr.responseJSON.message);
                    $('#editModal').modal('hide');
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
                    toastr.success('Данные успешно отправлены!');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    toastr.error('Произошла ошибка при отправке данных.');
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
                /** Устанавливаем `title` как значение в поле `contract` */
                $("#contract").val(ui.item.value);

                /** Подставляем остальные значения в соответствующие поля */
                const selectedData = ui.item.data;
                $("#bitrix_id").val(selectedData.bitrix_id);
                $("#retailer").val(selectedData.retailer);
                $("#date").val(selectedData.date);
                $("#external").val(selectedData.shop);
                $("#store").val(selectedData.shop_address);
                $("#counteragent").val(selectedData.client);

                if (!$("#detailToggle").is(':checked')) {
                    $("#detailToggle").trigger('click');
                }


                $("#fruits_price").val(selectedData.price.price_fruits_vegetables || selectedData.price.price);
                $("#bread_price").val(selectedData.price.price_bakery || selectedData.price.price);
                $("#milk_price").val(selectedData.price.price_dairy || selectedData.price.price);
                $("#used_vegetable_oil_price").val(selectedData.price.price_used_oil || selectedData.price.price);
                $("#groceries_price").val(selectedData.price.price_grocery || selectedData.price.price);
                $("#food_waste_price").val(selectedData.price.price_waste || selectedData.price.price);
                $("#other_price").val(selectedData.price.other || selectedData.price.price);

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
                    toastr.error(xhr.responseJSON.message);
                }
            });
        });

        $('#confirmPassed').click(function() {
            var selectedRows = table.rows({ selected: true }).data();
            var writeoffs = $.map(selectedRows, function(row) {
                return row.id;
            });

            $.ajax({
                url: '{{ route('write-off.passed') }}',
                method: 'PATCH',
                data: {
                    writeoffs: writeoffs
                },
                success: function(response) {
                    table.ajax.reload();
                    $('#confirmPassedModal').modal('hide');
                },
                error: function(xhr) {
                    $('#confirmPassedModal').modal('hide');
                    toastr.error(xhr.responseJSON.message);
                }
            });
        });

        $('#confirmCanceled').click(function() {
            var selectedRows = table.rows({ selected: true }).data();
            var writeoffs = $.map(selectedRows, function(row) {
                return row.id;
            });

            $.ajax({
                url: '{{ route('write-off.canceled') }}',
                method: 'PATCH',
                data: {
                    writeoffs: writeoffs
                },
                success: function(response) {
                    table.ajax.reload();
                    $('#confirmCanceledModal').modal('hide');
                },
                error: function(xhr) {
                    $('#confirmCanceledModal').modal('hide');
                    toastr.error(xhr.responseJSON.message);
                }
            });
        });

        $('#confirmFree').click(function() {
            var selectedRows = table.rows({ selected: true }).data();
            var writeoffs = $.map(selectedRows, function(row) {
                return row.id;
            });

            $.ajax({
                url: '{{ route('write-off.free') }}',
                method: 'PATCH',
                data: {
                    writeoffs: writeoffs
                },
                success: function(response) {
                    table.ajax.reload();
                    $('#confirmFreeModal').modal('hide');
                },
                error: function(xhr) {
                    $('#confirmFreeModal').modal('hide');
                    toastr.error(xhr.responseJSON.message);
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
            var button = $(this)

            if (!supplierType || !fileInput) {
                toastr.error('Пожалуйста, выберите тип поставщика и файл для загрузки.');
                return;
            }

            var formData = new FormData();
            formData.append('supplierType', supplierType);
            formData.append('file', fileInput);

            button.attr('disabled', true);

            $.ajax({
                url: '{{ route('write-off.upload') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#uploadModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(response.message);
                    button.attr('disabled', false);

                },
                error: function(xhr, status, error) {
                    toastr.error('Произошла ошибка при загрузке файла: ' + error);
                    button.attr('disabled', false);

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
