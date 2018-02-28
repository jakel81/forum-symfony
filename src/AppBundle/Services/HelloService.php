<?php
/**
 * Created by PhpStorm.
 * User: Jo
 * Date: 28/02/2018
 * Time: 14:23
 */

namespace AppBundle\Services;

use AppBundle\Entity\Author;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class HelloService
 * @package AppBundle\Services
 */
class HelloService
{
    /**
     * @var string
     */
    private $name;

    /**
     * HelloService constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        /**
         * @var Author
         */
        $user = $tokenStorage->getToken()->getUser();

        if (!$user instanceof Author) {
            $this->name = "Anonymous";
        } else {
            $this->name = $user->getFullName();
        }
    }

    /**
     * @return string
     */
    public function greet()
    {
        return "Hello " . $this->name;
    }
}