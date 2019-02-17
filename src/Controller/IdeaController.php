<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Idea;
use App\Entity\Vote;
use App\Form\CommentType;
use App\Form\IdeaType;
use App\Service\Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IdeaController extends AbstractController
{
    /**
     * @Route("/edit/{entity}", name="edit_idea")
     */
    public function edit(Request $request, Idea $entity, Access $accessSrv)
    {
        $collection = $entity->getCollection();
        if (!$accessSrv->checkAccess($this->getUser(), $collection)) {
            return $this->redirectToRoute('collections_index');
        }

        $form = $this->createForm(IdeaType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setDatetime(new \DateTime('now'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("collection_index", [
                "entity" => $collection->getId(),
            ]);
        }

        return $this->render('ideas/edit.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("/voteFor/{entity}", name="vote_for_idea")
     */
    public function voteFor(Idea $entity, ValidatorInterface $validator, Access $accessSrv)
    {
        $user = $this->getUser();
        $collection = $entity->getCollection();
        $userCollection = $accessSrv->getUserCollections($user, $collection)->first();
        if (!$accessSrv->checkAccess($user, $collection)) {
            return $this->redirectToRoute('collections_index');
        }

        $em = $this->getDoctrine()->getManager();
        $existingVote = $em->getRepository(Vote::class)->findOneBy([
            'voter' => $userCollection,
            'idea' => $entity,
        ]);
        if ($existingVote) {
            $em->remove($existingVote);
            $em->flush();
        } else {
            $vote = new Vote();
            $vote->setDatetime(new \DateTime('now'))->setValue(1)->setVoter($userCollection)->setIdea($entity);
            $errors = $validator->validate($vote);
            if (count($errors) == 0) {
                $em->persist($vote);
                $em->flush();
            } else {
                return new JsonResponse(['success' => false]);
            }
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/comment/{entity}", name="comment_idea")
     */
    public function comment(Request $request, ValidatorInterface $validator, Idea $entity, Access $accessSrv)
    {
        $collection = $entity->getCollection();
        $user = $this->getUser();
        if (!$accessSrv->checkAccess($user, $collection)) {
            return $this->redirectToRoute('collections_index');
        }

        $comment = new Comment();
        $comment->setCreator($accessSrv->getUserCollections($user, $collection)->first());
        $comment->setIdea($entity);
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setDatetime(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('collection_index', [
                'entity' => $collection->getId()
            ]);
        }

        return $this->render('ideas/comment.html.twig', [
            'form' => $form->createView(),
            'idea' => $entity,
        ]);
    }

    /**
     * @Route("/create/{collection}", name="create_idea")
     */
    public function create(Request $request, Collection $collection, Access $accessSrv)
    {
        $user = $this->getUser();
        if (!$accessSrv->checkAccess($user, $collection)) {
            return $this->redirectToRoute('collections_index');
        }

        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $form->getData();
            $idea->setCollection($collection);
            $em = $this->getDoctrine()->getManager();
            $idea->setIdeaId($em->getRepository(Idea::class)->findNextAvailableIdeaId());
            $idea->setCreator($accessSrv->getUserCollections($user, $collection)->first());
            $idea->setDatetime(new \DateTime('now'));
            $em->persist($idea);
            $em->flush();

            return $this->redirectToRoute("collection_index", [
                'entity' => $collection->getId(),
            ]);
        }

        return $this->render('ideas/new.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("/show/{entity}", name="show_idea")
     */
    public function show(Request $request, Idea $entity, Access $accessSrv)
    {
        $user = $this->getUser();
        $collection = $entity->getCollection();
        if (!$accessSrv->checkAccess($user, $collection)) {
            return $this->redirectToRoute('collections_index');
        }
        $comment = new Comment();
        $comment->setCreator($accessSrv->getUserCollections($user, $collection)->first());
        $comment->setIdea($entity);
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setDatetime(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('collection_index', [
                'entity' => $collection->getId()
            ]);
        }

        return $this->render('ideas/show.html.twig', [
            'idea' => $entity,
            'form' => $form->createView(),
        ]);
    }
}
