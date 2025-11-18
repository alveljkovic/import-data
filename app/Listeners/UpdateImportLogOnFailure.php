<?php

namespace App\Listeners;

use App\Events\ImportFailed;
use App\Mail\ImportFailedMail;
use App\Models\DataImportLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UpdateImportLogOnFailure implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ImportFailed $event): void
    {
        $event->importLogger->updateLog([
            'status' => 'unsuccessful',
            'completed_at' => now()
        ]);

        $this->notifyUser($event->log);
        Storage::delete($event->filePath);
    }

    /**
     * Notify user about failed import
     *
     * @param DataImportLog $log
     * @return void
     */
    protected function notifyUser(DataImportLog $log): void
    {
        if (!$log->user || !$log->user->email) {
            return;
        }

        Mail::to($log->user->email)->send(new ImportFailedMail($log));
    }
}
