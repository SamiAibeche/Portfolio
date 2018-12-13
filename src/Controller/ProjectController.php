<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\ProjectType;

class ProjectController extends AbstractController
{
    /**
     * @Route("/admin/project", name="admin_project")
     */
    public function index()
    {
        $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();

        return $this->render('admin/project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects
        ]);
    }


    /**
     * @Route("/admin/project/add", name="admin_project_add")
     */
    public function add(Request $request, ValidatorInterface $validator)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $errors = $validator->validate($project);
            if (count($errors) > 0) {
                return $this->render('admin/project/add.html.twig', [
                    'form'              => $form->createView(),
                    'controller_name' => 'ProjectController',
                    'errors' => $errors
                ]);
            }

            if($form->isValid()) {
                // get data in form
                $this->getDoctrine()->getManager()->persist($project);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->set('success', 'The Project has been successfully created !');
                return $this->redirectToRoute("admin_project");
            }
        }


        return $this->render('admin/project/add.html.twig', [
            'form'              => $form->createView(),
            'controller_name'   => 'ProjectController'
        ]);
    }

}
