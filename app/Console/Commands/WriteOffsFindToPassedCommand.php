<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\Invoice;
use App\Models\InvoiceWriteOffs;
use App\Models\WriteOff;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Console\Command;

class WriteOffsFindToPassedCommand extends Command {
    protected $signature = 'writeoffs:find-to-passed';

    protected $description = 'Команда которая проходит по всем найденным списаниям и проводит их';

    public function handle(): void {

        DB::beginTransaction();

        try {
            $writeOffs = WriteOff::where('status', 'find')->get();

            foreach ($writeOffs as $writeOff) {

                if($writeOff->total_amount <= 0) {
                    continue;
                }

                if(empty($writeOff->contract_id)) {
                    continue;
                }

                $contract = Contract::find($writeOff->contract_id);

                if(empty($contract)) {
                    continue;
                }

                $balanceSnapshot = $contract->local_balance;
                $contract->local_balance = $contract->local_balance - $writeOff->total_amount;
                $contract->save();

                $writeOff->status = 'passed';
                $writeOff->save();

                ContractsBalanceHistory::create([
                    'type' => 'writeOff',
                    'type_relation' => $writeOff->id,
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => -$writeOff->total_amount,
                    'end_balance' => $balanceSnapshot - $writeOff->total_amount,
                    'comment' => "Отгрузка / {$writeOff->date} / BH {$writeOff->store_number}",
                ]);

                $invoice = Invoice::firstOrCreate([
                    'contract_id' => $contract->id,
                    'generated' => false,
                    'date_created' => Carbon::now()->toDateTimeString(),
                ]);

                InvoiceWriteOffs::updateOrCreate([
                    'invoice_id' => $invoice->id,
                    'write_off_id' => $writeOff->id,
                ]);

            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);

        }
    }
}
