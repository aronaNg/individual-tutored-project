<?php
namespace App\Controller\Client;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Form\ContactType;
//use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use \DateTime;

class PanierController extends AbstractController
{
    /**
     * @Route("/client", name="client_panier_index", methods={"GET"})
     * @Route("/client/panierProduits/show", name="client_panier_showProduits", methods={"GET"})
     */
    public function showPanierProduits(Request $request)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);
        $typeProduits=$this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $monpanier = $this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits, 'monpanier' => $monpanier,'typeProduits'=>$typeProduits]);
    }

    /**
     * @Route("/client/contact", name="client_contact")
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $email= (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to("ngomarona51@gmail.com")
                ->subject('Contact depuis le site Sen Jaba')
                ->htmlTemplate('client/contact/email.html.twig')
                ->context([
                    'mail' => $contact->get('email')->getData(),
                    'sujet' => $contact->get('sujet')->getData(),
                    'message' => $contact->get('message')->getData()

                ])
            ;

            $mailer->send($email);
            $this->addFlash('message', 'mail de contact envoyé');
            return $this->redirectToRoute('client_contact');

        }

        return $this->render('client/contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/panier/add/", name="panier_add",methods={"GET"})
     */

    public function add(Request $request,Environment $twig){
        $produitchoisi=$this->getDoctrine()->getRepository(Produit::class)->find($request->get('id'));
        $ligne_panier=$this->getDoctrine()->getRepository(Panier::class)->findOneBy(['produit'=>$produitchoisi,'user'=>$this->getUser()]);
        if($ligne_panier){
            $quantite=$ligne_panier->getQuantite();
            $ligne_panier->setQuantite($quantite);
        }
        else{
            $ligne_panier=new Panier();
            $ligne_panier->setUser($this->getUser());
            $ligne_panier->setDateAchat(new DateTime());

            $ligne_panier->setQuantite(1);
            $ligne_panier->setProduit($produitchoisi);
        }
        $this->getDoctrine()->getManager()->persist($ligne_panier);
        $produitchoisi->setStock($produitchoisi->getStock()-1);
        $this->getDoctrine()->getManager()->persist($produitchoisi);
        $this->getDoctrine()->getManager()->flush();
        $monpanier = $this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits,'monpanier' => $monpanier]);
    }

    /**
     * @Route("/panier/delete/{id}", name="panier_delete")
     */

    public function delete($id,Request $request, Environment $twig)
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);

        $produitchoisi = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $ligne_panier = $this->getDoctrine()->getRepository(Panier::class)->findOneBy(['produit' => $produitchoisi, 'user' => $this->getUser()]);

        if ($ligne_panier) {
            $quantite = $ligne_panier->getQuantite();
            $ligne_panier->setQuantite($quantite);
            $this->getDoctrine()->getManager()->remove($ligne_panier);
            $produitchoisi->setStock($produitchoisi->getStock() + 1);
            $this->getDoctrine()->getManager()->persist($produitchoisi);
            $this->getDoctrine()->getManager()->flush();
        }

        $monpanier = $this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits,'monpanier' => $monpanier]);
    }

    /**
     * @Route("/panier/augmenter", name="panier_v1_augmenter")
     */

    public function augmenter(Request $request,Environment $twig){
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);
        $produitchoisi=$this->getDoctrine()->getRepository(Produit::class)->find($request->get('id'));
        $ligne_panier=$this->getDoctrine()->getRepository(Panier::class)->findOneBy(['produit'=>$produitchoisi,'user'=>$this->getUser()]);
        if($ligne_panier){
            $quantite=$ligne_panier->getQuantite();
            $ligne_panier->setQuantite($quantite+1);
        }
        else{
            $ligne_panier=new Panier();
            $ligne_panier->setUser($this->getUser());
            $ligne_panier->setDateAchat(new DateTime());
            $ligne_panier->setQuantite(1);
            $ligne_panier->setProduit($produitchoisi);
        }
        $this->getDoctrine()->getManager()->persist($ligne_panier);
        $produitchoisi->setStock($produitchoisi->getStock()-1);
        $this->getDoctrine()->getManager()->persist($produitchoisi);
        $this->getDoctrine()->getManager()->flush();
        $monpanier = $this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits,'monpanier' => $monpanier]);

    }

    /**
     * @Route("/panier/diminuer", name="panier_v1_diminuer")
     */

    public function diminuer(Request $request,Environment $twig){
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([], ['typeProduit' => 'ASC', 'stock' => 'ASC']);
        $produitchoisi=$this->getDoctrine()->getRepository(Produit::class)->find($request->get('id'));
        $ligne_panier=$this->getDoctrine()->getRepository(Panier::class)->findOneBy(['produit'=>$produitchoisi,'user'=>$this->getUser()]);
        if($ligne_panier){
            $quantite=$ligne_panier->getQuantite();
            if($quantite>0){
                $ligne_panier->setQuantite($quantite-1);
            }
        }
        else{
            $ligne_panier=new Panier();
            $ligne_panier->setUser($this->getUser());
            $ligne_panier->setDateAchat(new DateTime());
            $ligne_panier->setQuantite(1);
            $ligne_panier->setProduit($produitchoisi);
        }
        $this->getDoctrine()->getManager()->persist($ligne_panier);
        $produitchoisi->setStock($produitchoisi->getStock()-1);
        $this->getDoctrine()->getManager()->persist($produitchoisi);
        $this->getDoctrine()->getManager()->flush();
        $monpanier = $this->getDoctrine()->getRepository(Panier::class)->findBy(['user'=>$this->getUser()]);
        return $this->render('client/boutique/panier_produit.html.twig', ['produits' => $produits,'monpanier' => $monpanier]);

    }
}