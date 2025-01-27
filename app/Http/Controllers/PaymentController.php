<?php

namespace App\Http\Controllers;

use App\Http\Requests\paymentRequest;
use App\Models\PaymentModel;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
  public function index(){
    $payment = PaymentModel::all();
    return view('admin.payment.index', compact('payment'));
  }  
  public function create(){
    return view('admin.payment.create');
  }
  public function store(paymentRequest $request)
  {  
    dd($request); 
    PaymentModel::create([
         'pay_mod' => $request->payMode
     ]);
     return view('admin.payment.create');
     }
}
