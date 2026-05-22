<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalLossReport extends Model
{
    protected $fillable = [
        'asset_loss_report_id',

        // Section 1: Asset Details
        'asset_tag_no',
        'asset_description',
        'asset_category',
        'asset_serial_no',
        'asset_location_registered',
        'last_custodian',

        // Section 2: Loss Description
        'incident_description',
        'incident_date',
        'incident_time',
        'incident_location_details',
        'incident_circumstances',

        // Section 3: Police Findings
        'police_investigation_findings',
        'police_officer_name',
        'police_station',
        'police_report_ref',

        // Section 4: Witness Statements
        'witness_1_statement',
        'witness_1_name',
        'witness_2_statement',
        'witness_2_name',

        // Section 5: Procedural Compliance
        'complied_with_procedures',
        'procedural_compliance_notes',
        'procedural_gaps',

        // Section 6: Prevention Steps
        'prevention_actions_taken',
        'prevention_recommendations',

        // Section 7: Investigation Summary
        'investigation_conclusion',

        // Section 8: Recommendation
        'recommended_action',
        'recommendation_rationale',
    ];

    protected $casts = [
        'incident_date'            => 'date',
        'complied_with_procedures' => 'boolean',
    ];

    /**
     * Get the parent loss report for this final investigation.
     */
    public function assetLossReport(): BelongsTo
    {
        return $this->belongsTo(AssetLossReport::class);
    }
}
