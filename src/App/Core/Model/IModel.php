<?php
/**
 * Created by PhpStorm.
 * User: ng
 * Date: 03/07/17
 * Time: 21:30
 */

namespace App\Core\Model;


interface IModel
{
    public function create( Array $data );

    public function update( Array $data );

    public function findAll( Array $data );

    /**
     * Hydrate object from array data;
     *
     * @param Object $obj
     * @param Array $data
     *
     * @return Entity
     */
     function populateObject($obj);

}
