<?php

namespace App\Jobs;

use App\Exports\ProductExport;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProductExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request;
    private $batchID;
    private $fileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $batchID, $fileName)
    {
        $this->request = $request;
        $this->batchID = $batchID;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newCollection = [];
        $count = 1;
        $productService = new ProductService();

        $productService->getProducts($this->request)->chunk(100, function($products) use (&$count) {
            foreach ($products as $product) { 
                $data = [
                    'sku' => $product->sku ?? '',
                    'name' => $product->name ?? '',
                    'category_name' => $product->category->name ?? '',
                    'name' => $product->name ?? '',
                    'description' => $product->description ?? '',
                    'price' => $product->price ?? '',
                    'quantity' => $product->quantity ?? '',
                    'used_qty' => $product->used_qty ?? '',
                    'rem_qty' => $product->rem_qty ?? '',
                    'product_condition' => $product->product_condition ?? '',
                    'status' => ($product->status == 1) ? 'Active' : 'Inactive'
                ];

                $newCollection[] = (object) $data;
                $count++;
            }
        });

        //Generate csv or excel from here
        Excel::store(new ProductExport($newCollection), 'export/' . $this->fileName);
    }
}
