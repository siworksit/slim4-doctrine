<?php
namespace App\Core\Controller;

use App\Core\Traits\Model\Model;


Abstract class AbstractController
{
    protected $entityManager;
    protected $modelEntity;
    use Model;

    public function __construct($container)
    {
        $this->entityManager = $container['em'];
        $this->modelEntity = $this->inheritanceModel($this->entityManager);
    }

    public function createAction($request, $response, $args)
    {
        $entityObject =  $this->modelEntity->create($request->getQueryParams());
        return $response->withJSON($entityObject->toArray());
    }

    public function updateAction($request, $response, $args)
    {
        $entityObject =  $this->modelEntity->update($request->getQueryParams());
        return $response->withJSON($entityObject->toArray());
    }

    public function fetchAllAction($request, $response, $args)
    {
        $this->fetchValidate($request->getQueryParams());
        $results =  $this->modelEntity->findAll($request->getQueryParams());

        return $response->withJSON($results);
    }

    public function fetchOneAction($request, $response, $args)
    {
        $this->fetchValidate($request->getQueryParams());
        $account = $this->modelEntity->findOne($this->fetchValidate($request->getQueryParams()));
        if ($account)
        {
            return $response->withJSON(get_object_vars($account));
        }
        return $response->withStatus(404, 'No photo found with slug '.$args['slug']);
    }

    public function fetchValidate(Array $args)
    {
        if (isset($args['filters']) && ! is_array($args['filters']) )
        {
            throw new \InvalidArgumentException("Attribute 'filters' is required or is not Array");
        }

        if ( isset($args['order']) && count(array_intersect(array('asc','desc'), array_values($args['order']))) ==0 )
        {
            throw new \InvalidArgumentException("value 'orders' is invalid required [asc, desc] (ABMD00035exc)");
        }

        return $args;
    }
}
