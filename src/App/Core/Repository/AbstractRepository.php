<?php
namespace App\Core\Repository;
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 02/06/17
 * Time: 16:54
 */

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\ORMInvalidArgumentException as ORMInvalidArgumentException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityRepository as EntityRepository;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

Abstract class AbstractRepository extends EntityRepository
{


    /**
     * Get single entity object by Id
     *
     * @param Int $id
     *
     * @throws \Doctrine\ORM\NoResultException
     *
     * @return Object | NULL
     */
    public function getSingleById($id)
    {
        try
        {
            $obj = $this->getEntityManager()->find($this->_entityName, $id);

            if ($obj instanceof $this->_entityName)
            {
                return $obj;
            }

            return NULL;
        }
        catch(\Doctrine\ORM\NoResultException $e)
        {
            throw $e;
        }
    }

    /**
     * Return list of records according to given start index and length
     *
     * @param Array $criteria specify where conditions
     * @param String $orderby specify columns, in which data should be ordered
     * @param Int $limit Determines how many records to fetch
     * @param Int $offset the start index number for the result entity list
     *
     * @return Array | NULL
     */
    function getSimpleListBy(Array $criteria = array(), $orderBy = NULL, $limit=10, $offset=0)
    {
        try
        {
            if ( is_null($criteria) || ! $this->checkAttrib($criteria) )
            {
                $message = InvalidArgumentException::noColumnsSpecifiedForTable()->getMessage() . "(ABSREP00012exc)";
                throw new InvalidArgumentException($message);
            }

            return $this->getEntityManager()->getRepository($this->_entityName)->findBy($criteria, $orderBy, $limit, $offset);
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * generic search to all repositories.
     *
     * @param string $status
     * @param int    $hydrate
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return \Paginator
     */
    public function getListBy(Array $filters, $limit, $hydrate = Query::HYDRATE_SINGLE_SCALAR)
    {
        try{

            if ( ! self::checkAttrib($this->_entityName, array_keys($filters)) )
            {
                throw new ORMInvalidArgumentException("Invalid attribute filter (ACCREP exc001)");
            }

            $qb = $this->createQueryBuilder('i');
            $qb->select('e')
                ->from($this->_entityName);

            if (isset($filters['where']))
            {
                $qb = self::createWhere($filters['where'], $qb);
            }

            // fica para futuro
            /** if (isset($filters['COND'])) {
            $qb = parent::createConditional($filters['COND'], $qb);
            }**/

            if (isset($filters['orderBy']))
            {
                $qb = self::createOrderBy($filters['orderBy'], $qb);
            }

            $query = $qb->getQuery();

            return self::getPaginate($query);
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }


    public function checkAttrib(Array $list)
    {

        $list_filtred = $this->getAttributesList($list);
        if ( ! class_exists($this->_entityName) )
        {
            throw new ClassNotFoundException("Namespace {$this->_entityName} not found (ABSREP0011exc)");
        }


        foreach ($list_filtred as $attrib => $values)
        {
            if ( ! property_exists($this->_entityName, $attrib) )
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function getAttributesList(array $list)
    {
        $list = array_filter($list, function($value) {
            $arr = parent::createQueryBuilder('e')->getDQLParts();
            foreach ($arr as $keys){
                if ($keys == $value)
                {
                    return false;
                }
            }
            return $value;
        },ARRAY_FILTER_USE_BOTH);

        return $list;
    }

    /**
     * Create generics "where" on DQL with QueryBuilder
     *
     * @param array $arr
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function createWhere( Array $arr, QueryBuilder $qb)
    {

        foreach ($arr as $field => $value)
        {
            if ( ! $this->getClassMetadata()->hasField($field) )
            {
                continue;
            }

            $qb ->andWhere($qb->expr()->eq('e.'.$field, ':'.$field))
                ->setParameter($field, $value);
        }

        return $qb;
    }

    /**
     * Não está pronto parei aqui mas a ideia é boa =D, só não queria perder ela..where condicional generica
     *  ex.: Where (status = 1 AND status = 2 ) AND dataCreate = '0000-00-00'...
     *
     * @deprecated
     **/
    public function createConditional(Array $arr, $instr = null, QueryBuilder $qb)
    {
        if ( is_array($arr) )
        {
            foreach($arr as $field => $value)
            {
                if (is_array($value) && ! property_exists($this->getEntityName(), $field))
                {
                    $qb = self::createConditional($value, $field, $qb);
                }
                else{

                }
            }
        }
        else {
        }
    }

    public function createOrderBy(Array $arr, $dql){}

    public function createGroupBy(Array $arr, $dql){}

    /**
     * Get paged users
     * @param int $offset
     * @param int $limit
     * @return Paginator
     */
    public function getPaginate( $offset = 0, $limit = 10, Query $query)
    {
        $paginator = new Paginator($query->getDQL());

        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @param Object $obj
     * @return Object
     * @throws \Exception
     */
    public function save($obj)
    {
        try{
            $this->getEntityManager()->persist($obj);
            $this->getEntityManager()->flush($obj);

            return $obj;
        }
        catch(ORMInvalidArgumentException $e)
        {
            throw ORMInvalidArgumentException ($e->getMessage() . "(ABSREP0001exc)");
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function remove($obj)
    {
        try{
            $this->getEntityManager()->remove($obj);
            $this->getEntityManager()->flush($obj);

            return $obj;
        }
        catch(ORMInvalidArgumentException $e)
        {
            throw ORMInvalidArgumentException ($e->getMessage() . "(ABSREP0002exc)");
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

}