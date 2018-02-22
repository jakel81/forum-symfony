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
     * @param $id
     * @Route("/post/{id}",
     *          name="post_details"
     * )
     * @return Response
     */
    public function detailsAction($id){

        $postRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        /** @var $post Post */
        $post = $postRepository->findOneById($id);

        if(! $post){
            throw new NotFoundHttpException("post introuvable");
        }

        return $this->render("post/details.html.twig", [
            "post" => $post,
            "answerList" => $post->getAnswers()
        ]);
    }



}