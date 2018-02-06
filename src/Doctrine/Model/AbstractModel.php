<?php
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 13/06/17
 * Time: 13:54
 */

namespace Siworks\Slim\Doctrine\Model;

use Siworks\Slim\Doctrine\Traits\Helpers\ObjectHelpers;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid as Uuid;
use GeneratedHydrator\Configuration;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\EntityManager as EntityManager;
use Doctrine\ORM\EntityRepository;

Abstract Class AbstractModel implements IModel
{
    use ObjectHelpers;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * Entity namespace default for this model
     */
    public $entityName;

    /**
     * @var array data
     *
     */

    protected $data = array();

    /**
     * AbstractModel constructor.
     * @param EntityManager $entityManager
     */
    public function __construct( EntityManager	$entityManager )
    {
        $this->setEntityManager($entityManager);
        $this->setEntityName($this->repository->getClassName());
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param mixed $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Object $data
     *
     * return Object
     */
    public function create(array $data)
    {
        try
        {
            $this->setData($data);
            $obj = $this->populateAssociation($this->getObj());
            $obj = $this->populateObject($obj);

            return  $this->repository->save($obj);
        }
        catch (PDOException $e){
            throw new PDOException( "{$e->getMessage()} . (ABSMD-01001exc)", 1001);
        }
    }

    public function getObj()
    {
        $mapperClass = new \ReflectionClass($this->entityName);
        return $mapperClass->newInstance();
    }

    /**
     * @param  Array $data
     * @return Object | NULL
     * @throws \Exception
     */
    public function update(array $data)
    {
        try{
            if ( ! isset($data['id']) || (! Uuid::isValid($data['id']) && !is_numeric($data['id'])) )
            {
                throw new \InvalidArgumentException("Argument 'Id' value is not set or is invalid (ABSMD-01003exc)");
            }

            $this->setData($data);
            $obj = $this->repository->findOneById($this->getData()['id']);
            if( ! $obj instanceof $this->entityName )
            {
                return null;
            }

            $obj = $this->populateAssociation($obj);
            $obj = $this->populateObject($obj, $data);

            $obj = $this->repository->save($obj); // TODO: Change the autogenerated stub

            return $obj;
        }
        catch (PDOException $e){
            throw new PDOException( "{$e->getMessage()} . (AMD0001exc)");
        }
    }

    /**
     * @param Integer $id
     *
     * @return Boolean
     */
    public function remove(array $data)
    {
        try
        {
            if ( ! isset($data['id']) || (! Uuid::isValid($data['id']) && !is_numeric($data['id'])) )
            {
                throw new \InvalidArgumentException("Argument 'Id' value is not set or is invalid (ABSMD-01004exc)");
            }

            $obj = $this->repository->findOneById($data['id']);
            if( ! $obj instanceof $this->entityName )
            {
                return null;
            }
            $res = $this->repository->remove($obj);
            return $res;
        }
        catch (PDOException $e){
            throw new PDOException($e->getMessage() . " (AMD0003exc)");
        }
    }

    public function findAll(array $data)
    {
        try
        {
            $arrObjs = $this->repository->getSimpleListBy($data['filters'], $data['order'], $data['limit'], $data['offset']);
            return $res;
        }
        catch(\PDOException $e){
            throw $e;
        }
    }

    public function populateObject($obj)
    {
        try {

            if ($obj->getId())
            {
                $objData = $obj->toArray();
                $this->setData(array_filter(array_merge($objData, $this->getData())));
            }

            $this->getHydrator()->hydrate($this->getData(), $obj);

            return $obj;
        }
        catch(\Exception $e){
            throw $e;
        }
    }

    public function getHydrator()
    {
        $config = new Configuration($this->getEntityName());
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $hydrator = new $hydratorClass();
        return $hydrator;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    protected function populateAssociation($obj)
    {
        try {
            $metaData = $this->entityManager->getClassMetadata(get_class($obj));

            foreach($this->getData() as $attr => $value)
            {
                if($metaData->hasAssociation($attr))
                {
                    $association = $metaData->getAssociationMapping($attr);
                    if ( ! isset($association['targetToSourceKeyColumns']) )
                    {
                        throw new \Doctrine\ORM\ORMInvalidArgumentException("This relation is inversed (ABSMD-01004exc)");
                    }

                    $assocAttr = array_keys($association['targetToSourceKeyColumns']);

                    if( $metaData->isAssociationWithSingleJoinColumn($attr) )
                    {
                        $this->getData()[$attr] =  $this->entityManager->getRepository($association['targetEntity'])
                            ->findOneBy(array($assocAttr[0] => $value));
                    }
                    else{

                        $this->getData()[$attr] =  new ArrayCollection($this->entityManager->getRepository($association['targetEntity'])
                            ->findBy(array($assocAttr[0] => $value)));
                    }
                }
            }
            return $obj;
        }
        catch(\Exception $e){
            throw $e;
        }

    }

    public function extractObject($obj)
    {
        return $this->getHydrator()->extract($obj);
    }

    /**
     * @param $repository
     * @return $this
     */
    public function setRepository(EntityRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

}