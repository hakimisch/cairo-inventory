<?php

namespace App\Listeners;

use App\Events\LossReportStatusChanged;

class UpdateAssetStatusOnLossReport
{
    public function handle(LossReportStatusChanged $event): void
    {
        $lossReport = $event->lossReport;
        $asset = $lossReport->asset;

        if (!$asset) {
            return;
        }

        // When a loss report is approved/closed, mark the asset as lost
        if (in_array($event->newStatus, ['approved', 'closed'])) {
            $asset->status = 'lost';
            $asset->save();
        }

        // When reverted from approved/closed to investigation/draft, reset
        if (in_array($event->newStatus, ['under_investigation', 'draft']) && 
            in_array($event->oldStatus, ['approved', 'closed'])) {
            $asset->status = 'active';
            $asset->save();
        }
    }
}
