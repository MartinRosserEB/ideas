<?php

namespace App\Controller;

use App\Entity\AdminSettings;
use App\Entity\Collection;
use App\Entity\Idea;
use App\Entity\UserCollection;
use App\Form\CollectionType;
use App\Form\IdeaType;
use App\Service\Access;
use Dompdf\Dompdf;
use Dompdf\Options;
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
            $collection->setDescription($collection->getDescription());
            $user = $this->getUser();
            $existingUC = $user->getUserCollections()->first();
            $adminSettings = new AdminSettings();
            $adminSettings->setCollection($collection)
                ->setVotingActive(false)
                ->setAnonymousVote(true);
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

    /**
     * @Route("/collection/{entity}/export/pdf", name="collection_export_pdf")
     */
    public function collectionExportPdf(Collection $entity, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $entity, true)) {
            return $this->redirectToRoute('collections_index');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('collection/pdfExport.html.twig', [
            'collection' => $entity,
            'ideas' => $this->getDoctrine()->getManager()->getRepository(Idea::class)->findLatestDistinctIdeas($entity),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Export.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/collection/{entity}/edit", name="collection_edit")
     */
    public function collectionEdit(Request $request, Collection $entity, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $entity, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $form = $this->createForm(CollectionType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("collection_index", [
                "entity" => $entity->getId(),
            ]);
        }

        return $this->render('collection/collectionEdit.html.twig', [
            'form' => $form->createView(),
            'collection' => $entity,
        ]);
    }
}
