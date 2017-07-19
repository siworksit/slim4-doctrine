<?php
namespace App\Example\Controller;

use App\Example\Model\ContractModel;
use App\Core\Controller\AbstractController;

class ContractController extends AbstractController
{

    public function __construct($container)
    {
        parent::__construct($container);
        $this->modelEntity = new ContractModel($container->get('em'));
    }
}