<?php

namespace App\Controller;

use App\Entity\AdminSettings;
use App\Entity\Comment;
use App\Entity\Idea;
use App\Entity\Vote;
use App\Form\CommentType;
use App\Form\IdeaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IdeaController extends AbstractController
{
    /**
     * @Route("/", name="ideas_index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $adminSettings = $em->getRepository(AdminSettings::class)->findOneById(1);
        if (!$adminSettings) {
            $adminSettings = new AdminSettings();
        }

        return $this->render('index/index.html.twig', [
            'ideas' => $em->getRepository(Idea::class)->findLatestDistinctIdeas(),
            'settings' => $adminSettings,
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="edit_idea")
     */
    public function edit(Request $request, Idea $entity)
    {
        $newEntity = clone $entity; // detach before edit for versioning
        $form = $this->createForm(IdeaType::class, $newEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newEntity->setDatetime(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($newEntity);
            $em->flush();

            return $this->redirectToRoute("ideas_index");
        }

        return $this->render('index/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/voteFor/{entity}", name="vote_for_idea")
     */
    public function voteFor(Idea $entity, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $existingVote = $em->getRepository(Vote::class)->findOneBy([
            'voter' => $this->getUser(),
            'idea' => $entity,
        ]);
        if ($existingVote) {
            $em->remove($existingVote);
            $em->flush();
        } else {
            $vote = new Vote();
            $vote->setDatetime(new \DateTime('now'))->setValue(1)->setVoter($this->getUser())->setIdea($entity);
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
    public function comment(Request $request, ValidatorInterface $validator, Idea $entity)
    {
        $comment = new Comment();
        $comment->setCreator($this->getUser());
        $comment->setIdea($entity);
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setDatetime(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute("ideas_index");
        }

        return $this->render('index/comment.html.twig', [
            'form' => $form->createView(),
            'idea' => $entity,
        ]);
    }

    /**
     * @Route("/create", name="create_idea")
     */
    public function create(Request $request)
    {
        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $idea->setIdeaId($em->getRepository(Idea::class)->findNextAvailableIdeaId());
            $idea->setCreator($this->getUser());
            $em->persist($idea);
            $em->flush();

            return $this->redirectToRoute("ideas_index");
        }

        return $this->render('index/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{entity}", name="show_idea")
     */
    public function show(Idea $entity)
    {
        $entities = $this->getDoctrine()->getManager()->getRepository(Idea::class)->findAllForIdeaId($entity->getIdeaId());

        return $this->render('index/show.html.twig', [
            'entities' => $entities,
        ]);
    }
}
