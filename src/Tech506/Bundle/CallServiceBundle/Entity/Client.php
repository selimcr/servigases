<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 * @ORM\Table(name="clients")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\ClientsRepository")
 * @UniqueEntity("identification")
 * @UniqueEntity("phone")
 * @UniqueEntity("email")
 */
class Client {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cellPhone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $address;

    public function __construct() {
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCellPhone() {
        return $this->cellPhone;
    }

    /**
     * @param mixed $cellPhone
     * @return Client
     */
    public function setCellPhone($cellPhone) {
        $this->cellPhone = $cellPhone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     * @return Client
     */
    public function setFullName($fullName) {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return Client
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Client
     */
    public function setEmail($email) {
        $this->email = $email;
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
     * @return Client
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function toString(){
        return $this->fullName . " [" . $this->phone . "] ";
    }

    public function __toString(){
        return "[" . $this->phone . "] " . $this->fullName;
    }
}
?>
