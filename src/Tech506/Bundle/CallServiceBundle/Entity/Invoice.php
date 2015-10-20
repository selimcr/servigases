<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\CallServiceBundle\Util\Enum\InvoiceStatus;
use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="invoices")
 * @ORM\Entity()
 */
class Invoice {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $observations;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ManyToOne(targetEntity="Client")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ManyToOne(targetEntity="Tech506\Bundle\SecurityBundle\Entity\User")
     * @JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;

    public function __construct() {
        $this->status = InvoiceStatus::CREATED;
        $this->observations = "";
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getObservations() {
        return $this->observations;
    }

    /**
     * @param mixed $observations
     * @return Invoice
     */
    public function setObservations($observations) {
        $this->observations = $observations;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Invoice
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @return Invoice
     */
    public function setClient($client) {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeller() {
        return $this->seller;
    }

    /**
     * @param mixed $seller
     * @return Invoice
     */
    public function setSeller($seller) {
        $this->seller = $seller;
        return $this;
    }

    public function __toString(){
        return $this->id;
    }
}
?>
