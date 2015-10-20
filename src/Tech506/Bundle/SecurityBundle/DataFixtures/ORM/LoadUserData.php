<?php

namespace Tecnotek\Bundle\AsiloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tech506\Bundle\CallServiceBundle\Entity\Seller;
use Tech506\Bundle\CallServiceBundle\Entity\Technician;
use Tech506\Bundle\SecurityBundle\Entity\User;
use Tech506\Bundle\SecurityBundle\Util\Enum\IdentificationTypesEnum;
use Tech506\Bundle\SecurityBundle\Util\Enum\RolesEnum;

class LoadUserData implements FixtureInterface, ContainerAwareInterface {
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager) {
        //Create Roles
        $roleAdmin = new \Tech506\Bundle\SecurityBundle\Entity\Role();
        $roleAdmin->setName("Administrator");
        $roleAdmin->setRole(RolesEnum::ADMINISTRATOR);
        $manager->persist($roleAdmin);

        $roleTechnician = new \Tech506\Bundle\SecurityBundle\Entity\Role();
        $roleTechnician->setName("Technician");
        $roleTechnician->setRole(RolesEnum::TECHNICIAN);
        $manager->persist($roleTechnician);

        $roleSeller = new \Tech506\Bundle\SecurityBundle\Entity\Role();
        $roleSeller->setName("Seller");
        $roleSeller->setRole(RolesEnum::SELLER);
        $manager->persist($roleSeller);

        $this->createUsers($manager, $roleAdmin, $roleTechnician, $roleSeller);

        $manager->flush();
    }

    public function createUsers(ObjectManager $manager, $roleAdmin, $roleTechnician, $roleSeller){
        /*** Create Users ***/
        $admin = new User();
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($admin);
        $admin->setUsername('selim')->setEmail('selimdiaz@gmail.com')
            ->setPassword($encoder->encodePassword('password', $admin->getSalt()))
            ->setName("Selim")->setLastname("Díaz")->setCellPhone("3012526470")->setGender(1)
            ->setIdentificationType(IdentificationTypesEnum::FOREIGNER)->setIdentification("489079");
        $admin->getUserRoles()->add($roleAdmin);
        $manager->persist($admin);

        $guille = new User();
        $guille->setUsername('guille')->setEmail('guille@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $guille->getSalt()))
            ->setName("Guillermo")->setLastname("Higuita")->setCellPhone("")->setGender(1)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("1111111");
        $guille->getUserRoles()->add($roleAdmin);
        $manager->persist($guille);

        $esteban = new User();
        $esteban->setUsername('esteban')->setEmail('esteban@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $esteban->getSalt()))
            ->setName("Esteban")->setLastname("Cuartas")->setCellPhone("")->setGender(1)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("2222222");
        $esteban->getUserRoles()->add($roleAdmin);
        $manager->persist($esteban);

        $this->createSellers($manager, $roleSeller);

        $this->createTechnicians($manager, $roleTechnician);
    }

    public function createSellers(ObjectManager $manager, $roleSeller){
        $ana = new User();
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($ana);
        $ana->setUsername('anamaria')->setEmail('anamaria@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $ana->getSalt()))
            ->setName("Ana")->setLastname("María")->setCellPhone("555555501")->setGender(2)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("101");
        $ana->getUserRoles()->add($roleSeller);
        $manager->persist($ana);

        $anaE = new Seller();
        $anaE->setUser($ana);
        $manager->persist($anaE);

        $catalina = new User();
        $catalina->setUsername('catalina')->setEmail('catalina@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $catalina->getSalt()))
            ->setName("Catalina")->setLastname("Rojas")->setCellPhone("555555502")->setGender(2)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("102");
        $catalina->getUserRoles()->add($roleSeller);
        $manager->persist($catalina);

        $catalinaE = new Seller();
        $catalinaE->setUser($catalina);
        $manager->persist($catalinaE);
    }

    public function createTechnicians(ObjectManager $manager, $roleTechnician){
        $juanguillermo = new User();
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($juanguillermo);
        $juanguillermo->setUsername('juanguillermo')->setEmail('juanguillermo@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $juanguillermo->getSalt()))
            ->setName("Juan")->setLastname("Guillermo")->setCellPhone("555555511")->setGender(1)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("201");
        $juanguillermo->getUserRoles()->add($roleTechnician);
        $manager->persist($juanguillermo);

        $juanGuillermoE = new Technician();
        $juanGuillermoE->setUser($juanguillermo);
        $manager->persist($juanGuillermoE);

        $milton = new User();
        $milton->setUsername('milton')->setEmail('milton@servigases.com.co')
            ->setPassword($encoder->encodePassword('password', $milton->getSalt()))
            ->setName("Milton")->setLastname("")->setCellPhone("555555512")->setGender(1)
            ->setIdentificationType(IdentificationTypesEnum::NATIONAL)->setIdentification("202");
        $milton->getUserRoles()->add($roleTechnician);
        $manager->persist($milton);

        $miltonE = new Technician();
        $miltonE->setUser($milton);
        $manager->persist($miltonE);
    }
}