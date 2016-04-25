<?php
namespace Tech506\Bundle\CallServiceBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tech506\Bundle\CallServiceBundle\Entity\Client;
use Tech506\Bundle\CallServiceBundle\Entity\Invoice;
use Tech506\Bundle\CallServiceBundle\Entity\InvoiceDetail;
use Tech506\Bundle\CallServiceBundle\Entity\ProductSaleType;
use Tech506\Bundle\CallServiceBundle\Entity\TechnicianService;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;
use Tecnotek\Bundle\AsiloBundle\Util\Enum\PermissionsEnum;

/**
 * Service that handles some logic related with the system products
 */
class CallService {

    private $securityContext;
    private $router;
    private $logger;
    private $em;
    private $conn;
    private $translator;
    private $session;

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

    public function createService($request) {
        $client = $this->getRelatedClient($request);
        $user = $this->em->getRepository("Tech506SecurityBundle:User")->find($request->get("sellerId"));
        $service = new TechnicianService();
        $service->setClient($client);
        $service->setSeller($user);
        $service->setNeighborhood($request->get('neighborhood'));
        $service->setState($request->get('state'));
        $service->setReferencePoint($request->get('referencePoint'));
        $service->setAddress($request->get('serviceAddress'));
        $service->setObservations($request->get('observations'));
        $service->setAddressDetail($request->get('addressDetail'));
        $service->setSecurityCode($request->get('securityCode'));
        $scheduleDate = $request->get('scheduleDate');
        if(isset($scheduleDate)) {
            $service->setScheduleDate(\DateTime::createFromFormat('d/m/Y', $scheduleDate));
        } else {
            $service->setScheduleDate(null);
        }
        $service->setHour($request->get('scheduleHour'));
        $this->em->persist($service);
        $servicesIds = $request->get("servicesIds");
        $this->generateServicesDetails($servicesIds, $service, $request);
        $this->em->flush();
    }

    private function generateServicesDetails($servicesId, $service, $request) {
        $services = preg_split("/[\s,]+/", $servicesId);
        foreach($services as $serviceId){
            //$productDetail = new ProductSaleType();
            $productDetail = $this->em->getRepository('Tech506CallServiceBundle:ProductSaleType')->find($serviceId);
            $serviceDetail = $productDetail->getNewServiceDetailFromMe();
            $serviceDetail->setTechnicianService($service);
            $this->em->persist($serviceDetail);
        }
        //$this->generateTechService($client, $invoice, $call, $request);
        //return $invoice;
    }

    private function generateInvoice($servicesId, $client, $user, $call, $request) {
        $invoice = new Invoice();
        $invoice->setClient($client);
        $invoice->setSeller($user);
        $this->em->persist($invoice);
        $services = preg_split("/[\s,]+/", $servicesId);
        foreach($services as $serviceId){
            //$productDetail = new ProductSaleType();
            $productDetail = $this->em->getRepository('Tech506CallServiceBundle:ProductSaleType')->find($serviceId);
            $invoiceDetail = $productDetail->getNewInvoiceDetailFromMe();
            $invoiceDetail->setInvoice($invoice);
            $this->em->persist($invoiceDetail);
        }

        $this->generateTechService($client, $invoice, $call, $request);
        return $invoice;
    }

    private function generateTechService($client, $invoice, $call, $request) {
        $service = new TechnicianService();
        $service->setClient($client);
        $service->setInvoice($invoice);
        $service->setCall($call);
        $service->setState($request->get('state'));
        $service->setReferencePoint($request->get('referencePoint'));
        $service->setAddress($request->get('serviceAddress'));
        $service->setObservations($request->get('observations'));
        $service->setAddressDetail($request->get('addressDetail'));
        $service->setSecurityCode($request->get('securityCode'));
        $scheduleDate = $request->get('scheduleDate');
        if(isset($scheduleDate)) {
            $service->setScheduleDate(\DateTime::createFromFormat('d/m/Y', $scheduleDate));
        } else {
            $service->setScheduleDate(null);
        }
        $service->setHour($request->get('scheduleHour'));
        $this->em->persist($service);
    }


    private function getRelatedClient($request) {
        $client = null;
        $clientId = $request->get('clientId');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $cellPhone = $request->get('cellPhone');
        if($clientId == 0) { //No client found yet, try to find and create if not exists
            $client = $this->em->getRepository('Tech506CallServiceBundle:Client')
                ->findClient($phone, $cellPhone, $email);
        } else {
            $client = $this->em->getRepository('Tech506CallServiceBundle:Client')->find($clientId);
        }
        if( !isset($client) ) {
            $client = new Client();
        }
        $client->setPhone($phone);
        $client->setCellPhone($cellPhone);
        $client->setEmail($email);
        $client->setFullName($request->get('fullName'));
        $client->setExtraInformation($request->get('extraInformation'));
        $client->setAddress("");
        $this->em->persist($client);
        $this->em->flush($client);
        return $client;
    }
}