<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class invoicesController extends Controller {
    public function list(Request $request) {
        try {
            return view('invoices.list', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                'invoices' => Invoice::all()
            ]);
        } catch (Exception $exception) {
            report($exception);
            dd($exception);
        }
    }

    public function generate(Invoice $invoice, $generate) {
        try {
            $writeOffs = [];
            foreach ($invoice->writeOffs as $writeOff) {
                $writeOffs[] = $writeOff->writeOff->toArray();
            }

            $writeOffs = collect($writeOffs);

            $totalWeightSum = $writeOffs->sum(function ($item) {
                return (float) $item['total_weight'];
            });

            $totalAmountSum = $writeOffs->sum(function ($item) {
                return (float) $item['total_amount'];
            });

            $totalAmountSum = number_format($totalAmountSum, 2, '.', '');

            $reason = $invoice->contract->shop.'/'.$invoice->contract->number.'/'.$invoice->contract->client;

            $strQRCode = 'ST00012|Name=ИП Кучаев Дмитрий Николаевич|PersonalAcc=40802810828000050817|BankName=Коми отделение №8617  ПАО "Сбербанк"|BIC=048702640|CorrespAcc=30101810400000000640|PayeeINN=110112027200|KPP=|persAcc=';
            $strQRCode .= $invoice->contract->shop.'|LASTNAME=|payerAddress=';
            $strQRCode .= $invoice->contract->shop_address.'|Purpose=';
            $strQRCode .= $reason;
            $strQRCode .= '|Sum='.str_replace(".", "", $totalAmountSum);

            $data = [
                'invoice_number' => $invoice->id,
                'invoice_date' => Carbon::parse($invoice->date_created)->format('d-m-Y'),
                'customer_name' => $invoice->contract->client,
                'invoice_reason' => $reason,
                'strQRCode' => mb_convert_encoding($strQRCode, 'UTF-8', 'auto'),
                'stamp_link' => asset('assets/img/invoice/stamp.png'),
                'logo_link' => asset('assets/img/logo.png'),
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'За предыдущий период с '.Carbon::parse($writeOffs->min('date'))->format('d-m-Y').' по '.Carbon::parse($writeOffs->max('date'))->format('d-m-Y').' с МАГ. '.$reason.' вывезено '.$totalWeightSum.' кг. на общую сумму '.$totalAmountSum.' руб.',
                        'quantity' => $totalWeightSum,
                        'price' => round($totalAmountSum / $totalWeightSum, 2),
                        'total' => number_format($totalAmountSum, 2, '.', ' '),
                    ],
                ],
                'total' => number_format($totalAmountSum, 2, '.', ' ').' ₽',
                'total_text' => getAmountInWords($totalAmountSum),
            ];

            $html = View::make('invoices.templates.fact', $data)->render();
            $pdf = Pdf::loadHTML($html);

            if($generate == 'true') {
                $invoice->generated = true;
                $invoice->date_generated = Carbon::now()->toDateTimeString();
                $invoice->save();
            }

            return $pdf->stream('invoice.pdf');
        } catch (Exception $exception) {
            report($exception);
            dd($exception);
        }
    }
}
