<?php

namespace Siworks\Slim\Doctrine\Traits\Model;

trait Model
{
    /**
     * @TODO alter to Dependency Injection
     */
    public function inheritanceModel(\Doctrine\ORM\EntityManagerInterface $entityManager){
      $modelEntity  = str_replace(
                      'Controller',
                      'Model',
                      get_called_class()
                      );
      return new $modelEntity($entityManager);
    }
}
