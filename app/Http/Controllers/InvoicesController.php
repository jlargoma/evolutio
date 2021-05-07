<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use PDF;
use Auth;
use App\Models\Invoices;
use App\Models\Rates;


class InvoicesController extends Controller {

  public function index($order = null) {
    $year = getYearActive();
    $sql_invoices = Invoices::whereYear('date', '=', $year);
    $orderDate = '';
    if ($order) {
      switch ($order) {
        case "date-desc":
          $sql_invoices->orderBy('date', 'DESC');
          $orderDate = $order;
          break;
        case "date-asec":
          $sql_invoices->orderBy('date', 'ASC');
          $orderDate = $order;
          break;
      }
    } else {
      $sql_invoices->orderBy('created_at', 'DESC');
    }
    $invoices = $sql_invoices->get();
    $totalValue = $invoices->sum('total_price');
    return view('invoices.index', compact('invoices', 'totalValue', 'orderDate'));
  }
  
  
  
  public function getData($oInvoice){
    if ($oInvoice){
      $emisores = $oInvoice->emisor();
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize ($items);
      return ['oInvoice'=>$oInvoice,'items'=>$items,'emisores'=>$emisores,'emisor'=>$oInvoice->emisor];
    }
    $oInvoice = new Invoices();
    $emisores = $oInvoice->emisor();
    return ['oInvoice'=>$oInvoice,'items'=>[],'emisores'=>$emisores,'emisor'=>null];
  }
  
  public function update($id){
    $oInvoice = Invoices::find($id);
    return view('invoices.forms._form',$this->getData($oInvoice)); 
  }
  public function update_modal($book_id){
    $oInvoice = Invoices::where('book_id',$book_id)->first();
    
    $data = $this->getData($oInvoice);
    $data['book_id'] = $book_id;
    
    $oBook = \App\Book::find($book_id);
    
    if (!$data['items'] || count($data['items']) == 0){
      
      
      $detail = 'Alojamiento ';
      
      $oRoomType = $oBook->room->RoomsType()->first();
      if ($oRoomType){
        $detail .= '('.$oRoomType->title.')';
      }
      $detail .= PHP_EOL.'*Fechas: del '. convertDateToShow_text($oBook->start,true);
      $detail .= ' al '. convertDateToShow_text($oBook->start,true).'*';
      
      $data['items'][] = [
        'detail'=> $detail,
        'iva'   => 10,
        'price' => $oBook->total_price,
      ];
      
    }
    $oInvoice = $data['oInvoice'];
    if (!$oInvoice->id){
      $customer = $oBook->customer;
      $email= (trim($customer->email_notif) == '') ? $customer->email : $customer->email_notif;
//      dd($customer);
      $oInvoice->name = $customer->name;
      $oInvoice->email = $email;
      $oInvoice->nif  = $customer->DNI;
      $oInvoice->address = $customer->address;
      $oInvoice->phone   = $customer->phone;
      $oInvoice->zip_code= $customer->zipCode;
    }
    
    return view('invoices.forms/_form-modal',$data); 
  }
  
  public function save(Request $request){
    
    $id = $request->input('id',null);
    $items = $request->input('item',[]);
    $iva = $request->input('iva',[]);
    $prices = $request->input('price',[]);
    
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    
    
    if (!$oInvoice){
      $oInvoice = new Invoices();
      $oInvoice->date = date('Y-m-d');
      $oInvoice->code = 'SN'.date('y');
      $oInvoice->status = 1;
      $nextNumber = Invoices::select('number')
              ->withTrashed()
              ->whereYear('date','=',date('Y'))
              ->orderBy('number','desc')->first();
      if ($nextNumber){
        $oInvoice->number = $nextNumber->number+1;
      } else $oInvoice->number = 1;
    }
    
    $emisor = $request->input('emisor',null);
    $aEmisor = $oInvoice->emisor($emisor);
    if ($emisor && $aEmisor){
      $oInvoice->emisor = $emisor;
      $oInvoice->name_business = $aEmisor['name'];
      $oInvoice->nif_business  =  $aEmisor['nif'];
      $oInvoice->address_business  = $aEmisor['address'];
      $oInvoice->phone_business    = $aEmisor['phone'];
      $oInvoice->zip_code_business = $aEmisor['zipcode'];
    }
    
    
    $oInvoice->name = $request->input('name');
    $oInvoice->nif  = $request->input('nif');
    $oInvoice->address = $request->input('address');
    $oInvoice->phone   = $request->input('phone');
    $oInvoice->email   = $request->input('email');
    $oInvoice->zip_code= $request->input('zip_code');
    $oInvoice->total_price= array_sum($prices);
    $oInvoice->save();
    

    $oInvoice->deleteMetaContent('items');
    $invItems = [];
    foreach ($items as $k=>$v){
      $invItems[] = [
        'detail'=> $v,
        'iva'   => isset($iva[$k]) ? $iva[$k] : '',
        'price' => isset($prices[$k]) ? floatVal($prices[$k]) : 0
      ];
      
    }
    if (count($invItems)>0)  
      $oInvoice->setMetaContent('items', serialize ($invItems));
    
    if ($request->input('confirm',null)) {
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
//      $aRoomsLst = \App\Rooms::getRoomList();
      return view('invoices.invoice_confirm', ['oInvoice'=>$oInvoice,'items'=>$items]);
    }
    return redirect(route('invoice.edit',$oInvoice->id));
  }

  public function view($id) {
    $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
      $url = '/factura/'.encriptID($id).'/'.encriptID($oInvoice->number).'/'.md5($oInvoice->email);
      return view('invoices.invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'url'=>$url]);
    }
    
    return back();
  }

  public function donwload_external($id,$num,$email) {
    $id = desencriptID($id);
    $num = desencriptID($num);
    $oInvoice = Invoices::where('id',$id)->where('number',$num)->first();
    if ($oInvoice){
      if (md5($oInvoice->email) == $email){
        $items = $oInvoice->getMetaContent('items');
        if ($items) $items = unserialize ($items);
        $numFact = $oInvoice->num;
        $pdf = PDF::loadView('invoices.invoice', ['oInvoice'=>$oInvoice,'items'=>$items]);
        return $pdf->stream('factura-' . $numFact . '-' . str_replace(' ', '-', strtolower($oInvoice->name)) . '.pdf');
      }
    }
    return view('404');
  }
  public function download($id) {
    $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize ($items);
      $numFact = $oInvoice->num;
      $pdf = PDF::loadView('invoices.invoice', ['oInvoice'=>$oInvoice,'items'=>$items]);
      return $pdf->stream('factura-' . $numFact . '-' . str_replace(' ', '-', strtolower($oInvoice->name)) . '.pdf');
    }
    
    return back();
    
  }

  public function downloadAll($year = '') {
    set_time_limit(0);
    ini_set('memory_limit', '1024M');
    $year = getYearActive();
    $sql_invoices = Invoices::whereYear('date', '=', $year);
    $sql_invoices->orderBy('number', 'ASC');
    $oInvoices = $sql_invoices->get();
    $pdf = PDF::loadView('invoices.invoice-lst-pdf', ['oInvoices' => $oInvoices]);
    return $pdf->stream('facturas-' . $year . '.pdf');
  }

  public function sendMail(Request $request) {
    
    $id = $request->input('id',null);
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    if ($oInvoice){
      
      $items = $oInvoice->getMetaContent('items');
      if ($items) $items = unserialize($items);
      $url = url('/factura/').'/'.encriptID($id).'/'.encriptID($oInvoice->number).'/'.md5($oInvoice->email);
      $content = view('invoices.invoice', ['oInvoice'=>$oInvoice,'items'=>$items,'url'=>$url]);
      $to = $oInvoice->email;
      $subject = 'Factura';
      
      if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return $to.' no es un mail válido';
      
      $sended = \Illuminate\Support\Facades\Mail::send('emails.base', [
            'mailContent' => $content,
            'tit'         => $subject
        ], function ($message) use ($to, $subject) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($to);
            $message->subject($subject);
        });
     
      
      if ($sended){
        return '<p class="alert alert-success">Factura enviada a '.$oInvoice->email.'</p>';
      }
    }
    return '<p class="alert alert-warmimg">Factura no enviada a '.$oInvoice->email.'</p>';
  }
  
  public function delete(Request $request) {
    
    $id = $request->input('id',null);
    $oInvoice = null;
    if ($id) $oInvoice = Invoices::find($id);
    if ($oInvoice){
      $oInvoice->delete();
      return 'OK';
    }

    return 'error';
  }

  
}
