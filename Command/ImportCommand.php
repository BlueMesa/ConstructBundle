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

use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManager;
use Bluemesa\Bundle\ConstructBundle\Entity\Construct;
use Bluemesa\Bundle\ConstructBundle\Entity\ConstructTag;
use Bluemesa\Bundle\ConstructBundle\Entity\Gateway;
use Bluemesa\Bundle\ConstructBundle\Entity\RestrictionLigation;
use Bluemesa\Bundle\ConstructBundle\Entity\TopoTa;
use Bluemesa\Bundle\ConstructBundle\Entity\VectorInsert;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;


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
            ->addArgument(
                'user',
                InputArgument::REQUIRED,
                'User who owns the constructs'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $excelFile = $input->getArgument('excel');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelperSet()->get('question');
        $this->container = $this->getApplication()->getKernel()->getContainer();

        /** @var EntityManager $dm */
        $dm = $this->container->get('doctrine')->getManager();
        /** @var SecureObjectManager $om */
        $om = $this->container->get('bluemesa.core.doctrine.registry')->getManagerForClass(Construct::class);
        /** @var \PHPExcel $excel */
        $excel = $this->container->get('phpexcel')->createPHPExcelObject($excelFile);
        /** @var ObjectManager $tm */
        $tm = $this->container->get('bluemesa.core.doctrine.registry')->getManagerForClass(ConstructTag::class);
        $tr = $tm->getRepository(ConstructTag::class);


        $om->disableAutoAcl();

        $sheet = $excel->setActiveSheetIndex(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $constructsWithId = new ArrayCollection();
        $constructsWithoutId = new ArrayCollection();
        $tags = array();

        $output->writeln("Reading file " . $excelFile . "...");
        $progress = new ProgressBar($output, $highestRow - 1);
        $progress->start();

        for ($row = 2; $row <= $highestRow; $row++) {
            // Basic construct information
            $data = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $construct = new Construct();
            $construct->setId($this->readInteger($data[0][0]));
            $construct->setType($this->readString($data[0][1], 'plasmid'));
            $construct->setName($this->readString($data[0][2]));
            $construct->setResistances($this->readArray($data[0][3]));
            $construct->setVendor($this->readString($data[0][5]));
            $construct->setNotes($this->readString($data[0][7]));

            // Tags
            $tagValues = explode(',', $data[0][6]);
            foreach ($tagValues as $tagValue) {
                $tagValue = trim($tagValue);
                if (! empty($tagValue)) {
                    if (isset($tags[$tagValue])) {
                        $tag = $tags[$tagValue];
                    } else {
                        $tag = $tr->findOneByName($tagValue);
                        if (null === $tag) {
                            $tag = new ConstructTag();
                            $tag->setName($tagValue);
                        }
                        $tags[$tagValue] = $tag;
                    }
                    $construct->addTag($tag);
                }
            }

            // Cloning methods
            switch(trim($data[0][8])) {
                case 'restriction_ligation':
                    $method = new RestrictionLigation();
                    $method->setVectorUpstreamSite($this->readString($data[0][14]));
                    $method->setVectorDownstreamSite($this->readString($data[0][15]));
                    $method->setInsertUpstreamSite($this->readString($data[0][16]));
                    $method->setInsertDownstreamSite($this->readString($data[0][17]));
                    break;
                case 'gateway':
                    $method = new Gateway();
                    $method->setVector($this->readString($data[0][18]));
                    $method->setVectorSize($this->readFloat($data[0][19]));
                    $method->setDestinationVector($this->readString($data[0][20]));
                    $method->setDestinationVectorSize($this->readFloat($data[0][21]));
                    break;
                case 'topo_ta':
                    $method = new TopoTa();
                    $method->setBlunt($this->readBoolean($data[0][22]));
                    break;
                default:
                    $method = null;
            }

            if ($method instanceof VectorInsert) {
                if (! $method instanceof Gateway) {
                    $method->setVector($this->readString($data[0][9]));
                    $method->setVectorSize($this->readFloat($data[0][10]));
                }
                $method->setInsert($this->readString($data[0][11]));
                $method->setInsertSize($this->readFloat($data[0][12]));
                $method->setInsertOrientation($this->readOrientation($data[0][13]));
            }
            $construct->setMethod($method);

            if ($construct->getId() !== null) {
                $constructsWithId->add($construct);
            } else {
                $constructsWithoutId->add($construct);
            }
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(" <info>Done</info>");

        $output->writeln("Writing entries to database");
        $progress = new ProgressBar($output, count($tags) + $constructsWithId->count() + $constructsWithoutId->count());
        $dm->getConnection()->beginTransaction();

        foreach ($tags as $tag) {
            $tm->persist($tag);
            $progress->advance();
        }
        $tm->flush();

        $metadata = $dm->getClassMetadata(Construct::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach ($constructsWithId as $construct) {
            /** @var Construct $construct */
            $om->persist($construct);
            if (null !== $construct->getMethod()) {
                $om->persist($construct->getMethod());
            }
            $progress->advance();
        }
        $om->flush();

        $metadata = $dm->getClassMetadata(Construct::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);
        $metadata->setIdGenerator(new IdentityGenerator());

        foreach ($constructsWithoutId as $construct) {
            /** @var Construct $construct */
            $om->persist($construct);
            if (null !== $construct->getMethod()) {
                $om->persist($construct->getMethod());
            }
            $progress->advance();
        }
        $om->flush();
        $progress->finish();
        $output->writeln(" <info>Done</info>");

        $output->write("Creating ACLs... ");
        try {
            $user = $this->container->get('user_provider')->loadUserByUsername($input->getArgument('user'));
        } catch (UsernameNotFoundException $e) {
            $user = null;
        }

        if ($user instanceof UserInterface) {
            $om->createACL($constructsWithId, $user);
            $om->createACL($constructsWithoutId, $user);
        }
        $output->writeln("<info>Done</info>");

        $message = 'Stocks and vials have been created. Commit?';
        $question = new ConfirmationQuestion($message, false);
        if ($questionHelper->ask($input, $output, $question)) {
            $dm->getConnection()->commit();
            $output->writeln("<info>Import finished!</info>");
            $message = "" . ($constructsWithId->count() + $constructsWithoutId->count()) .
                " constructs were added to the database.";
            $output->writeln($message);
        } else {
            $dm->getConnection()->rollback();
            $dm->getConnection()->close();
            $output->writeln("<comment>Import cancelled!</comment>");
        }
    }

    private function readString($string, $default = null) {
        $string = trim($string);
        return (! empty($string)) ? $string : $default;
    }

    private function readInteger($string, $default = null) {
        $string = trim($string);
        return (! empty($string)) ? intval($string) : $default;
    }

    private function readFloat($string, $default = null) {
        $string = trim(str_replace(",", ".", $string));
        return (! empty($string)) ? floatval($string) : $default;
    }

    private function readBoolean($string, $default = null) {
        $string = trim($string);
        return (! empty($string)) ? (strtolower($string) == 'true') : $default;
    }

    private function readOrientation($string, $default = null) {
        $string = trim($string);
        return (! empty($string)) ? ((strtolower($string) == 'sense')||($string == '+')) : $default;
    }

    private function readArray($string, $default = null) {
        $string = trim($string);
        return (! empty($string)) ? explode(',', preg_replace('/\s+/', '', $string)) : null;
    }
}
