<?php

namespace App\Events;

use App\Models\AssetLossReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LossReportStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AssetLossReport $lossReport;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(AssetLossReport $lossReport, string $oldStatus, string $newStatus)
    {
        $this->lossReport = $lossReport;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
