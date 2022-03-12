<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeProduit;
use App\Entity\Produit;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadTypeProduits($manager);
        $this->loadEtats($manager);
        $this->loadProduits($manager);
        $this->loadUsers($manager);
    }


    public function loadUsers(ObjectManager $manager)
    {
        echo " \n\nles utilisateurs : \n";

        $admin = new User();
        $password = $this->passwordEncoder->encodePassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN'])
            ->setUsername('admin')->setEmail('admin@example.com')->setIsActive('1');
        $manager->persist($admin);
        echo $admin."\n";

        $client = new User();
        $password = $this->passwordEncoder->encodePassword($client, 'client');
        $client->setPassword($password);
        $client->setRoles(['ROLE_CLIENT'])->setUsername('client')
            ->setEmail('client@example.com')->setIsActive('1');
        $manager->persist($client);
        echo $client."\n";

        $client2 = new User();
        $password = $this->passwordEncoder->encodePassword($client, 'client2');
        $client2->setPassword($password);
        $client2->setRoles(['ROLE_CLIENT'])->setUsername('client2')
            ->setEmail('client2@example.com')->setIsActive('1');
        $manager->persist($client2);
        echo $client2."\n";

        $manager->flush();
    }

    private function loadTypeProduits(ObjectManager $manager)
    {
        echo " \n\nles type de produits : \n";

        $typesProduits = [
            ['id' => 1,'libelle' => 'Fourniture de bureau'],
            ['id' => 2,'libelle' => 'Mobilier'],
            ['id' => 3,'libelle' => 'Mobilier Jardin'],
            ['id' => 4,'libelle' => 'Arrosage'],
            ['id' => 5,'libelle' => 'Outils'],
            ['id' => 6,'libelle' => 'Divers']
        ];
        foreach ($typesProduits as $type)
        {
            $type_new = new TypeProduit();
            $type_new->setLibelle($type['libelle']);
            echo $type_new."\n";
            $manager->persist($type_new);
            $manager->flush();
        }
    }

    private function loadEtats(ObjectManager $manager)
    {
        echo " \n\nles etats des produits : \n";

        $etats = [
            ['id' => 1,'nom' => 'en attente'],
            ['id' => 2,'nom' => 'expedie'],
            ['id' => 3,'nom' => 'en attente'],
            ['id' => 4,'nom' => 'expedie'],
            ['id' => 5,'nom' => 'en attente'],
            ['id' => 6,'nom' => 'expedie']
        ];
        foreach ($etats as $etat)
        {
            $etat_new = new Etat();
            $etat_new->setNom($etat['nom']);
            echo $etat_new."\n";
            $manager->persist($etat_new);
            $manager->flush();
        }
    }

    private function loadProduits(ObjectManager $manager)
    {
        echo " \n\nles produits : \n";

        $produits = [
            [ 'nom' => 'Enveloppes (50p)', 'prix' => '2', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Fourniture de bureau', 'photo' => null],
            [ 'nom' => 'Stylo noir', 'prix' => '1', 'stock' => '13', 'disponible' => false, 'typeProduit'  => 'Fourniture de bureau', 'photo' => 'stylo.jpeg'],
            [ 'nom' => 'Boite de rangement', 'prix' => '3', 'stock' => '12', 'disponible' => true, 'typeProduit'  => 'Fourniture de bureau', 'photo' => 'boites.jpeg'],
            [ 'nom' => 'Chaise', 'prix' => '40', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier', 'photo' => 'chaise.jpeg'],
            [ 'nom' => 'Tables', 'prix' => '200', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier', 'photo' => 'table.jpeg'],
            [ 'nom' => 'Salon de Jardin alu', 'prix' => '149', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'salonJardin2.jpg'],
            [ 'nom' => 'Table+6 fauteuilles de Jardin', 'prix' => '790', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'tableFauteuilsJardin1.jpg'],
            [ 'nom' => 'Set Table + 4 bancs', 'prix' => '229', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Mobilier Jardin', 'photo' => 'setTableChaises.jpg'],
            [ 'nom' => 'arrosoir bleu', 'prix' => '13.50', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'arrosoir1.jpg'],
            [ 'nom' => 'arrosoir griotte', 'prix' => '9.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'arrosoir2.jpg'],
            [ 'nom' => 'tuyau arrosage', 'prix' => '31.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Arrosage', 'photo' => 'tuyauArrosage1.jpg'],
            [ 'nom' => 'tournevis', 'prix' => '23.90', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'lotTourneVis.jpg'],
            [ 'nom' => 'marteau menuisier', 'prix' => '7.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'marteau.jpg'],
            [ 'nom' => 'pince multiprise', 'prix' => '21.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'pinceMultiprise.jpg'],
            [ 'nom' => 'perceuse', 'prix' => '149.80', 'stock' => '3', 'disponible' => true, 'typeProduit'  => 'Outils', 'photo' => 'perceuse.jpg'],
        ];
        foreach ($produits as $produit)
        {
            $new_produit = new Produit();
            $new_produit->setNom($produit['nom']);
            $new_produit->setPrix($produit['prix']);
            $new_produit->setPhoto($produit['photo']);
            $new_produit->setStock($produit['stock']);
            if($produit['stock'] <10)
                $new_produit->setDateLancement(\DateTime::createFromFormat('Y-m-d','2020-10-19'));
            else
                $new_produit->setDateLancement(Null);
            $new_produit->setDisponible($produit['disponible']);
            $type_produit = $manager->getRepository(TypeProduit::class)->findOneBy(["libelle"  =>  $produit['typeProduit']] );
            if($type_produit != null)
                $new_produit->setTypeProduit($type_produit);
            echo $new_produit."\n";
            $manager->persist($new_produit);
            $manager->flush();
        }
    }
}
