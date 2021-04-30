<?php

namespace App\Repository;

use App\Entity\Abonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Orx;

/**
 * @method Abonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnement[]    findAll()
 * @method Abonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

    // /**
    //  * @return Abonnement[] Returns an array of Abonnement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Abonnement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // Get the total number of elements
    //  public function count() 
    //  {
    //      return $this
    //          ->createQueryBuilder('object')
    //          ->select("count(object.id)")
    //          ->getQuery()
    //          ->getSingleScalarResult();
    //  }


    public function nbre()
    {
        // exit("<br/>\n -------- quitter ");

        return $this->createQueryBuilder('a')
            ->select("count(a.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function activer()
    {
        // exit("<br/>\n -------- quitter ");
    //   return 
      $this->createQueryBuilder('')
        ->update('Abonnement', 'a')
        ->set('a.ACTIF', '?1')
        // ->set('u.last_login', '?')
         ->setParameter(1, 1)
    ;


    }


    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // dump($start);
        // dump($length);
        // dump($orders);
        //  dump($search);
        // dump($columns);
        // dump($otherConditions);

        // foreach($search as $key=>$recherche){
        //     dump($key);
        //     dump($recherche);
        // }

        //exit("<br/>\n-----quitter");   

        // Create Main Query
        $query = $this->createQueryBuilder('a');

        // Create Count Query
        $countQuery = $this->createQueryBuilder('a');
        $countQuery->select('COUNT(a.id)');

        // Create inner joins
        // $query
        //     ->join('a.department', 'department')
        //     ->join('department.region', 'region');

        // $countQuery
        //     ->join('a.department', 'department')
        //     ->join('department.region', 'region');

        // Other conditions than the ones sent by the Ajax call ?
        if ($otherConditions === null) {
            // No
            // However, add a "always true" condition to keep an uniform treatment in all cases
            $query->where("1=1");
            $countQuery->where("1=1");
        } else {
            // Add condition
            $query->where($otherConditions);
            $countQuery->where($otherConditions);
        }


        $orX = $query->expr()->orX();
        $aLike = array();


        // Fields Search
        foreach ($columns as $key => $column) {
            //  dump($key);
            //  dump($column);

            if ($column['search']['value'] != '') {
                //  dump($column['search']['value']);

                // $searchItem is what we are looking for
                $searchItem = $column['search']['value'];
                $searchQuery = null;

                // $column['name'] is the name of the column as sent by the JS
                switch ($column['name']) {
                    case 'phone': {
                            $searchQuery = 'a.PHONE LIKE \'%' . $searchItem . '%\'';
                            break;
                        }
                    case 'client': {
                            $searchQuery = 'a.CLIENT LIKE \'%' . $searchItem . '%\'';
                            break;
                        }
                    case 'compte': {
                            $searchQuery = 'a.COMPTE LIKE \'%' . $searchItem . '%\'';
                            break;
                        }
                    case 'agence': {
                            $searchQuery = 'a.AGENCE LIKE \'%' . $searchItem . '%\'';
                            break;
                        }
                    case 'actif': {
                            if ($searchItem == 'A')
                                $searchItem = '1';
                            else
                                $searchItem = '0';

                            $searchQuery = 'a.ACTIF LIKE \'%' . $searchItem . '%\'';
                            break;
                        }
                }

                if ($searchQuery !== null) {
                    $query->andWhere($searchQuery);
                    $countQuery->andWhere($searchQuery);
                }
            }

            //else {




            //-----------------------------------------------------
            // --------FAIRE UNE RECHERCHE SUR TOUTES LES COLONNES //perso koufide 16042019
            // dump($search['value']);
            // dump($column['name']);
            // exit("exit");

            if ($search['value'] != '') {
                //  dump($column['search']['value']);

                // $searchItem is what we are looking for
                $searchItem = $search['value'];

                //$searchQuery = 'a.'.strtoupper($column['name']).' LIKE \'%'.$searchItem.'%\'';

                $aLike[] = $query->expr()->like('a.' . strtoupper($column['name']), '\'%' . $searchItem . '%\'');
                $orX->add($query->expr()->like('a.' . strtoupper($column['name']), '\'%' . $searchItem . '%\''));
            }
            //}


        } //for

        if (count($aLike) > 0)
            $query->add('where', $orX);
        else unset($aLike);


        // exit("exit");
        //---------------------

        // Limit
        $query->setFirstResult($start)->setMaxResults($length);

        // Order
        foreach ($orders as $key => $order) {
            // $order['name'] is the name of the order column as sent by the JS
            if ($order['name'] != '') {
                $orderColumn = null;

                switch ($order['name']) {
                    case 'phone': {
                            $orderColumn = 'a.PHONE';
                            break;
                        }
                    case 'client': {
                            $orderColumn = 'a.CLIENT';
                            break;
                        }
                    case 'COMPTE': {
                            $orderColumn = 'a.COMPTE';
                            break;
                        }
                    case 'agence': {
                            $orderColumn = 'a.AGENCE';
                            break;
                        }
                }

                if ($orderColumn !== null) {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }

        // Execute
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results"         => $results,
            "countResult"    => $countResult
        );
    }




    public function getFilteredCount_v2(array $get)
    {

        /* Indexed column (used for fast and accurate table cardinality) */
        // $alias = 'u';

        /* DB table to use */
        $tableObjectName = 'AlcalisKiosqueBundle:User';

        /**
         *  Set to default
         */
        if (!isset($get['columns']) || empty($get['columns']))
            $get['columns'] = array('id');

        $aColumns = array();
        //    foreach($get['columns'] as $value) $aColumns[] = $alias .'.'. $value;
        foreach ($get['columns'] as $value) $aColumns[] =  $value;



        $qb = $this->createQueryBuilder('u');
        // $qb->innerJoin('u.souscriptions','s'); // <== abonne
        // $qb->innerJoin('s.service','se');


        //var_dump($statut);
        // if( isset($statut)  ){
        //      $qb->where('u.statut = :statut');
        //      $qb->setParameter('statut',$statut);

        // }

        //    if($array_serviceCode !== null && !empty($array_serviceCode)){

        //               $qb->andwhere( $qb->expr()->in('se.code',':array_serviceCode') );
        //               $qb->setParameter('array_serviceCode', $array_serviceCode);
        //     }

        $qb->select(' COUNT(DISTINCT u) ');

        // exit("quitter");

        /*
    * Filtering
    * NOTE this does not match the built-in DataTables filtering which does it
    * word by word on any field. It's possible to do here, but concerned about efficiency
    * on very large tables, and MySQL's regex functionality is very limited
    */
        $orX = $qb->expr()->orX();
        if (isset($get['sSearch']) && $get['sSearch'] != '') {
            $aLike = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if (isset($get['bSearchable_' . $i]) && $get['bSearchable_' . $i] == "true") {
                    $aLike[] = $qb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\'');
                    $orX->add($qb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\''));
                }
            }

            if (count($aLike) > 0)
                //   $qb->andWhere(new Expr\Orx($aLike));
                //   $orX->addMultiple($aLike);
                $qb->add('where', $orX);
            else unset($aLike);
        }







        /*
     * SQL queries
     * Get data to display
     */
        $query = $qb->getQuery();
        $aResultTotal = $query->getResult();

        return $aResultTotal[0][1];
    } //getFilteredCount_2



    public function ajaxTable_v2(array $get, $flag = false)
    {


        $tableObjectName = 'AlcalisKiosqueBundle:User';

        if (!isset($get['columns']) || empty($get['columns']))
            $get['columns'] = array('id');


        $aColumns = array();
        foreach ($get['columns'] as $value) $aColumns[] = $value;

        // $fct=new MesFonctions();
        // $roleCode=$fct->getRoleCodeAbonne();

        $qb = $this->createQueryBuilder('u');
        // $qb->innerJoin('u.userroles','ur');
        // $qb->innerJoin('ur.role','r');
        // $qb->innerJoin('u.souscriptions','s');
        //  $qb->innerJoin('s.service','se');


        // $qb->where('r.code = :roleCode');
        // $qb->setParameter('roleCode', $roleCode);

        // if($array_serviceCode !== null && !empty($array_serviceCode)){
        //     $qb->andwhere( $qb->expr()->in('se.code',':array_serviceCode') );
        //     $qb->setParameter('array_serviceCode', $array_serviceCode);
        // }

        //         if(isset($statut)){
        // //              $qb->andWhere('u.statut = :statut');
        //               $qb->andWhere('s.statut = :statut');
        //               $qb->setParameter('statut', $statut);
        //         }
        #->orderBy('p.codeService');
        $qb->select('distinct ' . str_replace(" , ", " ", implode(", ", $aColumns)));;


        //exit("<br> sdd ".count($rResult));


        if (isset($get['iDisplayStart']) && $get['iDisplayLength'] != '-1') {
            $qb->setFirstResult((int)$get['iDisplayStart'])
                ->setMaxResults((int)$get['iDisplayLength']);
        }

        /*
     * Ordering
     */
        if (isset($get['iSortCol_0'])) {
            for ($i = 0; $i < intval($get['iSortingCols']); $i++) {
                if ($get['bSortable_' . intval($get['iSortCol_' . $i])] == "true") {
                    $qb->orderBy($aColumns[(int)$get['iSortCol_' . $i]], $get['sSortDir_' . $i]);
                }
            }
        }

        /*
       * Filtering
       * NOTE this does not match the built-in DataTables filtering which does it
       * word by word on any field. It's possible to do here, but concerned about efficiency
       * on very large tables, and MySQL's regex functionality is very limited
       */

        $orX = $qb->expr()->orX();
        //    $globalSearch = new Orx();

        if (isset($get['sSearch']) && $get['sSearch'] != '') {
            $aLike = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if (isset($get['bSearchable_' . $i]) && $get['bSearchable_' . $i] == "true") {
                    $aLike[] = $qb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\'');
                    $orX->add($qb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\''));
                }
            }
            //   if(count($aLike) > 0) $qb->andWhere(new Expr\Orx($aLike));
            if (count($aLike) > 0)
                //   $orX->add($aLike)
                //    $orX->add($aLike)
                $qb->add('where', $orX)
                    //   $qb->expr()->orX($aLike)
                ;
            else unset($aLike);
        }

        /*
     * SQL queries
     * Get data to display
     */
        $query = $qb->getQuery();

        if ($flag)
            return $query;
        else
            return $query->getResult();
    } //ajaxTable_2








}
