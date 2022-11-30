<?php

namespace App\Command;

use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear-picture-alone',
    description: 'Clear all the picture not linked to an picture since X days',
)]
class ClearPictureAloneCommand extends Command
{
    
        public function __construct(private readonly PictureRepository $repopicture, private readonly EntityManagerInterface $manager,  ?string $name=null)
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
                $io->note(sprintf('We are going to delete all the picture not linked from %s days', $from));
            }
    
            $pictures = $this->repopicture
            ->createQueryBuilder('a')
            ->where('a.advert is null')
            ->andWhere("a.createdAt <= :limit")
            ->setParameter('limit', (new \DateTimeImmutable())->sub(new \DateInterval(sprintf('P%dD', $from))))
            ->getQuery()
            ->getResult();
    
    
            $io->note(sprintf('%d pictures will be deleted', \count($pictures)));
            $progressBar = $io->createProgressBar(\count($pictures));
            foreach($pictures as $picture) {
                $this->manager->remove($picture);
                $progressBar->advance();
            }
    
            $this->manager->flush();
    
            $io->success('The rejected pictures are correctly deleted ');
    
            return Command::SUCCESS;
        }
}