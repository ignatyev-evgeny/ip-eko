<style>
    table {
        border-collapse: collapse; /* Убирает двойные линии */
    }
    th, td {
        padding: 10px; /* Отступ внутри ячеек */
        border: 1px solid black; /* Границы ячеек */
    }
</style>

<table>
    <thead>
        <th>ID</th>
        <th>number</th>
        <th style="width: 300px">uuid / operationId</th>
        <th style="width: 100px">amount</th>
        <th>correspondingAccount</th>
        <th style="width: 200px">document / operation</th>
        <th>paymentPurpose</th>
        <th>payeeName</th>
        <th>payerAccount</th>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr @if(!$transaction->is_found) style="background-color: red;color: white" @endif>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transaction->number }}</td>
                <td style="text-align: center">{{ $transaction->uuid }}<br>{{ $transaction->operationId }}</td>
                <td>{{ $transaction->amountRub->amount }} {{ $transaction->amountRub->currencyName }}</td>
                <td>{{ $transaction->correspondingAccount }}</td>
                <td>{{ $transaction->documentDate }}<br>{{ $transaction->operationDate }}</td>
                <td>{{ $transaction->paymentPurpose }}</td>
                <td>{{ $transaction->rurTransfer->payerName }}</td>
                <td>{{ $transaction->rurTransfer->payerAccount }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
