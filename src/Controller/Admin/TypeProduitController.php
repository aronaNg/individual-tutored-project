<?php

namespace App\Controller\Admin;

use App\Entity\TypeProduit;
use App\Form\TypeProduitType;
use App\Repository\TypeProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type-produit")
 */
class TypeProduitController extends AbstractController
{
    /**
     * @Route("/", name="admin_type_produit_index", methods={"GET"})
     */
    public function index(TypeProduitRepository $typeProduitRepository): Response
    {
        return $this->render('admin/type_produit/index.html.twig', [
            'type_produits' => $typeProduitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="type_produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $typeProduit = new TypeProduit();
        $form = $this->createForm(TypeProduitType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeProduit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_type_produit_index');
        }

        return $this->render('admin/type_produit/new.html.twig', [
            'type_produit' => $typeProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_produit_show", methods={"GET"})
     */
    public function show(TypeProduit $typeProduit): Response
    {
        return $this->render('admin/type_produit/show.html.twig', [
            'type_produit' => $typeProduit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeProduit $typeProduit): Response
    {
        $form = $this->createForm(TypeProduitType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_type_produit_index');
        }

        return $this->render('admin/type_produit/edit.html.twig', [
            'type_produit' => $typeProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeProduit $typeProduit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeProduit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_type_produit_index');
    }
}
