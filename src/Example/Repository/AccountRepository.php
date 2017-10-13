<?php

namespace Siworks\Slim\Doctrine\Example\Repository;

/**
 * AccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use Siworks\Slim\Doctrine\Repository\AbstractRepository as  AbstractRepository;
use Doctrine\ORM\Query;

class AccountRepository extends AbstractRepository
{

    public function getListBy(Array $filters, $limit, $hydrate = Query::HYDRATE_SINGLE_SCALAR)
    {
        $paginate = parent::getListBy($filters, $limit, $hydrate);
        return $paginate->getIterator();  // TODO: Change the autogenerated stub
    }

    public function getListFormBy(Array $filters, $limit, $hydrate = Query::HYDRATE_SINGLE_SCALAR)
    {
        return parent::getListBy($filters, $limit, $hydrate);  // TODO: Change the autogenerated stub
    }

    /**
     * busca accounts com base no status.
     *
     * @param string $status
     * @param int    $hydrate
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return int
     */
    public function getAccountsByStatus($status, $hydrate = Query::HYDRATE_SINGLE_SCALAR)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('i')
            ->where('i.status = :status')
            ->setParameter('status', $status);

        $paginator = parent::getPaginate($qb->getQuery());

        return $paginator->getIterator();
    }

}