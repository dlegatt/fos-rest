<?php

namespace AppBundle\Controller;

use AppBundle\Form\PostType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Post;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DefaultController extends FOSRestController
{

    /**
     * @Rest\Get("/post")
     * @ApiDoc()
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
     * @ApiDoc()
     */
    public function postAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $post;
        }
        return $form;
    }

    /**
     * @param Post $post
     * @param Request $request
     * @return Post|\Symfony\Component\Form\Form
     * @Rest\Put("/post/{id}")
     * @ApiDoc()
     */
    public function updateAction(Post $post,Request $request)
    {
        $form = $this->createForm(PostType::class, $post,
            ["method" => "PUT"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $post;
        }
        return $form;
    }

    /**
     * @param Post $post
     * @return Response
     * @Rest\Delete("/post/{id}")
     * @ApiDoc()
     */
    public function deleteAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $view = $this->view($post,Response::HTTP_OK);
        $em->flush();
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/play")
     */
    public function playAction()
    {
        return ['All secure in sector 7!'];
    }
}
