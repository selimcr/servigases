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
 * @Route("/dashboard")
 */
class DashboardController extends Controller {

    /**
     * Returns the List of Services paginated for Bootstrap Table depending of the current User
     *
     * @Route("/services/list", name="_admin_dashboard_services_list")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function getServicesListForDashboardAction() {
        $logger = $this->get("logger");
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
                ->findForDashboard($offset, $limit, $search, $sort, $order, $userId, $status, $technician,
                    $seller, $date, $search);
            $results = $this->getDashboardServicesResults($paginator);
            return new Response(json_encode(array(
                'total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Services::paginatedListAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getDashboardServicesResults($paginator) {
        $results = array();
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        foreach($paginator as $service) {
            //$service = new TechnicianService();
            $technician = $service->getTechnician();
            $creationDate = $service->getCreationDate();
            $visitDate = $service->getScheduleDate();
            $stateId = $service->getState();
            $location = "";
            if ( isset($stateId) && $stateId > 0) {
                $state = $em->getRepository('Tech506CallServiceBundle:state')->find($stateId);
                $location = $state->getName();
            }
            $location .= ($service->getNeighborhood() != "" )? ", " . $service->getNeighborhood():"";
            //$location .= ($service->getNeighborhood() != "" ) ", " . $service->getNeighborhood():"";
            $addressParts = explode("::", $service->getAddress());
            $location .= "; " . $addressParts[0] . " ";
            if (sizeof($addressParts) > 1) {
                $location .= " " . $addressParts[1];
                if (sizeof($addressParts) > 2) {
                    $location .= " #" . $addressParts[2];
                    if (sizeof($addressParts) > 3) {
                        $location .= " " . $addressParts[3];
                    }
                }
            }

                array_push($results, array(
                'id'            => $service->getId(),
                'technician'    => (isset($technician)? $technician->getUser()->getFullName():"-"),
                'client'        => $service->getClient()->toString(),
                'clientCellPhone' => $this->getStringValue($service->getClient()->getCellPhone()),
                'clientEmail'     => $this->getStringValue($service->getClient()->getEmail()),
                'clientExtraInfo' => $this->getStringValue($service->getClient()->getExtraInformation()),
                'status'        => $translator->trans('services.status.' . $service->getStatus()),
                'statusCode'    => $service->getStatus(),
                'creationDate'  => (isset($creationDate))? $creationDate->format('d/m/Y'):'',
                'visitDate'     => (isset($visitDate))? $visitDate->format('d/m/Y'):'',
                'visitHour'     => $this->getStringValue($service->getHour()),
                'seller'        => $service->getSeller()->getFullName(),
                    'location' => $location, 
                    'addressDetail'=> $this->getStringValue($service->getAddressDetail()),
                    'referencePoint'=> $this->getStringValue($service->getReferencePoint()),
            ));
        }
        return $results;
    }

    private function getStringValue($value) {
        if (isset($value)) {
            return $value;
        } else {
            return "-";
        }
    }
}
