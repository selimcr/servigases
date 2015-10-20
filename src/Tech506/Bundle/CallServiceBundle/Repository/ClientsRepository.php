<?php
namespace Tech506\Bundle\CallServiceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Tech506\Bundle\SecurityBundle\Repository\GenericRepository;

/**
 * Custom repository for the Clients
 */
class ClientsRepository extends GenericRepository {

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    public function findClient($phone, $cellPhone, $email) {
        try {
            $dql = "SELECT c FROM " . $this->getEntityName() . " c ";
            $where = "";
            $where .= (isset($phone) && trim($phone) != "")?
                " c.phone = '" . $phone . "'" : "";
            $where .= (isset($cellPhone) && trim($cellPhone) != "")?
                ($where == ""? "":" AND ") . " c.cellPhone = '" . $cellPhone . "'" : "";
            $where .= (isset($email) && trim($email) != "")?
                ($where == ""? "":" AND ") . " c.email = '" . $email . "'" : "";
            $dql .= ($where == "")? "": " WHERE " . $where;
            $query = $this->getEntityManager()->createQuery($dql);
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
?>
