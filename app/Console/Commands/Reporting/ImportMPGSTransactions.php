<?php

namespace App\Console\Commands\Reporting;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\AllTransactions;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportMPGSTransactions extends Command
{
    protected $signature = 'reporting:import-mpgs';
    protected $description = 'Import MPGS transactions from storage folder';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $inFolder = 'mpgs_reports' . DIRECTORY_SEPARATOR . 'in' . DIRECTORY_SEPARATOR;
        $processedFolder = 'mpgs_reports' . DIRECTORY_SEPARATOR . 'processed' . DIRECTORY_SEPARATOR;
        $errorFolder = 'mpgs_reports' . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR;

        $files = Storage::files($inFolder);
        $this->info('Found ' . count($files) . ' files to process.');
        $failed = 0;
        $successful = 0;
        //sort files by date to get the newest first
        usort($files, function ($a, $b) {
            return filemtime(storage_path('app' . DIRECTORY_SEPARATOR . $b)) - filemtime(storage_path('app' . DIRECTORY_SEPARATOR . $a));
        });

        foreach ($files as $file) {
            $this->info('Processing file: ' . $file);
            try {
                $csv = Reader::createFromPath(storage_path('app' . DIRECTORY_SEPARATOR . $file), 'r');
                $csv->setHeaderOffset(0);
                $records = (new Statement())->process($csv);

                $this->output->progressStart(count($records));
                foreach ($records as $record) {
                    $this->output->progressAdvance();
                    try {

                        $existingTransaction = AllTransactions::where('txn_id', $record['txn_id'])
                            ->where('order_id', $record['order_id'])
                            ->where('source', 'MPGS')
                            ->where('card_number', $record['card_number'])
                            ->where('txn_type', $record['type'])
                            ->where('txn_date', date('Y-m-d H:i:s', strtotime($record['time'])))
                            ->where('txn_amount', $record['amount'])
                            ->where('result', $record['result'])
                            ->where('response_acquirer_code', $record['acquirerCode'])
                            ->first();

                        if ($existingTransaction) {
//                            $this->info("\nDuplicate transaction found for order_id: " . $record['order_id']);
                            continue;
                        }

                        $transaction = new AllTransactions();
                        $transaction->source = 'MPGS';
                        $transaction->merchant = $record['merchant'];
                        $transaction->result = $record['result'];
                        $transaction->txn_date = date('Y-m-d H:i:s', strtotime($record['time']));
                        $transaction->order_id = $record['order_id'];
                        $transaction->txn_id = $record['txn_id'];
                        $transaction->card_number = $record['card_number'];
                        $transaction->card_expiry_month = $record['card_expiry_month'];
                        $transaction->card_expiry_year = $record['card_expiry_year'];
                        $transaction->txn_amount = $record['amount'];
                        $transaction->txn_currency = $record['currency'];
                        $transaction->txn_type = $record['type'];
                        $transaction->txn_acquirer_id = $record['acquirer'];
                        $transaction->response_acquirer_code = $record['acquirerCode'];
                        $transaction->raw_data = json_encode($record);
                        $transaction->status = 'PROCESSED';
                        $transaction->save();
                        $successful++;
                    } catch (\Exception $e) {
                        $failed++;
//                        $this->error("Failed to process record: " . json_encode($record) . ". Error: " . $e->getMessage());
                    }
                }
                $this->output->progressFinish();

                Storage::move($file, $processedFolder . basename($file));
                $this->info(count($records) . ' processed; ' . $failed . ' failed; ' . $successful . ' successful.');
            } catch (\Exception $e) {
                Storage::move($file, $errorFolder . basename($file));
                $this->error("Failed to process file: $file. Error: " . $e->getMessage());
            }
        }

        $this->info('MPGS transactions import completed.');
    }
}

//      $table->string('merchant_id')->nullable();
//            $table->string('result')->nullable();
//            $table->string('order_currency')->nullable();
//            $table->timestamp('time_of_record')->nullable();
//            $table->string('order_id')->nullable();
//            $table->string('txn_id')->nullable();
//            $table->string('card_number')->nullable();
//            $table->string('card_expiry_month')->nullable();
//            $table->string('card_expiry_year')->nullable();
//            $table->decimal('txn_amount', 15, 2)->nullable();
//            $table->string('txn_currency')->nullable();
//            $table->string('txn_type')->nullable();
//            $table->string('txn_acquirer_id')->nullable();
//            $table->string('response_acquirer_code')->nullable();
//            $table->timestamp('submit_time_utc')->nullable();
//            $table->string('application_name')->nullable();
//            $table->string('reason_code')->nullable();
//            $table->string('r_code')->nullable();
//            $table->string('r_flag')->nullable();
//            $table->string('reconciliation_id')->nullable();
//            $table->string('r_message')->nullable();
//            $table->string('return_code')->nullable();
//            $table->string('client_reference_code')->nullable();
//            $table->string('eci_raw')->nullable();
//            $table->string('bill_to_address1')->nullable();
//            $table->string('bill_to_state')->nullable();
//            $table->string('bill_to_city')->nullable();
//            $table->string('bill_to_country')->nullable();
//            $table->string('bill_to_postal_code')->nullable();
//            $table->string('bill_to_email')->nullable();
//            $table->string('bill_to_phone_number')->nullable();
//            $table->string('bill_to_first_name')->nullable();
//            $table->string('bill_to_last_name')->nullable();
//            $table->decimal('amount_details_total_amount', 15, 2)->nullable();
//            $table->string('amount_details_currency')->nullable();
//            $table->string('payment_type')->nullable();
//            $table->string('payment_method')->nullable();
//            $table->string('card_suffix')->nullable();
//            $table->string('card_prefix')->nullable();
//            $table->string('card_type')->nullable();
//            $table->string('commerce_indicator')->nullable();
//            $table->string('commerce_indicator_label')->nullable();
//            $table->string('processor_name')->nullable();
//            $table->string('approval_code')->nullable();
//            $table->string('terminal_id')->nullable();
//            $table->json('raw_data')->nullable();
