<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/connexion", name="security_login")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security_login.html.twig', [
            'page_name' => 'Login',
            'last_username'=>$lastUsername,
            'error'=>$error
        ]);
    }

    /**
     * @Route("/add_user", name="add_user")
     */

    public function addUser(Request $request,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em){
        $user = new User();

        $formUser = $this->createForm(UserType::class,$user);

        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()){

            $password = $passwordEncoder->encodePassword($user, $user->getMotdepasse());
            $user->setPassword($password);
            $user->setRoles('ROLE_USER');
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','User successfully added');
            return $this->redirectToRoute('idea');
        }

        return $this->render('main/index.html.twig',[
            'formUser'=>$formUser->createView(),
            'date'=>date('Y')
        ]);
    }
}
