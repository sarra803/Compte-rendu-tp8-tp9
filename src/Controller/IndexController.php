<?php
namespace App\Controller;
use App\Entity\PriceSearch;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\Category;
use App\Entity\PropertySearch;
use App\Repository\ArticleRepository;
use App\Form\CategoryType;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\PropertySearchType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
 /**
 *@Route("/",name="article_list")
 */
public function home(Request $request)
{
$PropertySearch = new PropertySearch();
$form = $this->createForm(PropertySearchType::class,$PropertySearch);
$form->handleRequest($request);
//initialement le tableau des articles est vide,
//c.a.d on affiche les articles que lorsque l'utilisateur
//clique sur le bouton rechercher
$articles= [];

if($form->isSubmitted() && $form->isValid()) {
//on récupère le nom d'article tapé dans le formulaire
$nom = $PropertySearch->getNom();
 if ($nom!="")
 //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
 $articles= $this->getDoctrine()->getRepository(Article::class)->findBy(['nom' => $nom] );
 else
 //si si aucun nom n'est fourni on affiche tous les articles
 $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
 }
 return $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
 }

  /**
 * @Route("/article/save")
 */
 public function save() {
  $entityManager = $this->getDoctrine()->getManager();
  $article = new Article();
  $article->setNom('Article 2');
  $article->setPrix(10200);
  
  $entityManager->persist($article);
  $entityManager->flush();
  return new Response('Article enregisté avec id '.$article->getId());
  }
/**
 * @IsGranted("ROLE_EDITOR")
 * @Route("/article/new", name="new_article")
 * Method({"GET", "POST"})
 */
public function new(Request $request) {

  $article = new Article();
  $form = $this->createForm(ArticleType::class,$article);
  $form->handleRequest($request);
  if($form->isSubmitted() && $form->isValid()) {
  $article = $form->getData();
  $entityManager = $this->getDoctrine()->getManager();
  $entityManager->persist($article);
  $entityManager->flush();
  return $this->redirectToRoute('article_list');
  }
  return $this->render('articles/new.html.twig',['form' => $form->createView()]);
  }
 /**
 * @Route("/article/{id}", name="article_show")
 */
public function show($id) {
  $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  return $this->render('articles/show.html.twig',
  array('article' => $article));
   }
   /**
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
/**
 * @IsGranted("ROLE_EDITOR")
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
public function edit(Request $request, $id) {

  $article = new Article();
 $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
  $form = $this->createForm(ArticleType::class,$article);
  
  $form->handleRequest($request);
  if($form->isSubmitted() && $form->isValid()) {
  
  $entityManager = $this->getDoctrine()->getManager();
  $entityManager->flush();
  
  return $this->redirectToRoute('article_list');
  }
  
  return $this->render('articles/edit.html.twig', ['form' =>
 $form->createView()]);
  }
 
  /**
  * @IsGranted("ROLE_EDITOR")
  * @Route("/article/delete/{id}",name="delete_article")
  * @Method({"DELETE"})
  */
  public function delete(Request $request, $id) {
 
  $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
  $entityManager = $this->getDoctrine()->getManager();
  $entityManager->remove($article);
  $entityManager->flush();
  
  $response = new Response();
  $response->send();
  return $this->redirectToRoute('article_list');
  }
  /**
 * @Route("/category/newCat", name="new_category")
 * Method({"GET", "POST"})
 */
 public function newCategory(Request $request) {
 $category = new Category();
 $form = $this->createForm(CategoryType::class,$category);
 $form->handleRequest($request);
 if($form->isSubmitted() && $form->isValid()) {
 $article = $form->getData();
 $entityManager = $this->getDoctrine()->getManager();
 $entityManager->persist($category);
 $entityManager->flush();

 }
return $this->render('articles/newCategory.html.twig',['form'=>$form->createView()]);
}
/**
 * @Route("/art_prix/", name="article_par_prix")
 * Method({"GET"})
 */
 public function articlesParPrix(Request $request)
 {
 
 $priceSearch = new PriceSearch();
 $form = $this->createForm(PriceSearchType::class,$priceSearch);
 $form->handleRequest($request);
 $articles= [];
 if($form->isSubmitted() && $form->isValid()) {
 $minPrice = $priceSearch->getMinPrice();
 $maxPrice = $priceSearch->getMaxPrice();
 $articles= $this->getDoctrine()->getRepository(App\Repository\ArticleRepository::class);
 //$article=$articles->findByPriceRange($minPrice,$maxPrice);
 }
 return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]); 
 }
}