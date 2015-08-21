<?php

namespace Foo\NewsBundle\Controller;
use Foo\NewsBundle\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $news = $this->getDoctrine()
            ->getRepository('FooNewsBundle:News')
            ->findAll();
      if (!$news) {
        throw $this->createNotFoundException('No news found');
      }
    
      $build['news'] = $news;
      return $this->render('FooNewsBundle:Default:news_show_all.html.twig', $build);
        
        #return $this->render('FooNewsBundle:Default:index.html.twig', array('name' => $name));
    }

    public function showAction($id) {
      $news = $this->getDoctrine()
            ->getRepository('FooNewsBundle:News')
            ->find($id);
      if (!$news) {
        throw $this->createNotFoundException('No news found by id ' . $id);
      }
    
      $build['news_item'] = $news;
      return $this->render('FooNewsBundle:Default:news_show.html.twig', $build);
    }
    
    public function addAction(Request $request) {

     $news = new News();
     $news->setCreatedDate(new \DateTime());//definir a data atual
    
     $form = $this->createFormBuilder($news)//criar formulário
        ->add('title', 'text')
        ->add('body', 'text')
        ->add('save', 'submit')
        ->getForm();

     $form->handleRequest($request);//enviar    
     if ($form->isValid()) {
       $em = $this->getDoctrine()->getManager();
       $em->persist($news);
       $em->flush();
       return new Response('News added successfuly');
     }
    
     $build['form'] = $form->createView();//cria o formulario e passar para o modelo
     return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);
 }
 
 public function editAction($id, Request $request) {

    $em = $this->getDoctrine()->getManager();
    $news = $em->getRepository('FooNewsBundle:News')->find($id);
    if (!$news) {
      throw $this->createNotFoundException(
              'No news found for id ' . $id
      );
    }
    
    $form = $this->createFormBuilder($news)
        ->add('title', 'text')
        ->add('body', 'text')
        ->add('save', 'submit')
        ->getForm();

    $form->handleRequest($request);
 
    if ($form->isValid()) {
        $em->flush();
        return new Response('News updated successfully');
    }
    
    $build['form'] = $form->createView();

    return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);
 }
 
 public function deleteAction($id, Request $request) {

    $em = $this->getDoctrine()->getManager();
    $news = $em->getRepository('FooNewsBundle:News')->find($id);
    if (!$news) {
      throw $this->createNotFoundException(
              'No news found for id ' . $id
      );
    }

    $form = $this->createFormBuilder($news)
            ->add('delete', 'submit')
            ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
      $em->remove($news);
      $em->flush();
      return new Response('News deleted successfully');
    }
    
    $build['form'] = $form->createView();
    return $this->render('FooNewsBundle:Default:news_add.html.twig', $build);
}
}
