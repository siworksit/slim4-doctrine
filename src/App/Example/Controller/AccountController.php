<?php
namespace App\Example\Controller;

use App\Example\Model\AccountModel;
use App\Core\Controller\AbstractController;

class AccountController extends AbstractController
{

    public function __construct($container)
    {
        parent::__construct($container);
        $this->modelEntity = new AccountModel($container->get('em'));
    }

}