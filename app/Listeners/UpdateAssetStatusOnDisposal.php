<?php

namespace App\Listeners;

use App\Events\DisposalStatusChanged;

class UpdateAssetStatusOnDisposal
{
    public function handle(DisposalStatusChanged $event): void
    {
        $disposal = $event->disposal;
        $asset = $disposal->asset;

        if (!$asset) {
            return;
        }

        // When a disposal is approved or completed, mark the asset as disposed
        if (in_array($event->newStatus, ['approved', 'completed'])) {
            $asset->status = 'disposed';
            $asset->save();
        }

        // When reverted from approved/completed to draft/cancelled, reset
        if (in_array($event->newStatus, ['draft', 'cancelled']) && 
            in_array($event->oldStatus, ['approved', 'completed'])) {
            $asset->status = 'active';
            $asset->save();
        }
    }
}
