<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    protected function getModuleName(Model $model): string
    {
        return strtolower(class_basename($model));
    }

    protected function logAction(Model $model, string $action)
    {
        // Don't log if running from console/seeder and no user is authenticated
        if (app()->runningInConsole()) {
            return;
        }

        $user = request()->user();
        if (!$user) {
            // For endpoints that don't use Sanctum properly, we might extract from request or skip
            // The frontend usually passes auth user data or we can just log 'System'
        }

        ActivityLog::create([
            'user_id'     => $user ? $user->id : null,
            'user_name'   => $user ? $user->name : 'System',
            'user_role'   => $user ? $user->role : null,
            'action'      => $action,
            'module'      => $this->getModuleName($model),
            'target_id'   => $model->getKey(),
            'target_name' => $model->name ?? $model->title ?? $model->tag ?? $model->getKey(),
            'changes'     => $action === 'updated' ? $model->getChanges() : ($action === 'created' ? $model->toArray() : null),
            'station_id'  => $model->station_id ?? ($user ? $user->station_id : null),
            'ip_address'  => request()->ip(),
            'severity'    => $action === 'deleted' ? 'warning' : 'info',
        ]);
    }

    public function created(Model $model)
    {
        $this->logAction($model, 'created');
    }

    public function updated(Model $model)
    {
        $this->logAction($model, 'updated');
    }

    public function deleted(Model $model)
    {
        $this->logAction($model, 'deleted');
    }
}
