<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 */
class CommentController extends Controller
{
    
    /**
     * Отображние пустой формы комментария для конкретного блога
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);
        
        $comment = new Comment();
        $comment->setBlog($blog);
        $form   = $this->createForm(CommentType::class, $comment);
        
        return $this->render('BloggerBlogBundle:Comment:form.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Сохранения комментария (формы) для конкретного блога
     * @param Request $request
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $blog_id)
    {
        $blog = $this->getBlog($blog_id);
        
        $comment  = new Comment();
        $comment->setBlog($blog);
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            
            $em->persist($comment);
            $em->flush();
            
            return $this->redirect($this->generateUrl('BloggerBlogBundle_show', array(
                    'id' => $comment->getBlog()->getId())) .
                '#comment-' . $comment->getId()
            );
        }
        
        return $this->render('BloggerBlogBundle:Comment:create.html.twig', array(
            'comment' => $comment,
            'form'    => $form->createView()
        ));
    }
    
    /**
     * Получение блога по id
     * @param $blog_id
     * @return \Blogger\BlogBundle\Entity\Blog|null|object
     */
    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
            ->getManager();
        
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);
        
        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }
        
        return $blog;
    }
    
}
