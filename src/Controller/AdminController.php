<?php

namespace App\Controller;

use App\Entity\AdminSettings;
use App\Entity\Collection;
use App\Entity\User;
use App\Entity\UserCollection;
use App\Form\AdminSettingsType;
use App\Form\UserCollectionType;
use App\Service\Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/admin/")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("users/show/{collection}", name="show_users")
     */
    public function showUsers(Collection $collection, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $collection, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $em = $this->getDoctrine()->getManager();

        return $this->render('admin/users.html.twig', [
            'collectionUsers' => $collection->getCollectionUsers(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("user/edit/{userCollection}", name="edit_user")
     */
    public function editUser(Request $request, UserCollection $userCollection, Access $accessSrv)
    {
        $collection = $userCollection->getCollection();
        if (!$accessSrv->checkAccess($this->getUser(), $collection, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $form = $this->createForm(UserCollectionType::class, $userCollection);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCollection = $form->getData();
            $userCollection->getUser()->setEmail($form->get("email")->getData());
            $em = $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("show_users", [
                'collection' => $collection->getId(),
            ]);
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("user/create/{collection}", name="create_user")
     */
    public function createUser(Request $request, Collection $collection, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $collection, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $userCollection = new UserCollection();
        $form = $this->createForm(UserCollectionType::class, $userCollection);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCollection = $form->getData();
            $userCollection->setCollection($collection);
            $em = $this->getDoctrine()->getManager();
            $email = $form->get('email')->getData();
            $user = $em->getRepository(User::class)->findOneByEmail($email);
            if (!$user) {
                $user = new User();
                $user->setEmail($email);
            }
            $now = new \DateTime('now');
            $hash = md5('prefix'.$now->format('Y-m-d H:i:s').'suffix');
            $user->setApiToken($hash);
            $user->setPassword($hash);
            $userCollection->setUser($user);
            $em->persist($user);
            $em->persist($userCollection);
            $em->flush();

            return $this->redirectToRoute("show_users", [
                'collection' => $collection->getId(),
            ]);
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("settings/edit/{collection}", name="edit_admin_settings")
     */
    public function editAdminSettings(Request $request, Collection $collection, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $collection, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $em = $this->getDoctrine()->getManager();
        $adminSettings = $collection->getAdminSettings();
        $form = $this->createForm(AdminSettingsType::class, $adminSettings);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminSettings = $form->getData();
            $em->persist($adminSettings);
            $em->flush();

            return $this->redirectToRoute('show_users', [
                'collection' => $collection->getId(),
            ]);
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("send/mail/{collection}", name="send_mail")
     */
    public function sendMail(\Swift_Mailer $mailer, Collection $collection, Access $accessSrv)
    {
        if (!$accessSrv->checkAccess($this->getUser(), $collection, true)) {
            return $this->redirectToRoute('collections_index');
        }

        $em = $this->getDoctrine()->getManager();
        $adminSettings = $em->getRepository(AdminSettings::class)->findOneById(1);
        if (!$adminSettings || !$adminSettings->getMailText() || !$adminSettings->getMailSubject()) {
            throw new \Exception('E-Mail text or subject not defined.');
        }
        $mailText = $adminSettings->getMailText();
        $mailSubject = $adminSettings->getMailSubject();

        $collectionUsers = $collection->getCollectionUsers();
        foreach ($collectionUsers as $collectionUser) {
            $user = $collectionUser->getUser();
            $adaptedText = str_replace(
                '%Link%', $this->generateUrl('collection_index', ['entity' => $collection->getId()], UrlGeneratorInterface::ABSOLUTE_URL).'?u='.$user->getApiToken(), str_replace(
                    '%Nachname%', $collectionUser->getFamilyName(), str_replace(
                        '%Vorname%', $collectionUser->getFirstName(), $mailText)));
            $message = (new \Swift_Message($mailSubject))
                ->setFrom($this->getUser()->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'admin/email.html.twig',
                        ['mailBody' => nl2br($adaptedText)]
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }

        return $this->redirectToRoute("show_users", [
            'collection' => $collection->getId(),
        ]);
    }
}
