<?php

namespace App\Controller\Admin;

use App\Data\SearchData;
use App\Form\SearchForm;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\HttpFoundation\Response;    // objet RESPONSE

use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Form\ProduitType;

/**
 * @Route(path="/admin",name="admin_")
 */

class ProduitController extends AbstractController
{
     /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $repository, Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $produit = $repository->findSearch($data);
        return $this->render('client/boutique/panier_produit.html.twig', [
            'produits' => $produit,
            'form' => $form->createView()
        ]);
        return $this->redirectToRoute('admin_produit_show');
    }
    /**
     * @Route("/produit/show", name="produit_show", methods={"GET"})
     */
    public function showProduits(Request $request)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([],['typeProduit' => 'ASC']);

        return $this->render('admin/produit/showProduits.html.twig', ['produits' => $produits]);
    }
    /**
     * @Route("/produit/add", name="produit_add", methods={"GET","POST"})
     */
    public function addProduit(Request $request, ValidatorInterface $validator)
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success","Produit ". $produit->getNom()." ajouté avec succès");
            return $this->redirectToRoute('admin_produit_show');
        }

        return $this->render('admin/produit/addProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/produit/edit/{id}<\d+>", name="produit_edit",  methods={"GET","PUT"})
     */
    public function editProduit(Request $request, $id=null)
    {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $form = $this->createForm(ProduitType::class, $produit, [
            'action' => $this->generateUrl('admin_produit_edit',['id'=>$id]),
            'method' => 'PUT',]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();
           $this->addFlash("info","Produit ". $produit->getNom()." modifié avec succès");
            return $this->redirectToRoute('admin_produit_show');
        }

        return $this->render('admin/produit/editProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/produit/delete", name="produit_delete", methods={"DELETE"})
     */
    public function deleteProduit(Request $request)
    {
        if(!$this->isCsrfTokenValid('produit_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $entityManager->remove($produit);
        $entityManager->flush();
        $this->addFlash("error","Produit ". $produit->getNom()." supprimé");
        return $this->redirectToRoute('admin_produit_show');
    }

    /**
     * @Route("/produit/details", name="produit_details", methods={"GET"})
     */
    public function detailsProduit(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $typeProduits=$entityManager->getRepository(Produit::class)->getDetailsProduits();
        dump($typeProduits);

        return $this->render('admin/produit/detailsTypeProduit.html.twig', ['typeProduits' => $typeProduits]);
    }
}
