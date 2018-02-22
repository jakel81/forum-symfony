<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Post;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Answer
 *
 * @ORM\Table(name="answers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnswerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Answer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=50)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="answers")
     */
    private $post;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="answer")
     */
    private $votes;


    public function getTotalVotes(){
        $total = 0;
        foreach ($this->votes->toArray() as $vote){
            $total += $vote->getVote();
        }
        return $total;
    }


    public function canVoteUp($user){
        return $this->canVote($user, -1);
    }

    public function canVoteDown($user){
        return $this->canVote($user, 1);
    }

    private function canVote($user, $targetVote){
        $foundVote = $this->votes->exists(function ($key, $item) use($user, $targetVote){
            $found = $item->getAuthor()== $user;
            return $found  and $item->getVote() == $targetVote;
        });

        $hasvote = $this->votes->exists(function ($key, $item) use($user){
            return $item->getAuthor() == $user;
        });

        dump($foundVote or ! $hasvote);

        return $foundVote or ! $hasvote;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Answer
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Answer
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Answer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return Answer
     */
    public function setPost(\AppBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add vote
     *
     * @param \AppBundle\Entity\Vote $vote
     *
     * @return Answer
     */
    public function addVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AppBundle\Entity\Vote $vote
     */
    public function removeVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
