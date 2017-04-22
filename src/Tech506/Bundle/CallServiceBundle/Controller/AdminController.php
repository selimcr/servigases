<?php

namespace Tech506\Bundle\CallServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdminController: Handles the basic requests of the admin site
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller {

    /**
     * @Route("/", name="_admin_home")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function indexAction() {

        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        if ($isAdmin) {
            return $this->render(
                'Tech506CallServiceBundle:Admin:dashboards/index.html.twig',
                $this->renderAdminDashboard()
            );
        } else {
            return $this->render(
                'Tech506CallServiceBundle:Admin:dashboards/seller.html.twig',
                $this->renderSellerDashboard()
            );
        }
    }

    public function renderAdminDashboard() {
        // Get initial data to render the dashboard
        $em = $this->getDoctrine()->getEntityManager();
        $today = new \DateTime();
        $date = strtotime(date_format($today, "Y/m/d") . ' -7 day');
        $initialDay = date('Y-m-d', $date);
        $logger = $this->get("logger");
        $logger->info($initialDay);
        $result = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
            ->findServicesCounter($initialDay, date_format($today, "Y/m/d"));
        $data = "";
        $logger = $this->get("logger");
        foreach($result as $row) {
            $data .= '["' . $row['date'] . '",' . $row['counter'] . '],';
            //$logger->info($row['date'] . " :: " . $row['counter']);
        }
        return array('sales_chart_data' => substr($data, 0, -1),
            'data' => $result
        );
    }

    public function renderSellerDashboard() {
        $em = $this->getDoctrine()->getEntityManager();
        $today = new \DateTime();
        $date = strtotime(date_format($today, "Y/m/d") . ' -7 day');
        $initialDay = date('Y-m-d', $date);
        $logger = $this->get("logger");
        $logger->info($initialDay);
        $result = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
            ->findServicesCounter($initialDay, date_format($today, "Y/m/d"));
        $data = "";
        $logger = $this->get("logger");
        foreach($result as $row) {
            $data .= '["' . $row['date'] . '",' . $row['counter'] . '],';
            //$logger->info($row['date'] . " :: " . $row['counter']);
        }
        return array('sales_chart_data' => substr($data, 0, -1),
            'data' => $result
        );
    }

    /**
     * @Route("/data/completed", name="_admin_data_completed")
     * @Security("is_granted('ROLE_EMPLOYEE')")
     * @Template()
     */
    public function getCompletedDataAction() {
        // Get initial data to render the dashboard
        $logger = $this->get("logger");
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();
        $logger->info($request->get('startDate') . ":" . $request->get('endDate'));
        $result = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
            ->findServicesCounter($request->get('startDate'), $request->get('endDate'));
        /*$data = "";

        foreach($result as $row) {
            $data .= '["' . $row['date'] . '",' . $row['counter'] . '],';
            //$logger->info($row['date'] . " :: " . $row['counter']);
        }
        return array('sales_chart_data' => substr($data, 0, -1),
            'data' => $result
        );*/
        return new Response(json_encode(array('error' => false, 'data' => $result)));
    }
}
