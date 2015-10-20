<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Proxies\__CG__\Tech506\Bundle\SecurityBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Tech506\Bundle\CallServiceBundle\Entity\Seller;
use Tech506\Bundle\SecurityBundle\Entity\User;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;

class SellersController extends Controller {

    /**
     * @Route("/sellers", name="_admin_sellers")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function sellersAction() {
        return $this->render('Tech506CallServiceBundle:Admin:Users/sellers.html.twig', array(
            'role' => RolesEnum::SELLER
        ));
    }

    /**
     * Returns the List of Sellers paginated for Bootstrap Table
     *
     * @Route("/sellers/list", name="_admin_sellers_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function paginatedListAction() {
        return $this->getList();
    }

    /**
     * Save or Update a Entity from the Catalog
     *
     * @Route("/sellers/save", name="_admin_sellers_save")
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
                $entity = new Seller();
                $user = new User();
                if($id != 0) { //It's updating, find the user
                    $entity = $em->getRepository('Tech506CallServiceBundle:Seller')->find($id);
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
                    $birthDate = $request->get('birthDate');
                    $user->setBirthdate((isset($birthDate) && trim($birthDate) != "")?
                        new \DateTime($birthDate) : null);
                    $user->setIsActive( (isset($isActive))? 1:0);
                    $rawPassword = "abc123";
                    if($id == 0) { // If it's new must generates a new password
                        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                        $user->setPassword($encoder->encodePassword($rawPassword, $user->getSalt()));
                        $isCreating = true;
                    }

                    $vinculationDate = $request->get('vinculationDate');
                    $entity->setVinculationDate((isset($vinculationDate) && trim($vinculationDate) != "")?
                        new \DateTime($vinculationDate) : null);

                    $vinculationEndingDate = $request->get('vinculationEndingDate');
                    $entity->setVinculationEndingDate((isset($vinculationEndingDate) && trim($vinculationEndingDate) != "")?
                        new \DateTime($vinculationEndingDate) : null);

                    $entity->setObservations($request->get('observations'));
                    $entity->setEps($request->get('eps'));
                    $entity->setArl($request->get('arl'));
                    $entity->setCompensationFund($request->get('compensationFund'));
                    $entity->setPensionFund($request->get('pensionFund'));

                    $userId = $user->getId();
                    $userId = (isset($userId))? $userId:0;
                    if($em->getRepository("Tech506SecurityBundle:User")
                        ->checkUniqueUsernameAndEmail($user->getUsername(), $email, $userId) ) {
                        if($isCreating) { // If it's new must email the new account email including the password
                            $role = $em->getRepository('Tech506SecurityBundle:Role')->findOneByRole(RolesEnum::SELLER);
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
                            'msg' => $translator->trans('seller.save.success'))));
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
            $logger->err('Seller::saveAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'msg' => $info)));
        }
    }

    /**
     * Delete an Entity of the Catalog
     *
     * @Route("/sellers/delete", name="_admin_sellers_delete")
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
            $paginator = $em->getRepository('Tech506CallServiceBundle:Seller')
                ->getPageWithFilter($offset, $limit, $search, $sort, $order);
            $results = $this->getResults($paginator);
            return new Response(json_encode(array('total'   => count($paginator),
                'rows'    => $results)));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Seller::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    private function getResults($paginator) {
        $results = array();
        foreach($paginator as $seller) {
            $user = $seller->getUser();
            $birtDate = $user->getBirthdate();
            $vinculationDate = $seller->getVinculationDate();
            $vinculationEndingDate = $seller->getVinculationEndingDate();
            array_push($results, array(
                'id' => $seller->getId(),
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'cellPhone' => $user->getCellPhone(),
                'isActive'  => $user->isActive(),
                'username'  => $user->getUsername(),
                'identification' => $user->getIdentification(),
                'identificationType' => $user->getIdentificationType(),
                'birthDate' => (isset($birtDate))? $birtDate->format('Y-m-d'):'',
                'birthPlace' => $user->getBirthPlace(),
                'rh' => $user->getRh(),
                'neighborhood' => $user->getNeighborhood(),
                'address' => $user->getAddress(),
                'homePhone' => $user->getHomePhone(),
                'maritalStatus' => $user->getMaritalStatus(),
                'gender' => $user->getGender(),
                'picture'   => $user->getPicture(),

                'vinculationDate' => (isset($vinculationDate))? $vinculationDate->format('Y-m-d'):'',
                'vinculationEndingDate' =>(isset($vinculationEndingDate))? $vinculationEndingDate->format('Y-m-d'):'',
                'observations' => $seller->getObservations(),
                'eps' => $seller->getEps(),
                'arl' => $seller->getArl(),
                'compensationFund' => $seller->getCompensationFund(),
                'pensionFund' => $seller->getPensionFund(),
            ));
        }
        return $results;
    }
}