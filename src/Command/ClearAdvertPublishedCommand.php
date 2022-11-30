<?php

namespace App\Command;


use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear-advert-published',
    description: 'Clear all the published adverts after X days',
)]
class ClearAdvertPublishedCommand extends Command
{

    public function __construct(private readonly AdvertRepository $repoAdvert, private readonly EntityManagerInterface $manager,  ?string $name=null)
    {
        parent::__construct($name);
    }
    protected function configure(): void
    {
        $this
            ->addArgument('from', InputArgument::REQUIRED, 'From how many days ?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');

        if ($from) {
            $io->note(sprintf('We are going to delete all the advert published from %s days', $from));
        }

        $adverts = $this->repoAdvert
        ->createQueryBuilder('a')
        ->where('a.state = :published')
        ->andWhere("a.publishedAt <= :limit")
        ->setParameter('published', 'published')
        ->setParameter('limit', (new \DateTimeImmutable())->sub(new \DateInterval(sprintf('P%dD', $from))))
        ->getQuery()
        ->getResult();


        $io->note(sprintf('%d published adverts will be deleted', \count($adverts)));
        $progressBar = $io->createProgressBar(\count($adverts));
        foreach($adverts as $advert) {
            $this->manager->remove($advert);
            $progressBar->advance();
        }

        $this->manager->flush();

        $io->success('The published adverts are correctly deleted ');

        return Command::SUCCESS;
    }
}