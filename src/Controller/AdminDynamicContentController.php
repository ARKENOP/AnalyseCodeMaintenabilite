<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\DynamicContent;
use App\Form\DynamicContentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/admin')]
class AdminDynamicContentController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/contenu-dynamique/modifier/{id}/{slug}/{name}/', name: 'dynamic_content_edit', requirements: ["name" => "[a-z0-9_-]{2,50}"])]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function dynamicContentEdit(string $name, Request $request, Customer $customer): Response
    {
        $dynamicContentRepo = $this->entityManager->getRepository(DynamicContent::class);
        $currentDynamicContent = $dynamicContentRepo->findOneByName($name);

        if (!$currentDynamicContent) {
            $currentDynamicContent = new DynamicContent();
            $currentDynamicContent->setName($name);
            $this->entityManager->persist($currentDynamicContent);
        }

        $form = $this->createForm(DynamicContentType::class, $currentDynamicContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Le contenu a bien été modifié !');

            return $this->redirectToRoute('app_customer', [
                'id' => $customer->getId(),
                'slug' => $customer->getSlug()
            ]);
        }

        return $this->render('dynamic_content/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
