<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 10/8/17
 * Time: 6:41 PM
 */

namespace Unit\Doctrine\Traits\Model;

use Siworks\Slim\Tests\Unit\BaseTestCase;

class ModelTest extends BaseTestCase
{
    public function testInheritanceModel()
    {
        $mock = $this->getMockForTrait('Siworks\Slim\Doctrine\Traits\Model\Model');

        $mock->inheritanceModel($this->getEntityManagerMock());
    }
}
