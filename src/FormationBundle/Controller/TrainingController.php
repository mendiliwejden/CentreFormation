<?php

namespace FormationBundle\Controller;

use FormationBundle\Entity\Rating;
use FormationBundle\Entity\Training;
use Stripe\Error\Card;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Training controller.
 *
 */
class TrainingController extends Controller
{
    /**
     * Lists all training entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->query->get('q');
        if ($q) {
            $trainings = $em->getRepository('FormationBundle:Training')->findBy(['name' => $q]);
        } else {
            $trainings = $em->getRepository('FormationBundle:Training')->findAll();
        }
        return $this->render('training/index.html.twig', array(
            'trainings' => $trainings,
        ));
    }

    /**
     * Creates a new training entity.
     *
     */
    public function newAction(Request $request)
    {
        $training = new Training();
        $form = $this->createForm('FormationBundle\Form\TrainingType', $training);
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
                    $location = $this->getParameter('img_directory') . '/trainings';
                    $imgFile->move(
                        $location,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new FileException('Failed to upload file' . $e->getMessage());
                }

                $training->setImg($newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($training);
                $em->flush();
                
                return $this->redirectToRoute('training_show', array('id' => $training->getId()));
            }
        }

        return $this->render('training/new.html.twig', array(
            'training' => $training,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a training entity.
     * @param Training $training
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Training $training)
    {
        $deleteForm = $this->createDeleteForm($training);
        $rating = new Rating();
        $ratingForm = $this->createForm('FormationBundle\Form\RatingType', $rating,
            array('action'=> $this->generateUrl('training_rate', array('id' => $training->getId()))));
        $stripe_form = $this->createPaymentForm($training);

        return $this->render('training/show.html.twig', array(
            'training' => $training,
            'delete_form' => $deleteForm->createView(),
            'rating_form' => $ratingForm->createView(),
            'stripe_form' => $stripe_form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ));
    }

    /**
     * Displays a form to edit an existing training entity.
     * @param Request $request
     * @param Training $training
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Training $training)
    {
        $deleteForm = $this->createDeleteForm($training);
        $editForm = $this->createForm('FormationBundle\Form\TrainingType', $training);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('training_edit', array('id' => $training->getId()));
        }

        return $this->render('training/edit.html.twig', array(
            'training' => $training,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a training entity.
     *
     */
    public function deleteAction(Request $request, Training $training)
    {
        $form = $this->createDeleteForm($training);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($training);
            $em->flush();
        }

        return $this->redirectToRoute('training_index');
    }

    /**
     * Creates a form to delete a training entity.
     *
     * @param Training $training The training entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Training $training)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('training_delete', array('id' => $training->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function createPaymentForm(Training $training)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('payment-form')
            ->setAction($this->generateUrl('training_payment', array('id' => $training->getId())))
            ->add('token', HiddenType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    public function rateAction(Request $request, Training $training)
    {
        $rating = new Rating();
        $form = $this->createForm('FormationBundle\Form\RatingType', $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rating->setRate($form['rate']->getData());
            $user = $this->getUser();
            $rating->setClient($user);
            $rating->setTraining($training);
            $em->persist($rating);
            $em->flush();
        }

        return $this->redirectToRoute('training_index', array('id' => $training->getId()));
    }

    public function paymentAction(Request $request, Training $training)
    {
        $form = $this->createPaymentForm($training);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            // data is an array with "name", "email", and "message" keys
            $this->get('formation.stripe')->createCharge($this->getUser(), $form->get('token')->getData());
            $user = $this->getUser();
            $user->addTraining($training);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success","payment successful");
        }
        return $this->redirectToRoute('training_show', array('id' => $training->getId()));
    }
}
