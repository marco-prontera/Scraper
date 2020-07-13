<?php

namespace App\Command;

use App\Service\AdsScraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdsScraperCommand extends Command
{
    /**
     * @var AdsScraper
     */
    private $adsScraper;

    public function __construct(string $name = null, AdsScraper $adsScraper)
    {
        parent::__construct($name);
        $this->adsScraper = $adsScraper;
    }

    protected function configure()
    {
        $this
            ->setName('ads-scraper')
            ->setDescription('Check if given domain have ads.txt file with more than 10 lines.')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain name of website.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getArgument('domain');
        $result = $this->adsScraper->checkDomainHaveMoreThanTenLineOfAds($domain);
        $output->writeln($result);

        return Command::SUCCESS;
    }
}