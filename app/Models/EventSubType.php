<?php

namespace App\Models;

use App\Models\Scopes\EnabledEventSubTypeScope;
use Illuminate\Database\Eloquent\Model;

class EventSubType extends Model
{
    protected $table = 'event_sub_types';

    protected $fillable = [
        'event_type_id',
        'event_sub_type_name',
        'status'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new EnabledEventSubTypeScope);
    }
    public function scopeSearch($query, $term)
    {
        return $query->where('event_sub_type_name',  'like', '%' . $term . '%');
    }
    public function scopeSearchEventType($query, $term)
    {
        return $query->where('event_type_id',  'like', '%' . $term . '%');
    }
    public function EventType()
    {
        return $this->belongsTo(EventType::class,'event_type_id');
    }

}
