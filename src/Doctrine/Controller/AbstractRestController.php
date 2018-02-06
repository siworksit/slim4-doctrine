<?php
namespace Siworks\Slim\Doctrine\Controller;

use Doctrine\ORM\EntityManager;
use Siworks\Slim\Doctrine\Model\IModel;

Abstract class AbstractRestController
{
    const LIMIT  = 10;
    const OFFSET = 0;

    protected $entityManager;
    protected $modelEntity;
    protected $logger;

    public function __construct($container)
    {
        $this->setEntityManager($container['em']);
        $this->setLogger($container['logger']);
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

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function createAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        try
        {
            $data = $request->getParsedBody();

            if (count($files = $request->getUploadedFiles()) > 0)
            {
                $data = array_merge($data, $files);
            }

            $entityObject =  $this->modelEntity->create($data);

            return $response->withJSON($entityObject->extractObject());

        }
        catch (\Exception $e){
            //trato aqui
            return $response->withJSON($entityObject->extractObject());
        }

    }

    public function updateAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        try
        {
            $data = $request->getParsedBody();

            if (count($files = $request->getUploadedFiles()) > 0) {
                $data = array_merge($data, $files);
            }

            $entityObject = $this->modelEntity->update($data);
            return $response->withJSON($entityObject->extractObject());
        } catch (\Exception $e) {
            //trato aqui

            return $response->withJSON($entityObject->extractObject());
        }
    }

    public function removeAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        try
        {
            $entityObject =  $this->modelEntity->remove($request->getQueryParams());
            return $response->withJSON($entityObject->extractObject());

        }
        catch (\Exception $e) {
            //trato aqui

            return $response->withJSON($entityObject->extractObject());
        }
    }

    public function fetchAllAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        try
        {
            $data = $this->fetchValidate($request->getQueryParams());
            $results =  $this->modelEntity->findAll();

            $res = array();
            if (count($arrObjs) > 0)
            {
                foreach ($arrObjs as $key => $obj)
                {
                    $res['data'] [$key] = $obj->toArray();
                    $res['data'] [$key] = $this->convertObjectToHateoas($obj);
                }
            }

            return $response->withJSON($results);

        }
        catch (\Exception $e) {
            //trato aqui

            return $response->withJSON($entityObject->extractObject());
        }
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

    public function fetchValidate(Array $data)
    {

        $data['filters'] = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : array();
        $data['order']   = (isset($data['order']) && is_array($data['order']))     ? $data['order']   : array();
        $data['limit']   = (isset($data['limit']) && is_numeric($data['limit']))   ? $data['limit']   : self::LIMIT;
        $data['offset']  = (isset($data['offset']) && is_numeric($data['offset'])) ? $data['offset']  : self::OFFSET;

        if (isset($data['filters']) && ! is_array($data['filters']) )
        {
            throw new \InvalidArgumentException("Attribute 'filters' is required or is not array (ABSCTR-040034exc)");
        }

        if ( isset($data['order']) && count(array_intersect(array('asc','desc'), array_values($data['order']))) ==0 )
        {
            throw new \InvalidArgumentException("value 'orders' is invalid required [asc, desc] (ABMD00035exc)");
        }

        return $data;
    }

    public function getPatternResponseRestFull ($action, $data, \Psr\Http\Message\ResponseInterface $response)
    {
        switch ($action)
        {
            case "POST":
                    $response->withStatus(201, "Created");
                    $jsonResp = [
                        "code"    => 201,
                        "message" => "created",
                        "data"    => $data,
                    ];
                break;
            case "PUT":
            case "PATCH":
                    $response->withStatus(200, "Ok");
                    $jsonResp = [
                        "code"    => 200,
                        "message" => "ok",
                        "data"    => $data,
                    ];
                break;
            case "DELETE":
                    $response->withStatus(204, "Ok");
                    $jsonResp = [
                        "code"    => 204,
                        "message" => "ok",
                        "data"    => $data,
                    ];
                break;
        }
    }

    public function mountStructResponse(array $res, array $data) : array
    {
        $previousOffset = $data['offset'] - $data['limit'];
        $previousOffset = ( $previousOffset <= 0 ) ? 0 : $previousOffset;
        $res['links'] ['previous'] = [
            "href"      => "/{$class_name}?filters={$data['filters']}&offset={$previousOffset}&limit={$data['limit']}&order={$data['order']}",
        ];

        $nextOffset = $data['offset'] + $data['limit'];
        $res['links'] ['next'] = [
            "href"      => "/{$class_name}?filters={$data['filters']}&offset={$nextOffset}&limit={$data['limit']}&order={$data['order']}",
        ];

        $res['total'] = count($res['data']);

        return $res;
    }

    public function convertObjectToHateoas($obj)
    {
        $class_name = get_class($obj);
        $arr = $obj->extractObject();
        $arr["link"] ['_self']= [
            [
                "rel"       => "self",
                "href"      => "/{$class_name}/{$obj->getId()}",
                "method"    => "get"
            ],
        ];
    }
}
