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
            $res = $this->responseException($e);
            return $response->withJSON($res);
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

            $entityObject = $this->modelEntity->update($args, $data);
            $data = $this->getPatternResponseRestFull("PUT", $entityObject->extractObject());

            return $response->withJSON($data);
        } catch (\Exception $e) {
            //trato aqui
            $res = $this->responseException($e);
            return $response->withJSON($res);
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
            $res = $this->responseException($e);
            return $response->withJSON($res);
        }
    }

    public function fetchAllAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        try
        {
            $data = $this->fetchValidate($request->getQueryParams());
            $results =  $this->modelEntity->findAll($data);

            $res ['data']= [];
            if (count($results['data']) > 0)
            {
                foreach ($results['data'] as $key => $obj)
                {
                    $res['data'] [$key] = $obj->toArray();
                    $res['data'] [$key] ['_links']= [
                        "_self" => [
                            "href"      => "{$request->getUri()->getPath()}/{$obj->getId()}",
                            "method"    => "get"
                        ]
                    ];
                }
            }
            $res = $this->mountStructResponse($res, $data, $request);
            return $response->withJSON($res);
        }
        catch (\Exception $e) {
            //trato aqui

            return $response->withJSON($entityObject->extractObject());
        }
    }

    /**
     * @TODO make method fetchOneAction
     */
    public function fetchAction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
    {
        try{
            $obj = $this->modelEntity->findOne($args['id']);
            if ($obj)
            {
                $res = $this->convertObjectToHateoas($obj, $request);
                return $response->withJSON($res);
            }
        }
        catch(\InvalidArgumentException $e){
            $res = $this->responseException($e);
            return $response->withJSON($res);
        }
        catch(\Exception $e){
            $res = $this->responseException($e);
            return $response->withJSON($res);
        }

    }

    public function fetchValidate(Array $data)
    {
        $data['filters'] = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : array();
        $data['order']   = (isset($data['order']) && is_array($data['order']))     ? $data['order']   : array();
        $data['limit']   = (isset($data['limit']) && is_numeric($data['limit']))   ? $data['limit']   : self::LIMIT;
        $data['offset']  = (isset($data['offset']) && is_numeric($data['offset'])) ? $data['offset']  : self::OFFSET;

        if (isset($data['filters']) && ! is_array($data['filters']) )
        {
            throw new \InvalidArgumentException("Attribute 'filters' is not array (ABSRESCT-4001exc)", 4001);
        }

        if ( isset($data['order']) && count(array_intersect(array('asc','desc'), array_values($data['order']))) != 0 )
        {
            throw new \InvalidArgumentException("value 'orders' is invalid required [asc, desc] (ABSRESCT-4002exc)", 4002);
        }

        unset($data['access_token']);

        return $data;
    }

    public function getPatternResponseRestFull ($action, $data, \Psr\Http\Message\ResponseInterface $response)
    {
        switch ($action)
        {
            case "POST":
                $response->withStatus(201, "Created");
                $arrResp = [
                    "code"    => 201,
                    "message" => "created",
                    "data"    => $data,
                ];
                break;
            case "PUT":
            case "PATCH":
                $response->withStatus(200, "Ok");
                $arrResp = [
                    "code"    => 200,
                    "message" => "ok",
                    "data"    => $data,
                ];
                break;
            case "DELETE":
                $response->withStatus(204, "Ok");
                $arrResp = [
                    "code"    => 204,
                    "message" => "ok",
                    "data"    => $data,
                ];
                break;
        }
        return $arrResp;
    }

    public function mountStructResponse(array $res, array $data, \Psr\Http\Message\ServerRequestInterface $request) : array
    {
        $previousOffset = $data['offset'] - $data['limit'];
        $previousOffset = ( $previousOffset <= 0 ) ? 0 : $previousOffset;

        $nextOffset = $data['offset'] + $data['limit'];

        $uri = $request->getUri()->getPath();

        $filters = $this->implodeQueryParams('filters', $data['filters']);
        $order = $this->implodeQueryParams('order', $data['order']);

        $res['_links'] = [
            'previous' => [
                "href"      => "{$uri}?{$filters}{$order}offset={$previousOffset}&limit={$data['limit']}",
            ],
            'next' => [
                "href"      => "{$uri}?{$filters}{$order}offset={$nextOffset}&limit={$data['limit']}",
            ]
        ];

        $res['total'] = count($res['data']);

        return $res;
    }

    public function implodeQueryParams($param, $value)
    {
        if(count($value))
        {
            foreach($value as $key => $v)
            {
                $res .= "{$param}[{$key}]={$v}&";
            }
            return $res;
        }
        return NUll;
    }

    public function responseException(\Exception $e){
        //echo $e->getMessage();exit;
        $data = [
            "status"    => "error",
            "type"      => get_class($e),
            "code"      => $e->getCode(),
            "message"   => $e->getMessage()
        ];

        return $data;
    }

    public function convertObjectToHateoas($obj, \Psr\Http\Message\ServerRequestInterface $request)
    {
        $class_name = get_class($obj);
        $arr = $obj->extractObject();
        $arr["_links"] = [
            "update" => [
                "rel"       => "self",
                "href"      => "{$request->getUri()->getPath()}",
                "method"    => "put"
            ],
            "remove" => [
                "rel"       => "self",
                "href"      => "{$request->getUri()->getPath()}",
                "method"    => "delete"
            ]
        ];
        return $arr;
    }
}
