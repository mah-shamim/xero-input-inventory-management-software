<?php

namespace App\Traits;

trait Permissions
{
    public function hasPermissions($model, $method)
    {
        if (session()->has('permissions')) {
            $permissions = session()->get('permissions');
            foreach ($permissions as $permission) {
                if ($permission['model'] === $model && $permission['method'] === $method) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    public function checkPermissionFromSet(array $arg, $permissions)
    {
        if ($permissions) {
            foreach ($permissions as $permission) {
                //                if($permission['model']===$model && $permission['method']===$method){
                //                    return true;
                //                }
            }
        } else {
            return true;
        }
    }
}
