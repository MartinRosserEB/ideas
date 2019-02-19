<?php

namespace App\Controller;

use App\Entity\AdminSettings;
use App\Entity\Collection;
use App\Entity\Idea;
use App\Entity\UserCollection;
use App\Form\CollectionType;
use App\Form\IdeaType;
use App\Service\Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CollectionController extends AbstractController
{
    /**
     * @Route("/", name="collections_index")
     */
    public function collectionsIndex(Request $request)
    {
        $collection = new Collection();
        $form = $this->createForm(CollectionType::class, $collection);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection = $form->getData();
            $collection->setDescription(nl2br($collection->getDescription()));
            $user = $this->getUser();
            $existingUC = $user->getUserCollections()->first();
            $adminSettings = new AdminSettings();
            $adminSettings->setCollection($collection)
                ->setVotingActive(false);
            $userCollection = new UserCollection();
            $userCollection->setCollection($collection)
                ->setUser($user)
                ->setFirstName($existingUC->getFirstName())
                ->setFamilyName($existingUC->getFamilyName())
                ->setRole('Admin');
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->persist($userCollection);
            $em->persist($adminSettings);
            $em->flush();

            return $this->redirectToRoute("collection_index", [
                "entity" => $collection->getId(),
            ]);
        }

        $userCollections = $this->getUser()->getUserCollections();
        if (count($userCollections) === 1) {
            return $this->redirectToRoute('collection_index', [
                'entity' => $userCollections->first()->getCollection()->getId()
            ]);
        }

        return $this->render('collection/collectionsIndex.html.twig', [
            'userCollections' => $userCollections,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/collection/{entity}", name="collection_index")
     */
    public function collectionIndex(Collection $entity, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $entity)) {
            return $this->redirectToRoute('collections_index');
        }

        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea, [
            'action' => $this->generateUrl('create_idea', [
                'collection' => $entity->getId(),
            ])
        ]);

        return $this->render('collection/collectionIndex.html.twig', [
            'collection' => $entity,
            'form' => $form->createView(),
            'ideas' => $this->getDoctrine()->getManager()->getRepository(Idea::class)->findLatestDistinctIdeas($entity),
            'admin' => $accessSrv->checkAccess($this->getUser(), $entity, true),
        ]);
    }
}
