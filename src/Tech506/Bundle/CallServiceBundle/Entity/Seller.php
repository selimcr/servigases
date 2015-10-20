<?php

namespace Tech506\Bundle\CallServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Tech506\Bundle\SecurityBundle\Entity\User;


/**
 * @ORM\Table(name="sellers")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\CallServiceBundle\Repository\SellersRepository")
 */
class Seller extends Employee {

    public function __construct() {
        parent::__construct();
    }

    public function __toString() {
        return "Seller [" . $this->id . "]";
    }
}
?>
