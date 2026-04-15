<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(
        int $id,
        ArticleRepository $articleRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article introuvable');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setIsApproved(false);
            $comment->setStatus('pending');

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire soumis, en attente de validation.');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        // Seulement les commentaires approuvés
        $approvedComments = $article->getComments()->filter(
            fn(Comment $c) => $c->isApproved() === true
        );

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comments' => $approvedComments,
            'commentForm' => $form,
        ]);
    }
}
