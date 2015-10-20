<?php
namespace Tech506\Bundle\SecurityBundle\Twig\Extensions;

use Tech506\Bundle\SecurityBundle\Entity\User;

/**
 *
 */
class MenuExtension extends \Twig_Extension
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
            'renderMenu' => new \Twig_Function_Method($this, 'renderMenu'),
        );
    }

    public function renderMenu( ) {
        $html = "";
        $user = $this->securityContext->getToken()->getUser();
        $isAdmin = $this->securityContext->isGranted('ROLE_ADMIN');
        $roleId = "" . $user->getFirstRoleId();

        $allowed = array();
        foreach($user->getMenuOptions() as $menuOption){
            array_push($allowed, $menuOption->getId());
        }

        $dql = "SELECT e FROM Tech506SecurityBundle:ActionMenu e WHERE e.parent is null order by e.sortOrder asc";
        $query = $this->em->createQuery($dql);
        $headers = $query->getResult();


/*
            <li>
                <a href="{{ path('_admin_home') }}">
                    <i class="fa fa-home"></i><span>{{ 'home'|trans }}</span>
                </a>
            </li>
--------------------------------------------------------
            <li>
                <a><i class="fa fa-edit"></i> Forms <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none">
                    <li>
                        <a href="#">General Form</a>
                    </li>
                </ul>
            </li>
        */
        $menu = '';
        foreach($headers as $header){
            $menu = '';
            foreach($header->getChildrens() as $children) {
                $submenuHtml = '';
                if(sizeof($children->getChildrens()) == 0) {
                    if($isAdmin || in_array($children->getId(), $allowed)
                        || (strpos($children->getRoles(),$roleId) !== false)){
                        $menu .= '<li><a href="' .
                            $this->getUrl($children) . '">'
                            . $this->translator->trans($children->getLabel()) . '</a></li>';
                    }
                }
            }//End of childrens

            if($menu != '') { // There are subelements of the menu
                $html .= '<li>';
                $html .= '    <a href="' . $this->getUrl($header) . '">'
                    . ($header->getCssClass() != ""? '<i class="' . $header->getCssClass() . '"></i>':'')
                    . $this->translator->trans($header->getLabel())
                    . ' <span class="fa fa-chevron-down"></span></a>';
                $html .= '    <ul class="nav child_menu" style="display: none">';
                $html .= $menu;
                $html .= '    </ul>';
                $html .= '</li>';
            }
        }

        return $html;
    }

    private function getUrl($actionMenu) {
        $url = ($actionMenu->getRoute() == "#"? "#":$this->router->generate($actionMenu->getRoute()));
        $url .= $actionMenu->getAdditionalPath();
        return $url;
    }

    public function twig_include_raw($file) {
        return file_get_contents($file);
    }

    public function getFilters() {
        return array(
            'renderPercentage' => new \Twig_Filter_Method($this, 'renderPercentage'),
        );
    }

    public function renderPercentage( $value ) {
        return round($value, 1) . "%";
    }

    public function getName()
    {
        return 'menu_twig_extension';
    }
}