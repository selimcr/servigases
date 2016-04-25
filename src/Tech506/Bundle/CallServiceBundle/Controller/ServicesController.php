<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tech506\Bundle\CallServiceBundle\Entity\Call;
use Tech506\Bundle\CallServiceBundle\Entity\Client;
use Tech506\Bundle\CallServiceBundle\Entity\ServiceDetail;
use Tech506\Bundle\CallServiceBundle\Entity\TechnicianService;
use Tech506\Bundle\CallServiceBundle\Services\TechnicianServiceService;
use Tech506\Bundle\CallServiceBundle\Util\DateUtil;
use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/services")
 */
class ServicesController extends Controller {

    /******************************************************************************/
    /************* METHODS TO HANDLE THE CHANGE OF STATUS OF SERVICES *************/
    /******************************************************************************/

    /**
     * @Route("/change-status", name="_admin_services_handle_status")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function renderStatusChangeViewAction() {
        $em = $this->getDoctrine()->getManager();
        $services = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->findScheduledServices("creationDate", "asc");
        $technicians = $em->getRepository('Tech506CallServiceBundle:Technician')->findAll();
        return $this->render('Tech506CallServiceBundle:Admin:Services/status_change.html.twig', array(
            'services' => $services,
            'technicians' => $technicians,
        ));
    }

    /******************************************************************************/
    /**************** METHODS TO HANDLE THE SERVICES SCHEDULE PAGE ****************/
    /******************************************************************************/

    /**
     * @Route("/schedule", name="_admin_services_schedule_view")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function renderScheduleViewAction() {
        $em = $this->getDoctrine()->getManager();
        $services = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->findScheduledServices("creationDate", "asc");
        $technicians = $em->getRepository('Tech506CallServiceBundle:Technician')->findAll();
        return $this->render('Tech506CallServiceBundle:Admin:Services/schedule.html.twig', array(
            'services' => $services,
            'technicians' => $technicians,
        ));
    }

    /**
     * @Route("/schedule/update", name="_admin_service_schedule_update")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function changeScheduleInfoAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $serviceId = $request->get('id');
            $date = $request->get('date');
            $hour = $request->get('hour');
            $technicianId = $request->get('technicianId');
            $error = true;
            if (isset($serviceId) && isset($date) && isset($hour) && isset($technicianId)) {
                $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($serviceId);
                if (isset($technicianService)) {
                    $technician = $em->getRepository('Tech506CallServiceBundle:Technician')->find($technicianId);
                    $technicianService->setTechnician($technician);
                    if(isset($date) && $date != "") {
                        $technicianService->setScheduleDate(\DateTime::createFromFormat('d/m/Y', $date));
                    } else {
                        $technicianService->setScheduleDate(null);
                    }
                    $technicianService->setHour($hour);
                    if( isset($technician) && isset($date) && $date != "" && isset($hour) && $hour != "") {
                        $technicianService->setStatus(TechnicianServiceStatus::SCHEDULED);
                    } else {
                        $technicianService->setStatus(TechnicianServiceStatus::CREATED);
                    }
                    $em->persist($technicianService);
                    $em->flush();
                    $error = false;
                    $message = "El servicio se ha actualizado correctamente";
                } else {
                    $message = "El servicio no existe con el ID: " . $serviceId;
                }
            } else {
                $message = "No se recibieron todos los datos requeridos";
            }

            return new Response(json_encode(array(
                'error' => $error,
                'msg' => $message
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Service::chanceStatusAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/register", name="_admin_services_register")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function registerAction() {
        $em = $this->getDoctrine()->getManager();
        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $users = "";
        $userId =  0;
        if($isAdmin){
            $users = $em->getRepository('Tech506SecurityBundle:User')->findAll();
            $usr= $this->get('security.context')->getToken()->getUser();
            $userId = $usr->getId();
        }
        return $this->render('Tech506CallServiceBundle:Admin:Services/register.html.twig', array(
            'isAdmin' => $isAdmin,
            'users' => $users,
            'sellerId'  => $userId,
            'securityCode'  => TechnicianServiceService::generateRandomSecurityCode(),
            'technicianId' => 0
        ));
    }

    /**
     * @Route("/status/change", name="_admin_services_change_status")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function changeStatusAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $serviceId = $request->get('serviceId');
            $status = $request->get('status');
            $message = $request->get('msg');
            $error = true;
            if (isset($serviceId) && isset($status)) {
                $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($serviceId);
                if (isset($technicianService)) {
                    $currentStatus = $technicianService->getStatus();
                    $technicianService->setResultMsg($message);
                    $message = "";
                    switch ($status) {
                        case "3": //canceling the service
                            //Only can cancel from 1:Created or 2:Scheduled
                            if ($currentStatus == TechnicianServiceStatus::CREATED
                                || $currentStatus == TechnicianServiceStatus::SCHEDULED
                            ) {
                                $technicianService->setStatus(TechnicianServiceStatus::CANCELED);
                                $em->persist($technicianService);
                                $em->flush();
                                $error = false;
                                $message = "El servicio se ha cancelado correctamente";
                            } else {
                                $message = "El estado actual no permite cancelar este servicio";
                            }
                            break;
                        case "4": //finalizing the service
                            //Only can cancel from 1:Created or 2:Scheduled
                            if ($currentStatus == TechnicianServiceStatus::CREATED
                                || $currentStatus == TechnicianServiceStatus::SCHEDULED
                            ) {
                                $technicianService->setStatus(TechnicianServiceStatus::REALIZED);
                                $em->persist($technicianService);
                                $em->flush();
                                $error = false;
                                $message = "El servicio se ha marcado como realizado correctamente y las comisiones sera aplicadas segun corresponda";
                            } else {
                                $message = "El estado actual no permite cancelar este servicio";
                            }
                            break;
                        default:
                            $message = "No se est� realizando un cambio v�lido";
                            break;
                    }
                } else {
                    $message = "El servicio no existe con el ID: " . $serviceId;
                }
            } else {
                $message = "No se recibieron todos los datos requeridos";
            }

            return new Response(json_encode(array(
                'error' => $error,
                'msg' => $message
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Service::chanceStatusAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/parts/save", name="_admin_services_save_parts")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function savePartsAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $serviceId = $request->get('serviceId');
            $parts = $request->get('parts');
            $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($serviceId);
            if(isset($technicianService)) {
                $service = $this->get('technician_service_service');
                $service->saveServiceParts($technicianService, $parts);
                return new Response(json_encode(array(
                    'error' => false,
                    'msg'   => "Los repuestos se han actualizado correctamente: " . $serviceId
                )));
            } else {
                return new Response(json_encode(array(
                    'error' => true,
                    'msg'   => "El servicio no existe con el ID: " . $serviceId
                )));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Seller::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/schedule/open/{id}", name="_admin_services_schedule", defaults={"id" = 0})
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function scheduleAction($id) {
        $em = $this->getDoctrine()->getManager();
        $technicianService = null;
        $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($id);
        if(isset($technicianService)){
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
            $users = "";
            if($isAdmin){
                $users = $em->getRepository('Tech506SecurityBundle:User')->findAll();
            }

            $technician = $technicianService->getTechnician();
            $technicianId = isset($technician)? $technician->getId():0;
            $technicians = $em->getRepository('Tech506CallServiceBundle:Technician')->findAll();
            return $this->render('Tech506CallServiceBundle:Admin:Sales/Services/schedule.html.twig', array(
                'id'            => $id,
                'service'       => $technicianService,
                'technicians'   => $technicians,
                'technicianId'  => $technicianId,
                'isAdmin'       => $isAdmin,
                'users'         => $users
            ));
            /*
            if($technicianService->getStatus() == TechnicianServiceStatus::CREATED
                || $technicianService->getStatus() == TechnicianServiceStatus::SCHEDULED) {

            } else {
                return new RedirectResponse($this->generateUrl('_admin_services'));
            }*/
        } else {
            return new RedirectResponse($this->generateUrl('_admin_services'));
        }
    }

    /**
     * @Route("/schedule/do", name="_admin_services_schedule_do")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function doScheduleAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $serviceId = $request->get('serviceId');
            $technicianId = $request->get('technicianId');
            $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($serviceId);
            $technician = $em->getRepository('Tech506CallServiceBundle:Technician')->find($technicianId);
            $seller = $em->getRepository('Tech506SecurityBundle:User')->find($request->get("sellerId"));
            $technicianService->setSeller($seller);
            $technicianService->setTechnician($technician);
            $scheduleDate = $request->get('scheduleDate');
            if(isset($scheduleDate)) {
                $technicianService->setScheduleDate(\DateTime::createFromFormat('d/m/Y', $scheduleDate));
                if ( isset($technician) ) {
                    $technicianService->setStatus(TechnicianServiceStatus::SCHEDULED);
                } else {
                    $technicianService->setStatus(TechnicianServiceStatus::CREATED);
                }
            } else {
                $technicianService->setScheduleDate(null);
                $technicianService->setStatus(TechnicianServiceStatus::CREATED);
            }
            $technicianService->setObservations($request->get('observations'));
            $technicianService->setReferencePoint($request->get('referencePoint'));
            $technicianService->setState($request->get('state'));
            $technicianService->setAddress($request->get('address'));
            $technicianService->setAddressDetail($request->get('addressDetail'));
            $technicianService->setHour($request->get('scheduleHour'));
            $technicianService->setNeighborhood($request->get('neighborhood'));
            $technicianService->setSecurityCode($request->get('securityCode'));
            $em->persist($technicianService);
            $em->flush();
            $translator = $this->get('translator');
            return new Response(json_encode(array(
                'error' => false,
                'msg' => $translator->trans('information.save.success')
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Services::doScheduleAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /****************************************************************/
    /***********************  SERVICES ******************************/
    /****************************************************************/

    /**
     * @Route("/save", name="_admin_service_create")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function createServiceAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $callService = $this->get('call_service');
            $callService->createService($request);
            $translator = $this->get('translator');
            return new Response(json_encode(array(
                'error' => false,
                'msg'   => $translator->trans('service.save.success')
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Services::createServiceAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/", name="_admin_services")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function listAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Services/list.html.twig', array(
        ));
    }

    /**
     * Returns the List of Services paginated for Bootstrap Table depending of the current User
     *
     * @Route("/list", name="_admin_services_list")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function paginatedListAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $limit = $request->get('limit');
            $offset = $request->get('offset');
            $order = $request->get('order');
            $search = $request->get('search');
            $sort = $request->get('sort');
            $status = $request->get('status');
            $technician = $request->get('technician');
            $seller = $request->get('seller');
            $date = DateUtil::getDateValueFromUI($request->get('date'));
            $em = $this->getDoctrine()->getManager();
            $usr= $this->get('security.context')->getToken()->getUser();
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
            $userId = $isAdmin? 0:$usr->getId();
            $paginator = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
                ->getPageWithFilterForUser($offset, $limit, $search, $sort, $order, $userId, $status, $technician, $seller, $date);
            $results = $this->getResults($paginator);
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Services::paginatedListAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getResults($paginator) {
        $results = array();
        $translator = $this->get('translator');
        foreach($paginator as $service) {
            //$service = new TechnicianService();
            $technician = $service->getTechnician();
            $creationDate = $service->getCreationDate();

            array_push($results, array(
                'id'            => $service->getId(),
                'technician'    => (isset($technician)? $technician->getUser()->getFullName():"-"),
                'client'        => $service->getClient()->toString(),
                'status'        => $translator->trans('services.status.' . $service->getStatus()),
                'statusCode'    => $service->getStatus(),
                'creationDate'  => (isset($creationDate))? $creationDate->format('Y-m-d H:i:s'):'',
                'seller'        => $service->getSeller()->getFullName()
                //'generateService'   => $service->getGenerateService(),
            ));
        }
        return $results;
    }

    /**
     * @Route("/{id}", name="_admin_services_manage", defaults={"id" = 0})
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function manageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $technicianService = null;
        $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($id);
        if(isset($technicianService)){
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
            $users = "";
            if($isAdmin){
                $users = $em->getRepository('Tech506SecurityBundle:User')->findAll();
            }

            $technician = $technicianService->getTechnician();
            $technicianId = isset($technician)? $technician->getId():0;
            $technicians = $em->getRepository('Tech506CallServiceBundle:Technician')->findAll();
            return $this->render('Tech506CallServiceBundle:Admin:Services/manage.html.twig', array(
                'id'            => $id,
                'service'       => $technicianService,
                'technicians'   => $technicians,
                'technicianId'  => $technicianId,
                'isAdmin'       => $isAdmin,
                'users'         => $users
            ));
            /*
            if($technicianService->getStatus() == TechnicianServiceStatus::CREATED
                || $technicianService->getStatus() == TechnicianServiceStatus::SCHEDULED) {

            } else {
                return new RedirectResponse($this->generateUrl('_admin_services'));
            }*/
        } else {
            return new RedirectResponse($this->generateUrl('_admin_services'));
        }
    }

    /**
     * @Route("/details/save", name="_admin_services_rows_save")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function saveServiceRowsAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $serviceId = $request->get('serviceId');
            $technicianService = $em->getRepository('Tech506CallServiceBundle:TechnicianService')->find($serviceId);
            $oldDetails = $technicianService->getDetails();
            $data = $request->get('data');
            /*
            0: detail.id
            1: detail.productSaleType.product.id
            2: detail.productSaleType.id
            3: detail.fullPrice
            4: detail.sellerWin
            5: detail.technicianWin
            6: detail.transportationCost
            7: detail.utility
            8: detail.observations
            */
            $rows = explode("<>", $data);
            $updatedRows = array();
            foreach($rows as $row) {
                if( $row != "") {
                    $rowData = explode("/", $row);
                    /*$logger->info("--------------------------------------------");
                    $logger->info("row: " . $row);
                    $logger->info("detail id: " . $rowData[0]);
                    $logger->info("product id: " . $rowData[1]);
                    $logger->info("produc sale type id: " . $rowData[2]);
                    $logger->info("full price: " . $rowData[3]);
                    $logger->info("seller: " . $rowData[4]);
                    $logger->info("technician: " . $rowData[5]);
                    $logger->info("transportation: " . $rowData[6]);
                    $logger->info("utility: " . $rowData[7]);
                    $logger->info("observations: " . $rowData[8]);*/
                    $serviceDetail = new ServiceDetail();
                    if($rowData[0] != 0) { //Must update the Row
                        array_push($updatedRows, $rowData[0]);
                        $serviceDetail = $em->getRepository('Tech506CallServiceBundle:ServiceDetail')
                            ->find($rowData[0]);
                    } else {
                        $serviceDetail->setTechnicianService($technicianService);
                        $saleType = $em->getRepository('Tech506CallServiceBundle:ProductSaleType')->find($rowData[2]);
                        $serviceDetail->setProductSaleType($saleType);
                    }
                    $serviceDetail->setFullPrice($rowData[3]);
                    $serviceDetail->setSellerWin($rowData[4]);
                    $serviceDetail->setTechnicianWin($rowData[5]);
                    $serviceDetail->setTransportationCost($rowData[6]);
                    $serviceDetail->setUtility($rowData[7]);
                    $serviceDetail->setObservations($rowData[8]);
                    $em->persist($serviceDetail);
                    //$serviceDetail->setQuantity();
                }
            }
            $detailsToRemove = array();
            foreach($oldDetails as $detail) {
                if( !in_array($detail->getId(), $updatedRows) ) {
                    array_push($detailsToRemove, $detail);
                    $em->remove($detail);
                }
            }
            foreach($detailsToRemove as $detail) {
                $technicianService->removeDetail($detail);
            }
            $em->persist($technicianService);
            $em->flush();
            $translator = $this->get('translator');
            return new Response(json_encode(array(
                'error' => false,
                'msg' => $translator->trans('information.save.success')
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Services::saveServiceRowsAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }
}
