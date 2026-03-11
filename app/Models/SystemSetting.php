<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Scope by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope by group.
     */
    public function scopeOfGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Set a setting value (create or update).
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        $setting = static::byKey($key)->ofGroup($group)->first();

        if ($setting) {
            $setting->update(compact('value', 'type'));
            return $setting;
        }

        return static::create([
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);
    }

    /**
     * Get a setting value by key and group.
     */
    public static function get(string $key, string $group = 'general', $default = null)
    {
        $setting = static::byKey($key)->ofGroup($group)->first();

        return $setting ? $setting->value : $default;
    }
}
