<?php

namespace App\Command;

use App\Entity\CallDetails;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class InjectDataCommand
 * @package App\Command
 */
class InjectDataCommand extends Command
{
    protected static $defaultName = 'inject:data';

    /** @var string */
    private string $csvFullPath;

    /** @var ObjectManager */
    private ObjectManager $manager;

    /** @var ValidatorInterface */
    private ValidatorInterface $validator;

    /**
     * InjectDataCommand constructor.
     * @param string $rootDir
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     */
    public function __construct(string $rootDir, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $this->csvFullPath = $rootDir . '/public/file/tickets_appels_201202.csv';
        $this->manager = $doctrine->getManager();
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Script to inject data CSV in db');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '1G');

        $io = new SymfonyStyle($input, $output);

        if (file_exists($this->csvFullPath)) {
            if(($handle = fopen($this->csvFullPath, "r")) !== FALSE)
            {
                $line = 0;
                while(($rows = fgetcsv($handle, 1000, ';', '"')) !== FALSE) {
                    $line++;
                    if ($line >= 4 && count($rows) === 8) {
                        if (
                            !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", DateTime::createFromFormat("d/m/Y", $rows[3])->format("Y-m-d"))
                            || !preg_match('#^([01][0-9])|(2[0-4])(:[0-5][0-9]){1,2}$#', $rows[4])
                        ) {
                            $io->error('Wrong data in line N°' . $line);
                            continue;
                        }
                        $callDetails = new CallDetails();
                        $callDetails->accountBilling = (int)$rows[0];
                        $callDetails->idBilling = (int)$rows[1];
                        $callDetails->idSubscriber = (int)$rows[2];

                        $callDetails->date = DateTime::createFromFormat("d/m/Y", $rows[3]);
                        $callDetails->time = DateTime::createFromFormat("H:i:s", $rows[4]);
                        $callDetails->actualVolume = $rows[5];
                        $callDetails->billedVolume = $rows[6];
                        $callDetails->type = utf8_encode($rows[7]);

                        $errors = $this->validator->validate($callDetails);

                        if (count($errors) > 0) {
                            $io->error('Wrong data in line N°' . $line);
                            continue;
                        }

                        $this->manager->persist($callDetails);
                        $io->info('Data line N°' . $line . ' correctly');
                    }

                    unset($callDetails, $errors, $rows);
                }

                $this->manager->flush();
            }
        }

        $io->success('Script to inject data CSV in db FINISHED !');

        return 1;
    }
}