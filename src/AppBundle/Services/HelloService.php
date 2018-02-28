<?php
/**
 * Created by PhpStorm.
 * User: Jo
 * Date: 28/02/2018
 * Time: 14:23
 */

namespace AppBundle\Services;

use AppBundle\Entity\Author;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class HelloService
 * @package AppBundle\Services
 */
class HelloService implements EventSubscriberInterface
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

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $event->setResponse(new Response("maintenance"));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => "onKernelRequest"];
    }
}