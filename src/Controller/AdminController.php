<?php

namespace App\Controller;

use App\Entity\AdminSettings;
use App\Entity\User;
use App\Form\AdminSettingsType;
use App\Form\UserType;
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
     * @Route("users/show/", name="show_users")
     */
    public function showUsers()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('admin/users.html.twig', [
            'users' => $em->getRepository(User::class)->findAll(),
        ]);
    }

    /**
     * @Route("user/edit/{user}", name="edit_user")
     */
    public function editUser(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("show_users");
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/create", name="create_user")
     */
    public function createUser(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user->setRoles(['role' => 'ROLE_USER']);
            $now = new \DateTime('now');
            $hash = md5('prefix'.$now->format('Y-m-d H:i:s').'suffix');
            $user->setApiToken($hash);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("show_users");
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("settings/edit", name="edit_admin_settings")
     */
    public function editAdminSettings(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $adminSettings = $em->getRepository(AdminSettings::class)->findOneById(1);
        if (!$adminSettings) {
            $adminSettings = new AdminSettings();
        }
        $form = $this->createForm(AdminSettingsType::class, $adminSettings);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminSettings = $form->getData();
            $em->persist($adminSettings);
            $em->flush();

            return $this->redirectToRoute("show_users");
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("send/mail", name="send_mail")
     */
    public function sendMail(\Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $adminSettings = $em->getRepository(AdminSettings::class)->findOneById(1);
        if (!$adminSettings || !$adminSettings->getMailText() || !$adminSettings->getMailSubject()) {
            throw new \Exception('E-Mail text or subject not defined.');
        }
        $mailText = $adminSettings->getMailText();
        $mailSubject = $adminSettings->getMailSubject();

        $users = $em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $adaptedText = str_replace(
                '%Link%', $this->generateUrl('ideas_index', [], UrlGeneratorInterface::ABSOLUTE_URL).'?u='.$user->getApiToken(), str_replace(
                    '%Nachname%', $user->getFamilyName(), str_replace(
                        '%Vorname%', $user->getFirstName(), $mailText)));
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

        return $this->redirectToRoute("show_users");
    }
}
