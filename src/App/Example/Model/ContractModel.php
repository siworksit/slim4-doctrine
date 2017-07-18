<?php
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 23/06/17
 * Time: 20:31
 */

namespace App\Example\Model;

use App\Core\Model\AbstractModel;
use Doctrine\ORM\EntityManager;

class ContractModel extends AbstractModel
{

    public function __construct( EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->repository = $this->entityManager->getRepository('App\Example\Entity\Contract\Contract');
    }
}