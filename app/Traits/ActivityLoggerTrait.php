<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Activity;

trait ActivityLoggerTrait
{
    protected static function bootActivityLoggerTrait()
    {
        foreach (['created', 'updating', 'deleted'] as $event) {
            self::$event(function ($model) use ($event) {
                $user_id = auth()->id(); // Change this to get the user ID based on your authentication method
                if( !$user_id ) return;

                if($event == 'updating') {
                    $event = 'updated'; //Just to make consistency in string term
                }
                $type = $event;
                $model_name = class_basename($model);
                $msg = "{$model_name} {$event}";
                $action = $event;

                if ($event == 'updated') {
                    $oldData = $model->getOriginal();
                    $newData = $model->getAttributes();
                    self::create_activity($user_id, $type, $msg, $model_name, $action, $oldData, $newData);
                } else {
                    $data = $model->getAttributes();
                    self::create_activity($user_id, $type, $msg, $model_name, $action, [], $data);
                }
            });
        }
    }

    protected static function create_activity($user_id, $type, $msg, $model, $action, $oldData, $newData)
    {
        $changedAttributes = [];

        // Compare old and new data to find changed attributes
        foreach ($newData as $attribute => $value) {
            if($oldData == []) {
                $changedAttributes[$attribute] = $value;
            } else {
                if ($oldData[$attribute] != $value) {

                    $changedAttributes[$attribute] = [
                        'old' => $oldData[$attribute],
                        'new' => $value,
                    ];
                }
            }

        }

        $activityData = [
            'uuid' => Str::uuid(),
            'user_id' => $user_id,
            'type' => $type,
            'message' => $msg,
            'body' => [
                'model' => $model,
                'action' => $action,
                'changed_attributes' => $changedAttributes,
            ],
        ];

        Activity::create($activityData);
    }
}