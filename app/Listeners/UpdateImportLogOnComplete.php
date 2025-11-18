<?php

namespace App\Listeners;

use App\Events\ImportCompleted;
use App\Mail\ImportCompletedMail;
use App\Models\DataImportLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UpdateImportLogOnComplete implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ImportCompleted $event): void
    {
        $event->importLogger->updateLog([
            'status' => 'successful',
            'completed_at' => now()
        ]);

        $this->notifyUser($event->log);
        Storage::delete($event->filePath);
    }

    /**
     * Notify user aboute successful import
     *
     * @param DataImportLog $log
     * @return void
     */
    protected function notifyUser(DataImportLog $log): void
    {
        if (!$log->user || !$log->user->email) {
            return;
        }

        Mail::to($log->user->email)->send(new ImportCompletedMail($log));
    }
}
