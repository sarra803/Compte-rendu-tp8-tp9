<?php

namespace App\Controller;

use App\Entity\User as EntityUser;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
* @Route("/admin", name="admin_")
*/
class AdminController extends AbstractController
{
    /**
 * @Route("/utilisateurs", name="utilisateurs")
 */
 public function usersList(UserRepository $user) {
 return $this->render("admin/users.html.twig",[
 'users' => $user->findAll()
 ]);
 }
 /**
 * @Route("/utilisateurs/modifier/{id}", name="modifier_utilisateur")
 */
public function editUser(Request $request, EntityUser $user, EntityManagerInterface $em) {

$form = $this->createForm(EditUserType::class,$user);

$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()) {
$em->flush();

return $this->redirectToRoute('admin_utilisateurs');
}

return $this->render('admin/editUser.html.twig', ['formUser' => $form->createView()]);
}

   
}
