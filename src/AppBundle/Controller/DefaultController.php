<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Post;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FOSRestController
{

    /**
     * @Rest\Get("/post")
     */
    public function getAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
    }

    /**
     * @param Post $post
     * @return Post
     * @Rest\Get("/post/{id}")
     */
    public function idAction(Post $post)
    {
        return $post;
    }

    /**
     * @param Request $request
     * @Rest\Post("/post")
     */
    public function postAction(Request $request)
    {
        dump($request);
        return ['foo'];
    }
}
