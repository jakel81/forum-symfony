<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 07/09/2017
 * Time: 15:26
 */

namespace AppBundle\Form\Handler;


use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Doctrine\ORM\EntityManager;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PostFormHandler
{

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var Post
     */
    private $entity;

    /**
     * @var string
     */
    private $postType;

    /**
     * @var UploadableManager
     */
    private $uploadManager;


    /**
     * PostFormHandler constructor.
     * @param EntityManager $manager
     * @param RequestStack $requestStack
     * @param FormFactory $formFactory
     * @param Post $entity
     * @param string $postType
     * @param UploadableManager $uploadableManager
     */
    public function __construct(EntityManager $manager,
        RequestStack $requestStack,
        FormFactory $formFactory,
        Post $entity, string $postType,
        UploadableManager $uploadableManager)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->entity = $entity;
        $this->postType = $postType;
        $this->uploadManager = $uploadableManager;

        $this->request = $this->requestStack->getCurrentRequest();

    }

    public function process(){
        $success = false;
        $this->form = $this->formFactory->create($this->postType, $this->entity);
        $this->form->handleRequest($this->request);

        if($this->form->isSubmitted() and $this->form->isValid()){
            $this->onSuccess();
            $success = true;
        }

        return $success;
    }

    public function onSuccess(){

        if($this->entity->getImageFileName() instanceof UploadedFile){
            $this->uploadManager->markEntityToUpload($this->entity, $this->entity->getImageFileName());
        }

        $this->manager->persist($this->entity);
        $this->manager->flush();
    }

    /**
     * @return EntityManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param EntityManager $manager
     * @return PostFormHandler
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return RequestStack
     */
    public function getRequestStack()
    {
        return $this->requestStack;
    }

    /**
     * @param RequestStack $requestStack
     * @return PostFormHandler
     */
    public function setRequestStack($requestStack)
    {
        $this->requestStack = $requestStack;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return PostFormHandler
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactory $formFactory
     * @return PostFormHandler
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return PostFormHandler
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Post
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Post $entity
     * @return PostFormHandler
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getFormView(){
        return $this->form->createView();
    }



}