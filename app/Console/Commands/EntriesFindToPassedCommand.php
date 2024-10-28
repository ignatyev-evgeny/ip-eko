<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\Entry;
use App\Models\WriteOff;
use DB;
use Exception;
use Illuminate\Console\Command;

class EntriesFindToPassedCommand extends Command {
    protected $signature = 'entries:find-to-passed';

    protected $description = 'Команда которая проходит по всем найденным поступления и проводит их';

    public function handle(): void {

        DB::beginTransaction();

        try {
            $entries = Entry::where('status', 'contract')->get();

            foreach ($entries as $entry) {

                $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($entry->contract) . "%")->first();

                if(empty($contract)) {
                    continue;
                }

                $balanceSnapshot = $contract->local_balance;
                $contract->local_balance = $contract->local_balance + $entry->amount;
                $contract->save();

                $entry->status = 'passed';
                $entry->save();

                ContractsBalanceHistory::create([
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => $entry->amount,
                    'end_balance' => $balanceSnapshot + $entry->amount,
                ]);

            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);

        }
    }
}
