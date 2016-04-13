<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Tech506\Bundle\CallServiceBundle\Entity\Technician;
use Tech506\Bundle\SecurityBundle\Entity\User;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;
use Tech506\Bundle\CallServiceBundle\Util\DateUtil;

class TechniciansController extends Controller {

    /**
     * @Route("/technicians", name="_admin_technicians")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function techniciansAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Users/technicians.html.twig', array(
            'role' => 'ROLE_TECHNICIAN'
        ));
    }

    /**
     * Returns the List of Administrators paginated for Bootstrap Table
     *
     * @Route("/technicians/list", name="_admin_technicians_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function paginatedListAction() {
        return $this->getList();
    }

    /**
     * Save or Update a Entity from the Catalog
     *
     * @Route("/technicians/save", name="_admin_technicians_save")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function saveAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }

        try {
            //Get parameters
            $request = $this->getRequest()->request;
            $id = $request->get('id');
            $email = $request->get('email');
            $isActive = $request->get('isActive');
            $isCreating = false;

            $translator = $this->get("translator");

            if( isset($id)) {
                $em = $this->getDoctrine()->getManager();
                $entity = new Technician();
                $user = new User();
                if($id != 0) { //It's updating, find the user
                    $entity = $em->getRepository('Tech506CallServiceBundle:Technician')->find($id);
                    $user = (isset($entity))? $entity->getUser():null;
                }
                if( isset($entity) ) {
                    //Set User Information
                    $user->setName($request->get('name'));
                    $user->setLastname($request->get('lastname'));
                    $user->setUsername($request->get('username'));
                    $user->setCellPhone($request->get('cellPhone'));
                    $user->setHomePhone($request->get('homePhone'));
                    $user->setEmail($email);
                    $user->setIdentification($request->get("identification"));
                    $user->setIdentificationType($request->get("identificationType"));
                    $user->setBirthPlace($request->get("birthPlace"));
                    $user->setNeighborhood($request->get('neighborhood'));
                    $user->setAddress($request->get('address'));
                    $user->setRh($request->get('rh'));
                    $user->setMaritalStatus($request->get('maritalStatus'));
                    $user->setGender($request->get('gender'));
                    $user->setBirthdate(DateUtil::getDateValueFromUI($request->get('birthDate')));
                    $user->setIsActive( (isset($isActive))? 1:0);
                    $rawPassword = "abc123";
                    if($id == 0) { // If it's new must generates a new password
                        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                        $user->setPassword($encoder->encodePassword($rawPassword, $user->getSalt()));
                        $isCreating = true;
                    }
                    $entity->setVinculationDate(DateUtil::getDateValueFromUI($request->get('vinculationDate')));
                    $entity->setVinculationEndingDate(DateUtil::getDateValueFromUI($request->get('vinculationEndingDate')));

                    $lastUniformDate = $request->get('lastUniformDate');
                    $entity->setLastUniformDate((isset($lastUniformDate) && trim($lastUniformDate) != "")?
                        new \DateTime($lastUniformDate) : null);

                    $entity->setShirtSize($request->get('shirtSize'));
                    $entity->setPantsSize($request->get('pantsSize'));
                    $entity->setShoesSize($request->get('shoesSize'));
                    $entity->setMotorcycleLicensePlate($request->get('motorcycleLicensePlate'));
                    $entity->setObservations($request->get('observations'));

                    $entity->setEps($request->get('eps'));
                    $entity->setArl($request->get('arl'));
                    $entity->setCompensationFund($request->get('compensationFund'));
                    $entity->setPensionFund($request->get('pensionFund'));

                    $userId = $user->getId();
                    $userId = (isset($userId))? $userId:0;
                    $logger->info("Validate Emal with UserId: " . $userId);
                    if($em->getRepository("Tech506SecurityBundle:User")
                        ->checkUniqueUsernameAndEmail($user->getUsername(), $email, $userId) ) {
                        //$entity = new Technician();
                        if($isCreating) { // If it's new must email the new account email including the password
                            $role = $em->getRepository('Tech506SecurityBundle:Role')->findOneByRole(RolesEnum::TECHNICIAN);
                            $user->getUserRoles()->add($role);
                            $em->persist($user);
                            $em->flush();
                            $entity->setUser($user);
                            $em->persist($entity);
                            $em->flush();
                        } else {
                            $em->persist($user);
                            $em->persist($entity);
                            $em->flush();
                        }
                        return new Response(json_encode(array(
                            'error' => false,
                            'userId' => $user->getId(),
                            'msg' => $translator->trans('technician.save.success'))));
                    } else {
                        return new Response(json_encode(array(
                            'error' => true,
                            'msg' => $translator->trans('user.username.and.email.must.be.uniques'))));
                    }
                } else {
                    return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters 2")));
                }
            } else {
                return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters 1")));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('User::saveAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    /**
     * Delete an Entity of the Catalog
     *
     * @Route("/technicians/delete", name="_admin_technicians_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function deleteAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }

        try {
            //Get parameters
            $request = $this->get('request');
            $id = $request->get('id');
            $entity = $request->get('entity');
            $translator = $this->get("translator");

            if( isset($id) ){
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('Tech506SecurityBundle:User')->find($id);
                if( isset($user) ) {
                    $em->remove($user);
                    $em->flush();
                    return new Response(json_encode(array(
                        'error' => false,
                        'msg' => $translator->trans('catalog.delete.success'))));
                } else {
                    return new Response(json_encode(array(
                        'error' => true,
                        'msg' => $translator->trans('validation.not.found'))));
                }
            } else {
                return new Response(json_encode(array('error' => true, 'msg' => "Missing Parameters")));
            }
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Catalog::deleteAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    private function getList() {
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
            $em = $this->getDoctrine()->getManager();
            $usr= $this->get('security.context')->getToken()->getUser();
            $paginator = $em->getRepository('Tech506CallServiceBundle:Technician')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order);
            $results = $this->getResults($paginator);
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('User::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getResults($paginator) {
        $results = array();
        foreach($paginator as $technician) {
            $user = $technician->getUser();
            $birtDate = $user->getBirthdate();
            $vinculationDate = $technician->getVinculationDate();
            $vinculationEndingDate = $technician->getVinculationEndingDate();
            $lastUniformDate = $technician->getLastUniformDate();
            array_push($results, array(
                'id' => $technician->getId(),
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'cellPhone' => $user->getCellPhone(),
                'isActive'  => $user->isActive(),
                'username'  => $user->getUsername(),
                'identification' => $user->getIdentification(),
                'identificationType' => $user->getIdentificationType(),
                'birthDate' => (isset($birtDate))? $birtDate->format('d/m/Y'):'',
                'birthPlace' => $user->getBirthPlace(),
                'rh' => $user->getRh(),
                'neighborhood' => $user->getNeighborhood(),
                'address' => $user->getAddress(),
                'homePhone' => $user->getHomePhone(),
                'maritalStatus' => $user->getMaritalStatus(),
                'gender' => $user->getGender(),
                'picture'   => $user->getPicture(),

                'vinculationDate' => (isset($vinculationDate))? $vinculationDate->format('d/m/Y'):'',
                'vinculationEndingDate' =>(isset($vinculationEndingDate))? $vinculationEndingDate->format('d/m/Y'):'',
                'lastUniformDate' =>(isset($lastUniformDate))? $lastUniformDate->format('d/m/Y'):'',
                'shirtSize' => $technician->getShirtSize(),
                'pantsSize' => $technician->getPantsSize(),
                'shoesSize' => $technician->getShoesSize(),
                'motorcycleLicensePlate' => $technician->getMotorcycleLicensePlate(),
                'observations' => $technician->getObservations(),

                'eps' => $technician->getEps(),
                'arl' => $technician->getArl(),
                'compensationFund' => $technician->getCompensationFund(),
                'pensionFund' => $technician->getPensionFund(),
            ));
        }
        return $results;
    }
}