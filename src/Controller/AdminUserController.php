<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewUserType;
use App\Form\EditUserType;
use Cocur\Slugify\Slugify;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
class AdminUserController extends AbstractController
{
    private UserRepository $userRepository;
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/utilisateur', name: 'app_user_list')]
    public function showUserList(): Response
    {
        return $this->render('admin_main/user_list.html.twig', [
            'users' => $this->userRepository->findAll(),
            'customer' => $this->customerRepository->findAll()
        ]);
    }

    #[Route('/utilisateur/{id}/{slug}', name: 'app_user_show')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function showUser(User $user, string $slug): Response
    {
        if ($user->getSlug() !== $slug) {
            $this->addFlash('alert', 'Vous ne pouvez pas faire ça !');
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('admin_main/user_show.html.twig', [
            'user' => $user,
            'contacts' => $user->getContacts(),
            'customer' => $user->getCustomer(),
            'flash' => $this,
        ]);
    }

    #[Route('/nouvel-utilisateur', name: 'app_user_add')]
    public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search_email = $this->userRepository->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
                
                $slugify = new Slugify();
                $user->setSlug($slugify->slugify($user->getFirstname() . ' ' . $user->getLastname()));

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Le nouvel utilisateur est enregistré.');
                return $this->redirectToRoute('app_user_show', ['id' => $user->getId(), 'slug' => $user->getSlug()]);
            }

            $this->addFlash('alert', 'L\'email que vous avez renseigné existe déjà !!');
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('admin_main/user_new.html.twig', [
            'form' => $form->createView(),
            'flash' => $this
        ]);
    }

    #[Route('/utilisateur/{id}/{slug}/modifier', name: 'app_user_edit')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function editUser(Request $request, User $user): Response
    {
        if (!$user) {
            $this->addFlash('alert', 'L\'utilisateur n\'existe pas');
            return $this->redirectToRoute('app_user_list');
        }

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->flush();
                $this->addFlash('success', 'La modification du contact est bien enregistrée.');
                return $this->redirectToRoute('app_user_show', ['id' => $user->getId(), 'slug' => $user->getSlug()]);
            }
            $this->addFlash('alert', 'Erreur sur le formulaire.');
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('admin_main/user_edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
