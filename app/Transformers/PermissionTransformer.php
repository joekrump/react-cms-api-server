<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Permission;

class PermissionTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id'           => $permission->id,
            'name'         => $permission->name,
            'display_name' => $permission->display_name,
            'description'  => $permission->description,
            'primary'      => $permission->display_name,
            'secondary'    => $permission->description,
            'leaf'         => true
        ];
    }

}