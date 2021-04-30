<?php

namespace App\Service;

use Symfony\Component\Ldap\Ldap;


class MyLdap
{
    public function __construct()
    {

    }//construct

    public function findUser(
        $ldap_host,
        $ldap_port,
        $ldap_dn,
        $ldap_dom,
        $ldap_user,
        $ldap_password,
        $ldap_filtre
    ) {
        $tab = [];

        // var_dump($ldap_host);
        // var_dump($ldap_port);
        // var_dump($ldap_dn);
        // var_dump($ldap_dom);
        // var_dump($ldap_user);
        // var_dump($ldap_password);
        // var_dump($ldap_filtre);

        try {
            $ldap = Ldap::create('ext_ldap', array(
                'host' => $ldap_host,
                // 'encryption' => 'ssl',
                'encryption' => 'none',
            ));
            $ldap->bind($ldap_user . $ldap_dom, $ldap_password);
                // $ldap->bind($email . $ldap_dom, 'Marc123');
            // $ldap->bind('Marc@koufide.com', 'Marc123');
                // #$ldap->bind('Administrateur@koufide.com', 'Admin123');
                    //# $ldap->bind('CN=Administrateur,CN=Users,DC=koufide,DC=com', 'Admin123');
            $query = $ldap->query(
                $ldap_dn,
                    // '(&(objectCategory=person)(objectClass=user)(! (userAccountControl:1.2.840.113556.1.4.803:=2)))'
                $ldap_filtre
            );

            // $results = $query->execute();
            $results = $query->execute()->toArray();
            // dump($results);
            $entry = $results[0]->getAttributes();
            // dump($entry);
            // exit("<br/>\n--------quitter-----");
            // foreach ($results as $entry) {
            // dump($entry);
            // }
            $tab = [
                'login' => $this->getSamAccountName($entry), 'pass' => 'password', 'description' => $this->getDescription($entry), 'displayname' => $this->getDisplayName($entry), 'customname' => $this->getCustomName($entry), 'mail' => $this->getuserPrincipalName($entry), 'nomprenom' => $this->getName($entry), 'name' => $this->getName($entry), 'accountexpires' => $this->getAccountExpires($entry), 'lastlogontimestamp' => $this->getlastLogonTimestamp($entry), 'lastlogon' => $this->getlastLogon($entry), 'telephone' => $this->getTelephoneNumber($entry), 'employeeid' => $this->getEmployeeId($entry), 'samaccountname' => $this->getSamAccountName($entry), 'distinguishedname' => $this->getDistinguishedName($entry), 'manager' => $this->getManager($entry)
            ];

            // exit("<br/>\n--------------)");

            // $login = $this->getSamAccountName($entry);
            // dump($login);




        } catch (\Throwable $th) {
            // dump($th);
            // exit("<br/>\n--------------)");
            // $tab['erreur'] = $th->getMessage();
            $tab['erreur'] = 'UTILISATEUR INTROUVABLE =>' . $th->getMessage();
        }


        return $tab;

    }//findUser


    public function getMail(array $data)
    {
        $res = null;
        if (array_key_exists('mail', $data)) {
            $res = $data["mail"][0];
            //print"<br/>\n ---res: $res";  
        }
        return $res;
    }//getMail

    public function getDistinguishedName(array $data)
    {
        $res = null;
        if (array_key_exists('distinguishedName', $data)) {
            $res = $data["distinguishedName"][0];
            //print"<br/>\n ---res: $res";  
        }
        return $res;
    }//getDistinguishedName


    public function getDescription(array $data)
    {
        $res = null;
        if (array_key_exists('description', $data)) {
            $res = $data["description"][0];
            //print"<br/>\n ---res: $res";  
        }
        return $res;
    }//getDescription

    public function getuserPrincipalName(array $data)
    {
        $res = null;
        if (array_key_exists('userPrincipalName', $data)) {
            $res = $data["userPrincipalName"][0];
            //print"<br/>\n ---res: $res";  
        }
        return $res;
    }//getuserPrincipalName

    public function getlastLogon(array $data)
    {
        $res = null;
        if (array_key_exists('lastLogon', $data)) {
            $res = $data["lastLogon"][0];
            //print"<br/>\n ---res: $res";
            $res = date("d-m-Y H:i:s", ($res / 10000000) - 11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getlastLogon


    public function getLogonHours(array $data)
    {
        $res = null;
        if (array_key_exists('logonhours', $data)) {
            $res = $data["logonhours"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getLogonHours

    public function getAccountExpires(array $data)
    {
        $res = null;
        // if (array_key_exists('accountExpires', $data)) {
        $res = $data["accountExpires"][0];

        $AD2Unix = ((1970 - 1601) * 365 - 3 + round((1970 - 1601) / 4)) * 86400;

        $secsAfterADepoch = $res / 100000000;
        $unix_ts = intval($secsAfterADepoch - $AD2Unix);

    
    
            //print"<br/>\n ---res: $res";
//            $res= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
        $res = date("d-m-Y H:i:s", $unix_ts);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        // }
        //var_dump($res);
        //exit("\n<br/>---------quitter-----getAccountExpires--");
        return $res;
    }//getAccountExpires


    public function getlastLogonTimestamp(array $data)
    {
        $res = null;
        if (array_key_exists('lastLogonTimestamp', $data)) {
            $res = $data["lastLogonTimestamp"][0];
            //print"<br/>\n ---res: $res";
            $res = date("d-m-Y H:i:s", ($res / 10000000) - 11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getlastLogonTimestamp

    public function getTelephoneNumber(array $data)
    {
        //echo "<pre>";
        //print_r($data);
        $res = '0';
        if (array_key_exists('telephoneNumber', $data)) {
            $res = $data["telephoneNumber"][0];
            //print"<br/>\n ---res: $res";
            //$res='2';
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        if ($res == 0) {
            if (array_key_exists('mobile', $data)) {
                $res = $data["mobile"][0];
            }
        }
        //var_dump($res);
        //exit($res);
        return $res;
    }//getTelephoneNumber

    public function getDisplayName(array $data)
    {
        $res = null;
        if (array_key_exists('displayName', $data)) {
            $res = $data["displayName"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        } elseif (array_key_exists('displayname', $data)) {
            $res = $data["displayname"][0];
        }
        return $res;
    }//getDisplayName

    public function getMemberOf(array $data)
    {
        $res = array();
        if (array_key_exists('memberof', $data)) {
            $memberof_count = $data["memberof"]["count"];
            //print"<br/>\n ---memberof_count: $memberof_count";
            $memberof = $data["memberof"][0];
            for ($j = 0; $j < $memberof_count; $j++) {
                $memberof = $data["memberof"][$j];
                //print"<br/>\n ------memberof: $memberof";
                $res[] = $memberof;
            }
        }
        return $res;
    }//getMemberof

    public function getEmployeeId(array $data)
    {
        $res = 0;
        if (array_key_exists('employeeID', $data)) {
            $res = $data["employeeID"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        } else
            if (array_key_exists('employeeid', $data)) {
            $res = $data["employeeid"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getEmployeeid

    public function getSamAccountName(array $data)
    {
        // dump($data['sAMAccountName']);
        $res = null;
        // if (array_key_exists('samaccountname', $data)) {
        $res = $data["sAMAccountName"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
            // }
        // dump($res);
        // exit("<br/>\n--quitter------");
        return $res;
    }//getSamaccountname

    public function getManager(array $data)
    {
        $res = null;
        if (array_key_exists('manager', $data)) {
            $res = $data["manager"][0];
            $res = str_replace('CN=', '', explode(',', $res, 2)[0]);
            //var_dump($res);
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
            //exit("\n<br/>---------quitter-----getManager--");
        }
        return $res;
    }//getManager


    public function getCustomName(array $data)
    {
        $res = null;
        if (array_key_exists('cn', $data)) {
            $res = $data["cn"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getCustomName

    public function getName(array $data)
    {
        $res = null;
        if (array_key_exists('name', $data)) {
            $res = $data["name"][0];
            //print"<br/>\n ---res: $res";
            //$date_lastLogon= date("d-m-Y H:i:s", ($res/10000000)-11644473600);
            //print"<br/>\n ---xxxx date_lastLogon: $date_lastLogon";
        }
        return $res;
    }//getName





}//class 