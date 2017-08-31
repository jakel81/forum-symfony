<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{

    /**
     * @param $id
     * @Route("/post/{id}",
     *          name="post_details",
     *          requirements={"id":"\d+"}
     * )
     * @return Response
     */
    public function detailsAction($id){

        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        $post = $repository->find($id);

        if(! $post){
            throw new NotFoundHttpException("post introuvable");
        }

        return $this->render("post/details.html.twig", [
            "post" => $post,
            "answerList" => $post->getAnswers()
        ]);
    }

}