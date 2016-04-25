<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;
use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="technician_services")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\TechnicianServiceRepository")
 */
class TechnicianService {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable = true)
     */
    protected $observations;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $referencePoint;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $addressDetail;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $neighborhood;

    /**
     * @ORM\Column(type="string", length=20, nullable = true)
     */
    protected $hour;

    /**
     * @ORM\Column(type="string", length=20, nullable = false)
     */
    protected $securityCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $scheduleDate;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $resultMsg;

    /**
     * @ORM\Column(name="technician_commision_applied", type="boolean")
     */
    private $technicianCommisionApplied;

    /**
     * @ORM\Column(name="seller_commision_applied", type="boolean")
     */
    private $sellerCommisionApplied;

    /**
     * @ManyToOne(targetEntity="Client")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ManyToOne(targetEntity="Tech506\Bundle\CallServiceBundle\Entity\Technician")
     * @JoinColumn(name="technician_id", referencedColumnName="id")
     */
    private $technician;

    /**
     * @ManyToOne(targetEntity="Tech506\Bundle\SecurityBundle\Entity\User")
     * @JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;

    /**
     * @ORM\OneToOne(targetEntity="Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     **/
    private $invoice;

    /**
     * @var $details
     *
     * @ORM\OneToMany(targetEntity="ServiceDetail", mappedBy="technicianService", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $details;

    /**
     * @var $parts
     *
     * @ORM\OneToMany(targetEntity="TechnicianServicePart", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $parts;

    public function __construct() {
        $this->status = TechnicianServiceStatus::CREATED;
        $this->observations = "";
        $this->creationDate = new \DateTime();
        $this->technicianCommisionApplied = false;
        $this->sellerCommisionApplied = false;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     * @return Call
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObservations() {
        return $this->observations;
    }

    /**
     * @param mixed $observations
     * @return TechnicianService
     */
    public function setObservations($observations) {
        $this->observations = $observations;
        return $this;
    }

    public function getResultMsg() {
        return $this->resultMsg;
    }

    public function setResultMsg($resultMsg) {
        $this->resultMsg = $resultMsg;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return TechnicianService
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReferencePoint() {
        return $this->referencePoint;
    }

    /**
     * @param mixed $referencePoint
     * @return TechnicianService
     */
    public function setReferencePoint($referencePoint) {
        $this->referencePoint = $referencePoint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return TechnicianService
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getNeighborhood() {
        return $this->neighborhood;
    }

    public function setNeighborhood($neighborhood) {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHour() {
        return $this->hour;
    }

    /**
     * @param mixed $hour
     * @return TechnicianService
     */
    public function setHour($hour) {
        $this->hour = $hour;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return TechnicianService
     */
    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScheduleDate() {
        return $this->scheduleDate;
    }

    /**
     * @param mixed $scheduleDate
     * @return TechnicianService
     */
    public function setScheduleDate($scheduleDate) {
        $this->scheduleDate = $scheduleDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressDetail() {
        return $this->addressDetail;
    }

    /**
     * @param mixed $addressDetail
     * @return TechnicianService
     */
    public function setAddressDetail($addressDetail) {
        $this->addressDetail = $addressDetail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecurityCode() {
        return $this->securityCode;
    }

    /**
     * @param mixed $securityCode
     * @return TechnicianService
     */
    public function setSecurityCode($securityCode) {
        $this->securityCode = $securityCode;
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
     * @return TechnicianService
     */
    public function setClient($client) {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechnician() {
        return $this->technician;
    }

    /**
     * @param mixed $technician
     * @return TechnicianService
     */
    public function setTechnician($technician) {
        $this->technician = $technician;
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
     * @return TechnicianService
     */
    public function setSeller($seller) {
        $this->seller = $seller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoice() {
        return $this->invoice;
    }

    /**
     * @param mixed $invoice
     * @return TechnicianService
     */
    public function setInvoice($invoice) {
        $this->invoice = $invoice;
        return $this;
    }

    public function getParts(){
        return $this->parts;
    }

    public function removeDetail($detail) {
        //$this->details->remove($detail);
        $detail->setTechnicianService(null);
    }

    public function getDetails(){
        return $this->details;
    }

    public function getCommisionValues() {
        $technician = 0;
        $seller = 0;
        foreach($this->details as $detail) {
            $technician += $detail->getTechnicianWin();
            $seller += $detail->getSellerWin();
        }
        return array(
            'technicianCommision'   => $technician,
            'sellerCommision'       => $seller
        );
    }

    public function __toString(){
        return "Servicio [" . $this->id . "]";
    }
}
?>
