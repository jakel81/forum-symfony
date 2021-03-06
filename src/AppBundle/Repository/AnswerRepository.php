<?php

namespace AppBundle\Repository;

/**
 * AnswerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnswerRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param $post
     * @return array
     */
    public function getAnswersByPost($post)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a.author, a.text, a.createdAt')
            ->orderBy('a.createdAt', 'DESC')
            ->innerJoin('a.post', 'p')
            ->where('a.post=?1')
            ->setParameter(1, $post);

        return $qb->getQuery()->getArrayResult();
    }
}
