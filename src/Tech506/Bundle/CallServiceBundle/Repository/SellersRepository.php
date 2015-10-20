<?php
namespace Tech506\Bundle\CallServiceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Tech506\Bundle\SecurityBundle\Repository\GenericRepository;

/**
 * Custom repository for the Sellers
 */
class SellersRepository extends GenericRepository {

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    public function getPageWithFilter($offset, $limit, $search, $sort, $order ){
        $dql = "SELECT d FROM " . $this->getEntityName() . " d JOIN d.user u";
        $dql .= ($search == "")? "":" WHERE u.name LIKE :search OR u.lastName LIIKE :search OR u.email LIKE :search OR u.username LIKE :search";
        $dql .= ($sort == "")? "":" order by u." . $sort . " " . $order;

        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if($search != ""){
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        return $paginator;
    }
}
?>
