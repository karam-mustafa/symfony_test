<?php

namespace App\Command;

use App\Entity\DelayedOrders;
use App\Repository\DelayedOrdersRepository;
use App\Repository\OrdersRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(name: 'verify:delayed:order')]
class VerifyDelayedOrders extends Command
{
    protected static $defaultName = 'verify:delayed:order';
    private OrdersRepository $ordersRepository;
    private DelayedOrdersRepository $delayedOrdersRepository;
    private ContainerInterface $container;
    private $em;

    /**
     * @param OrdersRepository $ordersRepository
     * @param string|null $name
     */
    public function __construct(
        OrdersRepository        $ordersRepository,
        DelayedOrdersRepository $delayedOrdersRepository,
        ContainerInterface      $container,
        string                  $name = null
    )
    {
        parent::__construct($name);
        $this->ordersRepository = $ordersRepository;
        $this->delayedOrdersRepository = $delayedOrdersRepository;
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setDescription('Get all orders that current time is greater than it ETD and register them in delayed orders table');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->em = $this->container->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Processing the data');

        $ids = $this->delayedOrdersRepository->getIds();

        $data = $this->ordersRepository->findAllDataGreaterThanCurrentTime($ids ?? []);

        foreach ($data as $item) {
            $time = new \DateTime();
            $this->delayedOrdersRepository->fillObject([
                'current_system_time' => $time->format('Y-m-d H:i:s'),
                'expected_time_delivery' => $item->getDeliveryTime(),
                'order_id' =>$item->getId(),
            ])->add();
            $this->io->info('Persist Item With Id: ' . $item->getId());
        }
        $this->io->info('All data were flushed');
        $this->io->title('Done');

        return Command::SUCCESS;
    }
}
