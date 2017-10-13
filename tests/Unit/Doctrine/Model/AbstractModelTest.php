<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 11/10/17
 * Time: 20:15 PM
 */

namespace Unit\Doctrine\Model;

use Siworks\Slim\Doctrine\Model\AbstractModel;
use Siworks\Slim\Tests\Unit\BaseTestCase;
use Siworks\Slim\Doctrine\Repository;
use Symfony\Component\Config\Definition\Exception\Exception;

class AbstractModelTest extends BaseTestCase
{
    /**
     * @var \Siworks\Slim\Doctrine\Model\AbstractModel
     */
    protected $model;

    public function setUp()
    {
        $this->model = $this->getMockBuilder('Siworks\Slim\Doctrine\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->model->setEntityManager($this->getEntityManagerMock());
        $this->model->setEntityName('Account');
    }

    public function testGetEntityManager()
    {
        $this->assertInstanceOf('\Doctrine\ORM\EntityManagerInterface', $this->model->getEntityManager());
    }

    public function testSetEntityManager()
    {
        $this->model->setEntityManager($this->getEntityManagerMock());
        $this->assertInstanceOf('\Doctrine\ORM\EntityManagerInterface', $this->model->getEntityManager());
    }

    public function testSetNameEntity()
    {
        $this->assertEquals('Account',$this->model->getEntityName());
    }

    public function testGetNameEntity()
    {
        $this->model->setEntityName('Potato');
        $this->assertEquals('Potato',$this->model->getEntityName());
    }

    public function testCreate()
    {
        $stub = $this->getMockBuilder('Siworks\Slim\Doctrine\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->setMethods(array('getObj','populateAssociation','populateObject'))
            ->getMockForAbstractClass();
        $stubEntity = $this->getMockBuilder('Siworks\Slim\Doctrine\Entity\AbstractEntity')
            ->getMockForAbstractClass();
        // Now, mock the repository so it returns the mock of the entity
        $entityRepository = $this->getMockBuilder('Siworks\Slim\Doctrine\Repository\AbstractRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMockForAbstractClass();

        $entityRepository->expects($this->any())
            ->method('save')
            ->willReturn($stubEntity);


        $stub->setRepository($entityRepository);
        $stub->expects($this->any())
            ->method('getObj')
            ->willReturn($stubEntity);
        $stub->expects($this->any())
            ->method('populateAssociation')
            ->with($stubEntity)
            ->willReturn(TRUE);

        $stub->expects($this->any())
            ->method('populateObject')
            ->with($stubEntity)
            ->willReturn($stubEntity);
        $this->assertEquals($stubEntity,$stub->create(['12345' => '12345']));
    }
//
//    public function testUpdateAction()
//    {
//        $response = $this->model->updateAction($this->request,$this->response,[]);
//        $this->assertEquals('["retorno1","retorno2","retorno3"]',$response->getBody()->__toString());
//    }
//
//    public function testFetchAllAction()
//    {
//        $response = $this->model->fetchAllAction($this->request,$this->response,[]);
//        $this->assertEquals('["retorno1","retorno2","retorno3"]',$response->getBody()->__toString());
//    }
//
//    public function testFetchValidate()
//    {
//        $this->assertEquals(["retorno1","retorno2","retorno3"],$this->model->fetchValidate(["retorno1","retorno2","retorno3"]));
//    }
//
//    /**
//     * @expectedException        \InvalidArgumentException
//     * @expectedExceptionMessage Attribute 'filters' is required or is not array
//     */
//    public function testFetchValidateExpFilter()
//    {
//        $filterError = ['filters' => 'teste'];
//        $this->model->fetchValidate($filterError);
//    }
//
//    /**
//     * @expectedException        \InvalidArgumentException
//     * @expectedExceptionMessage value 'orders' is invalid required [asc, desc] (ABMD00035exc)
//     */
//    public function testFetchValidateExpOrder()
//    {
//        $filterError = ['order' => ['asdad' => 123]];
//        $this->model->fetchValidate($filterError);
//    }

}
