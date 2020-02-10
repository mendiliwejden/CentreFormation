<?php

namespace FormationBundle\Controller;

use FormationBundle\Entity\Certificate;
use FormationBundle\Entity\Certification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Certificate controller.
 *
 */
class CertificateController extends Controller
{
    /**
     * Lists all certificate entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $certificates = $em->getRepository('FormationBundle:Certificate')->findAll();

        return $this->render('certificate/index.html.twig', array(
            'certificates' => $certificates,
        ));
    }

    /**
     * Creates a new certificate entity.
     *
     */
    public function newAction(Request $request)
    {
        $certificate = new Certificate();
        $form = $this->createForm('FormationBundle\Form\CertificateType', $certificate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           /** @var UploadedFile $imgFile */
            $imgFile = $form['img']->getData();

           // this condition is needed because the 'img' field is not required
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
               // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();

               // Move the file to the directory where img are stored
                try {
                    $location = $this->getParameter('img_directory') . '/certificates';
                    $imgFile->move(
                        $location,
                        $newFilename
                    );
                } catch (FileException $e) {
                   // ... handle exception if something happens during file upload
                    throw new FileException('Failed to upload file' . $e->getMessage());
                }

                $certificate->setImg($newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($certificate);
                $em->flush();

            return $this->redirectToRoute('certificate_show', array('id' => $certificate->getId()));
            }
        }
        return $this->render('certificate/new.html.twig', array(
            'certificate' => $certificate,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a certificate entity.
     *
     */
    public function showAction(Certificate $certificate)
    {
        $deleteForm = $this->createDeleteForm($certificate);

        return $this->render('certificate/show.html.twig', array(
            'certificate' => $certificate,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing certificate entity.
     *
     */
    public function editAction(Request $request, Certificate $certificate)
    {
        $deleteForm = $this->createDeleteForm($certificate);
        $editForm = $this->createForm('FormationBundle\Form\CertificateType', $certificate);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('certificate_edit', array('id' => $certificate->getId()));
        }

        return $this->render('certificate/edit.html.twig', array(
            'certificate' => $certificate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a certificate entity.
     *
     */
    public function deleteAction(Request $request, Certificate $certificate)
    {
        $form = $this->createDeleteForm($certificate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($certificate);
            $em->flush();
        }

        return $this->redirectToRoute('certificate_index');
    }

    /**
     * Creates a form to delete a certificate entity.
     *
     * @param Certificate $certificate The certificate entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Certificate $certificate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('certificate_delete', array('id' => $certificate->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function buyAction(Certificate $certificate) 
    {
        $user = $this->getUser();
        $user->addCertificate($certificate);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('certificate_index');
    }
}
