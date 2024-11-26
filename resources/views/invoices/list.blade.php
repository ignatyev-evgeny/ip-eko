
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
                <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
                <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Счета</a></li>
                <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
                <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
            </ul>
        </header>
    </div>
@endif
<div class="container-fluid py-3">
    <main class="m-auto">
        <table class="table">
            <thead>
            <tr>
                <th scope="col" class="text-center" >#</th>
                <th scope="col" class="text-center" >Договор</th>
                <th scope="col" class="text-center" >Кол-во транзакций</th>
                <th scope="col" class="text-center" >Дата создания</th>
                <th scope="col" class="text-center" >Дата генерации</th>
                <th scope="col" class="text-center" >Дата отправки</th>
                <th scope="col" class="text-center" style="width: 150px">Действия</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            @foreach($invoices as $invoice)
                <tr @if($invoice->paid) class="table-success" @endif>
                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                    <td class="text-center align-middle">{{ $invoice->contract->title }}</td>
                    <td class="text-center align-middle" >{{ $invoice->writeOffs->count() }}</td>
                    <td class="text-center align-middle" >{{ $invoice->date_created ? Carbon\Carbon::parse($invoice->date_created)->format("d-m-Y H:i:s") : "-//-" }}</td>
                    <td class="text-center align-middle" >{{ $invoice->date_generated ? \Carbon\Carbon::parse($invoice->date_generated)->format("d-m-Y H:i:s") : "-//-" }}</td>
                    <td class="text-center align-middle" >{{ $invoice->date_sent ? \Carbon\Carbon::parse($invoice->date_sent)->format("d-m-Y H:i:s") : "-//-" }}</td>
                    <td class="text-center align-middle" >
                        <a href="{{ route('contract.invoice.transactions', ['invoice' => $invoice->id]) }}"><button type="button" class="btn btn-sm btn-warning" style="width: 150px">Транзакции</button></a><br>
                        <button type="button" disabled class="btn btn-sm btn-primary" style="width: 150px;margin-top: 5px">Счет</button><br>
                        <button type="button" disabled class="btn btn-sm btn-dark" style="width: 150px;margin-top: 5px">Сгенерировать</button><br>
                        <button type="button" disabled class="btn btn-sm btn-success" style="width: 150px;margin-top: 5px">Отправить</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </main>
</div>

</body>
</html>
