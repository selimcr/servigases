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
use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;

/**
 * Class AdminController: Handles the basic requests of the generation of reports
 *
 * @package Tech506\Bundle\CallServiceBundle\Controller
 * @Route("/reports")
 */
class ReportsController extends Controller {

    /**
     * @Route("/schedule/daily", name="_admin_reports_daily_schedule")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function renderDailyScheduleAction() {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get("request")->request;
        $technicianId = $request->get("technician");
        $visitDate = $request->get("visitDate");
        $tomorrow = new \DateTime('tomorrow');
        $visitDate = ( isset($visitDate) && $visitDate != '')? $visitDate:date_format($tomorrow, "d/m/Y");
        $vDate = \DateTime::createFromFormat('d/m/Y', $visitDate);

        $technicianId = isset($technicianId)? $technicianId:0;
        $services = $em->getRepository('Tech506CallServiceBundle:TechnicianService')
                            ->findScheduledServicesOnDay($technicianId, date_format($vDate, "Y-m-d"));
        $technicians = $em->getRepository('Tech506CallServiceBundle:Technician')->findAll();
        $technicianServices = array();
        $currentTech = null;
        $currentTechServices = array();

        foreach ($services as $service ) {
            if($currentTech != null && $service->getTechnician()->getId() != $currentTech->getId()) {
                $technicianArray  = array();
                $technicianArray['technician'] = $currentTech;
                $technicianArray['services'] = $currentTechServices;
                array_push($technicianServices, $technicianArray);
                $currentTechServices = array();
            }
            array_push($currentTechServices, $service);
            $currentTech = $service->getTechnician();
        }
        if (sizeof($currentTechServices) > 0) {
            $technicianArray  = array();
            $technicianArray['technician'] = $currentTech;
            $technicianArray['services'] = $currentTechServices;
            array_push($technicianServices, $technicianArray);
        }
        return $this->render('Tech506CallServiceBundle:Admin:Reports/daily_schedule.html.twig', array(
            'technicianId' => $technicianId,
            'visitDate' => $visitDate,
            'services' => $technicianServices,
            'technicians' => $technicians,
        ));
    }

}
