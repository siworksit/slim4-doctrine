<?php

namespace App\Core\Traits\Model;

trait Model
{
    public function inheritanceModel($entityManager){
      $modelEntity  = str_replace(
                      'Controller',
                      'Model',
                      get_called_class()
                      );
      return new $modelEntity($entityManager);
    }
}
