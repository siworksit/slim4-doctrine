<?php
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 13/06/17
 * Time: 13:54
 */

namespace Siworks\Slim\Doctrine\Model;


Abstract Class AbstractModelRest extends AbstractModel implements IModel
{

    /**
     * @param Object $data
     *
     * return Object
     */
    public function create(array $data)
    {
        try
        {
            $entity = parent::create($data);
            $entityArr = $this->convertObjectToHateoas($entity);
        }
        catch (\Exception $e){
            throw new \Exception( "{$e->getMessage()} . (ABSMDRest-04013exc)", 4013);
        }
    }


    /**
     * @param  Array $data
     * @return Object | NULL
     * @throws \Exception
     */
    public function update(array $data)
    {
        try{
        }
        catch (PDOException $e){
            throw new PDOException( "{$e->getMessage()} . (ABSMDRest-04001exc)", 4001);
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
        }
        catch (PDOException $e){
            throw new PDOException($e->getMessage() . " (ABSMDRest-04003exc)", 4003);
        }
    }

    public function findAll(array $data)
    {
        $data['filters'] = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : array();
        $data['order']   = (isset($data['order']) && is_array($data['order']))     ? $data['order']   : array();
        $data['limit']   = (isset($data['limit']) && is_numeric($data['limit']))   ? $data['limit']   : self::LIMIT;
        $data['offset']  = (isset($data['offset']) && is_numeric($data['offset'])) ? $data['offset']  : self::OFFSET;

        try
        {
            $arrObjs = $this->repository->getSimpleListBy($data['filters'], $data['order'], $data['limit'], $data['offset']);
            $res = array();
            if (count($arrObjs) > 0)
            {
                foreach ($arrObjs as $obj)
                {
                    $res['content']=  $obj->toArray(null, array('__cloner__', '__isInitialized__', '__initializer__'));

                }

            }
            return $res;
        }
        catch(\PDOException $e){
            throw $e;
        }
    }


    public function convertObjectToHateoas($obj)
    {
        $class_name = get_class($obj);
        $arr = $obj->extractObject();
        $arr["link"] = [
            [
                "rel"       => "self",
                "href"      => "/{$class_name}/{$obj->getId()}",
                "method"    => "get"
            ],
        ];
    }

}