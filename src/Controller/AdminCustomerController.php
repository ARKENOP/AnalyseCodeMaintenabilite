<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Form\CustomerType;
use App\Form\EditCustomerType;
use Cocur\Slugify\Slugify;
use App\Entity\DynamicContent;
use App\Form\DynamicContentType;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\TariffZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/admin')]
class AdminCustomerController extends AbstractController
{
    private CustomerRepository $customerRepository;
    private ContactRepository $contactRepository;
    private ContractRepository $contractRepository;
    private TariffZoneRepository $tariffZoneRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CustomerRepository $customerRepository,
        ContactRepository $contactRepository,
        ContractRepository $contractRepository,
        TariffZoneRepository $tariffZoneRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->contactRepository = $contactRepository;
        $this->contractRepository = $contractRepository;
        $this->tariffZoneRepository = $tariffZoneRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/client', name: 'app_customer_list')]
    public function showCustomers(): Response
    {
        return $this->render('admin_main/customer_list.html.twig', [
            'customers' => $this->customerRepository->findAll()
        ]);
    }

    #[Route('/client/{id}/{slug}', name: 'app_customer')]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function showCustomer(Customer $customer, string $slug, Request $request): Response
    {
        if (!$customer) {
            return $this->redirectToRoute('app_customer_list', [], 301);
        }

        if ($customer->getSlug() !== $slug) {
            $this->addFlash('alert', 'Vous ne pouvez pas faire ça !');
            return $this->redirectToRoute('app_customer_list', [], 301); 
        }

        $user = $customer->getUser();
        $dynamicContent = new DynamicContent();
        $form = $this->createForm(DynamicContentType::class, $dynamicContent);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($dynamicContent);
            $this->entityManager->flush();
        }

        return $this->render('admin_main/customer_show.html.twig', [
            'customer' => $customer,
            'user' => $user,
            'contacts' => $this->contactRepository->findBy(['user' => $user]),
            'contracts' => $this->contractRepository->findBy(['customer' => $customer]),
            'dynamicContent' => $dynamicContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/creer-un-client/{id}/{slug}', name: 'app_customer_add')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function createCustomer(Request $request, User $user): Response
    {
        if (!$this->tariffZoneRepository->findAll()) {
            $this->addFlash('notice', 'Vous n\'avez pas encore défini de zone tarifaire.');
            return $this->redirectToRoute('app_tariff_zone_new');
        }
        
        $customer = new Customer();
        $customer->setUser($user);
        
        $form = $this->createForm(CustomerType::class, $customer, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($customer->getSiret()) {
                $customer->setSiret(str_replace(' ', '', $form->get('siret')->getData()));
            }
            
            $slugify = new Slugify();
            $customer->setSlug($slugify->slugify($customer->getName()));

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            $this->addFlash('success', 'La création du client est bien enregistrée.');
            return $this->redirectToRoute('app_customer', ['id' => $customer->getId(), 'slug' => $customer->getSlug()]);
        }

        return $this->render('admin_main/customer_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'customer' => $customer,
            'user' => $user
        ]);
    }

    #[Route('/client/{id}/{slug}/modifier-un-client', name: 'app_customer_edit')]
    #[ParamConverter('customer', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function editCustomer(Request $request, Customer $customer, string $slug): Response
    {
        if (!$customer) {
            return $this->redirectToRoute('app_customer_list', [], 301);
        }
        
        if ($customer->getSlug() !== $slug) {
            $this->addFlash('alert', 'Vous ne pouvez pas faire ça !');
            return $this->redirectToRoute('app_customer', ['id' => $customer->getId(), 'slug' => $slug], 301);
        }
        
        $form = $this->createForm(EditCustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($customer->getSiret()) {
                $customer->setSiret(str_replace(' ', '', $form->get('siret')->getData()));
            }
            $this->entityManager->flush();

            $this->addFlash('success', 'La modification du client est bien enregistrée.');
            return $this->redirectToRoute('app_customer', ['id' => $customer->getId(), 'slug' => $slug]);
        }

        return $this->render('admin_main/customer_edit.html.twig', [
            'form' => $form->createView(),
            'flash' => $this,
            'customer' => $customer
        ]);
    }
}
