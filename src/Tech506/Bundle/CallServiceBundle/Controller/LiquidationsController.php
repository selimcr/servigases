<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tech506\Bundle\CallServiceBundle\Bean\UserCommisionsDetail;
use Tech506\Bundle\CallServiceBundle\Entity\Call;
use Tech506\Bundle\CallServiceBundle\Entity\Client;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/commisions")
 */
class LiquidationsController extends Controller {

    /**
     * @Route("/pending", name="_admin_liquidations_view_pending")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function allAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get("request")->request;
        $users = $em->getRepository('Tech506SecurityBundle:User')->getAllByName();
        $logger = $this->get('logger');
        $allUsers = array();
        foreach($users as $user) {
            $roles = $user->getUserRoles();
            foreach($roles as $role) {
                $logger->info($user->getFullName() . " : " . $role->getId());
                array_push($allUsers, array(
                    'id'    => $user->getId(),
                    'name'  => $user->getFullName(),
                    'role'  => $role->getId()
                ));
            }
        }
        return $this->render('Tech506CallServiceBundle:Admin:Liquidations/apply_commisions.html.twig', array(
            'users'     => $allUsers,
        ));
    }

    /**
     * Returns the List of Calls paginated for Bootstrap Table depending of the current User
     *
     * @Route("/get-pending", name="_admin_commisions_get_pending")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function getServicesCommisionPendingAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            //return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $em = $this->getDoctrine()->getManager();
            $usr= $this->get('security.context')->getToken()->getUser();
            $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
            $request = $this->get('request');
            $employeeType = $request->get('employeeType');
            $id = $request->get('id');
            $services = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
                ->getPendingOfApplyCommision($employeeType, $id);
            $servicesArray = array();
            foreach ($services as $service) {
                $seller = $service->getSeller();
                $technician = $service->getTechnician()->getUser();
                // ($id == 0 || $id == $seller->getId())
                if (array_key_exists($seller->getId(), $servicesArray)) {
                    $userCommisionDetail = $servicesArray[$seller->getId()];
                    $userCommisionDetail->includeService($service);
                    if ( $seller->getId() != $technician->getId()) {
                        if (array_key_exists($technician->getId(), $servicesArray)) {
                            $userCommisionDetail = $servicesArray[$technician->getId()];
                            $userCommisionDetail->includeService($service);
                        } else {
                            if (($id == 0 || $id == $technician->getId()) && ($employeeType == 0 || $technician->getUserRoleId() == $employeeType)) {
                                $userCommisionDetail = new UserCommisionsDetail();
                                $userCommisionDetail->setUser($technician);
                                $userCommisionDetail->includeService($service);
                                $servicesArray[$technician->getId()] = $userCommisionDetail;
                            }
                        }
                    }
                } else {
                    if (array_key_exists($technician->getId(), $servicesArray)) {
                        $userCommisionDetail = $servicesArray[$technician->getId()];
                        $userCommisionDetail->includeService($service);
                        if ( $seller->getId() != $technician->getId()) {
                            if (($id == 0 || $id == $seller->getId()) && ($employeeType == 0 || $seller->getUserRoleId() == $employeeType)) {
                                $userCommisionDetail = new UserCommisionsDetail();
                                $userCommisionDetail->setUser($seller);
                                $userCommisionDetail->includeService($service);
                                $servicesArray[$seller->getId()] = $userCommisionDetail;
                            }
                        }
                    } else {
                        if (($id == 0 || $id == $seller->getId()) && ($employeeType == 0 || $seller->getUserRoleId() == $employeeType)) {
                            $userCommisionDetail = new UserCommisionsDetail();
                            $userCommisionDetail->setUser($seller);
                            $userCommisionDetail->includeService($service);
                            $servicesArray[$seller->getId()] = $userCommisionDetail;
                        }
                        if ( $seller->getId() != $technician->getId()) {
                            if (($id == 0 || $id == $technician->getId()) && ($employeeType == 0 || $technician->getUserRoleId() == $employeeType)) {
                                $userCommisionDetail = new UserCommisionsDetail();
                                $userCommisionDetail->setUser($technician);
                                $userCommisionDetail->includeService($service);
                                $servicesArray[$technician->getId()] = $userCommisionDetail;
                            }
                        }
                    }
                }
            }
            $sss = array();
            foreach ($servicesArray as $userCommisionDetail) {
                $user = $userCommisionDetail->getUser();
                array_push($sss, array(
                    'user' => array(
                        'id' => $user->getId(),
                        'name' => $user->getFullName(),
                        'email' => $user->getEmail(),
                        'phone' => $user->getCellPhone(),
                    ),
                    'totalForSales' => $userCommisionDetail->getTotalForSales(),
                    'totalForTechnician' => $userCommisionDetail->getTotalForTechnician(),
                    'totalForTransportation' => $userCommisionDetail->getTotalForTransportation(),
                    'services' => $userCommisionDetail->getServices(),
                ));
            }

            return new Response(json_encode(array(
                'list'   => $sss,
                'error' => false,
                'msg'   => '')));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Liquidations::getServicesCommisionPendingAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }
}
