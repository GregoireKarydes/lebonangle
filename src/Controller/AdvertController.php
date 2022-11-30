<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Event\AdvertCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


#[Route('/admin/adverts')]
class AdvertController extends AbstractController
{

/**
 * It creates a query builder, creates a pagerfanta object, sets the number of results per page, and
 * sets the current page
 * 
 * @param AdvertRepository advertRepository the repository class for the Advert entity
 * @param Request request The current request.
 * 
 * @return Response A Response object
 */
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository, Request $request): Response
    {
        $queryBuilder = $advertRepository->createQueryBuilder('advert');
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));
        return $this->render('advert/index.html.twig', ['pager'=>$pager]);
    }
    


/**
 * It applies the publish transition to the advert object
 * 
 * @param Advert advert The advert object to apply the transition to.
 * @param WorkflowInterface advertPublishingStateMachine The workflow service.
 * @param AdvertRepository advertRepository The repository used to persist the advert object.
 * 
 * @return RedirectResponse A RedirectResponse object.
 */
    #[Route('/publish/{id}', name: 'app_advert_publish',methods: ['GET', 'POST'])]
    public function publish(Advert $advert, WorkflowInterface $advertPublishingStateMachine, AdvertRepository $advertRepository) : RedirectResponse {
        /* It applies the publish transition to the advert object. */
        $advertPublishingStateMachine->apply($advert, 'publish');
        $now = new DateTimeImmutable();
        $advert->setPublishedAt($now);
        $advertRepository->save($advert, true);
        return $this->redirectToRoute('app_advert_index', [], Response::HTTP_SEE_OTHER);
    }

 /**
  * It checks if the advert can be rejected or unpublished, and if so, it applies the appropriate
  * transition
  * 
  * @param Advert advert The advert object that we want to unpublish.
  * @param WorkflowInterface advertPublishingStateMachine This is the service that we created in the
  * previous step.
  * @param AdvertRepository advertRepository The repository for the Advert entity.
  * 
  * @return RedirectResponse A RedirectResponse object.
  */
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

  /**
   * It takes an advert object as an argument, and returns a response object
   * 
   * @param Advert advert The advert object that will be passed to the template.
   * 
   * @return Response A Response object
   */
    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

}
