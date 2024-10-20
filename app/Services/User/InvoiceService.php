<?php

namespace App\Services\User;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class   InvoiceService
{

  private $invoiceModel;

  public function __construct(Invoice $invoiceModel)
  {
    $this->invoiceModel = $invoiceModel;
  }

  public function createInvoice($order)
  {
    try{
      return Invoice::create([
        'order_id' => $order->id,
        'user_id' => $order->user_id,
        'status' => $order->payment_status,
        'invoice_number' => time(),
        'total' => $order->total, // this key is for user
        'discount' => $order->discount,
        'total_price' => $order->total_price,
        'vat' => $order->vat,
        'delivery_fees' => $order->delivery_fees,
        'coupon_discount' => $order->coupon_discount,
        'fast_delivery_fees' => $order->fast_delivery_fees,
      ]);
    } catch (\Throwable $exception) {
      Log::error($exception->getMessage());
      throw $exception;
    }
  }

  public function createInvoicePdf($order , $reportService,  $qrCodeService){
    $invoice = Invoice::where('order_id', $order->id)->first();

    $qr = str_replace('<?xml version="1.0" standalone="no"?>', ' ', $qrCodeService->generateQrCode($invoice));

    $data = [
      'invoice' => $invoice,
      'qr' => $qr,
    ];

    // Define the path to save the PDF
    $directory = public_path() . '/storage/invoices/' . $invoice->invoice_number . '/';
    if (!file_exists($directory)) {
      mkdir($directory, 0777, true); // Ensure the directory is writable
    }
    $file_name =$invoice->invoice_number . '.pdf';
    $path = $directory . $file_name;


    // Generate and save the PDF
    $reportService->savePdf($data, 'admin.v1.pdf.invoice_template', $path);

    // // Prepare file data to upload to OCI
    // $file = new \Illuminate\Http\UploadedFile($path, $file_name, 'application/pdf', null, true);

    // // Assuming $data is the request data you pass to storeFile
    // $data['file'] = $file; // Add the generated file to the request data

    // // Use your storeFile function to upload to OCI
    // $uploadResult = $this->store($data, 'file', 'invoices', $this->ociService);

    // // Optionally, you can delete the local file after upload
    // if ($uploadResult) {
    //   unlink($path);
    // }

    // Return the file name or any relevant data
    // return $uploadResult[0]['name'];

    // Optionally return the path if you need to use it
    return $file_name;
  }
}
