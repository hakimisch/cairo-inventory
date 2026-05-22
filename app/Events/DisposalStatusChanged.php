<?php

namespace App\Events;

use App\Models\AssetDisposal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisposalStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AssetDisposal $disposal;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(AssetDisposal $disposal, string $oldStatus, string $newStatus)
    {
        $this->disposal = $disposal;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
