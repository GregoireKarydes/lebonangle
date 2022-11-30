<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
/**
 * It creates a query builder, adds an order by clause, creates a pagerfanta object, sets the max per
 * page, sets the current page, and renders the template
 * 
 * @param CategoryRepository categoryRepository The repository class for the Category entity.
 * @param Request request The request object.
 * 
 * @return Response A response object
 */
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $queryBuilder = $categoryRepository->createQueryBuilder('category')
        ->addOrderBy('category.name', 'ASC');
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render('category/index.html.twig', ['pager'=>$pager
        ]);
    }

/**
 * If the form is submitted and valid, save the category and redirect to the index page.
 * 
 * @param Request request The request object
 * @param CategoryRepository categoryRepository The repository for the Category entity.
 * 
 * @return Response A response object.
 */
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

/**
 * It renders a form to edit a category
 * 
 * @param Request request The request object
 * @param Category category The entity we're editing
 * @param CategoryRepository categoryRepository The repository for the Category entity.
 * 
 * @return Response A Response object
 */
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

/**
 * It deletes a category if it has no adverts attached to it
 * 
 * @param Request request The request object.
 * @param Category category The Category object that will be passed to the template.
 * @param CategoryRepository categoryRepository The repository for the Category entity.
 * 
 * @return Response A Response object
 */
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if(count($category->getAdverts())>0)  {
            $error = "Impossible de supprimer cette categorie car des annonces y sont rattachées";
            $this->addFlash('error',$error);
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
