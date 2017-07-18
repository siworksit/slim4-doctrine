<?php
/**
 * Billing Project (http://www.siworks.com)
 *
 * Created by Rafael N. Garbinatto (rafael@siworks.com)
 * date 25/03/2017
 *
 * @copyright Siworks
 */
namespace App\Core\Entity;


use App\Core\Traits\Entity\TimeStampable;
use App\Core\Traits\Helpers\ObjectHelpers;
use Doctrine\ORM\Mapping as ORM;
use Exchanger\Exception\Exception;

/**
 * Class AbstractEntity
 * @package App\Core\Entity
 */
Abstract class AbstractEntity
{

    use TimeStampable,
        ObjectHelpers;


    public function __call($methodName, $params = null)
    {
        $action = substr($methodName, 0, 3);
        $field = lcfirst(substr($methodName, 3));

        if (property_exists($this, $field))
        {
            if ($action == 'set')
            {
                $this->$field = $params[0];
                return $this;
            }
            elseif($action == 'get')
            {
                return $this->$field;
            }
            throw new \RuntimeException('The method is not defined. (ABSENT0002exc)');
        }

        throw new \InvalidArgumentException('Attribute does not exist. (ABSENT0001exc)');
    }
}