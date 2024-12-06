<html>
<head>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        table.iksweb{
            width: 100%;
            border-collapse:collapse;
            border-spacing:0;
            height: auto; }
        table.iksweb,table.iksweb td, table.iksweb th {
            border: 1px solid #595959;
        }
        table.iksweb td,table.iksweb th {
            min-height:35px;padding: 3px;
            width: 30px;
            height: 35px; }
        table.iksweb th {
            background: #347c99;
            color: #fff;
            font-weight: normal;
        }
    </style>
</head>
<body>
<table class="iksweb" style="font-size: 11px">
    <tbody>
    <tr>
        <td colspan="4" rowspan="2">
            КОМИ ОТДЕЛЕНИЕ N8617 ПАО СБЕРБАНК<br><br>
            <span style="text-align: left;font-size: 10px;">Банк получателя</span>
        </td>
        <td style="text-align: center">БИК</td>
        <td style="text-align: center">048702640</td>
    </tr>
    <tr>
        <td style="text-align: center">Сч. №</td>
        <td style="text-align: center">30101810400000000640</td>
    </tr>
    <tr>
        <td style="text-align: center">ИНН</td>
        <td style="text-align: center">110112027200</td>
        <td style="text-align: center">КПП</td>
        <td style="text-align: center"></td>
        <td style="text-align: center">Сч. №</td>
        <td style="text-align: center">40802810828000050817</td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            ИП КУЧАЕВ Д. Н.<br><br>
            <span style="text-align: left;font-size: 10px;"><i>Получатель</i></span>
        </td>
        <td colspan="2" rowspan="2" style="padding: 5px;text-align: center">
            <img src="data:image/png;base64, {!! base64_encode(QrCode::style('dot')->encoding('UTF-8')->format('png')->size(200)->generate($strQRCode)) !!} ">
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            {{ $invoice_reason }}<br><br>
            <span style="text-align: left;font-size: 10px;"><i>Назначение платежа</i></span>
        </td>
    </tr>
    </tbody>
</table>
<div style="font-size: 11px;padding-bottom: 10px;padding-top: 10px;font-style: italic;color: red"><b>ВАЖНО: В назначении платежа обязательно указать {{ $invoice_reason }}, иначе платеж может быть не зачислен на ваш баланс</b></div>

<div style="text-align: center;font-size: 20px;font-weight: bold;padding-top: 10px;padding-bottom: 10px">Счёт на оплату № {{ $invoice_number }} от {{ $invoice_date }}</div>
<div style="font-size: 11px;padding-top: 5px;">Поставщик: ИП КУЧАЕВ Д. Н., ИНН 110112027200, КПП , А/Я 000, Сыктывкар, Республика Коми, Россия, 167000, тел.: +7 904 271-67-09</div>
<div style="font-size: 11px;padding-bottom: 30px;">Покупатель: {{ $customer_name }}</div>
<table class="iksweb" width="100%"  cellpadding="8" style="font-size: 11px">
    <thead>
    <tr>
        <th>№</th>
        <th>Товары (работы, услуги)</th>
        <th>Кол-во</th>
        <th>Ед.</th>
        <th>Цена</th>
        <th>Сумма</th>
    </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td style="text-align: center">{{ $item['id'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center">{{ $item['quantity'] }}</td>
                <td style="text-align: center">кг.</td>
                <td style="text-align: center">{{ $item['price'] }}</td>
                <td style="text-align: center">{{ $item['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div style="padding-top: 15px;text-align: right;">
    Итого: {{ $total }}
</div>
<div style="text-align: right;font-weight: bold">
    Итого к оплате: {{ $total }}
</div>
<div style="text-align: left;padding-top: 15px;font-size: 12px">
    Итого к оплате: {{ $total_text }}
</div>

<table style="width: 100%; padding-top: 10px; position: relative;">
    <tr>
        <td style="text-align: center; width: 30%; font-size: 15px; vertical-align: top;padding-top: 100px">ИП КУЧАЕВ Д. Н.</td>
        <td style="text-align: center; width: 40%; position: relative;">
            <!-- Картинка, которая не смещает содержимое -->
            <img src="{{ $stamp_link }}" style="height: 300px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 1;">
        </td>
        <td style="text-align: center; width: 30%; font-size: 15px; vertical-align: top;padding-top: 100px">КУЧАЕВ Д. Н.</td>
    </tr>
    <tr>
        <td colspan="3" style="font-style: italic; font-size: 11px; text-align: center;">
            * Оплачивая квитанцию, вы соглашаетесь с условиями договора-аферты, опубликованного на сайте ip-eko.ru
        </td>
    </tr>
</table>
</body>
</html>
