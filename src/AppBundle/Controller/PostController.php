<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Answer;
use AppBundle\Entity\Vote;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Post;

class PostController extends Controller
{

    /**
     * @param $slug
     * @Route("/post/{slug}",
     *          name="post_details"
     * )
     * @return Response
     */
    public function detailsAction($slug){

        $postRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");
        $answerRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Answer");

        /** @var $post Post */
        $post = $postRepository->findOneBySlug($slug);

        if(! $post){
            throw new NotFoundHttpException("post introuvable");
        }

        return $this->render("post/details.html.twig", [
            "post" => $post,
            "answerList" => $post->getSortedAnswers(),
            "test" => $post->getAnswers()->toArray()
        ]);
    }

    /**
     * @Route("/post-par-annee/{year}", name="post_by_year",
     *     requirements={"year":"\d{4}"})
     * @param $year
     * @return Response
     */
    public function postByYearAction($year){
        $postRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        return $this->render("default/theme.html.twig", [
            "title" => "Liste des posts par année ({$year})",
            "postList" => $postRepository->getPostsByYear($year)
        ]);
    }

    /**
     * @Route("/post/modif/{id}", name="post_edit")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function editAction(Request $request, Post $post){

        //Sécurisation de l'opération
        $user = $this->getUser();
        $roles = isset($user)?$user->getRoles():[];
        $userId = isset($user)?$user->getId(): null;
        if(!in_array("ROLE_AUTHOR", $roles) || $userId != $post->getAuthor()->getId() ){
            throw new AccessDeniedHttpException("Vous n'avez pas les droits pour modifier ce post");
        }

        //Création du formulaire
        $form = $this->createForm(PostType::class, $post);

        //Hydrataion de l'entité
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute(
                "theme_details",
                ["id" => $post->getTheme()->getId()]
            );
        }

        return $this->render("post/edit.html.twig", ["postForm"=>$form->createView()]);
    }

    /**
     * @Route("/answer-vote/{id}/{voteValue}", name="answer_vote")
     * @param Request $request
     * @param Answer $answer
     * @param $vote
     * @ParamConverter("answer", options={"exclude": {"vote"}})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function voteAction(Request $request, Answer $answer, $voteValue){
        $referer = $request->headers->get('referer');
        $user = $this->getUser();

        $vote = new Vote();
        $vote->setAuthor($user)
            ->setAnswer($answer)
            ->setVote($voteValue);
        $em = $this->getDoctrine()->getManager();

        $em->persist($vote);
        $em->flush();

        return $this->redirect($referer);
    }

}