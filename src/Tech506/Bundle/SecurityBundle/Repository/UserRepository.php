<?php
namespace Tech506\Bundle\SecurityBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Tech506\Bundle\SecurityBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 *
 */
class UserRepository extends CatalogRepository implements UserProviderInterface
{

    public function findAll() {
        return $this->findBy(array(),array('name' => 'asc', 'lastname' => 'asc'));
    }

    public function getEmployees() {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.roles', 'r')
            ->where('r.role = :role')
            ->setParameter('role', 'ROLE_EMPLOYEE')
            ->getQuery();
        $users = $q->getResult();
        return $users;
    }

    public function getListWithFilter($offset, $limit, $search, $sort, $order, $myId ){
        $dql = "SELECT d FROM " . $this->getEntityName() . " d WHERE d.id <> :myId";

        $dql .= ($search == "")? "":" AND (d.name LIKE :search OR d.lastname LIKE :search OR d.username LIKE :search OR d.email LIKE :search)";
        $dql .= ($sort == "")? "":" order by d." . $sort . " " . $order;

        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query->setParameter('myId', $myId);
        if($search != ""){
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        return $paginator;
    }

    public function getAllByName(){
        $dql = "SELECT d FROM " . $this->getEntityName() . " d order by d.name asc";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getListByRoleWithFilter($offset, $limit, $search, $sort, $order, $myId, $role ){
        $dql = "SELECT d FROM " . $this->getEntityName()
            . " d JOIN d.roles r WHERE d.id <> :myId AND r.role = '"
            . $role . "'";

        $dql .= ($search == "")? "":" AND (d.name LIKE :search OR d.lastname LIKE :search OR d.username LIKE :search OR d.email LIKE :search)";
        $dql .= ($sort == "")? "":" order by d." . $sort . " " . $order;

        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query->setParameter('myId', $myId);
        if($search != ""){
            $query->setParameter('search', "%" . $search . "%");
        }

        $paginator = new Paginator($query, $fetchJoinCollection = false);

        return $paginator;
    }

    public function checkUniqueUsernameAndEmail($username, $email, $userId) {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.roles', 'g')
            ->where('u.id <> :userId AND (u.username = :username OR u.email = :email)')
            ->setParameter('userId', $userId)
            ->setParameter('username', $username)
            ->setParameter('email', $email)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
            return false;
        } catch (NoResultException $e) {
            return true;
        }
    }

    public function loadUserByUsername($username) {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.roles', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(
                sprintf('Unable to find an active User object identified by "%s".', $username), 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * This method will return true or false depending if already exists
     * at least 1 record with the same email
     *
     * @param $email    Email to check
     *
     * @return bool     Exists or not
     */
    public function existsEmail($email) {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(u)')
            ->from('Tech506SecurityBundle:User', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery();

        $total = $query->getSingleScalarResult();

        return $total > 0;
    }

    public function getUsersByRole($role) {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.roles', 'r')
            ->where('r.role = :role')
            ->setParameter('role', $role)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $users = $q->getResult();
        } catch (NoResultException $e) {
        }

        return $users;
    }
}

?>
