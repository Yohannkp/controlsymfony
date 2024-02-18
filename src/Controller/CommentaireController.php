<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function Symfony\Component\Clock\now;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire ): Response
    {

        
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
            
        ]);
    }


    #[Route('commentaire/{id}', name: 'commenterarticle', methods: ['GET','POST'])]
    public function commenterarticle(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $commentaire->setArticle($article);
            $commentaire->setDatepublication($date);
            $commentaire->setAuteur($this->getUser());
            $commentaire->setEtat(true);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('commentaire/commenterarticle.html.twig', [
            'commentaire' => $commentaire,
            'form'=>$form->createView()
        ]);
    }


    #[Route('/activer/{id}', name: 'app_comm_active', methods: ['GET'])]
    public function ActiverCommentaire(Commentaire $commentaire,EntityManagerInterface $em,CommentaireRepository $commentaireRepository): Response
    {
        $commentaire->setEtat(true);
        $em->persist($commentaire);
        $em->flush();
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }


    #[Route('/active_desactive/{id}', name: 'app_comm_activ_desactive', methods: ['GET'])]
    public function ActiverDesactiverCommentaire(Commentaire $commentaire,EntityManagerInterface $em,CommentaireRepository $commentaireRepository): Response
    {
        if ($commentaire->isEtat() == false) {
            $commentaire->setEtat(true);
        $em->persist($commentaire);
        $em->flush();
        }else{
            $commentaire->setEtat(false);
        $em->persist($commentaire);
        $em->flush();
        }

        
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
