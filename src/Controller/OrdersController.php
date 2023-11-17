<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class OrdersController extends AbstractController {

    /**
     * @Route("/orders", name="app_orders")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        /*    
         * Убрать комментарии, если хотим сгенерировать еще заказы
           $modelManager = new \App\Entity\Manager();
          $em = $this->getDoctrine()->getManager();
 

          $nameArray = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"];
          $rndIndex = rand(0, 9);
          $firstName = $nameArray[$rndIndex];
          $rndIndex = rand(0, 9);
          $lastName = $nameArray[$rndIndex];
              $modelManager->setFirstName($firstName);
          $modelManager->setLastName($lastName);
        

          $em->persist($modelManager);
          $em->flush();
          

          for ($x = 0; $x < 5; $x++) {
          $modelOrder = new \App\Entity\Order();
          $rndIndex = rand(0, 9);
          $name = $nameArray[$rndIndex];
          $rndIndex = rand(0, 9);
          $name .= $nameArray[$rndIndex];
          $modelOrder->setName($name);
          $modelOrder->setManagerId($modelManager->getId());
          $em->persist($modelOrder);
          $em->flush();
          }


         */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select(['o.id,o.name,m.firstName,m.lastName'])
                ->from(\App\Entity\Order::class, 'o')
                ->leftJoin(\App\Entity\Manager::class, 'm', 'WITH', 'm.id = o.manager_id')
                ->addOrderBy('o.id', 'DESC');

        $pagination = $paginator->paginate(
                $qb, /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                10 /* limit per page */
        );

        return $this->render('orders/index.html.twig', [
                    'controller_name' => 'OrdersController',
                    "pagination" => $pagination
        ]);
    }
    
           
}
