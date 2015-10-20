<?php

namespace Tech506\Bundle\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Tech506\Bundle\SecurityBundle\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $identification;

    /**
     * @ORM\Column(type="integer", name="identification_type")
     */
    private $identificationType;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $cellPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $birthPlace;

    /**
     * @ORM\Column(type="string", length=25, nullable = true)
     */
    private $rh;

    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $neighborhood;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable = true)
     */
    private $homePhone;

    /**
     * @ORM\Column(type="integer")
     */
    private $maritalStatus;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\Column(name="birthdate", type="date", nullable = true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     *
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="ActionMenu", inversedBy="users")
     */
    private $menuOptions;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", inversedBy="users")
     */
    private $permissions;

    public function __construct() {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        $this->salt = md5(uniqid(null, true));
        $this->menuOptions = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->maritalStatus = 0;
    }

    public function getId() {
        return $this->id;
    }

    public function isActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        return $this->isActive = $isActive;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setIdentificationType($identificationType) {
        $this->identificationType = $identificationType;
        return $this;
    }

    public function getIdentificationType() {
        return $this->identificationType;
    }

    public function setIdentification($identification) {
        $this->identification = $identification;
        return $this;
    }

    public function getIdentification() {
        return $this->identification;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getFirstRoleId() {
        foreach ($this->roles as $role) {
            return $role->getId();
        }
        return 0;
    }

    public function getRoles() {
        //return array('ROLE_ADMIN');
        $assignedRoles = array();
        foreach ($this->roles as $role) {
            array_push($assignedRoles, $role->getRole());
        }
        return $assignedRoles;
    }

    public function getUserRoles() {
        return $this->roles;
    }

    public function getUserRoleId() {
        $roleId = 0;
        foreach ($this->roles as $role) {
            $roleId = $role->getId();
        }
        return $roleId;
    }

    public function hasRole($roleName) {
        foreach($this->roles as $role){
            if($role->getRole() == $roleName){
                return true;
            }
        }
        return false;
    }

    public function eraseCredentials(){}

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getLastName() {
        return $this->lastname;
    }

    public function setLastName($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    public function getCellPhone() {
        return $this->cellPhone;
    }

    public function setCellPhone($cellPhone) {
        $this->cellPhone = $cellPhone;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBirthPlace() {
        return $this->birthPlace;
    }

    /**
     * @param mixed $birthPlace
     * @return User
     */
    public function setBirthPlace($birthPlace) {
        $this->birthPlace = $birthPlace;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRh() {
        return $this->rh;
    }

    /**
     * @param mixed $rh
     * @return User
     */
    public function setRh($rh) {
        $this->rh = $rh;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicture() {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     * @return User
     */
    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNeighborhood() {
        return $this->neighborhood;
    }

    /**
     * @param mixed $neighborhood
     * @return User
     */
    public function setNeighborhood($neighborhood) {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return User
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHomePhone() {
        return $this->homePhone;
    }

    /**
     * @param mixed $homePhone
     * @return User
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param mixed $maritalStatus
     * @return User
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getMenuOptionsAsArray() {
        return $this->menuOptions->toArray();
    }

    public function getMenuOptions() {
        return $this->menuOptions;
    }

    public function addMenuOption($actionMenu) {
        $this->menuOptions->add($actionMenu);
        return true;
    }

    public function removeMenuOption($entity) {
        $this->menuOptions->removeElement($entity);
        return null;
    }

    /** Permissions Methods ***/
    public function getPermissionsAsArray() {
        return $this->permissions->toArray();
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function addPermission($permission) {
        $this->permissions->add($permission);
        return true;
    }

    public function removePermission($entity) {
        $this->permissions->removeElement($entity);
        return null;
    }

    public function getFullName() {
        return $this->name . " " . $this->lastname;
    }

    public function __toString(){
        return "" . $this->username;
    }

    public function getAvatarUrl($default, $avatarPath) {
        return (isset($this->picture) && $this->picture != "")? $avatarPath . $this->picture:$default;
    }
}