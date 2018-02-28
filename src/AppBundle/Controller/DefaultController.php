<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Post;
use AppBundle\Form\AuthorType;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Theme");
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $authorRepository = $this->getDoctrine()->getRepository('AppBundle:Author');

        $list = $repository->findAll();

        return $this->render('default/index.html.twig',
            [
                "themeList" => $list,
                "LastPosts" => $postRepository->getLastPosts(5),
                "authorSummary" => $authorRepository->getAuthorSummary(),
                "yearSummary" => $postRepository->getNumberOfPostsByYear(),
                "message" => $this->get("app.hello")->greet()
            ]);
    }

    /**
     * @Route("/theme/{id}", name="theme_details", requirements={"id":"\d+"})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function themeAction($id, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository("AppBundle:Theme");
        //$authorRepository = $this->getDoctrine()->getRepository("AppBundle:Author");

        $theme = $repository->find($id);

        $allThemes = $repository->findAll();

        if (!$theme) {
            throw new NotFoundHttpException("Thème introuvable");
        }

        //Permet d'afficher les posts même si on n'est pas connecté
        if ($this->getUser() != null) {
            //Création du formulaire
            $post = new Post();
            $post->setTheme($theme);
            $post->setCreatedAt(new \DateTime());
            $post->setAuthor($this->getUser());
            //$post->setAuthor($authorRepository->findOneByName("Hugo"));
            $form = $this->createForm(PostType::class, $post, ['attr' => ['novalidate' => 'novalidate']]);

            //Traitement du formulaire (hydratation)
            $form->handleRequest($request);

            //Sauvegarde des données si le formulaire est correct
            if ($form->isSubmitted() and $form->isValid()) {
                dump($post->getImage());
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);

                if ($post->getImage() instanceof UploadedFile) {
                    $uploadManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                    $uploadManager->markEntityToUpload($post, $post->getImage());
                }
                $em->flush();

                //Redirection pour ne pas avoir à recharger le formulaire
                return $this->redirectToRoute("theme_details", ["id" => $id]);
            }
            $formView = $form->createView();
        } else {
            $formView = null;
        }
        return $this->render('default/theme.html.twig', ["theme" => $theme, "postList" => $theme->getPosts(), "all" => $allThemes, "newPostForm" => $formView]);
    }

    /**
     * @Route("/posts-par-auteur/{id}", name="post_par_auteur", requirements={"id":"\d+"})
     *
     * @param Author $author
     * @return Response
     */
    public function postsByAuthorAction(Author $author)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $post = $postRepository->findByAuthor($author);

        return $this->render('default/posts_by_author.html.twig', ["postList" => $post, "author" => $author, "condition" => $author->getFullName()]);
    }

    /**
     * @Route("/post-par-annee/{year}", name="post_par_annee", requirements={"year":"\d{4}"})
     *
     * @param $year
     * @return Response
     */
    public function postsByYearAction($year)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $posts = $postRepository->getPostsByYear($year);

        return $this->render('default/posts_by_author.html.twig', ['condition' => "$year", 'postList' => $posts]);
    }

    /**
     * @Route("/admin-login", name="admin_login_route")
     *
     * @return Response
     */
    public function adminLoginAction()
    {
        //Récupération des erreurs
        $securityUtils = $this->get('security.authentication_utils');

        return $this->render('default/login-form.html.twig', ["action" => $this->generateUrl("admin_check_route"), "error" => $securityUtils->getLastAuthenticationError(),
            "userName" => $securityUtils->getLastUsername()]);
    }

    /**
     * @Route("/author-login", name="author_login_route")
     *
     * @return Response
     */
    public function authorLoginAction()
    {
        //Récupération des erreurs
        $securityUtils = $this->get('security.authentication_utils');

        return $this->render('default/login-form.html.twig', ["action" => $this->generateUrl("author_check_route"), "error" => $securityUtils->getLastAuthenticationError(),
            "userName" => $securityUtils->getLastUsername()]);
    }

    /**
     * @Route("/inscription-auteur", name="author_registration")
     * @param Request $request
     * @return Response
     */
    public function registerAuthorAction(Request $request)
    {
        //Nouvel auteur
        $author = new Author();
        //Génère un formulaire
        $form = $this->createForm(AuthorType::class, $author);

        //Traitement du formulaire
        $form->handleRequest($request);

        //Vérification du formulaire
        if ($form->isSubmitted() and $form->isValid()) {
            //Encodage du MDP
            $securityFactory = $this->get("security.encoder_factory");
            $encoder = $securityFactory->getEncoder($author);
            $author->setPassword($encoder->encodePassword($author->getPlainPassword(), null));
            $author->setPlainPassword(null);

            //Enregistrement dans la BD
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            //Message Flash
            $this->addFlash('info', 'Vous êtes inscrit et logué');

            //Authentification de l'utilisateur qui vient de s'inscrire
            //Création d'un token à partir des données de l'auteur
            $token = new UsernamePasswordToken($author, null, 'main', $author->getRoles());
            //Stockage du token
            $this->get("security.token_storage")->setToken($token);

            //Redirection vers la page d'accueil
            return $this->redirectToRoute("homepage");
        }
        return $this->render('default/author-registration.html.twig', ["registrationForm" => $form->createView()]);
    }
}