<?php

namespace App\Controller;

use App\Entity\AdminUser;
use App\Form\AdminUserType;
use App\Repository\AdminUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

#[Route('/admin/admin-user')]
class AdminUserController extends  AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(AdminUserRepository $adminUserRepository, Request $request): Response
    {
        $queryBuilder = $adminUserRepository->createQueryBuilder('admin_user');
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));
        return $this->render('admin_user/index.html.twig', ['pager'=>$pager]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdminUserRepository $adminUserRepository): Response
    {
        $adminUser = new AdminUser();
        $form = $this->createForm(AdminUserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUserRepository->save($adminUser, true);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/new.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(AdminUser $adminUser): Response
    {
        return $this->render('admin_user/show.html.twig', [
            'admin_user' => $adminUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminUser $adminUser, AdminUserRepository $adminUserRepository): Response
    {
        $form = $this->createForm(AdminUserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUserRepository->save($adminUser, true);

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/edit.html.twig', [
            'admin_user' => $adminUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, AdminUser $adminUser, AdminUserRepository $adminUserRepository, Security $security): Response
    {
        // CHECK THE NUMBER OF ADMIN CAN'T DELTE IF LESS THAN 2 ADMIN
        $allAdmins = $adminUserRepository->findAll();
        if(count($allAdmins)<2)  {
            $error = "Impossible de supprimer cet admin, vous devez avoir au moins 1 admin";
            $this->addFlash('error',$error);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        if($security->getUser()->getUserIdentifier() === $adminUser->getEmail())  {
            $error = "Impossible de supprimer votre propre compte";
            $this->addFlash('error',$error);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$adminUser->getId(), $request->request->get('_token'))) {
            $adminUserRepository->remove($adminUser, true);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

