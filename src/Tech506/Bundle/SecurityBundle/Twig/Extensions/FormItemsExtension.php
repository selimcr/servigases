<?php
namespace Tech506\Bundle\SecurityBundle\Twig\Extensions;

use Tech506\Bundle\CallServiceBundle\Util\Enum\Role;
use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;
use Tech506\Bundle\SecurityBundle\Entity\User;
use Tech506\Bundle\SecurityBundle\Util\Enum\IdentificationTypesEnum;
use Tech506\Bundle\SecurityBundle\Util\Enum\MaritalStatusEnum;
use Tech506\Bundle\SecurityBundle\Util\Enum\RHEnum;

/**
 *
 */
class FormItemsExtension extends \Twig_Extension
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
            'renderIdentificationTypesSelect' => new \Twig_Function_Method($this, 'renderIdentificationTypesSelect'),
            'renderRHSelect' => new \Twig_Function_Method($this, 'renderRHSelect'),
            'renderMaritalStatusSelect' => new \Twig_Function_Method($this, 'renderMaritalStatusSelect'),
            'renderGenderSelect' => new \Twig_Function_Method($this, 'renderGenderSelect'),
            'renderStates' => new \Twig_Function_Method($this, 'renderStates'),
            'renderAddressInputs' => new \Twig_Function_Method($this, 'renderAddressInputs'),
            'renderProductCategoriesSelect' => new \Twig_Function_Method($this, 'renderProductCategoriesSelect'),
            'renderProductCategoriesFilterSelect' => new \Twig_Function_Method($this, 'renderProductCategoriesFilterSelect'),
            'renderProductsCategorizedSelect' => new \Twig_Function_Method($this, 'renderProductsCategorizedSelect'),
            'renderServicesSelect' => new \Twig_Function_Method($this, 'renderServicesSelect'),
            'renderSellersSelect' => new \Twig_Function_Method($this, 'renderSellersSelect'),
            'renderTechniciansSelect' => new \Twig_Function_Method($this, 'renderTechniciansSelect'),
            'renderServiceStatusSelect' => new \Twig_Function_Method($this, 'renderServiceStatusSelect'),

            );
    }

    public function renderIdentificationTypesSelect( ) {
        $html = '<select id="identificationType" name="identificationType" class="form-control">';
        foreach(IdentificationTypesEnum::getConstants() as $type){
            $html .= '<option value="' . $type . '">' . $this->translator->trans('identification.type.' . $type) . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderRHSelect() {
        $html = '<select id="rh" name="rh" class="form-control">';
        $html .= '<option value=""></option>';
        foreach(RHEnum::getConstants() as $type){
            $html .= '<option value="' . $type . '">' . $type . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderMaritalStatusSelect() {
        $html = '<select id="maritalStatus" name="maritalStatus" class="form-control">';
        $html .= '<option value="0"></option>';
        foreach(MaritalStatusEnum::getConstants() as $type){
            $html .= '<option value="' . $type . '">' . $this->translator->trans('marital.status.' . $type) . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderGenderSelect() {
        $html = '<select id="gender" name="gender" class="form-control">';
        $html .= '<option value="0"></option>';
        $html .= '<option value="1">' . $this->translator->trans('gender.male') . '</option>';
        $html .= '<option value="2">' . $this->translator->trans('gender.female') . '</option>';
        $html .= "</select>";
        return $html;
    }

    public function renderStates($stateCode) {
        $html = '<select id="state" name="state" class="form-control read-only-on-canceled select2">';
        $html .= '<option value="0">&nbsp;</option>';
        $states = $this->em->getRepository("Tech506CallServiceBundle:State")->findAll();
        foreach($states as $state){
            $html .= '<option value="' . $state->getId() . '"' .
                ($stateCode == $state->getId()? ' selected="selected"':'') .
                '>' . $state->getName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderProductCategoriesSelect() {
        $html = '<select id="category" name="category" class="form-control read-only-on-canceled">';
        $categories = $this->em->getRepository("Tech506CallServiceBundle:ProductCategory")->findAll();
        foreach($categories as $cat){
            $html .= '<option value="' . $cat->getId() . '"' .
                '>' . $cat->getName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderProductCategoriesFilterSelect() {
        $html = '<select id="categoryFilter" name="categoryFilter" class="form-control read-only-on-canceled">';
        $html .= '<option value="0">Todas</option>';
        $categories = $this->em->getRepository("Tech506CallServiceBundle:ProductCategory")->findAll();
        foreach($categories as $cat){
            $html .= '<option value="' . $cat->getId() . '"' .
                '>' . $cat->getName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderProductsCategorizedSelect($productId) {
        $html = '<select id="product" name="product" class="select2_group form-control">';
        $categories = $this->em->getRepository("Tech506CallServiceBundle:ProductCategory")->findAll();
        foreach($categories as $cat){
            $html .= '<optgroup label="' . $cat->getName() . '">';
            foreach($cat->getProducts() as $product) {
                $html .= '<option value="' . $product->getId() . '"' .
                    ($productId == $product->getId()? ' selected="selected"':'') .
                    '>' . $product->getName() . '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderServicesSelect($onlyOnManaging) {
        $html = '<select id="product" name="product" class="form-control" style="display: inline-block; width: 350px;">';
        $services = $this->em->getRepository("Tech506CallServiceBundle:Product")->findServices($onlyOnManaging);
        $html .= '<option value="0">&nbsp;</option>';
        foreach($services as $service) {
            $html .= '<option value="' . $service->getId() . '"' .
                '>' . $service->getName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderSellersSelect() {
        $html = '<select id="sellersFilter" name="sellersFilter" class="form-control read-only-on-canceled">';
        $html .= '<option value="0">Todos</option>';
        $sellers = $this->em->getRepository("Tech506SecurityBundle:User")->getUsersByRole(Role::SELLER);
        foreach($sellers as $seller){
            $html .= '<option value="' . $seller->getId() . '"' .
                '>' . $seller->getFullName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderServiceStatusSelect() {
        $html = '<select id="serviceStatusFilter" name="serviceStatusFilter" class="form-control read-only-on-canceled">';
        $html .= '<option value="0">Todos</option>';
        $sellers = $this->em->getRepository("Tech506SecurityBundle:User")->getUsersByRole(Role::SELLER);
        foreach(TechnicianServiceStatus::getConstants() as $status){
            $html .= '<option value="' . $status . '"' .
                '>' . $this->translator->trans('services.status.' . $status) . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderTechniciansSelect() {
        $html = '<select id="techniciansFilter" name="techniciansFilter" class="form-control read-only-on-canceled">';
        $html .= '<option value="0">Todos</option>';
        $technicians = $this->em->getRepository("Tech506CallServiceBundle:Technician")->findAll();
        foreach($technicians as $technician){
            $html .= '<option value="' . $technician->getId() . '"' .
                '>' . $technician->getUser()->getFullName() . '</option>';
        }
        $html .= "</select>";
        return $html;
    }

    public function renderAddressInputs($address) {
        $dir1 = "";
        $dir2 = "";
        $dir3 = "";
        $dir4 = "";
        if($address != ""){
            $address = str_replace("::", "&", $address);
            $addressComponents = preg_split("/[&]+/", $address);

            $dir1 = trim($addressComponents[0]);
            $dir2 = sizeof($addressComponents) < 2? "":trim($addressComponents[1]);
            $dir3 = sizeof($addressComponents) < 3? "":trim($addressComponents[2]);
            $dir4 = sizeof($addressComponents) < 4? "":trim($addressComponents[3]);
        }
        $list = array("Calle", "Carrera", "Avenida", "Avenida Carrera", "Avenida Calle", "Circular", "Circunvalar",
            "Diagonal", "Manzana", "Transversal", "V�a");

        $html = '<span class="address-inputs">';
        $html .= '<select id="dir1" name="dir1" class="form-control combo first read-only-on-canceled">';
        foreach ($list as $item) {
            $html .= '<option value="' . $item . '"' . ($item == $dir1? ' selected="selected"':'') . '>' . $item . '</option>';
        }
        $html .= '</select>';
        $html .= '<input name="dir2" class="form-control read-only-on-canceled" id="dir2" value="' . $dir2 . '" style="width: 100%; font-size: 11px;" type="text">';
        $html .= '<br><label style="width: 10%;" name="">#&nbsp;&nbsp;</label>';
        $html .= '<input name="dir3" class="form-control read-only-on-canceled" id="dir3" value="' . $dir3 . '" style="width: 40%; font-size: 11px;" type="text">';
        $html .= '<label style="width: 10%;" name="">&nbsp;&nbsp;-&nbsp;&nbsp;</label>';
        $html .= '<input name="dir4" class="form-control last read-only-on-canceled" id="dir4" value="' . $dir4 . '" style="width: 40%; font-size: 11px;" type="text">';
            //<input type="hidden" name="direccion" id="direccion" value="{{dir1}} {{dir2}} # {{dir3}} - {{dir4}}, en {{dir5}}. Edif: {{dir6}}" style="">
        $html .= '</span>';
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
        return 'form_items_twig_extension';
    }
}