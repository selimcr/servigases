<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;


class Employee {

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
     * @ORM\Column(type="date", nullable = true)
     */
    protected $vinculationDate;

    /**
     * @ORM\Column(type="date", nullable = true)
     */
    protected $vinculationEndingDate;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $eps;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $arl;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $compensationFund;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $pensionFund;

    /**
     * @ORM\OneToOne(targetEntity="Tech506\Bundle\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    public function __construct() {
        $this->observations = "";
        $this->eps = "";
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEps() {
        return $this->eps;
    }

    /**
     * @param mixed $eps
     * @return Employee
     */
    public function setEps($eps) {
        $this->eps = $eps;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArl() {
        return $this->arl;
    }

    /**
     * @param mixed $arl
     * @return Technician
     */
    public function setArl($arl) {
        $this->arl = $arl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompensationFund() {
        return $this->compensationFund;
    }

    /**
     * @param mixed $compensationFund
     * @return Employee
     */
    public function setCompensationFund($compensationFund) {
        $this->compensationFund = $compensationFund;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPensionFund() {
        return $this->pensionFund;
    }

    /**
     * @param mixed $pensionFund
     * @return Employee
     */
    public function setPensionFund($pensionFund) {
        $this->pensionFund = $pensionFund;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVinculationDate() {
        return $this->vinculationDate;
    }

    /**
     * @param mixed $vinculationDate
     * @return Employee
     */
    public function setVinculationDate($vinculationDate) {
        $this->vinculationDate = $vinculationDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVinculationEndingDate() {
        return $this->vinculationEndingDate;
    }

    /**
     * @param mixed $vinculationEndingDate
     * @return Employee
     */
    public function setVinculationEndingDate($vinculationEndingDate) {
        $this->vinculationEndingDate = $vinculationEndingDate;
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
     * @return Employee
     */
    public function setObservations($observations) {
        $this->observations = $observations;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Employee
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function __toString(){
        return $this->id;
    }
}
?>
