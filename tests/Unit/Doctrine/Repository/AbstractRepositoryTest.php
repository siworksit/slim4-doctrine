<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 13/10/17
 * Time: 5:40 PM
 */

namespace Unit\Doctrine\Repository;

use Siworks\Slim\Tests\Unit\BaseTestCase;

class AbstractRepositoryTest extends BaseTestCase
{
    /**
     * @var \Siworks\Slim\Doctrine\Repository\AbstractRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Siworks\Slim\Doctrine\Repository\AbstractRepository')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->repository->__construct($this->getEntityManagerMock(), $this->getClassMetadataMock());
    }

    public function testGetSingleById()
    {
        $this->assertEquals($this->getEntityMock(),$this->repository->getSingleById(5));
    }

    public function testGetSingleByIdNull()
    {
        $classMeta = $this->getClassMetadataMock();
        $classMeta->name = 'Error';
        $this->repository->__construct($this->getEntityManagerMock(), $classMeta);
        $this->assertNull($this->repository->getSingleById(5));
    }

//    public function testGetSimpleListBy()
//    {
//        $this->repository->__construct($this->getEntityManagerMock(), $this->getClassMetadataMock());
//        $this->repository->getSimpleListBy();
//        //$this->assertEquals($this->getEntityMock(),);
//    }
}