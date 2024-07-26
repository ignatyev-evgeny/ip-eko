
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

</style>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="{{ route('index') }}" class="nav-link">Главная</a></li>
            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link" >Поставщики</a></li>
            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link">Клиенты</a></li>
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
                            <div class="mb-3">
                                <label for="status" class="form-label">Статус</label>
                                <input type="text" class="form-control" name="status" id="status">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="datetime" class="form-label">Дата платежа</label>
                                <input type="datetime-local" name="datetime" class="form-control" id="datetime">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="number" class="form-label">Номер</label>
                                <input type="text" class="form-control" name="number" id="number">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Поступило</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="amount" id="amount">
                                    <span class="input-group-text">₽</span>
                                    <span class="input-group-text">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="counteragent" class="form-label">Контрагент</label>
                                <input type="text" class="form-control" name="counteragent" id="counteragent">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="counteragent_bank_account" class="form-label">Контрагент р/с</label>
                                <input type="text" class="form-control" name="counteragent_bank_account" id="counteragent_bank_account">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label for="contract" class="form-label">Договор</label>
                            <input type="text" class="form-control contractInput" name="contract" id="contract" aria-describedby="contractInput">
                            <div class="mb-3"></div>
                        </div>

                        <div class="col-4">
                            <div class="mb-3">
                                <label for="payment_purpose" class="form-label">Назначение платежа</label>
                                <input type="text" class="form-control" name="payment_purpose" id="payment_purpose">
                            </div>
                        </div>
                        <div class="col-4">
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
</div>

<script>

    $(document).ready( function () {

        BX24.init(function(){
            console.log('Инициализация завершена!', BX24.isAdmin());
        });

        BX24.fitWindow()

        const table = $('#example').DataTable({
            scrollX: true,
            pageLength: 100,
            ajax: '{{ route('entry.data') }}',
            processing: true,
            columns: [
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
