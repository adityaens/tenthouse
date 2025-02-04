<?php
 
namespace App\Http\Controllers;


use App\Http\Requests\PaymentRequest;
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
  public function store(PaymentRequest $request)
  {  
    
    PaymentModel::create([
         'pay_mod' => $request->payMode
     ]);
     return redirect()->back()->with('success','Payment mode added successfully');
     }

     public function destroy($id){
      $payment_mode=PaymentModel::find($id);      
      if($payment_mode){
        $payment_mode->delete();
        return redirect()->route('admin.payment.index')->with('success','Payment Mode deleted successfully');
      }
      return redirect()->route('admin.payment.index')->with('error','Something went wrong');
     }
}
