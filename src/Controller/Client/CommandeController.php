<?php

namespace App\Controller\Client;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Etat;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\LigneCommande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use \DateTime;
class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
    /**
     * @Route("/commande/show", name="commande_show",methods={"GET"})
     */

    public function show(Request $request, Environment $twig)
    {
        $ligne_panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()],['dateAchat'=>'ASC']);
        //$prixTotal=$this->getDoctrine()->getRepository(Panier::class)->findPrixTotal($this->getUser()->getId());
        return new Response($twig->render('client/commande/mescommandes.html.twig',['ligne_panier'=>$ligne_panier]));
    }

    /**
     * @Route("/commande/add", name="commande_add",methods={"GET"})
     */
    public function add(Request $request, Environment $twig ){
        $lignes_panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        $commande = new Commande();
        $commande->setUser($this->getUser());
        $commande->setDate(new DateTime());
        $commande->setEtat($this->getDoctrine()->getRepository(Etat::class)->findOneBy(['nom'=>'En attente']));
        $this->getDoctrine()->getManager()->persist($commande);

        foreach($lignes_panier as $ligne_panier){
            $ligne_commande= new LigneCommande();
            $ligne_commande->setCommande($commande);
            $ligne_commande->setProduit($ligne_panier->getProduit());
            $ligne_commande->setQuantite($ligne_panier->getQuantite());
            $ligne_commande->setPrix($ligne_panier->getProduit()->getPrix());
            $this->getDoctrine()->getManager()->persist($ligne_commande);
            $this->getDoctrine()->getManager()->remove($ligne_panier);

        }
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("success","Commande ajoutée avec succès");
        return $this->redirectToRoute('client_panier_index');
    }

    /**
     * @Route("/commande/delete/{id}", name="commande_delete")
     */

    public function delete(Request $request, Environment $twig ){
        $lignes_panier=$this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);

        foreach($lignes_panier as $ligne_panier){
            $produitchoisi=$ligne_panier->getProduit();
            $produitchoisi->setStock($produitchoisi->getStock()+$ligne_panier->getQuantite());
            $this->getDoctrine()->getManager()->remove($ligne_panier);
            $this->getDoctrine()->getManager()->persist($produitchoisi);
        }
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash("error","Commande supprimée");
        return $this->redirectToRoute('client_panier_index');
    }

    /**
     * @Route("/commande/showAllCommandes", name="commande_showAllCommandes")
     */

    public function showAllCommande(Request $request, Environment $twig)
    {
        //$commande=$this->getDoctrine()->getRepository(Commande::class)->findBy(['user'=>$this->getUser());
        $commande=$this->getDoctrine()->getRepository(Commande::class)->findAll([$this->getUser()]);

        return new Response($twig->render('client/commande/showCommandeUser.html.twig',['commandes'=>$commande]));
    }
}