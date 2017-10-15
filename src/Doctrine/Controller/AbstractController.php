<?php
namespace Siworks\Slim\Doctrine\Controller;

use Doctrine\ORM\EntityManager;
use Siworks\Slim\Doctrine\Traits\Model\Model;
use Siworks\Slim\Doctrine\Model\IModel;

Abstract class AbstractController
{
    protected $entityManager;
    protected $modelEntity;
    use Model;

    public function __construct($container)
    {
        $this->setEntityManager($container['em']);
        $this->setModelEntity($this->inheritanceModel($this->entityManager));
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getModelEntity()
    {
        return $this->modelEntity;
    }

    /**
     * @param mixed $modelEntity
     */
    public function setModelEntity(\Siworks\Slim\Doctrine\Model\IModel $modelEntity)
    {
        $this->modelEntity = $modelEntity;
    }

    public function createAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        $entityObject =  $this->modelEntity->create($request->getQueryParams());
        return $response->withJSON($entityObject->toArray());
    }

    public function updateAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        $entityObject =  $this->modelEntity->update($request->getQueryParams());
        return $response->withJSON($entityObject->toArray());
    }

    public function removeAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        $entityObject =  $this->modelEntity->remove($request->getQueryParams());
        return $response->withJSON($entityObject->toArray());
    }

    public function fetchAllAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        $this->fetchValidate($request->getQueryParams());
        $results =  $this->modelEntity->findAll($request->getQueryParams());
        return $response->withJSON($results);
    }

    /**
     * @TODO make method fetchOneAction
     */
//    public function fetchOneAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
//    {
//        $this->fetchValidate($request->getQueryParams());
//        $entity = $this->modelEntity->findOne($this->fetchValidate($request->getQueryParams()));
//        if ($entity)
//        {
//            return $response->withJSON(get_object_vars($entity));
//        }
//        return $response->withStatus(404, 'No photo found with slug '.$args['slug']);
//    }

    public function fetchValidate(Array $args)
    {
        if (isset($args['filters']) && ! is_array($args['filters']) )
        {
            throw new \InvalidArgumentException("Attribute 'filters' is required or is not array");
        }

        if ( isset($args['order']) && count(array_intersect(array('asc','desc'), array_values($args['order']))) ==0 )
        {
            throw new \InvalidArgumentException("value 'orders' is invalid required [asc, desc] (ABMD00035exc)");
        }

        return $args;
    }

}
