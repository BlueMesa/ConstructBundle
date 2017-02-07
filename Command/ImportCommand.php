<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bluemesa\Bundle\ConstructBundle\Command;

use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Bluemesa\Bundle\ConstructBundle\Entity\Construct;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * ImportCommand
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ImportCommand extends Command
{
    private $container;

    protected function configure()
    {
        $this
            ->setName('labdb:constructs:import')
            ->setDescription('Import stocks from spreadsheet')
            ->addArgument(
                'excel',
                InputArgument::REQUIRED,
                'Spreadsheet file to import from'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $excelFile = $input->getArgument('excel');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelperSet()->get('question');
        $this->container = $this->getApplication()->getKernel()->getContainer();

        /** @var ObjectManager $dm */
        $dm = $this->container->get('doctrine')->getManager();
        /** @var SecureObjectManagerInterface $om */
        $om = $this->container->get('bluemesa.core.doctrine.registry')->getManagerForClass('Bluemesa\Bundle\ConstructBundle\Entity\Construct');
        /** @var \PHPExcel $excel */
        $excel = $this->container->get('phpexcel')->createPHPExcelObject($excelFile);

        $om->disableAutoAcl();

        $sheet = $excel->setActiveSheetIndex(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();


        for ($row = 2; $row <= $highestRow; $row++){
            $data = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $construct = new Construct();
            $construct->setId($data[0][0]);
            $construct->setType($data[0][1] !== null ? $data[1] : 'plasmid');
            $construct->setName($data[0][2]);
            $construct->setResistances($data[0][19] !== null ?
                explode(',', preg_replace('/\s+/', '', $data[0][19])) : null);
            dump($construct);
        }


        $om->enableAutoAcl();


        /**
        $stocks = array();
        $stock_register = array();
        $vials = array();
        
        if ($file) {
            while ($data = fgetcsv($file,0,"\t")) {
                $trim = " \t\n\r\0\x0B\"";
                $owner_name = trim($data[0],$trim);
                $stock_name = trim($data[1],$trim);
                $stock_genotype = trim($data[2],$trim);
                $creator_name = trim($data[4],$trim);
                $stock_notes = trim($data[5],$trim);
                $stock_vendor = trim($data[6],$trim);
                $stock_vendor_id = trim($data[7],$trim);
                $stock_info_url = str_replace(" ","",trim($data[8],$trim));
                $stock_verified = trim($data[9],$trim) == "yes" ? true : false;
                $stock_vials_size = trim($data[10],$trim);
                $stock_vials_size = $stock_vials_size == "" ? 'medium' : $stock_vials_size;
                $stock_vials_number = (integer) trim($data[11],$trim);
                $stock_vials_number = $stock_vials_number <= 0 ? 1 : $stock_vials_number;
                $stock_vials_food = trim($data[12],$trim);
                $stock_vials_food = $stock_vials_food == "" ? 'standard' : $stock_vials_food;
                
                $test = $om->getRepository('Bluemesa\Bundle\FliesBundle\Entity\Stock')->findOneByName($stock_name);
                
                if ((!in_array($stock_name, $stock_register))&&($creator_name == "")&&(null === $test)) {
                    
                    if (($stock_vendor != "")&&($stock_vendor_id != "")) {
                        $output->write("Querying FlyBase for " . $stock_name . ": ");
                        $stock_data = $this->getStockData($stock_vendor, $stock_vendor_id);
                        if (count($stock_data) == 1) {
                            $stock_genotype = $stock_data[0]['stock_genotype'];
                            $stock_info_url = $stock_data[0]['stock_link'];
                            $output->writeln("success");
                        } elseif (count($stock_data) != 1) {
                            $output->writeln("failed");
                        }
                    }
                    
                    $stock = new Stock();
                    $stock->setName($stock_name);
                    $stock->setGenotype($stock_genotype);
                    $stock->setNotes($stock_notes);
                    $stock->setVendor($stock_vendor);
                    $stock->setVendorId($stock_vendor_id);
                    $stock->setInfoURL($stock_info_url);
                    $stock->setVerified($stock_verified);
                    
                    for ($i = 0; $i < $stock_vials_number - 1; $i++) {
                        $vial = new StockVial();
                        $stock->addVial($vial);
                    }
                    $stock_vials = $stock->getVials();
                    foreach ($stock_vials as $vial) {
                        $vial->setSize($stock_vials_size);
                        $vial->setFood($stock_vials_food);
                    }
                    
                    $stock_register[] = $stock_name;
                    $stocks[$owner_name][$stock_name] = $stock;
                } else {
                    $vials[$owner_name][$stock_name]['size'] = $stock_vials_size;
                    $vials[$owner_name][$stock_name]['number'] = $stock_vials_number;
                    $vials[$owner_name][$stock_name]['food'] = $stock_vials_food;
                }
            }
        }

        $dm->getConnection()->beginTransaction();
        
        foreach ($stocks as $user_name => $user_stocks) {
            
            try {
                $user = $this->container->get('user_provider')->loadUserByUsername($user_name);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }
            
            if ($user instanceof UserInterface) {
                $output->writeln("Adding stocks for user " . $user_name . ":");
                $userStocks = new ArrayCollection();
                $userVials = new ArrayCollection();
                foreach ($user_stocks as $stock_name => $stock) {
                    $om->persist($stock);
                    $userStocks->add($stock);
                    $userVials->add($stock->getVials());
                    $output->write(".");
                    fprintf($logfile,"%s\n",$stock->getName());
                }
                $om->flush();
                $output->writeln("");
                $output->write("Creating ACLs...");
                $om->createACL($userStocks, $user);
                $output->writeln(" done");
            } else {
                $output->writeln("<error>User " . $user_name . " does not exits. Skipping!</error>");
            }
        }
        
        $message = 'Stocks and vials have been created. Commit?';
        $question = new Question(sprintf('<question>' . $message . '</question>', 'yes'));
        if ($questionHelper->ask($input, $output, $question) == 'yes') {
            $dm->getConnection()->commit();
        } else {
            $dm->getConnection()->rollback();
            $dm->getConnection()->close();
        }

        **/
    }
}
