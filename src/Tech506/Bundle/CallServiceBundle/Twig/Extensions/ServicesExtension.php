<?php
namespace Tech506\Bundle\CallServiceBundle\Twig\Extensions;
use Tech506\Bundle\CallServiceBundle\Entity\ServiceDetail;

/**
 *
 */
class ServicesExtension extends \Twig_Extension
{
    private $em;
    private $conn;
    private $translator;
    private $session;
    private $securityContext;
    private $logger;
    private $router;

    public function __construct(\Doctrine\ORM\EntityManager $em, $translator, $session, $securityContext,
                                $logger, $router) {
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->translator = $translator;
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->logger = $logger;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            'renderServiceScheduleBox' => new \Twig_Function_Method($this, 'renderServiceScheduleBox'),
            );
    }

    public function renderServiceScheduleBox( $service, $number ) {
        $totalAmount = 0;
        $servicesHtml = "";
        $title = '';
        foreach($service->getDetails() as $detail){
            //$detail = new ServiceDetail();
            $totalAmount += $detail->getFullPrice();
            $title = $detail->getProductSaleType()->getProduct()->getName();
            $servicesHtml .= $title . " | ";
        }
        $numberOfServices = sizeof($service->getDetails());
        if ($numberOfServices > 1) {
            $title = 'Servicios M&uacute;ltiples';
            $servicesHtml = '        <p><span>' . $this->translator->trans('services') .
                ': </span>' . $servicesHtml . '</p>';
        } elseif ($numberOfServices == 0) {
            $title = '' . $service;
            $servicesHtml = "";
        } else {
            $servicesHtml = "";
        }
        $html = '<li>';
        $html .= '<div class="service-schedule-box">';
            $html .= '<div class="block_content">';
            $html .= '    <h2 class="title">';
            $html .= '        <a>' . $number . '. ' . $title . ' ' . $this->renderMoney($totalAmount) . '</a>';
            $html .= '    </h2>';
            $html .= '        <p><span># Referencia: </span>' . $service->getId() . ' | <span>' . $this->translator->trans('hour') .
                ': </span><span class="hour">' . $service->getHour() . '</span></p>';
            $html .= '        <p><span>' . $this->translator->trans('client') . ': </span>' . $service->getClient() . '</p>';
            $state = $this->em->getRepository("Tech506CallServiceBundle:State")->find($service->getState());
            $html .= '        <p class="address-detail"><span>' . $this->translator->trans('address') . ': </span>';
            if(  isset($state) ) {
                $html .= $state;
            }
            $html .= '        | ' . $service->getAddress();
            if(  $service->getNeighborhood() != "") {
                $html .= ' | ' . $service->getNeighborhood();
            }
            if(  $service->getAddressDetail() != "") {
                $html .= ' | ' . $service->getAddressDetail();
            }
            if(  $service->getReferencePoint() != "") {
                $html .= ' | ' . $service->getReferencePoint();
            }
            $html .= '</p>';
            $html .= '    <div class="byline">';
            $html .= '        <a>' . $service->getObservations() . '</a>';
            $html .= '    </div>';
            $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    public function renderServiceScheduleBox1( $service, $number ) {
        //$html = '<p>' . $service . '</p>';
        $totalAmount = 0;
        $servicesHtml = "";
        $title = '';
        foreach($service->getDetails() as $detail){
            //$detail = new ServiceDetail();
            $totalAmount += $detail->getFullPrice();
            $title = $detail->getProductSaleType()->getProduct()->getName();
            $servicesHtml .= $title . " | ";
        }
        $numberOfServices = sizeof($service->getDetails());
        if ($numberOfServices > 1) {
            $title = 'Servicios M&uacute;ltiples';
            $servicesHtml = '        <p><span>' . $this->translator->trans('services') .
                ': </span>' . $servicesHtml . '</p>';
        } elseif ($numberOfServices == 0) {
            $title = '' . $service;
            $servicesHtml = "";
        } else {
            $servicesHtml = "";
        }
        $html = '<div class="col-md-6 col-lg-6 col-sm-12 service-schedule-box">';
        $html .= '    <blockquote>';
        $html .= '        <h2>' . $number . '. ' . $title . ' ' . $this->renderMoney($totalAmount) . '</h2>';
        $html .= '        <p><span>' . $this->translator->trans('hour') .
            ': </span><span class="hour">' . $service->getHour() . '</span></p>';
        $html .= '        <p><span>' . $this->translator->trans('client') . ': </span>' . $service->getClient() . '</p>';
        $state = $this->em->getRepository("Tech506CallServiceBundle:State")->find($service->getState());
        $html .= '        <p class="address-detail"><span>' . $this->translator->trans('address') . ': </span>';
        if(  isset($state) ) {
            $html .= $state;
        }
        $html .= '        | ' . $service->getAddress();
        if(  $service->getNeighborhood() != "") {
            $html .= ' | ' . $service->getNeighborhood();
        }
        if(  $service->getAddressDetail() != "") {
            $html .= ' | ' . $service->getAddressDetail();
        }
        if(  $service->getReferencePoint() != "") {
            $html .= ' | ' . $service->getReferencePoint();
        }
        $html .= '</p>';
        $html .= $servicesHtml;
        $html .= '        <footer>' . $service->getObservations() . '</footer>';
        $html .= '    </blockquote>';
        $html .= '</div>';
        return $html;
    }

    public function twig_include_raw($file) {
        return file_get_contents($file);
    }

    public function getFilters() {
        return array(
            'renderPercentage' => new \Twig_Filter_Method($this, 'renderPercentage'),
            'renderMoney' => new \Twig_Filter_Method($this, 'renderMoney'),
        );
    }

    public function renderPercentage( $value ) {
        return round($value, 1) . "%";
    }

    public function renderMoney( $value ) {
        return "$" . number_format($value, 2, '.', ',');
    }

    public function getName()
    {
        return 'form_services_twig_extension';
    }
}