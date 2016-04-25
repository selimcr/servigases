<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tech506\Bundle\CallServiceBundle\Entity\Client;

/**
 * Class AdminController: Handles the basic requests of the invoices generation and management
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/")
 */
class ClientsController extends Controller {

    /**
     * @Route("/clients/find", name="_admin_clients_find")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function findClientsAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $phone = $request->get('phone');
            $email = $request->get('email');
            $cellPhone = $request->get('cellPhone');
            $em = $this->getDoctrine()->getManager();
            $client = $em->getRepository('Tech506CallServiceBundle:Client')
                ->findClient($phone, $cellPhone, $email);
            if( isset($client) ) {
                return new Response(json_encode(array(
                    'error'             => false,
                    'found'             => true,
                    'msg'               => "Cliente encontrado",
                    'id'                => $client->getId(),
                    'fullName'          => $client->getFullName(),
                    'phone'             => $client->getPhone(),
                    'cellPhone'         => $client->getCellPhone(),
                    'email'             => $client->getEmail(),
                    'address'           => $client->getAddress(),
                    'extraInformation'   => $client->getExtraInformation(),
                )));
            } else {
                return new Response(json_encode(array(
                    'error' => false,
                    'found' => false,
                    'msg'   => "Cliente no encontrado con ninguno de los valores ingresados",
                    'id'    => 0,
                )));
            }

        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Clients::findClientsAction [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

    /**
     * @Route("/clients/save", name="_admin_clients_save")
     * @Security("is_granted('ROLE_SELLER')")
     * @Template()
     */
    public function saveClientAction() {
        $request = $this->get('request');
        if (!$this->get('request')->isXmlHttpRequest()) { // Is the request an ajax one?
            return new Response("<b>Not an ajax call!!!" . "</b>");
        }
        $logger = $this->get('logger');
        try {
            $id = $request->get('id');
            $fullName = $request->get('fullName');
            $address = $request->get('address');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $cellPhone = $request->get('cellPhone');
            $extraInformation = $request->get('extraInformation');
            $em = $this->getDoctrine()->getManager();
            $client = new Client();
            if($id != 0){
                $client = $em->getRepository('Tech506CallServiceBundle:Client')->find($id);
            } else {
                $client = $em->getRepository('Tech506CallServiceBundle:Client')
                    ->findClient($phone, $cellPhone, $email);
                if( !isset($client) ) {
                    $client = new Client();
                }
            }
            $client->setAddress($address);
            $client->setEmail($email);
            $client->setFullName($fullName);
            $client->setCellPhone($cellPhone);
            $client->setPhone($phone);
            $client->setExtraInformation($extraInformation);
            $em->persist($client);
            $em->flush();
            return new Response(json_encode(array(
                'error' => false,
                'msg'   => "Cliente guardado correctamente",
                'id'    => $client->getId(),
            )));
        } catch (Exception $e) {
            $info = toString($e);
            $logger->err('Seller::getUsersList [' . $info . "]");
            return new Response(json_encode(array('error' => true, 'message' => $info)));
        }
    }

}
