<?php
namespace Tech506\Bundle\CallServiceBundle\Bean;

use Tech506\Bundle\CallServiceBundle\Entity\TechnicianService;
use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 *
 */
class UserCommisionsDetail {

    private $user;
    private $totalForSales;
    private $totalForTechnicianService;
    private $totalForTransportation;
    private $listOfServices;

    public function __construct() {
        $this->totalForSales = 0;
        $this->totalForTechnicianService = 0;
        $this->totalForTransportation = 0;
        $this->listOfServices = array();
    }

    public function getUser() {
        return $this->user;
    }

    public function getTotalForSales() {
        return $this->totalForSales;
    }

    public function getTotalForTechnician() {
        return $this->totalForTechnicianService;
    }

    public function getTotalForTransportation() {
        return $this->totalForTransportation;
    }

    public function getServices() {
        return $this->listOfServices;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function includeService(TechnicianService $service) {
        if(isset($this->user)) {
            $isTechnician = ($service->getTechnician()->getUser()->getId() == $this->user->getId());
            $isSeller = ($service->getSeller()->getId() == $this->user->getId());
            $technician = 0;
            $seller = 0;
            $transportation = 0;
            $details = array();
            foreach($service->getDetails() as $detail) {
                $addService = false;
                if ($isTechnician && ($detail->getTechnicianWin() > 0 || $detail->getTransportationCost() > 0)) {
                    $technician += $detail->getTechnicianWin();
                    $transportation += $detail->getTransportationCost();
                    $addService = true;
                }
                if ($isSeller && $detail->getSellerWin() > 0) {
                    $seller += $detail->getSellerWin();
                    $addService = true;
                }
                if ($addService) {
                    $serv = array(
                        "id" => $detail->getId(),
                        "name"  => "" . $detail->getProductSaleType(),
                        "saleDate" => date_format($detail->getTechnicianService()->getCreationDate(),"d/m/Y"),
                        "serviceDate" => date_format($detail->getTechnicianService()->getScheduleDate(),"d/m/Y"),
                        "seller"    => $detail->getSellerWin(),
                        "technician" => $detail->getTechnicianWin(),
                        "transportation" => $detail->getTransportationCost()
                    );
                    array_push($details, $serv);
                }
            }
            $this->totalForSales += $seller;
            $this->totalForTechnicianService += $technician;
            $this->totalForTransportation += $transportation;
            $serviceData = array(
                "id" => $service->getId(),
                "sale"  => $seller,
                "technician" => $technician,
                "transportation" => $transportation,
                "details" => $details
            );
            array_push($this->listOfServices, $serviceData);
        } else {
            // Cannot add if the user is not defined
        }
    }
}
?>
