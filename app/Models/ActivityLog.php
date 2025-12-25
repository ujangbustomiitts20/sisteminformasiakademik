<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function setOldValuesAttribute($value)
    {
        $this->attributes['old_values'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setNewValuesAttribute($value)
    {
        $this->attributes['new_values'] = is_array($value) ? json_encode($value) : $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Log an activity
     */
    public static function log($action, $description = null, $model = null, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Log a login action
     */
    public static function logLogin($user)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'User ' . $user->name . ' logged in',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Log a logout action
     */
    public static function logLogout($user)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'description' => 'User ' . $user->name . ' logged out',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Log a create action
     */
    public static function logCreate($model, $description = null)
    {
        return self::log('create', $description ?? 'Created ' . class_basename($model), $model, null, $model->toArray());
    }

    /**
     * Log an update action
     */
    public static function logUpdate($model, $oldValues, $description = null)
    {
        return self::log('update', $description ?? 'Updated ' . class_basename($model), $model, $oldValues, $model->toArray());
    }

    /**
     * Log a delete action
     */
    public static function logDelete($model, $description = null)
    {
        return self::log('delete', $description ?? 'Deleted ' . class_basename($model), $model, $model->toArray(), null);
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'primary',
            'update' => 'warning',
            'delete' => 'danger',
            'view' => 'info'
        ];

        return $badges[$this->action] ?? 'secondary';
    }
}
