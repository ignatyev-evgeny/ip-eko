<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\WriteOff;
use Exception;
use Illuminate\Console\Command;

class WriteOffsFindToPassedCommand extends Command {
    protected $signature = 'writeoffs:find-to-passed';

    protected $description = 'Команда которая проходит по всем найденным списаниям и проводит их';

    public function handle(): void {
        try {
            $writeOffs = WriteOff::where('status', 'find')->get();

            foreach ($writeOffs as $writeOff) {

                $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($writeOff->contract) . "%")->first();

                if(empty($contract)) {
                    continue;
                }

                $contract->local_balance = $contract->local_balance - $writeOff->total_amount;
                $contract->save();

                $writeOff->status = 'passed';
                $writeOff->save();

            }

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
