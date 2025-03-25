<?php

namespace App\Console\Commands;

use App\Models\Emails;
use Illuminate\Console\Command;
use Mail;

class sendEmail extends Command
{

    protected $signature = 'sendemails';
    protected $description = 'Command description';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $pendingEmails = Emails::whereIn('status', ['PENDING'])->orderBy('id', 'desc')->get();
            $this->info(count($pendingEmails) . ' pending Emails found.');
            foreach ($pendingEmails as $email) {
                if ($email->email == '') {
                    $email->status = 'INVALID';
                    $email->save();
                    $this->info('Invalid address..skipping...');
                }
                $this->info("\nSending " . str_replace('_', ' ', $email->subject) . " to $email->email ");
                try {
                    $subject = $email->subject;
                    $rec_email = $email->email;
                    $rec_emails = explode(',', $rec_email);
                    foreach ($rec_emails as $rec_email) {
                        if (trim($rec_email) == '')
                            continue;
                        $enc_data = (is_string($email->data)) ? json_decode($email->data, true) : $email->data;

                        \Mail::send($email->view, $enc_data, function ($message) use ($rec_email, $subject) {
                            $message->to($rec_email)->subject($subject);
                        });
                    }
                } catch (\Exception $ex) {
                    $this->error('Send failed: ' . $ex->getMessage());
                    $email->status = 'FAILED';
                    $email->response = $ex->getMessage();
                    $email->save();
                    continue;
                }
                $email->status = 'COMPLETE';
                $email->save();
                $this->info('Email successfully sent');
            }
        } catch (\Exception $ex) {
            $this->error('Something went wrong: ' . $ex->getMessage() . ' ' . $ex->getFile() . ' ' . $ex->getLine());
        }
    }

}

