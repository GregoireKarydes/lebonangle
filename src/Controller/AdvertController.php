<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Picture;
use App\Event\AdvertCreatedEvent;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\DateImmutableType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Route('/admin/adverts')]
class AdvertController extends AbstractController
{

    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository, Request $request): Response
    {
        $queryBuilder = $advertRepository->createQueryBuilder('advert');
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));
        return $this->render('advert/index.html.twig', ['pager'=>$pager]);
    }
    

    #[Route('/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdvertRepository $advertRepository, EventDispatcherInterface $dispatcher): Response
    {
        $advert = new Advert();
        // $advert->addPicture(new Picture());
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advertRepository->save($advert, true);

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        $dispatcher->dispatch(new AdvertCreatedEvent($advert), AdvertCreatedEvent::NAME);

        return $this->renderForm('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/publish/{id}', name: 'app_advert_publish',methods: ['GET', 'POST'])]
    public function publish(Advert $advert, WorkflowInterface $advertPublishingStateMachine, AdvertRepository $advertRepository) : RedirectResponse {
        $advertPublishingStateMachine->apply($advert, 'publish');
        $now = new DateTimeImmutable();
        $advert->setPublishedAt($now);
        $advertRepository->save($advert, true);
        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/unpublish/{id}', name: 'app_advert_unpublish', methods: ['GET', 'POST'])]
    public function unpublish(Advert $advert, WorkflowInterface $advertPublishingStateMachine,  AdvertRepository $advertRepository) : RedirectResponse {
        if($advertPublishingStateMachine->can($advert, 'reject')) {
            $advertPublishingStateMachine->apply($advert, 'reject');
        }
        else if($advertPublishingStateMachine->can($advert, 'unpublish')) {
            $advertPublishingStateMachine->apply($advert, 'unpublish');
        }
        $advertRepository->save($advert, true);
        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Advert $advert, AdvertRepository $advertRepository): Response
    {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advertRepository->save($advert, true);

            return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_delete', methods: ['POST'])]
    public function delete(Request $request, Advert $advert, AdvertRepository $advertRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $advertRepository->remove($advert, true);
        }

        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }
}
