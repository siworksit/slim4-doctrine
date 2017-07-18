<?php
/**
 * Billing Project (http://www.siworks.com)
 *
 * Created by Rafael N. Garbinatto (rafael@siworks.com)
 * date 25/03/2017
 * @package App\Entity\Billing\Entry
 * @copyright SIWorks
 */

namespace App\Example\Entity\Contract;

use Doctrine\ORM\Mapping as ORM;
use \App\Core\Entity\AbstractEntity as AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="App\Example\Repository\ContractRepository")
 * @ORM\Table(name="contract", indexes={
 *     @ORM\Index(name="idx_start", columns={"start_dt"}),
 *     @ORM\Index(name="idx_end", columns={"end_dt"}),
 *     @ORM\Index(name="idx_canceled", columns={"canceled_at"}),
 *     @ORM\Index(name="idx_client", columns={"client_id"}),
 * })
 */
class Contract extends AbstractEntity
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="guid")
     */
    protected $ext_productId;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    protected $periodicity;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $status;

    /**
     * @ORM\Column(type="date")
     */
    protected $start_dt;

    /**
     * @ORM\Column(type="date")
     */
    protected $end_dt;

    /**
     * @ORM\Column(nullable=true, type="date")
     */
    protected $canceled_at;

    /**
     * @ORM\Column(type="string")
     */
    protected $client_id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Example\Entity\Account\Account", inversedBy="contracts")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     */
    protected $account;

}
