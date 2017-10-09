<?php

namespace Siworks\Slim\Tests\Unit;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrine\ORM\EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getEntityManagerMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getConnection',
                    'getClassMetadata',
                    'close',
                )
            )
            ->getMock();
        $mock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->getConnectionMock()));
        $mock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($this->getClassMetadataMock()));
        return $mock;
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getClassMetadataMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->setMethods(array('getTableName'))
            ->getMock();
        $mock->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('{tableName}'));
        return $mock;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDatabasePlatformMock()
    {
        $mock = $this->getAbstractMock(
            'Doctrine\DBAL\Platforms\AbstractPlatform',
            array(
                'getName',
                'getTruncateTableSQL',
            )
        );
        $mock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));
        $mock->expects($this->any())
            ->method('getTruncateTableSQL')
            ->with($this->anything())
            ->will($this->returnValue('#TRUNCATE {table}'));
        return $mock;
    }
    /**
     * @return \Doctrine\DBAL\Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getConnectionMock()
    {
        $mock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'beginTransaction',
                    'commit',
                    'rollback',
                    'prepare',
                    'query',
                    'executeQuery',
                    'executeUpdate',
                    'getDatabasePlatform',
                )
            )
            ->getMock();
        $mock->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($this->getStatementMock()));
        $mock->expects($this->any())
            ->method('query')
            ->will($this->returnValue($this->getStatementMock()));
        $mock->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($this->getDatabasePlatformMock()));
        return $mock;
    }
    /**
     * @return \Doctrine\DBAL\Driver\Statement|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getStatementMock()
    {
        $mock = $this->getAbstractMock(
            'Doctrine\DBAL\Driver\Statement', // In case you run PHPUnit <= 3.7, use 'Mocks\DoctrineDbalStatementInterface' instead.
            array(
                'bindValue',
                'execute',
                'rowCount',
                'fetchColumn',
            )
        );
        $mock->expects($this->any())
            ->method('fetchColumn')
            ->will($this->returnValue(1));
        return $mock;
    }
    /**
     * @param string $class   The class name
     * @param array  $methods The available methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAbstractMock($class, array $methods)
    {
        return $this->getMockForAbstractClass(
            $class,
            array(),
            '',
            true,
            true,
            true,
            $methods,
            false
        );
    }
}
