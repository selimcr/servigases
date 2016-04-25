<?php
namespace Tech506\Bundle\CallServiceBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tech506\Bundle\CallServiceBundle\Entity\ProductSaleType;
use Tech506\Bundle\CallServiceBundle\Entity\TechnicianServicePart;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;
use Tecnotek\Bundle\AsiloBundle\Util\Enum\PermissionsEnum;

/**
 * Service that handles some logic related with the system TechnicianService
 */
class TechnicianServiceService {

    private $securityContext;
    private $router;
    private $logger;
    private $em;
    private $conn;
    private $translator;
    private $session;

    const SECURITY_CODE_LENGTH = 6;

    public function __construct($securityContext, $router, $logger, \Doctrine\ORM\EntityManager $em,
                                $translator, $session) {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->logger = $logger;
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->translator = $translator;
        $this->session = $session;
    }

    public function saveServiceParts($service, $parts) {
        $parts = explode("><",$parts);
        $partsEntities = $service->getParts();
        $counter = 0;
        foreach($parts as $partInfo){
            $partInfo = trim($partInfo);
            if($partInfo != "") {
                $data = explode("<>",$partInfo);
                $partEntity = new TechnicianServicePart();
                if(sizeof($partsEntities) > $counter) {//actualizar el de la lista existente
                    $partEntity = $partsEntities[$counter];
                }
                $partEntity->setName($data[0]);
                $partEntity->setTechnicianCost($data[1]);
                $partEntity->setRealCost($data[2]);
                $partEntity->setFullPrice($data[3]);
                $partEntity->setTechnicianCommision($data[4]);
                $partEntity->setUtility($data[5]);
                $partEntity->setService($service);
                $this->em->persist($partEntity);
                //0: name, 1: costTechnician, 2: costReal, 3:price, 4: commision, 5:utility;
                //$this->logger->info($data[0]);
            }
            $counter++;
        }
        for($i = $counter; $i < sizeof($partsEntities); $i++){
            $partEntity = $partsEntities[$i];
            $this->em->remove($partEntity);
        }
        $this->em->flush();
    }

    public static function generateRandomSecurityCode() {
        $characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < TechnicianServiceService::SECURITY_CODE_LENGTH; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}