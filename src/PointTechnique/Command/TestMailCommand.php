<?php
namespace PointTechnique\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheCodingMachine\Mail\SwiftMailTemplate;

class TestMailCommand extends Command
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailService;

    /**
     * @var SwiftMailTemplate
     */
    protected $mailTemplate;

    public function __construct(\Swift_Mailer $mailService, SwiftMailTemplate $mailTemplate)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->mailTemplate = $mailTemplate;
    }

    protected function configure()
    {
        $this
            ->setName('PointTechnique:testmail')
            ->setDescription('Send mail')
            ->addArgument(
                'toAddress',
                InputArgument::REQUIRED,
                'Send the mail to?'
            )
            ->addArgument(
                'message',
                InputArgument::OPTIONAL,
                'What do you want to say?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $toAddress = $input->getArgument('toAddress');
        if (!$toAddress) {
            return;
        }
        $data = [];
        $message = $input->getArgument('message');
        if ($message) {
            $data['message'] = $message;
        }
        $message = $this->mailTemplate->renderMail($data);
        $message->setTo($toAddress);
        $this->mailService->send($message);
        $output->writeln("Email send");
    }
}