
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
                <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link active" aria-current="page">Договоры</a></li>
                <li class="nav-item"><a href="{{ route('invoices.list') }}" class="nav-link">Счета</a></li>
                <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
                <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
            </ul>
        </header>
    </div>
@endif
<div class="container-fluid py-3">
    <main class="m-auto">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        Счёт №{{ $invoice->id }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center">
                        Создан: {{ $invoice->date_created }}
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center">
                        Документ от: {{ $invoice->date_generated ?? "Не сгенерирован" }}
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center">
                        Отправлен: {{ $invoice->date_sent ?? "Не отправлен" }}
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center">
                        Оплачен: {{ $invoice->date_paid ?? "Не оплачен" }}
                    </div>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class="text-center" >#</th>
                    <th scope="col" class="text-center" >Дата</th>
                    <th scope="col" class="text-center" >Комментарий</th>
                    <th scope="col" class="text-center" >Магазин</th>
                    <th scope="col" class="text-center" >Отгружено</th>
                    <th scope="col" class="text-center" >Сумма</th>
                    <th scope="col" class="text-center" >Цена ≈</th>
                    <th scope="col" class="text-center" >Контрагент</th>
                    <th scope="col" class="text-center" >Ретейлер</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach($transactions as $transaction)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($transaction->writeOff->date)->format("d-m-Y") }}</td>
                        <td class="text-center align-middle">{{ $transaction->writeOff->comment }}</td>
                        <td class="text-center align-middle">{{ $transaction->writeOff->store_number }} {{ $transaction->store }}</td>
                        <td class="text-center align-middle">{{ number_format($transaction->writeOff->total_weight, 2, '.', '') }} кг.</td>
                        <td class="text-center align-middle">{{ number_format($transaction->writeOff->total_amount, 2, '.', ' ') }} ₽</td>
                        <td class="text-center align-middle">≈ {{ number_format($transaction->writeOff->total_amount / $transaction->writeOff->total_weight, 2, '.', ' ') }} ₽</td>
                        <td class="text-center align-middle">{{ $transaction->writeOff->counteragent }}</td>
                        <td class="text-center align-middle">{{ $transaction->writeOff->retailer }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center font-weight-bold"><b>Итого:</b></td>
                    <td class="text-center font-weight-bold"><b>{{ number_format($totalWeight, 2, '.', ' ') }} кг.</b></td>
                    <td class="text-center font-weight-bold"><b>{{ number_format($totalAmount, 2, '.', ' ') }} ₽</b></td>
                    <td class="text-center font-weight-bold"><b>≈ {{ number_format($averagePrice, 2, '.', ' ') }} ₽</b></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </main>
</div>

</body>
</html>
