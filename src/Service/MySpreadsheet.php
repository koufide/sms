<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use App\Service\MyReadFilter;

class MySpreadsheet
//implements IReadFilter
{
    public function __construct()
    { } //construct



    public function readExcelFileSMS($file)
    {
        //dump($file);
        // exit("<br/>\n-------------------quitter-------------");

        $inputFileType = IOFactory::identify($file);
        // var_dump($inputFileType);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load("$file");

        //info fichier
        $worksheetData = $reader->listWorksheetInfo($file);
        $worksheet_info = $worksheetData[0]; //premier classeur


        //--------------------------------------------------
        $worksheet = $spreadsheet->getSheet(0); //premier classeur


        $tab_info_entete = $this->readExcelFileEnteteSMS($worksheet, $worksheet_info);
        $highestRow = $tab_info_entete['totalRows'];

        $positionExcelBenef = 2;
        $caractereFin = '#';

        $tab_info_detail = $this->readExcelFileDetailSMS($worksheet, $positionExcelBenef, $highestRow, $caractereFin);
        // var_dump($tab_info_detail);

        $tab_info['entete'] = $tab_info_entete;
        $tab_info['detail'] = $tab_info_detail;

        //var_dump($tab_info);

        return $tab_info;
    } //readExcelFileSMS






    public function readExcelFile($file, $positionExcelBenef)
    {
        //dump($file);
        //exit("<br/>\n-------------------quitter-------------");

        $inputFileType = IOFactory::identify($file);
        // var_dump($inputFileType);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load("$file");

        //info fichier
        $worksheetData = $reader->listWorksheetInfo($file);
        $worksheet_info = $worksheetData[0]; //premier classeur

        // $worksheetName = $worksheet_info['worksheetName'];
        // $totalRows = $worksheet_info['totalRows'];
        // $totalColumns = $worksheet_info['totalColumns'];
        // $lastColumnLetter = $worksheet_info['lastColumnLetter'];

        //--------------------------------------------------
        #$worksheet = $spreadsheet->getActiveSheet();
        $worksheet = $spreadsheet->getSheet(0); //premier classeur


        $tab_info_entete = $this->readExcelFileEntete($worksheet, $worksheet_info);
        $highestRow = $tab_info_entete['totalRows'];

        //$positionBenef = '12';
        // $tab_info_detail = $this->readExcelFileDetail($worksheet, $positionBenef);
        $tab_info_detail = $this->readExcelFileDetail($worksheet, $positionExcelBenef, $highestRow);
        // var_dump($tab_info_detail);

        // $tab_info = array_merge($tab_info_entete, $tab_info_detail);
        $tab_info['entete'] = $tab_info_entete;
        $tab_info['detail'] = $tab_info_detail;


        //var_dump($tab_info);


        return $tab_info;
    } //readExcelFile



    private function readExcelFileEnteteSMS($worksheet, $worksheet_info)
    {
        // $dordre = $worksheet->getCell('C' . 4)->getValue();
        // $raisonSoci = $worksheet->getCell('C' . '5')->getValue();
        // $codeAPB = $worksheet->getCell('C' . 6)->getValue();
        // $cptADebiter = $worksheet->getCell('C' . '7')->getValue();
        // $libeVire = $worksheet->getCell('C' . '8')->getValue();


        $worksheetName = $worksheet_info['worksheetName'];
        $totalRows = $worksheet_info['totalRows'];
        $totalColumns = 3; //fixe
        $lastColumnLetter = 'C'; //fixe

        $sheetData = $worksheet->rangeToArray($lastColumnLetter . $totalRows . ':' . $lastColumnLetter . $totalRows, null, true, true, true);
        $mntVire = $sheetData[$totalRows][$lastColumnLetter];

        // var_dump($sheetData);
        $n = $totalRows;
        // $mntVire = $sheetData[$n][$lastColumnLetter];
        $totalRows = $n;

        // exit("<br/>\n------quitter-------");

        $tab_info = [
            // 'dordred' => $dordre,
            // 'raisonSoci' => $raisonSoci,
            // 'codeAPB' => $codeAPB,
            // 'cptADebiter' => $cptADebiter,
            // 'libeVire' => $libeVire,
            'worksheetName' => $worksheetName,
            'totalRows' => $totalRows,
            'totalColumns' => $totalColumns,
            'lastColumnLetter' => $lastColumnLetter,
            // 'mntVire' => $mntVire
        ];

        //var_dump($tab_info);
        return $tab_info;
    } //readExcelFileEnteteSMS


    private function readExcelFileEntete($worksheet, $worksheet_info)
    {
        $dordre = $worksheet->getCell('C' . 4)->getValue();
        $raisonSoci = $worksheet->getCell('C' . '5')->getValue();
        $codeAPB = $worksheet->getCell('C' . 6)->getValue();
        $cptADebiter = $worksheet->getCell('C' . '7')->getValue();
        $libeVire = $worksheet->getCell('C' . '8')->getValue();




        $worksheetName = $worksheet_info['worksheetName'];
        $totalRows = $worksheet_info['totalRows'];
        $totalColumns = 8; //fixe
        $lastColumnLetter = 'H'; //fixe

        $sheetData = $worksheet->rangeToArray($lastColumnLetter . $totalRows . ':' . $lastColumnLetter . $totalRows, null, true, true, true);
        $mntVire = $sheetData[$totalRows][$lastColumnLetter];

        // var_dump($sheetData);
        $n = $totalRows;
        // var_dump($totalRows);
        while ($mntVire == null) {
            $n = $n - 1;
            $sheetData = $worksheet->rangeToArray($lastColumnLetter . $n . ':' . $lastColumnLetter . $n, null, true, true, true);
            $mntVire = $sheetData[$n][$lastColumnLetter];
        }
        $totalRows = $n;

        // exit("<br/>\n------quitter-------");

        $tab_info = [
            'dordred' => $dordre,
            'raisonSoci' => $raisonSoci,
            'codeAPB' => $codeAPB,
            'cptADebiter' => $cptADebiter,
            'libeVire' => $libeVire,
            'worksheetName' => $worksheetName,
            'totalRows' => $totalRows,
            'totalColumns' => $totalColumns,
            'lastColumnLetter' => $lastColumnLetter,
            'mntVire' => $mntVire
        ];

        //var_dump($tab_info);
        return $tab_info;
    } //readExcelFileEntete


    private function readExcelFileDetailSMS($worksheet, $positionBenef, $highestRow, $caractereFin)
    {

        $highestColumn = 'C'; // e.g 'F'
        $highestColumn++;

        // var_dump($highestColumn);
        var_dump("$positionBenef : positionBenef");

        var_dump("$highestRow : avantDernLigne");

        // $avantDernLigne = $highestRow - 1;
        $avantDernLigne = $highestRow;

        // var_dump(" $avantDernLigne : avantDernLigne");

        $tab_info = array();

        for ($row = $positionBenef; $row <= $avantDernLigne; ++$row) {

            // print("<br/>\n row: $row");

            for ($col = 'A'; $col != $highestColumn; ++$col) {

                // print("<br/>\n col: $col");

                if ($col == 'A') {
                    if (trim($worksheet->getCell($col . $row)->getCalculatedValue()) == $caractereFin) {
                        break 2;
                    }
                    $numSeq = $worksheet->getCell($col . $row)->getCalculatedValue();
                }

                if ($col == 'B') {
                    if (trim($worksheet->getCell($col . $row)->getCalculatedValue()) == $caractereFin) {
                        break 2;
                    }
                    $nomBenef = $worksheet->getCell($col . $row)->getCalculatedValue();
                }

                if ($col == 'C') {
                    if (trim($worksheet->getCell($col . $row)->getCalculatedValue()) == $caractereFin) {
                        break 2;
                    }
                    $libeBnq = $worksheet->getCell($col . $row)->getCalculatedValue();
                }

                // if ($col == 'D') {
                //     $codeBnq = $worksheet->getCell($col . $row)->getCalculatedValue();
                // }
                // if ($col == 'E') {
                //     $codeGch = $worksheet->getCell($col . $row)->getCalculatedValue();
                // }
                // if ($col == 'F') {
                //     $compte = $worksheet->getCell($col . $row)->getCalculatedValue();
                // }
                // if ($col == 'G') {
                //     $clerib = $worksheet->getCell($col . $row)->getCalculatedValue();
                // }
                // if ($col == 'H') {
                //     $mnt = $worksheet->getCell($col . $row)->getCalculatedValue();
                // }
            } //for 2

            $row_vire = [
                'numSeq' => $numSeq,
                'nomBenef' => $nomBenef,
                'libeBnq' => $libeBnq,
                // 'codeBnq' => $codeBnq,
                //     'codeGch' => $codeGch,
                //     'compte' => $compte,
                //     'clerib' => $clerib,
                //     'mnt' => $mnt
            ];

            //var_dump($row_vire);

            $tab_info[] = $row_vire;
        } //for 1

        //var_dump($tab_info);
        return $tab_info;
    } //readExcelFileDetailSMS


    private function readExcelFileDetail($worksheet, $positionBenef, $highestRow)
    {

        $highestColumn = 'H'; // e.g 'F'
        $highestColumn++;
        // var_dump($highestColumn);

        $avantDernLigne = $highestRow - 1;

        $tab_info = array();

        for ($row = $positionBenef; $row <= $avantDernLigne; ++$row) {
            //print("<br/>\n row: $row");
            for ($col = 'A'; $col != $highestColumn; ++$col) {
                //print("<br/>\n col: $col");

                if ($col == 'A') {
                    $numSeq = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'B') {
                    $nomBenef = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'C') {
                    $libeBnq = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'D') {
                    $codeBnq = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'E') {
                    $codeGch = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'F') {
                    $compte = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'G') {
                    $clerib = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
                if ($col == 'H') {
                    $mnt = $worksheet->getCell($col . $row)->getCalculatedValue();
                }
            } //for 2
            $row_vire = [
                'numSeq' => $numSeq,
                'nomBenef' => $nomBenef,
                'libeBnq' => $libeBnq,
                'codeBnq' => $codeBnq,
                'codeGch' => $codeGch,
                'compte' => $compte,
                'clerib' => $clerib,
                'mnt' => $mnt
            ];

            //var_dump($row_vire);

            $tab_info[] = $row_vire;
        } //for 1

        //var_dump($tab_info);
        return $tab_info;
    } //readExcelFileDetailSMS
}
