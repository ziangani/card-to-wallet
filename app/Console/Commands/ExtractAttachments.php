<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use eXorus\PhpMimeMailParser\Parser as MimeParser;
use Illuminate\Support\Facades\Storage;

class ExtractAttachments extends Command
{
    protected $signature = 'extract:attachments';
    protected $description = 'Extracts attachments from .msg files in storage';

    public function handle()
    {
        $inputFolder = storage_path('app/email/raw');
        $outputFolder = storage_path('app/email/processed');

        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        // Get all .msg files
        $files = glob($inputFolder . '/*.msg');

        if (empty($files)) {
            $this->info('No .msg files found.');
            return;
        }

        foreach ($files as $file) {
            $parser = new MimeParser();
            $parser->setPath($file);

            foreach ($parser->getAttachments() as $attachment) {
                $attachmentPath = $outputFolder . '/' . $attachment->getFilename();
                file_put_contents($attachmentPath, $attachment->getContent());
                $this->info("Extracted: " . $attachment->getFilename() . " from " . basename($file));
            }

            // Move processed .msg file
            rename($file, $outputFolder . '/' . basename($file));
        }

        $this->info('All attachments extracted successfully.');
    }
}
