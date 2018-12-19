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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;


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

                if($form->get('image')->getData() !== true) {

                    if (!is_null($form->get('image')->getData())) {
                        // Get Posted Image datas
                        $file = $form->get('image')->getData();
                        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $file->move(
                                $this->getParameter('projects_directory'),
                                $fileName
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // instead of its contents
                        $project->setImage($fileName);
                    }
                }


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

    /**
     * @Route("/admin/project/edit/{id}", name="admin_project_edit")
     */
    public function edit(Request $request, ValidatorInterface $validator, $id)
    {
        $repo = $this->getDoctrine()->getRepository(Project::class);
        $project = $repo->find($id);
        $olderFile = $project->getImage();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

       //$postedDate = new Date(strtotime($form->get("createdAt")->getViewData()));
        //$project->setCreatedAt($project);
        //dump($postedDate);
        //die();

        if($form->isSubmitted()) {


            $errors = $validator->validate($project);
            if (count($errors) > 0) {
                return $this->render('admin/project/add.html.twig', [
                    'form'              => $form->createView(),
                    'controller_name' => 'ProjectController',
                    'errors' => $errors
                ]);
            }

            if($form->isValid()){
                if($form->get('image')->getData() !== true) {
                    //Remove previous file
                    $oldImagePath = $this->getParameter('projects_directory') . "/" . $olderFile;
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }

                    // Get Posted Image datas
                    $file = $form->get('image')->getData();
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('projects_directory'),
                            $fileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // instead of its contents
                    $project->setImage($fileName);
                } else {
                    $project->setImage($olderFile);
                }

                // get data in form
                $this->getDoctrine()->getManager()->persist($project);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->set('success', 'The Project has been successfully updated');
                return $this->render('admin/project/add.html.twig', [
                    'form'            => $form->createView(),
                    'controller_name' => 'ProjectController',
                    'image'           => $project->getImage()
                ]);
            }
        }




        return $this->render('admin/project/add.html.twig', [
            'form'            => $form->createView(),
            'controller_name' => 'ProjectController',
            'image'          => $project->getImage()
        ]);
    }

}
