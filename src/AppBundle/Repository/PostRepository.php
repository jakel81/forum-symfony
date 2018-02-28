<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $number
     * @return array
     */
    public function getLastPosts($number)
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.title, p.slug')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($number);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getNumberOfPostsByYear()
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('YEAR(p.createdAt) as yearPublished, COUNT(p.id) as numberOfPosts')
            ->groupBy('yearPublished');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param $year
     * @return array
     */
    public function getPostsByYear($year)
    {
        $qb = $this->createQueryBuilder("p");

        $qb->select('p')
            ->where('YEAR(p.createdAt)=:year')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('year', $year);

        return $qb->getQuery()->getResult();
    }
}