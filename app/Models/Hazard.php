<?php

namespace App\Models;

use App\Enums\HazardStatus;
use Illuminate\Database\Eloquent\Model;

class Hazard extends Model
{
    protected $table = 'hazard_reports';
    protected $fillable = [
        'event_type_id',
        'event_sub_type_id',
        'status',
        'department_id',
        'contractor_id',
        'penanggung_jawab_id',
        'pelapor_id',
        'manualPelaporName',
        'location_id',
        'location_specific',
        'tanggal',
        'description',
        'doc_deskripsi',
        'immediate_corrective_action',
        'doc_corrective',
        'key_word',
        'kondisi_tidak_aman_id',
        'tindakan_tidak_aman_id',
        'consequence_id',
        'likelihood_id',
        'risk_level',
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function eventSubType()
    {
        return $this->belongsTo(EventSubType::class, 'event_sub_type_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function consequence()
    {
        return $this->belongsTo(RiskConsequence::class);
    }

    public function likelihood()
    {
        return $this->belongsTo(Likelihood::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function assignedErms()
    {
        return $this->belongsToMany(User::class, 'hazard_erm_assignments', 'hazard_id', 'erm_id');
    }
    // Scope untuk filter status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function resolveCompanyId()
    {
        return $this->department->company_id ?? $this->contractor->company_id ?? null;
    }
}
