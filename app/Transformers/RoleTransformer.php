<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Role;

class RoleTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id'           => $role->id,
            'name'         => $role->name,
            'display_name' => $role->display_name,
            'description'  => $role->description,
            'primary'      => $role->display_name,
            'secondary'    => $role->description,
            'deletable'    => true,
            // 'unmovable'    => false,
            // 'denyNested'   => false
        ];
    }

}