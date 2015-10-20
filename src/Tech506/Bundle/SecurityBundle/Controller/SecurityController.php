<?php

namespace Tech506\Bundle\SecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 */
class SecurityController extends Controller {

    /**
     * @Route("/login", name="_demo_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            $error = "bad.credentials";
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/users/permissions", name="_admin_permissions")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function permissionsAction() {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("Tech506SecurityBundle:User")->getEmployees();
        $dql = "SELECT e FROM TecnotekAsiloBundle:ActionMenu e WHERE e.parent is null order by e.sortOrder";
        $query = $em->createQuery($dql);
        $menuOptions = $query->getResult();
        $dql = "SELECT e FROM TecnotekAsiloBundle:Permission e WHERE e.parent is null order by e.sortOrder";
        $query = $em->createQuery($dql);
        $permissions = $query->getResult();
        return $this->render('Tech506SecurityBundle:Admin:permissions.html.twig', array(
            'users'         => $users,
            'menuOptions'   => $menuOptions,
            'permissions'   => $permissions,
        ));
    }

    /**
     * @Route("/users/permissions/load", name="_admin_permissions_load")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function loadPrivilegesAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        try {
            $request = $this->get('request')->request;
            $userId = $request->get('userId');
            $translator = $this->get("translator");
            if( isset($userId) ) {
                $em = $this->getDoctrine()->getEntityManager();
                $user = $em->getRepository("Tech506SecurityBundle:User")->find($userId);
                $currentMenuOptions = $user->getMenuOptions();
                $menuOptions = array();
                foreach( $currentMenuOptions as $menuOption ) {
                    if( sizeof($menuOption->getChildrens()) == 0)
                        array_push($menuOptions, $menuOption->getId());
                }

                $currentPermissions = $user->getPermissions();
                $permissions = array();
                foreach( $currentPermissions as $permission ) {
                    if( sizeof($permission->getChildrens()) == 0)
                        array_push($permissions, $permission->getId());
                }

                return new Response(
                    json_encode(array(
                        'error' => false,
                        'menuOptions' => $menuOptions,
                        'permissions' => $permissions,
                    )));
            } else {
                return new Response(json_encode(array('error' => true, 'message' =>$translator->trans("error.paramateres.missing"))));
            }
        }
        catch (Exception $e) {
            $info = toString($e);
            $logger->err('SuperAdmin::loadPrivilegesAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/users/permissions/save", name="_admin_permissions_save")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function savePrivilegesAction() {
        $logger = $this->get('logger');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        try {
            $request = $this->get('request')->request;
            $userId = $request->get('userId');
            $access = $request->get('access');
            $type = $request->get('type');
            $translator = $this->get("translator");

            if( isset($userId) && isset($access) ) {
                $em = $this->getDoctrine()->getEntityManager();
                $user = $em->getRepository("Tech506SecurityBundle:User")->find($userId);

                if($type == 1) { // Save Menu Options
                    /*** Set Menu Options ***/
                    $currentMenuOptions = $user->getMenuOptions();
                    if($access == ""){
                        $newMenuOptions = array();
                    } else {
                        $newMenuOptions = explode(",", $access);
                    }

                    $optionsToRemove = array();
                    foreach($currentMenuOptions as $currentMenuOption){
                        if( !in_array($currentMenuOption->getId(), $newMenuOptions)){
                            array_push($optionsToRemove, $currentMenuOption );
                        }
                    }

                    foreach($optionsToRemove as $menuOption){
                        $user->removeMenuOption($menuOption);
                    }

                    foreach($newMenuOptions as $newMenuOption){
                        $found = false;
                        foreach($currentMenuOptions as $currentMenuOption){
                            if($currentMenuOption->getId() == $newMenuOption){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            $newEntityMenuOption = $em->getRepository("Tech506SecurityBundle:ActionMenu")
                                ->find($newMenuOption);
                            $user->addMenuOption($newEntityMenuOption);
                        }
                    }
                } else { // Save Permissions
                    $currentPermissions = $user->getPermissions();
                    if($access == ""){
                        $newPermissions = array();
                    } else {
                        $newPermissions = explode(",", $access);
                    }

                    $optionsToRemove = array();
                    foreach($currentPermissions as $currentPermission){
                        if( !in_array($currentPermission->getId(), $newPermissions)){
                            array_push($optionsToRemove, $currentPermission );
                        }
                    }

                    foreach($optionsToRemove as $permission){
                        $user->removePermission($permission);
                    }

                    foreach($newPermissions as $newMenuOption){
                        $found = false;
                        foreach($currentPermissions as $currentMenuOption){
                            if($currentMenuOption->getId() == $newMenuOption){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            $newEntityMenuOption = $em->getRepository("Tech506SecurityBundle:Permission")
                                ->find($newMenuOption);
                            $user->addPermission($newEntityMenuOption);
                        }
                    }
                }
                $em->persist($user);
                $em->flush();
                return new Response(json_encode(array('error' => false)));
            } else {
                return new Response(json_encode(array('error' => true, 'message' =>$translator->trans("error.paramateres.missing"))));
            }
        }
        catch (Exception $e) {
            $info = toString($e);
            $logger->err('SuperAdmin::createEntryAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }
}
