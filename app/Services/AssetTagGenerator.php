<?php

namespace App\Services;

use App\Models\Receiving;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for asset tag and receiving-number generation.
 *
 * Previously each flow (pipeline verify, scanner, legacy accept/store)
 * generated its own format with count()+1 and no locking — producing
 * divergent formats (AST-, CAIRO/…, RC-, REC-, REJ-) and race conditions.
 *
 * Formats (unified):
 *   asset tag:    AST-YYYYMMDD-NNNNNN   (6-digit daily sequence, Cache::lock)
 *   receive no:   REC-YYYYMMDD-NNNN     (4-digit daily sequence, Cache::lock)
 */
class AssetTagGenerator
{
    public function assetTag(string $prefix = 'AST'): string
    {
        $lock = Cache::lock('asset-tag', 10);
        $lock->block(5);

        try {
            $today = date('Ymd');
            // One parsing rule: sequence is everything after "PREFIX-YYYYMMDD-"
            // (position strlen(prefix) + 11, 1-indexed: PREFIX(3) + '-' + 8 digits + '-').
            $maxSeq = DB::table('assets')
                ->where('asset_tag', 'LIKE', $prefix . '-' . $today . '-%')
                ->max(DB::raw('CAST(SUBSTR(asset_tag, ' . (strlen($prefix) + 11) . ') AS INTEGER)'));

            return $prefix . '-' . $today . '-' . str_pad(($maxSeq ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        } finally {
            $lock->release();
        }
    }

    public function receiveNo(string $prefix = 'REC'): string
    {
        $lock = Cache::lock('receiving-number', 10);
        $lock->block(5);

        try {
            $seq = Receiving::whereDate('created_at', today())->count() + 1;

            return $prefix . '-' . date('Ymd') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        } finally {
            $lock->release();
        }
    }
}
