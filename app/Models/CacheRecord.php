<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CacheRecord extends Model
{
    protected $fillable = [
        'key',
        'type',
        'subtype',
        'value',
        'expiration',
        'created_by'
    ];

    protected $casts = [
        'value' => 'array',
        'expiration' => 'integer'
    ];

    public static function put(string $key, $value, string $type, ?string $subtype = null, ?int $seconds = null, ?string $createdBy = null)
    {
        $expiration = $seconds ? now()->addSeconds($seconds)->timestamp : null;
        
        return static::updateOrCreate(
            ['key' => $key],
            [
                'type' => $type,
                'subtype' => $subtype,
                'value' => $value,
                'expiration' => $expiration,
                'created_by' => $createdBy ?? 'system'
            ]
        );
    }

    public static function get(string $key)
    {
        $record = static::where('key', $key)
            ->where(function ($query) {
                $query->whereNull('expiration')
                    ->orWhere('expiration', '>', now()->timestamp);
            })
            ->first();

        return $record ? $record->value : null;
    }

    public static function getByType(string $type, ?string $subtype = null)
    {
        $query = static::where('type', $type)
            ->where(function ($query) {
                $query->whereNull('expiration')
                    ->orWhere('expiration', '>', now()->timestamp);
            });

        if ($subtype) {
            $query->where('subtype', $subtype);
        }

        return $query->get();
    }

    public static function getWithTimestamp(string $key): array
    {
        $record = static::where('key', $key)
            ->where(function ($query) {
                $query->whereNull('expiration')
                    ->orWhere('expiration', '>', now()->timestamp);
            })
            ->first();

        if (!$record) {
            return [
                'data' => null,
                'updated_at' => null,
                'created_by' => null,
                'type' => null,
                'subtype' => null
            ];
        }

        return [
            'data' => $record->value,
            'updated_at' => $record->updated_at,
            'created_by' => $record->created_by,
            'type' => $record->type,
            'subtype' => $record->subtype
        ];
    }

    public static function clearByType(string $type, ?string $subtype = null)
    {
        $query = static::where('type', $type);
        
        if ($subtype) {
            $query->where('subtype', $subtype);
        }
        
        return $query->delete();
    }
}
