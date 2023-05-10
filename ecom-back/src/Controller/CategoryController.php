<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
 
/**
 * @Route("/api", name="api_")
 */
 
class CategoryController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        
    }

    /**
    * @Route("/categories", name="category_index", methods={"GET"})
    */
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine
            ->getRepository(Category::class)
            ->findAll();
  
        $data = [];
  
        foreach ($categories as $category) {
           $data[] = [
               'id' => $category->getId(),
               'name' => $category->getName()
           ];
        }
  
  
        return $this->json($data);
    }

    /**
     * @Route("/category", name="category_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();
        $category= new Category();
        $category->setName($parameters['name']);
        $entityManager->persist($category);
        $entityManager->flush();
        return $this->json(['message' => 'category added'], 200);
    }
  
    /**
     * @Route("/category/{id}", name="category_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
  
        if (!$category) {
  
            return $this->json('No category found for id' . $id, 404);
        }
  
        $data =  [
            'id' => $category->getId(),
            'name' => $category->getName()
        ];
          
        return $this->json($data);
    }
  
    /**
     * @Route("/category/{id}", name="category_edit", methods={"PUT"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
  
        if (!$category) {
            return $this->json('No category found for id' . $id, 404);
        }
  
        $category->setName($parameters['name']);
        $entityManager->flush();
  
        $data =  [
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];
          
        return $this->json($data);
    }
  
    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
  
        if (!$category) {
            return $this->json('No category found for id' . $id, 404);
        }
  
        $entityManager->remove($category);
        $entityManager->flush();
  
        return $this->json('Deleted a category successfully with id ' . $id);
    }
}