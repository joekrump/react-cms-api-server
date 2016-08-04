<?php namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
  
  protected $visible = ['id', 'name', 'permissions'];
  /**
   * @return Illuminate\Database\Eloquent\Model
   */
  public function permissions()
  {
      return $this->belongsToMany('App\Permission');
  }


}