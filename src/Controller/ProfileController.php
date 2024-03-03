<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UpdateProfileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/update", name="profile_update")
     */
    public function updateProfile(Request $request, SluggerInterface $slugger)
    {
        $user = $this->getUser(); 
        $form = $this->createForm(UpdateProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();

            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();
                try {
                    $profilePicture->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setProfilePicture($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile_show');

        }

        return $this->render('/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
