<?php

namespace App\Command;

use App\Entity\CallDetails;
use App\Repository\CallDetailsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AnalyseDataCommand
 * @package App\Command
 */
class AnalyseDataCommand extends Command
{
    protected static $defaultName = 'analyse:data';

    /** @var CallDetailsRepository */
    private CallDetailsRepository $callDetailsRepository;

    /**
     * AnalyseDataCommand constructor.
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->callDetailsRepository = $doctrine->getRepository(CallDetails::class);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Script to analyse data in db');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Request 1 => Get Total Time Calls');
        $totalTimeCalls = $this->callDetailsRepository->getTotalTimeCalls('2012-02-15');

        if (is_array($totalTimeCalls) && key_exists('totalTime', $totalTimeCalls)) {
            $io->success('Total Time calls is ' . $totalTimeCalls['totalTime']);
        } else {
            $io->error('No Total Time calls found');
        }

        $io->info('Request 2 => Get Total SMS Sent');
        $totalSmsSent = $this->callDetailsRepository->getTotalSmsSent();

        if (is_array($totalSmsSent) && key_exists('totalSms', $totalSmsSent)) {
            $io->success('Total SMS sent is ' . $totalSmsSent['totalSms']);
        } else {
            $io->error('No Total SMS sent found');
        }

        $io->info('Request 3 => Get Top Ten Billed Volume');
        $topTenBilled = $this->callDetailsRepository->getTopTenBilledVolume();

        if (is_array($topTenBilled) && count($topTenBilled) > 0) {
            $top = 1;
            foreach ($topTenBilled as $data) {
                $io->success('TOP '. $top .' : Billed (' . $data['totalBilledVolume'] . ') & Id Subscriber (' . $data['idSubscriber'] . ')');
                $top++;
            }
        } else {
            $io->error('No Top Ten BilledVolume found');
        }

        $io->success('Script to analyse data in db FINISHED !');

        return 1;
    }
}