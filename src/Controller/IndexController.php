<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index(Request $request)
    {
        if($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_produit_show');
            // return $this->render('admin/produit/showProduits.html.twig');
        }
        if($this->isGranted('ROLE_USER')) {
/*            if(! is_null($this->getUser())){
                echo "<br>";
                echo " id: ".$this->getUser()->getId();
                echo " roles :   ";
                print_r($this->getUser()->getRoles());
                die();
            }*/
            return $this->redirectToRoute('client_panier_index');
            //  return $this->render('client/boutique/produit.html.twig');
        }
        return $this->render('accueil.html.twig');

    }
}