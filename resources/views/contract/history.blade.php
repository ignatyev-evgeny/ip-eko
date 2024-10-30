
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<body>
@if(empty($balanceFrame) || !$balanceFrame)
    <div class="container">
        <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="{{ route('index') }}" class="nav-link">Главная</a></li>
                {{--            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link">Поставщики</a></li>--}}
                {{--            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link" >Клиенты</a></li>--}}
                <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link" aria-current="page">Договоры</a></li>
                <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
                <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
            </ul>
        </header>
    </div>
@endif
<div class="container-fluid py-3">
    <main class="m-auto">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body text-center">
                        {{ $contract->title }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body d-flex justify-content-center align-items-center position-relative" @if($contract->local_balance < 0) style="color: red" @endif>
                        <!-- Блок текста с балансом по центру -->
                        <div class="balance-section text-center" style="flex-grow: 1;">
                            <span id="balance-display">{{ number_format($contract->local_balance, 2, '.', ' ') }} ₽</span>
                            <input type="number" id="balance-input" value="{{ $contract->local_balance }}" style="display:none; width: 300px; text-align:center;height: 24px;">
                            <input type="hidden" id="contract" value="{{ $contract->id }}">

                        </div>

                        <!-- Кнопки справа, абсолютное позиционирование -->
                        <div class="buttons position-absolute" style="right: 10px;">
                            <button id="edit-btn" class="btn btn-warning btn-sm">Редактировать</button>
                            <button id="save-btn" class="btn btn-success btn-sm" style="display:none;">Сохранить</button>
                            <button id="cancel-btn" class="btn btn-danger btn-sm">Аннулировать</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Тип транзакции</th>
                <th scope="col">Начальный баланс</th>
                <th scope="col">Сумма транзакции</th>
                <th scope="col">Балас после транзакции</th>
                <th scope="col">Дата транзакции</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach($transactions as $transaction)
                    <tr @if($transaction->amount < 0) class="table-danger" @elseif($transaction->amount > 0) class="table-success" @else class="table-primary" @endif>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>@if($transaction->amount < 0) Расход @elseif($transaction->amount > 0) Приход @else Изменение @endif</td>
                        <td>{{ number_format($transaction->start_balance, 2, '.', ' ') }} ₽</td>
                        <td>{{ number_format($transaction->amount, 2, '.', ' ') }} ₽</td>
                        <td>{{ number_format($transaction->end_balance, 2, '.', ' ') }} ₽</td>
                        <td>{{ date('H:i:s d/m/Y', \Carbon\Carbon::parse($transaction->created_at)->timestamp) }} UTC</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>

<script>
    $(document).ready(function() {

        $('#edit-btn').click(function() {
            $('#balance-display').hide();
            $('#edit-btn').hide();
            $('#balance-input').show();
            $('#save-btn').show();
        });

        $('#balance-input').on('input', function(e) {
            let value = $(this).val();

            // Оставляем только цифры, точку и минус
            value = value.replace(/[^0-9.-]/g, '');

            // Удаляем все точки, кроме первой
            if (value.includes('.')) {
                value = value.replace(/\.(?=.*\.)/, '');
            }

            // Удаляем все минусы, кроме первого и только если он в начале
            if (value.includes('-')) {
                value = value.replace(/(?!^)-/g, '');
            }

            // Обновляем значение в поле
            $(this).val(value);
        });


        // Кнопка "Сохранить"
        $('#save-btn').click(function() {
            var newBalance = $('#balance-input').val();
            var contract = $('#contract').val();

            $.ajax({
                url: '{{ route('contract.changeBalance') }}',
                method: 'POST',
                data: {
                    row: contract,
                    column: 2,
                    value: newBalance,
                },
                success: function(response) {

                    $('#balance-display').text(newBalance + ' ₽');
                    $('#balance-display').show();
                    $('#edit-btn').show();
                    $('#balance-input').hide();
                    $('#save-btn').hide();

                    toastr.success('Баланс договора успешно отредактирован');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error('Ошибка при сохранении баланса');
                }
            });

        });

        // Кнопка "Аннулировать"
        $('#cancel-btn').click(function() {
            var contract = $('#contract').val();

            $.ajax({
                url: '{{ route('contract.changeBalance') }}',
                method: 'POST',
                data: {
                    row: contract,
                    column: 2,
                    value: 0,
                },
                success: function(response) {

                    $('#balance-display').text(0 + ' ₽');
                    $('#balance-display').show();
                    $('#edit-btn').show();
                    $('#balance-input').hide();
                    $('#save-btn').hide();

                    toastr.success('Баланс договора успешно отредактирован');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error('Ошибка при сохранении баланса');
                }
            });
        });
    });
</script>

</body>
</html>
