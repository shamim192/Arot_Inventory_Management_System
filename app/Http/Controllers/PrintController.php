<?php

namespace App\Http\Controllers;

use Exception;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;

class PrintController
{
    public function sale($data)
    {
        //$connector = new FilePrintConnector("php://stdout");
        //$printer = new Printer($connector);

        // $connector = new NetworkPrintConnector(env('PRINTER_NAME'), 9100);
        // $printer = new Printer($connector);

        $profile = CapabilityProfile::load("simple");
        $connector = new WindowsPrintConnector(env('PRINTER_NAME'));
        $printer = new Printer($connector, $profile);
        
        try {
            /* Name of shop */
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(env('APP_NAME')."\n");
            $printer->selectPrintMode();
            $printer->text('Date: '.dateFormat($data->date)."\n");
            $printer->feed();
            
            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("SALES RECEIPT\n\n");
            $printer->setEmphasis(false);
            
            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CUSTOMER NAME: ".$data->customer->name."\n\n");
            
            $printer->setEmphasis(true);
            $printer->text(new item('', env('CURRENCY')));
            $printer->setEmphasis(false);
            foreach ($data->items as $item) {
                $printer->text(new item($item->product->name.'('.$item->unit->name.') x '.$item->quantity, $item->amount));
            }

            $printer->setEmphasis(true);
            $printer->text(new item('Subtotal', $data->items->sum('amount')));
            $printer->setEmphasis(false);
            $printer->feed();
            
            /* Vat and total */
            $printer->text(new item('Vat', $data->vat_amount));
            $printer->text(new item('Discount', $data->discount_amount));
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(new item('Total', $data->total_amount, true));
            $printer->selectPrintMode();
            
            /* Footer */
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for shopping at ".env('APP_NAME')."\n");
            $printer->feed(2);
            $printer->text(dateFormat(date('Y-m-d H:i:s'), 1) . "\n");
            
            /* Cut the receipt and open the cash drawer */
            $printer->cut();
            $printer->pulse();
            
        } catch (Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        } finally {
            $printer->close();
            return ['status' => true, 'error' => null];
        }
    }

    public function saleReturn($data)
    {
        //$connector = new FilePrintConnector("php://stdout");
        //$printer = new Printer($connector);

        // $connector = new NetworkPrintConnector(env('PRINTER_NAME'), 9100);
        // $printer = new Printer($connector);

        $profile = CapabilityProfile::load("simple");
        $connector = new WindowsPrintConnector(env('PRINTER_NAME'));
        $printer = new Printer($connector, $profile);
        
        try {
            /* Name of shop */
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(env('APP_NAME')."\n");
            $printer->selectPrintMode();
            $printer->text('Date: '.dateFormat($data->date)."\n");
            $printer->feed();
            
            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("SALE RETURN RECEIPT\n\n");
            $printer->setEmphasis(false);
            
            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("CUSTOMER NAME: ".$data->customer->name."\n\n");
            
            $printer->setEmphasis(true);
            $printer->text(new item('', env('CURRENCY')));
            $printer->setEmphasis(false);
            foreach ($data->items as $item) {
                $printer->text(new item($item->product->name.'('.$item->unit->name.') x '.$item->quantity, $item->amount));
            }
            
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(new item('Total', $data->items->sum('amount'), true));
            $printer->selectPrintMode();
            
            /* Footer */
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for shopping at ".env('APP_NAME')."\n");
            $printer->feed(2);
            $printer->text(dateFormat(date('Y-m-d H:i:s'), 1) . "\n");
            
            /* Cut the receipt and open the cash drawer */
            $printer->cut();
            $printer->pulse();
            
        } catch (Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        } finally {
            $printer->close();
            return ['status' => true, 'error' => null];
        }
    }
    
    public function customerPayment($data)
    {
        //$connector = new FilePrintConnector("php://stdout");
        //$printer = new Printer($connector);

        // $connector = new NetworkPrintConnector(env('PRINTER_NAME'), 9100);
        // $printer = new Printer($connector);

        $profile = CapabilityProfile::load("simple");
        $connector = new WindowsPrintConnector(env('PRINTER_NAME'));
        $printer = new Printer($connector, $profile);
        
        try {
            /* Name of shop */
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(env('APP_NAME')."\n");
            $printer->selectPrintMode();
            $printer->text('Date: '.dateFormat($data->date)."\n");
            if ($data->note != null) {
                $printer->text($data->note."\n");
            }
            $printer->feed();
            
            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("CUSTOMER PAYMENT RECEIPT\n\n");
            $printer->setEmphasis(false);
            
            /* Details */
            $printer->text(new item('Customer', $data->customer->name));
            $printer->text(new item('Bank', $data->bank->name));
            $printer->text(new item('Type', $data->type));
            $printer->feed();

            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(new item('Amount', $data->amount, true));
            $printer->selectPrintMode();
            
            /* Footer */
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for shopping at ".env('APP_NAME')."\n");
            $printer->feed(2);
            $printer->text(dateFormat(date('Y-m-d H:i:s'), 1) . "\n");
            
            /* Cut the receipt and open the cash drawer */
            $printer->cut();
            $printer->pulse();
            
        } catch (Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        } finally {
            $printer->close();
            return ['status' => true, 'error' => null];
        }
    }
}

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $currenctSign;

    public function __construct($name = '', $price = '', $currenctSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->currenctSign = $currenctSign;
    }
    
    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 38;
        if ($this->currenctSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols) ;
        
        $sign = ($this->currenctSign ? env('CURRENCY') : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}
