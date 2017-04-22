<?php
namespace Tech506\Bundle\CallServiceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Tech506\Bundle\CallServiceBundle\Util\Enum\TechnicianServiceStatus;
use Tech506\Bundle\SecurityBundle\Repository\GenericRepository;

/**
 * Custom repository for the Calls
 */
class TechnicianServiceRepository extends GenericRepository {

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    public function getPageWithFilter($offset, $limit, $search, $sort, $order ){
        return $this->getPageWithFilterForUser($offset, $limit, $search, $sort, $order, 0); // 0 for all users
    }

    public function getPageWithFilterForUser($offset, $limit, $search, $sort, $order, $userId, $status, $technician,
                         $seller, $date) {
        $dql = "SELECT d FROM " . $this->getEntityName() . " d 
        LEFT JOIN d.technician u 
        LEFT JOIN u.user tu 
        JOIN d.client c 
        JOIN d.seller s";
        $where = "";
        if ($userId != 0) {
            $where .= " WHERE s.id = " . $userId;
            /*$where .= ($search == "")? " WHERE s.id = " . $userId:" WHERE s.id = " . $userId .
                " AND (u.name LIKE :search OR u.lastname LIKE :search)";*/
        }
        if ($status != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "d.status = " . $status;
        }
        if ($technician != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "u.id = " . $technician;
        }
        if ($seller != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "s.id = " . $seller;
        }
        if ( isset($date)) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= " (d.creationDate BETWEEN '" . $date->format('Y-m-d 00:00:00') . "'  AND '" . $date->format('Y-m-d 23:59:59') . "')";
        }
        if ($search != "") {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= " (c.fullName LIKE :search)";
        }
        $dql .= $where;
        switch($sort){
            case 'creationDate':
            case 'status':
            case 'id':
                $dql .= " order by d. " . $sort . " " . $order;
                break;
            case 'client':
                $dql .= " order by c.fullName " . $order;
                break;
            case 'technician':
                $dql .= " order by tu.name, tu.lastname " . $order;
                break;
            default:
                $dql .= ($sort == "")? "":" order by d.creationDate desc";
                break;
        }
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if($search != ""){
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        return $paginator;
    }

    public function findForDashboard($offset, $limit, $search, $sort, $order, $userId, $status, $technician,
                                             $seller, $date, $search) {
        $dql = "SELECT d FROM " . $this->getEntityName() . " d 
        LEFT JOIN d.technician u 
        LEFT JOIN u.user tu 
        JOIN d.client c 
        JOIN d.seller s";
        $where = "";
        if ($userId != 0) {
            $where .= " WHERE s.id = " . $userId;
            /*$where .= ($search == "")? " WHERE s.id = " . $userId:" WHERE s.id = " . $userId .
                " AND (u.name LIKE :search OR u.lastname LIKE :search)";*/
        }
        if ($status != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "d.status = " . $status;
        }
        if ($technician != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "u.id = " . $technician;
        }
        if ($seller != 0) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= "s.id = " . $seller;
        }
        if ( isset($date)) {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= " (d.creationDate BETWEEN '" . $date->format('Y-m-d 00:00:00') . "'  AND '" . $date->format('Y-m-d 23:59:59') . "')";
        }
        if($search != "") {
            $where .= ($where == "")? " WHERE ":" AND ";
            $where .= " (c.fullName LIKE :search OR d.addressDetail LIKE :search " .
                " OR d.addressDetail LIKE :search " .
                " OR d.neighborhood LIKE :search) ";
        }
        $dql .= $where;
        switch($sort){
            case 'creationDate':
            case 'status':
            case 'id':
                $dql .= " order by d. " . $sort . " " . $order;
                break;
            case 'client':
                $dql .= " order by c.fullName " . $order;
                break;
            case 'technician':
                $dql .= " order by tu.name, tu.lastname " . $order;
                break;
            default:
                $dql .= ($sort == "")? "":" order by d.creationDate desc";
                break;
        }
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if($search != ""){
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        return $paginator;
    }

    public function findScheduledServices($sort, $order) {
        $dql = "SELECT d FROM " . $this->getEntityName() . " d LEFT JOIN d.technician u JOIN d.client c JOIN d.seller s";
        $dql .= " WHERE d.status in (" . TechnicianServiceStatus::CREATED . ',' . TechnicianServiceStatus::SCHEDULED . ")";
        $dql .= " order by d.scheduleDate asc, u.id asc, d.hour asc";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function findScheduledServicesOnDay($technicianId, $day) {
        $dql = "SELECT d FROM " . $this->getEntityName() . " d LEFT JOIN d.technician u JOIN d.client c JOIN d.seller s";
        $dql .= " WHERE d.status in (" . TechnicianServiceStatus::SCHEDULED . "," . TechnicianServiceStatus::REALIZED .
            ") AND d.scheduleDate between '" . $day . " 00:00:00' AND '" . $day . " 23:59:59'";
        if ( $technicianId != 0) {
            $dql .= " AND u.id = " . $technicianId . "";
        }
        $dql .= " order by u.id asc, d.scheduleDate asc, d.hour asc";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getPendingOfApplyCommision($employeeType, $id) {
        $dql = "SELECT ts FROM " . $this->getEntityName() . " ts JOIN ts.technician t "
            . " JOIN t.user tu JOIN ts.client c JOIN ts.seller s";
        if ($employeeType != 0) {
            $dql .= " JOIN tu.roles tr JOIN s.roles sr";
        }
        $dql .= " WHERE ts.status = " . TechnicianServiceStatus::REALIZED;
        if ($id != 0) {
            $dql .= " AND (s.id = " . $id . " OR tu.id = " . $id . ")";
        }
        if ($employeeType != 0) {
            $dql .= " AND (tr.id = " . $employeeType . " OR sr.id = " . $employeeType . ")";
        }
        $dql .= ' AND (ts.technicianCommisionApplied = 0 OR ts.sellerCommisionApplied = 0)';
        $dql .= " order by ts.scheduleDate asc, t.id asc, ts.hour asc";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /* Dashboards Widgets Data */
    public function findServicesCounter($initialDay, $finalDay) {
        $sql = "SELECT DATE_FORMAT(scheduleDate, '%Y-%m-%d') as 'date', count(*) as 'counter'
            FROM servigases.technician_services
            WHERE (scheduleDate between '" . $initialDay . " 00:00:00' AND '" . $finalDay . " 23:59:59') AND status = 4
            group by DATE_FORMAT(scheduleDate, '%Y-%m-%d')
            ORDER BY scheduleDate asc";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
