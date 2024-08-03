
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
                    <div class="row">
                        <div class="col-6">
                            <label for="contract" class="form-label">Договор</label>
                            <input type="text" class="form-control contractInput" name="contract" id="contract" aria-describedby="contractInput">
                            <div class="mb-3"></div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="retailer" class="form-label">Ретейлер</label>
                                <input type="text" class="form-control" name="retailer" id="retailer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="date" class="form-label">Дата</label>
                                <input type="datetime-local" name="date" class="form-control" id="date">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="total_weight" class="form-label">Отгружено</label>
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
            pageLength: 100,
            processing: true,
            ajax: '{{ route('write-off.data') }}',
            columns: [
                { data: 'external' },
                { data: 'date' },
                { data: 'store' },
                { data: 'total_weight' },
                { data: 'total_amount' },
                { data: 'counteragent' },
                { data: 'contract' },
                { data: 'retailer' },
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            }
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
