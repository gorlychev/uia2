<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Output\OutputInterface;


/**
 *
 *     $ php bin/console app:courier --max-weigth=6  --weigths="1, 2, 1, 5, 1, 3, 5, 2, 5, 5"  
 *
 * @author Vitaly Gorlychev <vitaly@gorlychev.com>
 */
class UiaCourierCommand extends Command {

    protected static $defaultName = 'app:courier';
     

    /**
     * {@inheritdoc}
     */
    protected function configure(): void {
        $this
                ->setDescription('Find tasks for courier')
                ->addOption('max-weigth', null, InputOption::VALUE_REQUIRED, 'Максимальный вес')
                ->addOption('weigths', null, InputOption::VALUE_REQUIRED, 'Массив весов')
        ;
    }

    
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $maxWeight = $input->getOption('max-weigth');
        $weigthStringArray = $input->getOption('weigths');
        $weigthArray = $this->stringToArray($weigthStringArray);
        $weigthArrayClean = $this->removeExtraWeight($weigthArray, $maxWeight);
        $number = $this->findCombinations($weigthArrayClean, $maxWeight);
        echo "\nКоличество выездов: " . $number."\n";
  
        return Command::SUCCESS;
    }

    protected function stringToArray($source, $delimeter = ",") {
        $out = explode($delimeter, $source);
        return $out;
    }

    protected function removeExtraWeight($weigthArray, $maxWeight) {
        $out = array();
        foreach ($weigthArray as $weight) {
            if ($weight <= $maxWeight) {
                $out[] = $weight;
            }
        }
        return $out;
    }

    protected function reorderKeys($weights) {
        $out = array();
        arsort($weights);
        foreach ($weights as $item) {
            $out[] = $item;
        }
        return $out;
    }

    protected function findCombinations($weights, $maxWeight, $number = 0) {
        $weightOrdered = $this->reorderKeys($weights);
        $summa = 0;
        $k = 0;
        $maxNumber = 0;
        if ($maxNumber < $number) {
            $maxNumber = $number;
        }
        foreach ($weightOrdered as $i => $item) {
            $summa = $summa + $item;
            if ($summa > $maxWeight) {
                $summa = $summa - $item;
            } else {
                $k++;
                if ($k == 2) {
                    $number++;
                    $k = 0;
                    unset($weightOrdered[$i]);
                    return $this->findCombinations($weightOrdered, $maxWeight, $number);
                } elseif ($k == 1) {
                    unset($weightOrdered[0]);
                }
            }
        }
        if (!empty($weightOrdered)) {
            return $this->findCombinations($weightOrdered, $maxWeight, $number);
        }
        return $maxNumber;
    }
}
