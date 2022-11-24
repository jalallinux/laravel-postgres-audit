<?php

namespace JalalLinuX\PostgreAudit;

use Illuminate\Database\Eloquent\Model;

class PGAudit extends Model
{
    protected $primaryKey = '_id';
    protected $casts = [
        'changed_at' => 'datetime',
        'changed' => 'array',
    ];

    public function getTable(): string
    {
        return config('pg_audit.table_name');
    }

    public function getChangedFromAttribute()
    {
        return $this->changed['from'];
    }

    public function getChangedToAttribute()
    {
        return $this->changed['to'];
    }
}
