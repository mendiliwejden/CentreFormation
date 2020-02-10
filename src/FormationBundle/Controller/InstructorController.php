<?php

namespace FormationBundle\Controller;

use FormationBundle\Entity\Instructor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Instructor controller.
 *
 */
class InstructorController extends Controller
{
    /**
     * Lists all instructor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $instructors = $em->getRepository('FormationBundle:Instructor')->findAll();

        return $this->render('instructor/index.html.twig', array(
            'instructors' => $instructors,
        ));
    }

    /**
     * Creates a new instructor entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $instructor = new Instructor();
        $form = $this->createForm('FormationBundle\Form\InstructorType', $instructor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form['img']->getData();

            $instructor->setImg('default.png');
            
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();

                // Move the file to the directory where img are stored
                try {
                    $location = $this->getParameter('img_directory') . '/instructors';
                    $imgFile->move(
                        $location,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new FileException('Failed to upload file' . $e->getMessage());
                }
                $instructor->setImg($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($instructor);
            $em->flush();

            return $this->redirectToRoute('instructor_show', array('id' => $instructor->getId()));
        }

        return $this->render('instructor/new.html.twig', array(
            'instructor' => $instructor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a instructor entity.
     *
     */
    public function showAction(Instructor $instructor)
    {
        $deleteForm = $this->createDeleteForm($instructor);

        return $this->render('instructor/show.html.twig', array(
            'instructor' => $instructor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing instructor entity.
     *
     */
    public function editAction(Request $request, Instructor $instructor)
    {
        $deleteForm = $this->createDeleteForm($instructor);
        $editForm = $this->createForm('FormationBundle\Form\InstructorType', $instructor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('instructor_edit', array('id' => $instructor->getId()));
        }

        return $this->render('instructor/edit.html.twig', array(
            'instructor' => $instructor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a instructor entity.
     *
     */
    public function deleteAction(Request $request, Instructor $instructor)
    {
        $form = $this->createDeleteForm($instructor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($instructor);
            $em->flush();
        }

        return $this->redirectToRoute('instructor_index');
    }

    /**
     * Creates a form to delete a instructor entity.
     *
     * @param Instructor $instructor The instructor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Instructor $instructor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('instructor_delete', array('id' => $instructor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
