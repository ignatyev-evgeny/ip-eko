
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<body>
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
                    <div class="card-body text-center" @if($contract->local_balance < 0) style="color: red" @endif>
                        {{ number_format($contract->local_balance, 2, '.', ' ') }} ₽
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

</body>
</html>
