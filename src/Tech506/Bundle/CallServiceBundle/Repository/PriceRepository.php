<?php
namespace Tech506\Bundle\CallServiceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Tech506\Bundle\SecurityBundle\Entity\Sport;

/**
 * This repository contains the common methods for all the repositories and will be use as the generic repository
 * when a specific repository is not required
 */
class PriceRepository extends EntityRepository {

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    public function getPageWithFilter($offset, $limit, $search, $sort, $order, $product ) {
        $product = $product * 1;
        $dql = "SELECT d FROM " . $this->getEntityName() . " d JOIN d.product p";
        $wheredql = ($product == 0)? "":" WHERE p.id = :product";
        $wheredql .= ($search == "")? "":
            (($wheredql == "")? " WHERE d.name LIKE :search":" AND d.name LIKE :search");
        $dql .= $wheredql;
        switch($sort) {
            case "categoryName":
                $dql .= " order by c.name " . $order;
                break;
            default:
                $dql .= " order by d." . $sort . " " . $order;
                break;
        }

        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        if($product != 0) {
            $query->setParameter('product', $product);
        }
        if($search != "") {
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        return $paginator;
    }
}
?>
