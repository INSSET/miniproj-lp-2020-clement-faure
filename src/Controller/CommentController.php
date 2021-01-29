<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }


    public function create(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextareaType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => 'Comment'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $creation_date = new DateTime();
            $comment->setCreationDate($creation_date);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        return $this->render('comment/create.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }
}
