<?php
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 27/06/17
 * Time: 20:50
 */

namespace App\Core\Traits\Helpers;


trait ObjectHelpers
{
    /**
     * Recursively converts an Object to an Array
     *
     * @return array
     */
    public function toArray($obj = null, $filterKeys = array())
    {
        $obj = (is_null($obj)) ? $this : $obj;

        $arr = (is_object($obj)) ? get_object_vars($obj) : $obj;

        foreach( $arr as $key => $val)
        {

            if($val instanceof \DateTime)
            {
                $val->format('Y-m-d H:i:s');
                $arr[$key] =  $val->format('Y-m-d H:i:s');
            }
            else if(is_object($val))
            {
                $arr[$key] = $this->toArray($val, $filterKeys);
            }
        }

        if(count($filterKeys) > 0)
        {
            $arr = array_filter($arr, function($key) use ($filterKeys)
            {

                return ( ! in_array($key, $filterKeys) )? $key : null;

            },ARRAY_FILTER_USE_KEY);
        }

        return $arr;
    }
}