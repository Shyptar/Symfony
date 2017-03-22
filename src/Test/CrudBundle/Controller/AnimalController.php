<?php

//src/Test/CrudBundle/Controller/AnimalController.php

namespace Test\CrudBundle\Controller;

use Test\CrudBundle\Entity\Animal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalController extends Controller
{
  public function indexAction()
  {

    // Pour récupérer la liste de toutes les animaux : on utilise findAll()
    $listAnimals = $this->getDoctrine()
      ->getManager()
      ->getRepository('TestCrudBundle:Animal')
      ->findAll()
    ;
      
    return $this->render('TestCrudBundle:Animal:index.html.twig', array(
      'listAnimals' => $listAnimals,
      'reptile' => array('reptile', 'ecaille'),
      'mammifere' => array('mammifere', 'fourrure'),
      'oiseau' => array('oiseau', 'plumage')
    ));
  }

  public function addAction(Request $request, $type, $enveloppe)
  {
    // On crée un objet Animal
    $animal = new Animal;
    $animal->setType($type);
    $animal->setEnveloppe($enveloppe);
      
    $form = $this->get('form.factory')->createBuilder(FormType::class, $animal)
      ->add('nom',            TextType::class)
      ->add('espece',         TextType::class)
      ->add('description',    TextType::class)
      ->add('enveloppe',      TextType::class)
      ->add('type',           TextType::class)
      ->add('save',           SubmitType::class)
      ->getForm()
    ;

    if ($request->isMethod('POST')) {
        
      // On fait le lien Requête <-> Formulaire
      // À partir de maintenant, la variable $Animal contient les valeurs entrées dans le formulaire par le visiteur
      $form->handleRequest($request);

      // On vérifie que les valeurs entrées sont correctes
      if ($form->isValid()) {
        // On enregistre l'objet $Animal dans la base de données
        $em = $this->getDoctrine()->getManager();
        $em->persist($animal);
        $em->flush();
      }
          
      $request->getSession()->getFlashBag()->add('notice', 'Animal bien enregistrée.');

      return $this->redirectToRoute('test_crud_homepage');
    }

    return $this->render('TestCrudBundle:Animal:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $animal = $em->getRepository('TestCrudBundle:Animal')->find($id);

    if (null === $animal) {
      throw new NotFoundHttpException("L'animal d'id ".$id." n'existe pas.");
    }

    $form = $this->get('form.factory')->createBuilder(FormType::class, $animal)
      ->add('nom',            TextType::class)
      ->add('espece',         TextType::class)
      ->add('description',    TextType::class)
      ->add('enveloppe',      TextType::class)
      ->add('type',           TextType::class)
      ->add('save',           SubmitType::class)
      ->getForm()
    ;

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      // Inutile de persister ici, Doctrine connait déjà notre animal
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Animal bien modifié.');

      return $this->redirectToRoute('test_crud_homepage');
    }

    return $this->render('TestCrudBundle:Animal:edit.html.twig', array(
      'animal' => $animal,
      'form'   => $form->createView(),
    ));
  }

  public function deleteAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    $animal = $em->getRepository('TestCrudBundle:Animal')->find($id);

    if (null === $animal) {
      throw new NotFoundHttpException("L'animal d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'un animal contre cette faille
    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($animal);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'animal a bien été supprimée.");

      return $this->redirectToRoute('test_crud_homepage');
    }
    
    return $this->render('TestCrudBundle:Animal:delete.html.twig', array(
      'animal' => $animal,
      'form'   => $form->createView(),
    ));
  }
}