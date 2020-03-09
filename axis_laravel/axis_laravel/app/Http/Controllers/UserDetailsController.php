<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\UserDetails;

class UserDetailsController extends Controller
{
    public function userdata(Request $request){
 
    $user = UserDetails::where('LoginPin','=',$request->pass)->first();
    if($user) {
      Auth::login($user);
      $user_data = DB::table('cost_center_access')
      ->join('local_users', 'local_users.user_id', '=', 'cost_center_access.user_id')
      ->select('ChainID','LocationID')
      ->where('local_users.LoginPin','=',$request->pass)
      ->get(); 
      $date = date('Y-m-d');
      $chainid = $user_data[0]->ChainID;
      $companyid = $user->CompanyID;
      $user_name =$user->user_name;
      $locationid =$user_data[0]->LocationID;
      $data =array('user_name'=>$user_name,'CompanyID'=>$companyid,'ChainID'=>$chainid,'LocationID'=>$locationid);
      $success['token']= $user->createToken('MyApp')->accessToken;

      $data2 = DB::table('sales_header')
      ->join('local_users', 'local_users.CompanyID', '=', 'sales_header.CompanyID')
      ->select('OrderType',DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as sales'))
      ->where('local_users.LoginPin','=',$request->pass)
      ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->where('VoucherDate','=',$date)
      ->groupBy('OrderType')
      ->get(); 
      $query = DB::getQueryLog();

      $data2 = json_decode(json_encode($data2), true);
      $i=0;
      $datatwo =array();
      foreach($data2 as $arr){
         $datatwo[$i]['OrderType'] = $arr['OrderType']; 
         $datatwo[$i]['trx'] = strval(round($arr['trx'],2));
         $datatwo[$i]['GuestCount'] = strval(round($arr['GuestCount'],2));
         $datatwo[$i]['sales'] = strval(round($arr['sales'],2));
         $i++;
      } 

      $data_total=DB::table('sales_header')
      ->join('local_users', 'local_users.CompanyID', '=', 'sales_header.CompanyID')
      ->select(DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as sales'))
      ->where('local_users.LoginPin','=',$request->pass)
      ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->where('VoucherDate','=',$date)
      ->get(); 
      $data_total1 = json_decode(json_encode($data_total), true);
      foreach($data_total1 as $arr1){

         $data_total2['trx'] = strval(round($arr1['trx'],2));
         $data_total2['GuestCount'] = strval(round($arr1['GuestCount'],2));
         $data_total2['sales'] = strval(round($arr1['sales'],2));
      
      } 
       $chain=DB::table('sales_header')
      ->select('ChainId')
      ->where('CompanyId','=',$companyid)
      ->groupBy('ChainId')
      ->get(); 
      $chain = json_decode(json_encode($chain), true);
      $chain[1]['ChainId'] = 'RTT';
      $chain[2]['ChainId'] ='ERD';
      $chain[3]['ChainId'] = 'WSR';

      $location=DB::table('sales_header')
       ->select('LocationId')
       ->where('ChainId','=',$chainid)
       ->groupBy('LocationId')
       ->get(); 
      // print_r($location);
      
      // print_r($location); exit();
       $location = json_decode(json_encode($location), true);
       $location[1]['LocationId'] = 'AMP';
       $location[2]['LocationId'] ='RGK';
       $location[3]['LocationId'] = 'FRG';



      echo json_encode(["status" => "success","data" => $data, "Orders"=>$datatwo,"Total"=>$data_total2,"Location"=>$location,"Chain"=>$chain]);
    }
    else{
        echo json_encode(["status" => "error", "message" => "Invalid user"]);
    }  
        
   }
   /*public function userdata(Request $request){
 
    $user = UserDetails::where('LoginPin','=',$request->pass)->first();
    if($user) {
      Auth::login($user);
      $user_data = DB::table('cost_center_access')
      ->join('local_users', 'local_users.user_id', '=', 'cost_center_access.user_id')
      ->select('ChainID','LocationID')
      ->where('local_users.LoginPin','=',$request->pass)
      ->get(); 
     
      $chainid = $user_data[0]->ChainID;
      $companyid = $user->CompanyID;
      $user_name =$user->user_name;
      $locationid =$user_data[0]->LocationID;
      $data =array('user_name'=>$user_name,'CompanyID'=>$companyid,'ChainID'=>$chainid,'LocationID'=>$locationid);
      $success['token']= $user->createToken('MyApp')->accessToken;
      $data2 = DB::table('sales_header')
      ->join('local_users', 'local_users.CompanyID', '=', 'sales_header.CompanyID')
      ->select('OrderType',DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as sales'))
      ->where('local_users.LoginPin','=',$request->pass)
      ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->where('VoucherDate','=','2018-07-24')
      ->groupBy('OrderType')
      ->get(); 
      $data2 = json_decode(json_encode($data2), true);
      $i=0;
      foreach($data2 as $arr){
         $datatwo[$i]['OrderType'] = $arr['OrderType']; 
         $datatwo[$i]['trx'] = strval(round($arr['trx'],2));
         $datatwo[$i]['GuestCount'] = strval(round($arr['GuestCount'],2));
         $datatwo[$i]['sales'] = strval(round($arr['sales'],2));
         $i++;
      } 

       $data_total=DB::table('sales_header')
      ->join('local_users', 'local_users.CompanyID', '=', 'sales_header.CompanyID')
      ->select(DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as sales'))
      ->where('local_users.LoginPin','=',$request->pass)
      ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->where('VoucherDate','=','2018-07-24')
      ->get(); 
      $data_total1 = json_decode(json_encode($data_total), true);

      foreach($data_total1 as $arr1){

         $data_total2['trx'] = strval(round($arr1['trx'],2));
         $data_total2['GuestCount'] = strval(round($arr1['GuestCount'],2));
         $data_total2['sales'] = strval(round($arr1['sales'],2));
      
      } 
    //   $chain=DB::table('sales_header')
    //   ->select('ChainId')
    //   ->where('CompanyId','=',$companyid)
    //   ->groupBy('ChainId')
    //   ->get(); 
    //   $location=DB::table('sales_header')
    //   ->select('LocationId')
    //   ->where('ChainId','=',$chainid)
    //   ->groupBy('LocationId')
    //   ->get(); 
    $chain=DB::table('sales_header')
      ->select('ChainId')
      ->where('CompanyId','=',$companyid)
      ->groupBy('ChainId')
      ->get(); 
      $chain = json_decode(json_encode($chain), true);
      $chain[1]['ChainId'] = 'RTT';
      $chain[2]['ChainId'] ='ERD';
      $chain[3]['ChainId'] = 'WSR';
    $location=DB::table('sales_header')
       ->select('LocationId')
       ->where('ChainId','=',$chainid)
       ->groupBy('LocationId')
       ->get(); 
      // print_r($location);
      
      // print_r($location); exit();
      $location = json_decode(json_encode($location), true);
       $location[1]['LocationId'] = 'AMP';
       $location[2]['LocationId'] ='RGK';
       $location[3]['LocationId'] = 'FRG';


      echo json_encode(["status" => "success","data" => $data, "Orders"=>$datatwo,"Total"=>$data_total2,"Location"=>$location,"Chain"=>$chain]);
    }
    else{
        echo json_encode(["status" => "error", "message" => "Invalid user"]);
    }  
        
    }*/
    
    public function userdataonchange(Request $request){
      
      $date_only=$request->date;
      $chin_id = $request->chain;
      $location_id =$request->location;
      $data2 = DB::table('sales_header')
      ->select('OrderType',DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('sum(NetSalesAfterDiscountAndTax) as sales'))
      ->where('ChainId','=',$chin_id)
      ->where('LocationId','=',$location_id)
      ->where('VoucherDate','=',$date_only)
      ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->groupBy('OrderType')
      ->get(); 

    
      $data2 = json_decode(json_encode($data2), true);
    
      $i=0;
      $datatwo=array();
      foreach($data2 as $arr){
         $datatwo[$i]['trx'] = strval(round($arr['trx'],2));
         $datatwo[$i]['GuestCount'] = strval(round($arr['GuestCount'],2));
         $datatwo[$i]['Sales'] = strval(round($arr['sales'],2));
         $i++;
      } 
    


     $data_total=DB::table('sales_header')
      ->select(DB::raw('COUNT(VoucherNo) as trx'),DB::raw('COUNT(GuestCount) as GuestCount'),DB::raw('sum(NetSalesAfterDiscountAndTax) as sales'))
     ->where('ChainId','=',$chin_id)
     ->where('LocationId','=',$location_id)
     ->where('VoucherDate','=',$date_only)
     ->where('sales_header.VoucherStatus','LIKE',"%S%")
      ->get(); 

      $data_total1 = json_decode(json_encode($data_total), true);
      $i=0;
      $data_total2= array();
      $i=0;
      foreach($data_total1 as $arr1){

        $data_total2[$i]['trx'] = strval(round($arr1['trx'],2));
        $data_total2[$i]['GuestCount'] = strval(round($arr1['GuestCount'],2));
        $data_total2[$i]['sales'] = strval(round($arr1['sales'],2));
     $i++;
     } 
   
      // $location=DB::table('location')
      // ->select('LocationId')
      // ->where(array('ChainId'=>$chainid))
      // ->get(); 


      @$company=DB::table('sales_header')
      ->select('CompanyId')
      ->where('ChainId','=',$chin_id)
      ->groupBy('CompanyId')
      ->get(); 
     
      $chain=DB::table('sales_header')
      ->select('ChainId')
      ->where('CompanyId','=',@$company[0]->CompanyId)
      ->groupBy('ChainId')
      ->get(); 

      $location=DB::table('sales_header')
      ->select('LocationId')
      ->where('ChainId','=',$chin_id)
      ->groupBy('LocationId')
      ->get(); 

      echo json_encode(["Orders"=>$datatwo,"Total"=>$data_total2,"Location"=>$location,"Chain"=>$chain]);


    }
     /* public function all_data(Request $request){

        $LoginPin=$request->pass;
        $date_only=$request->date;
        $chin_id = 'KHF';
        $location_id = 'JLT';
        $company_id = DB::table('local_users')
        ->select('CompanyID','user_name')
        ->where('LoginPin','=',$request->pass)
        ->get()->toArray(); 
        $company = $company_id[0]->CompanyID;
        $username =$company_id[0]->user_name;
        $userdata =array('user_name'=>$username,'CompanyID'=>$company,'ChainID'=>$chin_id,'LocationID'=>$location_id);
       // $company=$company_id[0]['CompanyID'];
      
       // -----------------order type----------------------
       $data_ordertype = DB::table('sales_header')
       ->select('OrderType',DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'),DB::raw('COUNT(GuestCount) as Guest'),DB::raw('COUNT(VoucherNo) as Transaction'))
       ->where('CompanyID','=',$company)
       ->where('VoucherDate','=',$date_only)
       ->where('sales_header.VoucherStatus','LIKE',"%S%")
       ->groupBy('OrderType')
       ->get(); 
       $data_ordertype1 = json_decode(json_encode($data_ordertype), true);
      $i=0;
      $data_ordertype=array();
      foreach($data_ordertype1 as $arr){
         $data_ordertype[$i]['Transaction'] = strval(round($arr['Transaction'],2));
         $data_ordertype[$i]['Guest'] = strval(round($arr['Guest'],2));
         $data_ordertype[$i]['Sales'] = strval(round($arr['Sales'],2));
         $i++;
      } 
    

     $data_ordertype_total = DB::table('sales_header')
     ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('sales_header.VoucherStatus','LIKE',"%S%")
     ->get(); 
     $data_ordertype_tota2 = json_decode(json_encode($data_ordertype_total), true);
     $i=0;
     $data_ordertype_total = array();
     foreach($data_ordertype_tota2 as $arr1){

       $data_ordertype_total['Sales'] = strval(round($arr1['Sales'],2));
    
    } 
    
    // ---------------Category 1-----------------------
   //   $query_category1=$this->db->query("select CategoryDes1, sum(SellingPrice) as sales, sum(Qty) as Guests from sales_line where CompanyID='$company' and DATE(VoucherDate) = '$date_only' group by CategoryDes1");
   //   $data_category1=$query_category1->result();
     
     $data_category1 = DB::table('sales_line')
     ->select('CategoryDes1',DB::raw('SUM(SellingPrice*Qty) as sales'),DB::raw('COUNT(VoucherNo) as Guest'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->groupBy('CategoryDes1')
     ->get(); 
     $data_category11 = json_decode(json_encode($data_category1), true);
     $i=0;
     $data_category1 = array();
     foreach($data_category11 as $arr){
        $data_category1[$i]['CategoryDes1'] = $arr['CategoryDes1'];
        $data_category1[$i]['Guests'] = strval(round($arr['Guest'],2));
        $data_category1[$i]['sales'] = strval(round($arr['sales'],2));
        $i++;
     } 
    
     $data_category1_total = DB::table('sales_line')
     ->select(DB::raw('COUNT(VoucherNo) as Guests'),DB::raw('SUM(SellingPrice*Qty) as sales'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)   
     ->where('SalesStatus','LIKE','%S%')
     ->get(); 
     $data_category1_total1 = json_decode(json_encode($data_category1_total), true);
     $data_category1_total = array();
     $i=0;
     foreach($data_category1_total1 as $arr){
        $data_category1_total['Guests'] = strval($arr['Guests']);
        $data_category1_total['sales'] = strval(round($arr['sales'],2));
        $i++;
     } 

     // --------------Payment---------------------------

   //   $payment_type = DB::table('sales_payment')
   //   ->select('PaymentType',DB::raw('round(SUM(Amount),3) as Amount'))
   //   ->where('CompanyID','=',$company)
   //   ->where('VoucherDate','=',$date_only)
   //   ->groupBy('PaymentType')
   //   ->get();


     $payment_type=array("CASH","VISA CARD",'MASTER CARD','STAFF DISCOUNT 30','ONLINE PAYMENTS');
  
    for ($i=0; $i < count($payment_type) ; $i++) { 
    

        $query_paymenttype = DB::table('sales_payment')
        ->select('PaymentType as Payment Type',DB::raw('round(SUM(Amount),3) as Amount'))
        ->where('CompanyID','=',$company)
        ->where('VoucherDate','=',$date_only)
        ->where('PaymentType','=',$payment_type[$i])
        ->groupBy('PaymentType')
        ->get(); 
        
        $quer = $query_paymenttype[0]->Amount;
        $id=2;
        if(($payment_type[$i] == 'VISA CARD') || ($payment_type[$i] == 'MASTER CARD')){
            $id=1;
        }
        $data_paymenttype[]=array("Group Id"=>$id,"Payment Type"=>$payment_type[$i],"Amount"=>strval(round($quer,2)));
        
      }
      $visa_amount=strval(round($data_paymenttype[1]['Amount'],2)); $master_amount=strval(round($data_paymenttype[2]['Amount'],2));
       $data_paymenttype[]=array("visa_master_total"=>strval(round($visa_amount+$master_amount,2)));
     
     // ---------------Category 2-----------------------
     $data_category2 = DB::table('sales_line')
     ->select('CategoryDes2',DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->groupBy('CategoryDes2')
     ->get(); 
     $data_category12 = json_decode(json_encode($data_category2), true);
     $i=0;
     $data_category2 = array();
     foreach($data_category12 as $arr){
        $data_category2[$i]['CategoryDes2'] = $arr['CategoryDes2'];
        $data_category2[$i]['Qty'] = strval(round($arr['Quantity'],2));
        $data_category2[$i]['SellingPrice'] = strval(round($arr['sales'],2));
        $i++;
     }
     
     $data_category2_total= DB::table('sales_line')
     ->select(DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->get(); 
     $data_category2_total2 = json_decode(json_encode($data_category2_total), true);
     $data_category2_total = array();
     $i=0;
     foreach($data_category2_total2 as $arr){
        $data_category2_total['Quantity'] = strval(round($arr['Quantity'],2));
        $data_category2_total['price'] = strval(round($arr['sales'],2));
        $i++;
     } 
  

   // -----------------Items--------------------------
   $categorydes1_array = DB::table('sales_line')
   ->select('CategoryDes1')
   ->where('CompanyID','=',$company)
   ->where('VoucherDate','=',$date_only)
   ->distinct('CategoryDesc1')
   ->get()->toArray(); 
  //print_r($categorydes1_array); exit();
   $i=0;
   foreach($categorydes1_array as $key => $value) {   
      $query_items = DB::table('sales_line')
      ->select('ItemDesc as item',DB::raw('SUM(Qty) as quantity'),DB::raw('SUM(SellingPrice*Qty) as price'))
      ->where('CompanyID','=',$company)
      ->where('VoucherDate','=',$date_only)
      ->where('SalesStatus','LIKE','%S%')
      ->where('CategoryDes1','=',$value->CategoryDes1)
      ->groupBy('ItemDesc')
      ->get(); 
      $query_items1 = json_decode(json_encode($query_items), true);
     $i=0;
     $query_items = array();
     foreach($query_items1 as $arr){
        $query_items[$i]['item'] = $arr['item'];
        $query_items[$i]['quantity'] = strval(round($arr['quantity'],2));
        $query_items[$i]['price'] = strval(round($arr['price'],2));
        $i++;
     }
      $items[]=array("Category"=>$value->CategoryDes1,"items"=>$query_items);
       $i=$i+1;
   }   

   $data_payment_total = DB::table('sales_line')
   ->select(DB::raw('SUM(Qty) as Quantity'),DB::raw('SUM(SellingPrice*Qty) as Price'))
   ->where('CompanyID','=',$company)
   ->where('VoucherDate','=',$date_only)
   ->get(); 

   $data_payment_total1 = json_decode(json_encode($data_payment_total), true);
   $data_payment_total = array();
   $i=0;
   foreach($data_payment_total1 as $arr){
      $data_payment_total['Quantity'] = strval(round($arr['Quantity'],2));
      $data_payment_total['Price'] = strval(round($arr['Price'],2));
      $i++;
   } 

  //---------------------------------Hourly Sale---------------------------
  $query_items = DB::table('sales_header')
  ->select(DB::raw('hour( StartTime ) as Hour'),DB::raw('COUNT(GuestCount) as Guest'),DB::raw('COUNT(VoucherNo) as Transcation'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'))
  ->where('CompanyID','=',$company)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('ChainID','=',$chin_id)
  ->groupBy(DB::raw('hour(StartTime)'),DB::raw('day(StartTime)'))
  ->get(); 
  $i=0;
  foreach($query_items as $data){

    $to =$data->Hour+1;
    $data_hourly_sale[$i]['Hours']=$data->Hour.' to '.$to;
    $data_hourly_sale[$i]['Guest']=strval(round($data->Guest,2));
    $data_hourly_sale[$i]['Transcation']=strval(round($data->Transcation,2));
    $data_hourly_sale[$i]['Sales']=strval(round($data->Sales,2));
    $i++;
  }

  //------------------------------------Credit Customer------------------------------------------
    $data_credit_customer= DB::table('credit_customer')
    ->select('CustomerID','CustomerName','Balance as Amount')
    ->where('CompanyID','=',$company)
    ->where('ChainID','=',$chin_id)
    ->get(); 
    $data_credit_customer1 = json_decode(json_encode($data_credit_customer), true);
    $data_credit_customer = array();
    $i=0;
    $data_credit_customer = array();
    foreach($data_credit_customer1 as $arr){
       $data_credit_customer[$i]['Amount'] = strval(round($arr['Amount'],2));
       $i++;
    } 
    $data_credit_customer= DB::table('credit_customer')
    ->select('CustomerID','CustomerName','Balance as Amount')
    ->where('CompanyID','=',$company)
    ->where('ChainID','=',$chin_id)
    ->get(); 
    $data_credit_customer1 = json_decode(json_encode($data_credit_customer), true);
    $data_credit_customer = array();
    $i=0;
    $data_credit_customer = array();
    foreach($data_credit_customer1 as $arr){
      $data_credit_customer[$i]['Amount'] = strval(round($arr['Amount'],2));
      $i++;
    } 

  //------------------------Sales Summary----------------------------------------------------------
  
 //------------------------Total Sales----------------------------------------------------------
 
 $data_total_sales= DB::table('sales_line')
  ->select(DB::raw('round(SUM(Qty*SellingPrice),3) as TotalSales'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('SalesStatus','LIKE','%S%')
  ->get(); 
  $data_total_sales1 = json_decode(json_encode($data_total_sales), true);
    $data_total_sales = array();
    $i=0;
    foreach($data_total_sales1 as $arr){
       $data_total_sales['TotalSales'] = strval(round($arr['TotalSales'],2));
       $i++;
    } 
  
  //------------------------Delivery Charges----------------------------------------------------------

  $data_delivery_charge= DB::table('sales_header')
  ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as DeliveryCharges'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('OrderType','=','DL')
  ->get(); 

  $data_delivery_charge1 = json_decode(json_encode($data_delivery_charge), true);
    $data_delivery_charge = array();
    $i=0;
    foreach($data_delivery_charge1 as $arr){
       $data_delivery_charge[$i]['DeliveryCharges'] = strval(round($arr['DeliveryCharges'],2));
       $i++;
    } 


 
 //------------------------Service Charges----------------------------------------------------------

  $data_service_charge= DB::table('sales_header')
  ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as ServiceCharges'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('OrderType','!=','DL')
  ->get();  
  $data_service_charge1 = json_decode(json_encode($data_service_charge), true);
  $data_service_charge = array();
  $i=0;
  foreach($data_service_charge1 as $arr){
     $data_service_charge[$i]['ServiceCharges'] = strval(round($arr['ServiceCharges'],2));
     $i++;
  } 

  
 //------------------------Gross Sales----------------------------------------------------------


  $data_gross_sales =strval(round($data_total_sales['TotalSales']+$data_delivery_charge[0]['DeliveryCharges']+$data_service_charge[0]['ServiceCharges'],2));
 //------------------------Staff Meal----------------------------------------------------------
  $data_staff_meal= DB::table('sales_line')
  ->select(DB::raw('SUM(Qty*SellingPrice) as StaffMeal'))
  ->where('sales_line.CompanyID','=',$company)
  ->where('sales_line.ChainID','=',$chin_id)
  ->where('sales_line.LocationID','=',$location_id)
  ->where('sales_line.VoucherDate','=',$date_only)
  ->leftJoin("sales_header",function($join){
    $join->on('sales_header.VoucherNo','=','sales_line.VoucherNo')
        ->on('sales_header.VoucherDate','=','sales_line.VoucherDate')
        ->on('sales_line.CompanyID','=','sales_header.CompanyID')
        ->on('sales_line.ChainID','=','sales_header.ChainID');
     })  
  ->get();
 
  $data_staff_meal1 = json_decode(json_encode($data_staff_meal), true);
  $data_staff_meal = array();
  $i=0;
  foreach($data_staff_meal1 as $arr){
     $data_staff_meal[$i]['StaffMeal'] = strval(round($arr['StaffMeal'],2));
     $i++;
  } 

 //------------------------Discount----------------------------------------------------------
  $data_discount= DB::table('sales_payment')
  ->select(DB::raw('SUM(Amount) as Discount'))
  ->leftJoin("sales_header",function($join){
  $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
      ->on('sales_header.VoucherDate','=','sales_payment.VoucherDate')
      ->on('sales_header.CompanyID','=','sales_payment.CompanyID')
      ->on('sales_header.ChainID','=','sales_payment.ChainID')
      ->on('sales_header.LocationID','=','sales_payment.LocationID');
  }) 
  // ->leftJoin("sales_payment_types",function($join1){
  // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
  //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
  //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
  // })  
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->where('OrderType','!=','SM')
  // ->where('sales_payment_types.IsDiscountCard'=>'1')
  ->get();
  $data_discount1 = json_decode(json_encode($data_discount), true);
  $data_discount = array();
  $i=0;
  foreach($data_discount1 as $arr){
     $data_discount[$i]['Discount'] = strval(round($arr['Discount'],2));
     $i++;
  } 

//------------------------Taxable Value----------------------------------------------------------
  $data_tax= DB::table('sales_header')
  ->select(DB::raw('SUM(ServiceTaxAmount+SalesTaxAmount) as TaxableValue'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->get(); 
  $data_tax1 = json_decode(json_encode($data_tax), true);
  $data_tax = array();
  $i=0;
  foreach($data_tax1 as $arr){
     $data_tax[$i]['TaxableValue'] = strval(round($arr['TaxableValue'],2));
     $i++;
  } 

 //------------------------Net Sales----------------------------------------------------------
  $data_net_sales =strval(round($data_gross_sales-$data_staff_meal[0]['StaffMeal']-$data_discount[0]['Discount'],3));

  //------------------------PayOut----------------------------------------------------------
  $data_payout='0';
 //------------------------Credit Sales----------------------------------------------------------
  $data_creditsales = DB::table('sales_payment')
  ->select(DB::raw('SUM(Amount) as CreditSales'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->leftJoin("sales_header",function($join){
  $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
      ->on('sales_header.VoucherDate','=','sales_payment.VoucherDate')
      ->on('sales_payment.CompanyID','=','sales_header.CompanyID')
      ->on('sales_payment.ChainID','=','sales_header.ChainID')
      ->on('sales_payment.LocationID','=','sales_header.LocationID');
  }) 
  // ->leftJoin("sales_payment_types",function($join1){
  // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
  //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
  //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
  // })  
  // ->where('sales_payment_types.IsCreditCard'=>'1')
  //->where('sales_payment_types.IsDiscountCard','!=','1')
  ->where('OrderType','!=','SM')
  ->get(); 
  $data_creditsales1 = json_decode(json_encode($data_creditsales), true);
  $data_creditsales = array();
  $i=0;
  foreach($data_creditsales1 as $arr){
     $data_creditsales[$i]['CreditSales'] = strval(round($arr['CreditSales'],2));
     $i++;
  } 
  $data_locationlist= DB::table('sales_header')
  ->select('LocationID')
  ->groupby('LocationID')
  ->get(); 

  //------------------------Actual Banking----------------------------------------------------------
  $data_actualbanking =strval(round($data_net_sales-$data_creditsales[0]['CreditSales']-$data_payout,3));

  //------------------------Voids----------------------------------------------------------
  $data_items_void = DB::table('sales_header')
  ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Amount'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%V%')
  ->groupby('VoucherNo')
  ->get();
  $data_items_void1 = json_decode(json_encode($data_items_void), true);
  $data_items_void = array();
  $i=0;
  foreach($data_items_void1 as $arr){
     $data_items_void['Count'] = strval(round($arr['Count'],2));
     $data_items_void['Amount'] = strval(round($arr['Amount'],2));
     $i++;
  } 


  $data_items_order = DB::table('sales_header')
  ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Amount'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->groupby('VoucherNo')
  ->get();
  $data_items_order1 = json_decode(json_encode($data_items_order), true);
  $data_items_order = array();
  $i=0;
  foreach($data_items_order1 as $arr){
     $data_items_order['Count'] = strval(round($arr['Count'],2));
     $data_items_order['Amount'] = strval(round($arr['Amount'],2));
     $i++;
  } 


  echo json_encode(["data"=>$userdata,"Sales by Category 1"=>$data_category1,"Category1_total"=>$data_category1_total,"Sales by Category 2"=>$data_category2,"Category2_total"=>$data_category2_total,"Sales by Items"=>$items,"Items_total"=>$data_payment_total,"Sales by Order Type"=>$data_ordertype,"Order_type_total"=>$data_ordertype_total,"Hourly Sale"=>$data_hourly_sale,"Credit Customer"=>$data_credit_customer,"Total Sales"=>$data_total_sales,"Delivery Charges"=>$data_delivery_charge,"Service Charges"=>$data_service_charge,"Gross Sales"=>$data_gross_sales,"Staff Meals"=>$data_staff_meal,"Discount"=>$data_discount,"Taxable Values"=>$data_tax,"Net Sales"=>$data_net_sales,"Credit Sales"=>$data_creditsales,"Actual Banking"=>$data_actualbanking,"Locations"=>$data_locationlist,"Item Void"=>$data_items_void,"Item Order"=>$data_items_order]);
 }*/
public function all_data(Request $request){

        $LoginPin=$request->pass;
        $date_only=$request->date;
        $chin_id =$request->chain;
        @$location_id =$request->location;
        
        @$company_id = DB::table('local_users')
        ->select('CompanyID','user_name')
        ->where('LoginPin','=',$request->pass)
        ->get()->toArray(); 
        $company = @$company_id[0]->CompanyID;
        $username =@$company_id[0]->user_name;
        $userdata =array('user_name'=>$username,'CompanyID'=>$company,'ChainID'=>$chin_id,'LocationID'=>$location_id);
       // $company=$company_id[0]['CompanyID'];
      
       // -----------------order type----------------------
       $data_ordertype = DB::table('sales_header')
       ->select('OrderType',DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'),DB::raw('COUNT(GuestCount) as Guest'),DB::raw('COUNT(VoucherNo) as Transaction'))
       ->where('CompanyID','=',$company)
       ->where('VoucherDate','=',$date_only)
       ->where('sales_header.VoucherStatus','LIKE',"%S%")
       ->groupBy('OrderType')
       ->get(); 
       $data_ordertype1 = json_decode(json_encode($data_ordertype), true);
      $i=0;
      $data_ordertype=array();
      foreach($data_ordertype1 as $arr){
         $data_ordertype[$i]['Transaction'] = strval(round($arr['Transaction'],2));
         $data_ordertype[$i]['Guest'] = strval(round($arr['Guest'],2));
         $data_ordertype[$i]['Sales'] = strval(round($arr['Sales'],2));
         $i++;
      } 
    

     $data_ordertype_total = DB::table('sales_header')
     ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('sales_header.VoucherStatus','LIKE',"%S%")
     ->get(); 
     $data_ordertype_tota2 = json_decode(json_encode($data_ordertype_total), true);
     $i=0;
     $data_ordertype_total = array();
     foreach($data_ordertype_tota2 as $arr1){

       $data_ordertype_total['Sales'] = strval(round($arr1['Sales'],2));
    
    } 
    
    // ---------------Category 1-----------------------
   //   $query_category1=$this->db->query("select CategoryDes1, sum(SellingPrice) as sales, sum(Qty) as Guests from sales_line where CompanyID='$company' and DATE(VoucherDate) = '$date_only' group by CategoryDes1");
   //   $data_category1=$query_category1->result();
     
     $data_category1 = DB::table('sales_line')
     ->select('CategoryDes1',DB::raw('SUM(SellingPrice*Qty) as sales'),DB::raw('COUNT(VoucherNo) as Guest'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->groupBy('CategoryDes1')
     ->get(); 
     $data_category11 = json_decode(json_encode($data_category1), true);
     $i=0;
     $data_category1 = array();
     foreach($data_category11 as $arr){
        $data_category1[$i]['CategoryDes1'] = $arr['CategoryDes1'];
        $data_category1[$i]['Guests'] = strval(round($arr['Guest'],2));
        $data_category1[$i]['sales'] = strval(round($arr['sales'],2));
        $i++;
     } 
    
     $data_category1_total = DB::table('sales_line')
     ->select(DB::raw('COUNT(VoucherNo) as Guests'),DB::raw('SUM(SellingPrice*Qty) as sales'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)   
     ->where('SalesStatus','LIKE','%S%')
     ->get(); 
     $data_category1_total1 = json_decode(json_encode($data_category1_total), true);
     $data_category1_total = array();
     $i=0;
     foreach($data_category1_total1 as $arr){
        $data_category1_total['Guests'] = strval($arr['Guests']);
        $data_category1_total['sales'] = strval(round($arr['sales'],2));
        $i++;
     } 

     // --------------Payment---------------------------

   //   $payment_type = DB::table('sales_payment')
   //   ->select('PaymentType',DB::raw('round(SUM(Amount),3) as Amount'))
   //   ->where('CompanyID','=',$company)
   //   ->where('VoucherDate','=',$date_only)
   //   ->groupBy('PaymentType')
   //   ->get();


     $payment_type=array("CASH","VISA CARD",'MASTER CARD','STAFF DISCOUNT 30','ONLINE PAYMENTS');
  
    for ($i=0; $i < count($payment_type) ; $i++) { 
    

        @$query_paymenttype = DB::table('sales_payment')
        ->select('PaymentType as Payment Type',DB::raw('round(SUM(Amount),3) as Amount'))
        ->where('CompanyID','=',$company)
        ->where('VoucherDate','=',$date_only)
        ->where('PaymentType','=',$payment_type[$i])
        ->groupBy('PaymentType')
        ->get(); 
        
        $quer = @$query_paymenttype[0]->Amount;
        $id=2;
        if(($payment_type[$i] == 'VISA CARD') || ($payment_type[$i] == 'MASTER CARD') || ($payment_type[$i] == 'ONLINE PAYMENTS')){
            $id=1;
        }else if($payment_type[$i] == 'CASH'){
            $id=2;
        }else if($payment_type[$i] == 'STAFF DISCOUNT 30'){
            $id=3;
        }
        $data_paymenttype[]=array("Group Id"=>$id,"Payment Type"=>$payment_type[$i],"Amount"=>strval(round($quer,2)));
        
      }
      $visa_amount['Visa Amount']=strval(round($data_paymenttype[1]['Amount'],2));
      $master_amount['Master Amount']=strval(round($data_paymenttype[2]['Amount'],2));
      $data_visamastertotal[]=array("visa_master_total"=>strval(round($visa_amount+$master_amount,2)));
     
     // ---------------Category 2-----------------------
     $data_category2 = DB::table('sales_line')
     ->select('CategoryDes2',DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->groupBy('CategoryDes2')
     ->get(); 
     $data_category12 = json_decode(json_encode($data_category2), true);
     $i=0;
     $data_category2 = array();
     foreach($data_category12 as $arr){
        $data_category2[$i]['CategoryDes2'] = $arr['CategoryDes2'];
        $data_category2[$i]['Qty'] = strval(round($arr['Quantity'],2));
        $data_category2[$i]['SellingPrice'] = strval(round($arr['sales'],2));
        $i++;
     }
     
     $data_category2_total= DB::table('sales_line')
     ->select(DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
     ->where('CompanyID','=',$company)
     ->where('VoucherDate','=',$date_only)
     ->where('SalesStatus','LIKE','%S%')
     ->get(); 
     $data_category2_total2 = json_decode(json_encode($data_category2_total), true);
     $data_category2_total = array();
     $i=0;
     foreach($data_category2_total2 as $arr){
        $data_category2_total['Quantity'] = strval(round($arr['Quantity'],2));
        $data_category2_total['price'] = strval(round($arr['sales'],2));
        $i++;
     } 
  

   // -----------------Items--------------------------
   $categorydes1_array = DB::table('sales_line')
   ->select('CategoryDes1')
   ->where('CompanyID','=',$company)
   ->where('VoucherDate','=',$date_only)
   ->distinct('CategoryDesc1')
   ->get()->toArray(); 
  //print_r($categorydes1_array); exit();
   $i=0;
   foreach($categorydes1_array as $key => $value) {   
      $query_items = DB::table('sales_line')
      ->select('ItemDesc as item',DB::raw('SUM(Qty) as quantity'),DB::raw('SUM(SellingPrice*Qty) as price'))
      ->where('CompanyID','=',$company)
      ->where('VoucherDate','=',$date_only)
      ->where('SalesStatus','LIKE','%S%')
      ->where('CategoryDes1','=',$value->CategoryDes1)
      ->groupBy('ItemDesc')
      ->get(); 
      $query_items1 = json_decode(json_encode($query_items), true);
     $i=0;
     $query_items = array();
     foreach($query_items1 as $arr){
        $query_items[$i]['item'] = $arr['item'];
        $query_items[$i]['quantity'] = strval(round($arr['quantity'],2));
        $query_items[$i]['price'] = strval(round($arr['price'],2));
        $i++;
     }
      $items[]=array("Category"=>$value->CategoryDes1,"items"=>$query_items);
       $i=$i+1;
   }   

   $data_payment_total = DB::table('sales_line')
   ->select(DB::raw('SUM(Qty) as Quantity'),DB::raw('SUM(SellingPrice*Qty) as Price'))
   ->where('CompanyID','=',$company)
   ->where('VoucherDate','=',$date_only)
   ->get(); 

   $data_payment_total1 = json_decode(json_encode($data_payment_total), true);
   $data_payment_total = array();
   $i=0;
   foreach($data_payment_total1 as $arr){
      $data_payment_total['Quantity'] = strval(round($arr['Quantity'],2));
      $data_payment_total['Price'] = strval(round($arr['Price'],2));
      $i++;
   } 

  //---------------------------------Hourly Sale---------------------------
  $query_items = DB::table('sales_header')
  ->select(DB::raw('hour( StartTime ) as Hour'),DB::raw('COUNT(GuestCount) as Guest'),DB::raw('COUNT(VoucherNo) as Transcation'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'))
  ->where('CompanyID','=',$company)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('ChainID','=',$chin_id)
  ->groupBy(DB::raw('hour(StartTime)'),DB::raw('day(StartTime)'))
  ->get(); 
  $i=0;
  $data_hourly_sale=array();
  foreach($query_items as $data){

    $to =$data->Hour+1;
    $data_hourly_sale[$i]['Hours']=strval($data->Hour.' to '.$to);
    $data_hourly_sale[$i]['Guest']=strval(round($data->Guest,2));
    $data_hourly_sale[$i]['Transcation']=strval(round($data->Transcation,2));
    $data_hourly_sale[$i]['Sales']=strval(round($data->Sales,2));
    $i++;
  }

  //------------------------------------Credit Customer------------------------------------------
    // $data_credit_customer= DB::table('credit_customer')
    // ->select('CustomerID','CustomerName','Balance as Amount')
    // ->where('CompanyID','=',$company)
    // ->where('ChainID','=',$chin_id)
    // ->get(); 
    // $data_credit_customer1 = json_decode(json_encode($data_credit_customer), true);
    // $data_credit_customer = array();
    // $i=0;
    // $data_credit_customer = array();
    // foreach($data_credit_customer1 as $arr){
    //   $data_credit_customer['Amount'] = strval(round($arr['Amount'],2));
    //   $i++;
    // } 
     $data_credit_customer= DB::table('sales_payment2')
    ->select('CustomerName',DB::raw('SUM(Amount) as Amount'))
    ->where('CompanyID','=',$company)
    ->where('ChainID','=',$chin_id)
    ->groupBy('CustomerName')
    ->get(); 
    // return response()->json([$data_credit_customer]);
    // $data_credit_customer1 = json_decode(json_encode($data_credit_customer), true);
    // $data_credit_customer = array();
    // $i=0;
    // $data_credit_customer = array();
    // foreach($data_credit_customer1 as $arr){
    //   $data_credit_customer['Amount'] = strval(round($arr['Amount'],2));
    //   $i++;
    // } 

  //------------------------Sales Summary----------------------------------------------------------
  
 //------------------------Total Sales----------------------------------------------------------
 
 $data_total_sales= DB::table('sales_line')
  ->select(DB::raw('round(SUM(Qty*SellingPrice),3) as TotalSales'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('SalesStatus','LIKE','%S%')
  ->get(); 
  $data_total_sales1 = json_decode(json_encode($data_total_sales), true);
    $data_total_sales = array();
    $i=0;
    foreach($data_total_sales1 as $arr){
       $data_total_sales['TotalSales'] = strval(round($arr['TotalSales'],2));
       $i++;
    } 
  
  //------------------------Delivery Charges----------------------------------------------------------

  $data_delivery_charge= DB::table('sales_header')
  ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as DeliveryCharges'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('OrderType','=','DL')
  ->get(); 

  $data_delivery_charge1 = json_decode(json_encode($data_delivery_charge), true);
    $data_delivery_charge = array();
    $i=0;
    foreach($data_delivery_charge1 as $arr){
       $data_delivery_charge['DeliveryCharges'] = strval(round($arr['DeliveryCharges'],2));
       $i++;
    } 
 //------------------------Service Charges----------------------------------------------------------

  $data_service_charge= DB::table('sales_header')
  ->select(DB::raw('SUM(NetSalesAfterDiscountAndTax) as ServiceCharges'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('LocationID','=',$location_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->where('OrderType','!=','DL')
  ->get();  
  $data_service_charge1 = json_decode(json_encode($data_service_charge), true);
  $data_service_charge = array();
  $i=0;
  foreach($data_service_charge1 as $arr){
     $data_service_charge['ServiceCharges'] = strval(round($arr['ServiceCharges'],2));
     $i++;
  } 

  
 //------------------------Gross Sales----------------------------------------------------------


  $data_gross_sales['Gross Sale']=strval(round($data_total_sales['TotalSales']+$data_delivery_charge['DeliveryCharges']+$data_service_charge['ServiceCharges'],2));
 //------------------------Staff Meal----------------------------------------------------------
  $data_staff_meal= DB::table('sales_line')
  ->select(DB::raw('SUM(Qty*SellingPrice) as StaffMeal'))
  ->where('sales_line.CompanyID','=',$company)
  ->where('sales_line.ChainID','=',$chin_id)
  ->where('sales_line.LocationID','=',$location_id)
  ->where('sales_line.VoucherDate','=',$date_only)
  ->leftJoin("sales_header",function($join){
    $join->on('sales_header.VoucherNo','=','sales_line.VoucherNo')
        ->on('sales_header.VoucherDate','=','sales_line.VoucherDate')
        ->on('sales_line.CompanyID','=','sales_header.CompanyID')
        ->on('sales_line.ChainID','=','sales_header.ChainID');
     })  
  ->get();
 
  $data_staff_meal1 = json_decode(json_encode($data_staff_meal), true);
  $data_staff_meal = array();
  $i=0;
  foreach($data_staff_meal1 as $arr){
     $data_staff_meal['StaffMeal'] = strval(round($arr['StaffMeal'],2));
     $i++;
  } 

 //------------------------Discount----------------------------------------------------------
  $data_discount= DB::table('sales_payment')
  ->select(DB::raw('SUM(Amount) as Discount'))
  ->leftJoin("sales_header",function($join){
  $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
      ->on('sales_header.VoucherDate','=','sales_payment.VoucherDate')
      ->on('sales_header.CompanyID','=','sales_payment.CompanyID')
      ->on('sales_header.ChainID','=','sales_payment.ChainID')
      ->on('sales_header.LocationID','=','sales_payment.LocationID');
  }) 
  // ->leftJoin("sales_payment_types",function($join1){
  // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
  //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
  //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
  // })  
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->where('OrderType','!=','SM')
  // ->where('sales_payment_types.IsDiscountCard'=>'1')
  ->get();
  $data_discount1 = json_decode(json_encode($data_discount), true);
  $data_discount = array();
  $i=0;
  foreach($data_discount1 as $arr){
     $data_discount['Discount'] = strval(round($arr['Discount'],2));
     $i++;
  } 

//------------------------Taxable Value----------------------------------------------------------
  $data_tax= DB::table('sales_header')
  ->select(DB::raw('SUM(ServiceTaxAmount+SalesTaxAmount) as TaxableValue'))
  ->where('CompanyID','=',$company)
  ->where('ChainID','=',$chin_id)
  ->where('VoucherDate','=',$date_only)
  ->where('VoucherStatus','LIKE','%S%')
  ->get(); 
  $data_tax1 = json_decode(json_encode($data_tax), true);
  $data_tax = array();
  $i=0;
  foreach($data_tax1 as $arr){
     $data_tax['TaxableValue'] = strval(round($arr['TaxableValue'],2));
     $i++;
  } 

 //------------------------Net Sales----------------------------------------------------------
  $data_net_sales['Net Sales'] = strval(round($data_gross_sales['Gross Sale']-$data_staff_meal['StaffMeal']-$data_discount['Discount'],3));

  //------------------------PayOut----------------------------------------------------------
  $data_payout['Payout']='0';
 //------------------------Credit Sales----------------------------------------------------------
  $data_creditsales = DB::table('sales_payment')
  ->select(DB::raw('SUM(Amount) as CreditSales'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->leftJoin("sales_header",function($join){
  $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
      ->on('sales_header.VoucherDate','=','sales_payment.VoucherDate')
      ->on('sales_payment.CompanyID','=','sales_header.CompanyID')
      ->on('sales_payment.ChainID','=','sales_header.ChainID')
      ->on('sales_payment.LocationID','=','sales_header.LocationID');
  }) 
  // ->leftJoin("sales_payment_types",function($join1){
  // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
  //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
  //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
  // })  
  // ->where('sales_payment_types.IsCreditCard'=>'1')
  //->where('sales_payment_types.IsDiscountCard','!=','1')
  ->where('OrderType','!=','SM')
  ->get(); 
  $data_creditsales1 = json_decode(json_encode($data_creditsales), true);
  $data_creditsales = array();
  $i=0;
  foreach($data_creditsales1 as $arr){
     $data_creditsales['CreditSales'] = strval(round($arr['CreditSales'],2));
     $i++;
  } 
  $data_locationlist= DB::table('sales_header')
  ->select('LocationID')
  ->groupby('LocationID')
  ->get(); 

  //------------------------Actual Banking----------------------------------------------------------
  $data_actualbanking['Actual Banking'] =strval(round($data_net_sales['Net Sales']-$data_creditsales['CreditSales']-$data_payout['Payout'],3));

  //------------------------Voids----------------------------------------------------------
  $data_items_void = DB::table('sales_header')
  ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Amount'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%V%')
  ->groupby('VoucherNo')
  ->get();
  $data_items_void1 = json_decode(json_encode($data_items_void), true);
  $data_items_void = array();
  $i=0;
  foreach($data_items_void1 as $arr){
     $data_items_void['Count'] = strval(round($arr['Count'],2));
     $data_items_void['Amount'] = strval(round($arr['Amount'],2));
     $i++;
  } 


  $data_items_order = DB::table('sales_header')
  ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Amount'))
  ->where('sales_header.CompanyID','=',$company)
  ->where('sales_header.ChainID','=',$chin_id)
  ->where('sales_header.LocationID','=',$location_id)
  ->where('sales_header.VoucherDate','=',$date_only)
  ->where('sales_header.VoucherStatus','LIKE','%S%')
  ->groupby('VoucherNo')
  ->get();
  $data_items_order1 = json_decode(json_encode($data_items_order), true);
  $data_items_order = array();
  $i=0;
  foreach($data_items_order1 as $arr){
     $data_items_order['Count'] = strval(round($arr['Count'],2));
     $data_items_order['Amount'] = strval(round($arr['Amount'],2));
     $i++;
  } 
  //----------------------------------------------Location---------------------------------------------------------------
   $location=DB::table('sales_header')
       ->select('LocationId')
       ->where('ChainId','=',$chin_id)
       ->groupBy('LocationId')
       ->get(); 
      // print_r($location);
      
      // print_r($location); exit();
       $location = json_decode(json_encode($location), true);
       $location[1]['LocationId'] = 'AMP';
       $location[2]['LocationId'] ='RGK';
       $location[3]['LocationId'] = 'FRG';
//----------------------------------------------Chain---------------------------------------------------------------
   $chain=DB::table('sales_header')
      ->select('ChainId')
      ->where('CompanyId','=',$company)
      ->groupBy('ChainId')
      ->get(); 
      $chain = json_decode(json_encode($chain), true);
      $chain[1]['ChainId'] = 'RTT';
      $chain[2]['ChainId'] ='ERD';
      $chain[3]['ChainId'] = 'WSR';
 



  echo json_encode(["data"=>$userdata,"Sales by Category 1"=>$data_category1,"Category1_total"=>$data_category1_total,"Sales by Category 2"=>$data_category2,"Category2_total"=>$data_category2_total,"Sales by Items"=>$items,"Items_total"=>$data_payment_total,"Sales by Order Type"=>$data_ordertype,"Order_type_total"=>$data_ordertype_total,"Hourly Sale"=>$data_hourly_sale,"Credit Customer"=>$data_credit_customer,"Total Sales"=>$data_total_sales,"Delivery Charges"=>$data_delivery_charge,"Service Charges"=>$data_service_charge,"Gross Sales"=>$data_gross_sales,"Staff Meals"=>$data_staff_meal,"Discount"=>$data_discount,"Taxable Values"=>$data_tax,"Net Sales"=>$data_net_sales,"Credit Sales"=>$data_creditsales,"Actual Banking"=>$data_actualbanking,"Locations"=>$data_locationlist,"Item Void"=>$data_items_void,"Item Order"=>$data_items_order,"Visa Card"=>$visa_amount,"Master Amount"=>$master_amount,"Payment Card"=>$data_paymenttype,'Location'=>$location,"Chain"=>$chain]);
 }
 
 public function all_dataonchange(Request $request){

   $datefrom=$request->datefrom;
   $dateto=$request->dateto;
   $chin_id = $request->chain;
   $location_id =$request->location;
   $pass   = $request->pass;
 
  if($datefrom > $dateto){
     $datef=$datefrom;
     $datefrom = $dateto;
     $dateto   = $datef;
   }

   
   @$location =explode(',',$location_id);
   if(!empty($location)){
     foreach($location as $loc){
       if($loc!='') $locationlist[]=$loc;
     }
   }
 
 
 @$company_id = DB::table('local_users')
 ->select('CompanyID','user_name')
 ->where('LoginPin','=',$request->pass)
 ->get()->toArray(); 
 
 $company = @$company_id[0]->CompanyID;
 $username =@$company_id[0]->user_name;
 $userdata =array('user_name'=>$username,'CompanyID'=>$company,'ChainID'=>$chin_id,'LocationID'=>$location_id);
 // $company=$company_id[0]['CompanyID'];
 
 // -----------------order type----------------------
 $data_ordertype = DB::table('sales_header')
 ->select('OrderType',DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as Sales'),DB::raw('round(sum(GuestCount),3) as Guest'),DB::raw('COUNT(VoucherNo) as Transaction'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->groupBy('OrderType')
 ->get(); 
 $data_ordertype1 = json_decode(json_encode($data_ordertype), true);
 $i=0;
 $data_ordertype=array();
 foreach($data_ordertype1 as $arr){
  $data_ordertype[$i]['Transaction'] = strval(round($arr['Transaction'],2));
  $data_ordertype[$i]['Guest'] = strval(round($arr['Guest'],2));
  $data_ordertype[$i]['Sales'] = strval(round($arr['Sales'],2));
  $i++;
 } 
 
 
 $data_ordertype_total = DB::table('sales_header')
 ->select(DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as Sales'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->get(); 
 $data_ordertype_tota2 = json_decode(json_encode($data_ordertype_total), true);
 $i=0;
 $data_ordertype_total = array();
 foreach($data_ordertype_tota2 as $arr1){
 
 $data_ordertype_total['Sales'] = strval(round($arr1['Sales'],2));
 
 } 
 
 // ---------------Category 1-----------------------
 //   $query_category1=$this->db->query("select CategoryDes1, sum(SellingPrice) as sales, sum(Qty) as Guests from sales_line where CompanyID='$company' and DATE(VoucherDate) = '$date_only' group by CategoryDes1");
 //   $data_category1=$query_category1->result();
 
 $data_category1 = DB::table('sales_line')
 ->select('CategoryDes1',DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(sum(Qty),3) as Guest'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->groupBy('CategoryDes1')
 ->get(); 
 $data_category11 = json_decode(json_encode($data_category1), true);
 $i=0;
 $data_category1 = array();
 foreach($data_category11 as $arr){
 $data_category1[$i]['CategoryDes1'] = $arr['CategoryDes1'];
 $data_category1[$i]['Guests'] = strval(round($arr['Guest'],2));
 $data_category1[$i]['sales'] = strval(round($arr['sales'],2));
 $i++;
 } 
 
 $data_category1_total = DB::table('sales_line')
 ->select(DB::raw('COUNT(DISTINCT VoucherNo) as Guests'),DB::raw('round(SUM(SellingPrice),3) as sales'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->get(); 
 $data_category1_total1 = json_decode(json_encode($data_category1_total), true);
 $data_category1_total = array();
 $i=0;
 foreach($data_category1_total1 as $arr){
 $data_category1_total['Guests'] = strval($arr['Guests']);
 $data_category1_total['sales'] = strval(round($arr['sales'],2));
 $i++;
 } 
 
 // --------------Payment---------------------------
 
 $payment_type = DB::table('sales_payment')
 ->select('PaymentType as Payment Type',DB::raw('round(SUM(Amount),3) as Amount'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->groupBy('PaymentType')
 ->get(); 
 $payment_type=array("CASH","VISA CARD",'MASTER CARD','STAFF DISCOUNT 30','ONLINE PAYMENTS');
 
    for ($i=0; $i < count($payment_type) ; $i++) { 
    

        @$query_paymenttype = DB::table('sales_payment')
        ->select('PaymentType as Payment Type',DB::raw('round(SUM(Amount),3) as Amount'))
        ->where('CompanyID','=',$company)
        ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
        ->where('PaymentType','=',$payment_type[$i])
        ->groupBy('PaymentType')
        ->get(); 
        
        $quer = @$query_paymenttype[0]->Amount;
        $id=2;
        if(($payment_type[$i] == 'VISA CARD') || ($payment_type[$i] == 'MASTER CARD') || ($payment_type[$i] == 'ONLINE PAYMENTS')){
            $id=1;
        }else if($payment_type[$i] == 'CASH'){
            $id=2;
        }else if($payment_type[$i] == 'STAFF DISCOUNT 30'){
            $id=3;
        }
        $data_paymenttype[]=array("Group Id"=>$id,"Payment Type"=>$payment_type[$i],"Amount"=>strval(round($quer,2)));
        
      }
 $visa_amount['Visa Amount']=strval(round($data_paymenttype[1]['Amount'],2));
 $master_amount['Master Amount']=strval(round($data_paymenttype[2]['Amount'],2));
 $data_visamastertotal[]=array("visa_master_total"=>strval(round($visa_amount+$master_amount,2)));
 
 // ---------------Category 2-----------------------
 $data_category2 = DB::table('sales_line')
 ->select('CategoryDes2',DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->groupBy('CategoryDes2')
 ->get(); 
 $data_category12 = json_decode(json_encode($data_category2), true);
 $i=0;
 $data_category2 = array();
 foreach($data_category12 as $arr){
 $data_category2[$i]['CategoryDes2'] = $arr['CategoryDes2'];
 $data_category2[$i]['Qty'] = strval(round($arr['Quantity'],2));
 $data_category2[$i]['SellingPrice'] = strval(round($arr['sales'],2));
 $i++;
 }
 
 $data_category2_total= DB::table('sales_line')
 ->select(DB::raw('round(SUM(SellingPrice),3) as sales'),DB::raw('round(SUM(Qty),3) as Quantity'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->get(); 
 $data_category2_total2 = json_decode(json_encode($data_category2_total), true);
 $data_category2_total = array();
 $i=0;
 foreach($data_category2_total2 as $arr){
 $data_category2_total['Quantity'] = strval(round($arr['Quantity'],2));
 $data_category2_total['price'] = strval(round($arr['sales'],2));
 $i++;
 } 
 
 
 // -----------------Items--------------------------
 $categorydes1_array = DB::table('sales_line')
 ->select('CategoryDes1')
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->distinct('CategoryDesc1')
 ->get()->toArray(); 
 //print_r($categorydes1_array); exit();
 $i=0;
 foreach($categorydes1_array as $key => $value) {   
 $query_items = DB::table('sales_line')
 ->select('ItemDesc as item',DB::raw('round(SUM(Qty),3) as quantity'),DB::raw('round(SUM(SellingPrice),3) as price'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('CategoryDes1','=',$value->CategoryDes1)
 ->groupBy('ItemDesc')
 ->get(); 
 $query_items1 = json_decode(json_encode($query_items), true);
 $i=0;
 $query_items = array();
 foreach($query_items1 as $arr){
 $query_items[$i]['item'] = $arr['item'];
 $query_items[$i]['quantity'] = strval(round($arr['quantity'],2));
 $query_items[$i]['price'] = strval(round($arr['price'],2));
 $i++;
 }
 $items[]=array("Category"=>$value->CategoryDes1,"items"=>$query_items);
 $i=$i+1;
 }   
 
 $data_payment_total = DB::table('sales_line')
 ->select(DB::raw('round(SUM(Qty),3) as Quantity'),DB::raw('round(SUM(SellingPrice),3) as Price'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->get(); 
 
 $data_payment_total1 = json_decode(json_encode($data_payment_total), true);
 $data_payment_total = array();
 $i=0;
 foreach($data_payment_total1 as $arr){
 $data_payment_total['Quantity'] = strval(round($arr['Quantity'],2));
 $data_payment_total['Price'] = strval(round($arr['Price'],2));
 $i++;
 } 
 
 //---------------------------------Hourly Sale---------------------------
 $query_items = DB::table('sales_header')
 ->select(DB::raw('hour( StartTime ) as Hour'),DB::raw('COUNT(GuestCount) as Guest'),DB::raw('COUNT(DISTINCT VoucherNo) as Transcation'),DB::raw('SUM(NetSalesAfterDiscountAndTax) as Sales'))
 ->where('CompanyID','=',$company)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('ChainID','=',$chin_id)
 ->groupBy(DB::raw('hour(StartTime)'),DB::raw('day(StartTime)'))
 ->get(); 
 $i=0;
 $data_hourly_sale=array();
 foreach($query_items as $data){
 
 $to =$data->Hour+1;
 $data_hourly_sale[$i]['Hours']=strval($data->Hour.' to '.$to);
 $data_hourly_sale[$i]['Guest']=strval(round($data->Guest,2));
 $data_hourly_sale[$i]['Transcation']=strval(round($data->Transcation,2));
 $data_hourly_sale[$i]['Sales']=strval(round($data->Sales,2));
 $i++;
 }
 
 //------------------------------------Credit Customer------------------------------------------
//  $data_credit_customer= DB::table('credit_customer')
//  ->select('CustomerID','CustomerName','Balance as Amount')
//  ->where('CompanyID','=',$company)
//  ->where('ChainID','=',$chin_id)
//  ->get(); 
//  $data_credit_customer1 = json_decode(json_encode($data_credit_customer), true);
//  $data_credit_customer = array();
//  $i=0;
//  $data_credit_customer = array();
//  foreach($data_credit_customer1 as $arr){
//  $data_credit_customer[$i]['Amount'] = strval(round($arr['Amount'],2));
//  $i++;
//  } 
 $data_credit_customer= DB::table('sales_payment2')
    ->select('CustomerName',DB::raw('SUM(Amount) as Amount'))
    ->where('CompanyID','=',$company)
    ->where('ChainID','=',$chin_id)
    ->groupBy('CustomerName')
    ->get(); 
 
 //------------------------Sales Summary----------------------------------------------------------
 
 //------------------------Total Sales----------------------------------------------------------
 
 $data_total_sales= DB::table('sales_line')
 ->select(DB::raw('round(SUM(Qty*SellingPrice),3) as TotalSales'))
 ->where('CompanyID','=',$company)
 ->where('ChainID','=',$chin_id)
 ->whereIn('LocationID',$locationlist)
 ->where('SalesStatus','LIKE','%S%')
 ->get(); 
 $data_total_sales1 = json_decode(json_encode($data_total_sales), true);
 $data_total_sales = array();
 $i=0;
 foreach($data_total_sales1 as $arr){
 $data_total_sales['TotalSales'] = strval(round($arr['TotalSales'],2));
 $i++;
 } 
 
 //------------------------Delivery Charges----------------------------------------------------------
 
 $data_delivery_charge= DB::table('sales_header')
 ->select(DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as DeliveryCharges'))
 ->where('CompanyID','=',$company)
 ->where('ChainID','=',$chin_id)
 ->whereIn('LocationID',$locationlist)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('VoucherStatus','LIKE','%S%')
 ->where('OrderType','=','DL')
 ->get(); 
 
 $data_delivery_charge1 = json_decode(json_encode($data_delivery_charge), true);
 $data_delivery_charge = array();
 $i=0;
 foreach($data_delivery_charge1 as $arr){
 $data_delivery_charge['DeliveryCharges'] = strval(round($arr['DeliveryCharges'],2));
 $i++;
 } 
 
 
 
 //------------------------Service Charges----------------------------------------------------------
 
 $data_service_charge= DB::table('sales_header')
 ->select(DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as ServiceCharges'))
 ->where('CompanyID','=',$company)
 ->where('ChainID','=',$chin_id)
 ->whereIn('LocationID',$locationlist)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('VoucherStatus','LIKE','%S%')
 ->where('OrderType','!=','DL')
 ->get();  
 $data_service_charge1 = json_decode(json_encode($data_service_charge), true);
 $data_service_charge = array();
 $i=0;
 foreach($data_service_charge1 as $arr){
 $data_service_charge['ServiceCharges'] = strval(round($arr['ServiceCharges'],2));
 $i++;
 } 
 
 
 //------------------------Gross Sales----------------------------------------------------------
 
 
 $data_gross_sales['Gross Sale'] =strval(round($data_total_sales['TotalSales']+$data_delivery_charge['DeliveryCharges']+$data_service_charge['ServiceCharges'],2));
 //------------------------Staff Meal----------------------------------------------------------
 $data_staff_meal= DB::table('sales_line')
 ->select(DB::raw('round(SUM(Qty*SellingPrice),3) as StaffMeal'))
 ->where('sales_line.CompanyID','=',$company)
 ->where('sales_line.ChainID','=',$chin_id)
 ->whereIn('sales_line.LocationID',$locationlist)
 ->whereBetween('sales_line.VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->leftJoin("sales_header",function($join){
 $join->on('sales_header.VoucherNo','=','sales_line.VoucherNo')
 ->on('sales_header.VoucherDate','=','sales_line.VoucherDate')
 ->on('sales_line.CompanyID','=','sales_header.CompanyID')
 ->on('sales_line.ChainID','=','sales_header.ChainID');
 })  
 ->get();
 
 $data_staff_meal1 = json_decode(json_encode($data_staff_meal), true);
 $data_staff_meal = array();
 $i=0;
 foreach($data_staff_meal1 as $arr){
 $data_staff_meal['StaffMeal'] = strval(round($arr['StaffMeal'],2));
 $i++;
 } 
 
 //------------------------Discount----------------------------------------------------------
  $data_discount= DB::table('sales_payment')
  ->select(DB::raw('round(SUM(Amount),3) as Discount'))
  ->whereBetween('sales_header.VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
  ->whereIn('sales_payment.LocationID',$locationlist)
  ->leftJoin("sales_header",function($join){
  $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
  ->on('sales_header.CompanyID','=','sales_payment.CompanyID')
  ->on('sales_header.ChainID','=','sales_payment.ChainID');
  }) 
  // ->leftJoin("sales_payment_types",function($join1){
  // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
  //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
  //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
  // })  
  ->where(array('sales_header.CompanyID'=>$company,'sales_header.VoucherStatus'=>'S','sales_header.ChainID'=>$chin_id))
  ->where('OrderType','!=','SM')
  // ->where('sales_payment_types.IsDiscountCard'=>'1')
  ->get();
  $data_discount1 = json_decode(json_encode($data_discount), true);
  $data_discount = array();
  $i=0;
  foreach($data_discount1 as $arr){
  $data_discount['Discount'] = strval(round($arr['Discount'],2));
  $i++;
  }
 //------------------------Taxable Value----------------------------------------------------------
 $data_tax= DB::table('sales_header')
 ->select(DB::raw('round(SUM(ServiceTaxAmount+SalesTaxAmount),3) as TaxableValue'))
 ->where('CompanyID','=',$company)
 ->where('ChainID','=',$chin_id)
 ->whereBetween('VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('VoucherStatus','LIKE','%S%')
 ->get(); 
 $data_tax1 = json_decode(json_encode($data_tax), true);
 $data_tax = array();
 $i=0;
 foreach($data_tax1 as $arr){
 $data_tax['TaxableValue'] = strval(round($arr['TaxableValue'],2));
 $i++;
 } 
 
 //------------------------Net Sales----------------------------------------------------------
 $data_net_sales['Net Sales'] =strval(round($data_gross_sales['Gross Sale']-$data_staff_meal['StaffMeal']-$data_discount['Discount'],3));
 
 //------------------------PayOut----------------------------------------------------------
 $data_payout['Payout']='0';
 //------------------------Credit Sales----------------------------------------------------------
 $data_creditsales = DB::table('sales_payment')
 ->select(DB::raw('round(SUM(Amount),3) as CreditSales'))
 ->where('sales_header.CompanyID','=',$company)
 ->where('sales_header.ChainID','=',$chin_id)
 ->whereBetween('sales_header.VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
   ->whereIn('sales_header.LocationID',$locationlist)
 ->where('sales_header.VoucherStatus','LIKE','%S%')
 ->leftJoin("sales_header",function($join){
 $join->on('sales_header.VoucherNo','=','sales_payment.VoucherNo')
 ->on('sales_header.VoucherDate','=','sales_payment.VoucherDate')
 ->on('sales_payment.CompanyID','=','sales_header.CompanyID')
 ->on('sales_payment.ChainID','=','sales_header.ChainID')
 ->on('sales_payment.LocationID','=','sales_header.LocationID');
 }) 
 // ->leftJoin("sales_payment_types",function($join1){
 // $join1->on('sales_payment.CompanyID','=','sales_payment_types.CompanyID')
 //     ->on('sales_payment.ChainID','=','sales_payment_types.ChainID')
 //     ->on('sales_payment.PaymentType','=','sales_payment_types.PaymentType');
 // })  
 // ->where('sales_payment_types.IsCreditCard'=>'1')
 //->where('sales_payment_types.IsDiscountCard','!=','1')
 ->where('OrderType','!=','SM')
 ->get(); 
 $data_creditsales1 = json_decode(json_encode($data_creditsales), true);
 $data_creditsales = array();
 $i=0;
 foreach($data_creditsales1 as $arr){
 $data_creditsales['CreditSales'] = strval(round($arr['CreditSales'],2));
 $i++;
 } 
 $data_locationlist= DB::table('sales_header')
 ->select('LocationID')
 ->groupby('LocationID')
 ->get(); 
 
 //------------------------Actual Banking----------------------------------------------------------
 $data_actualbanking['Actual Banking'] =strval(round($data_net_sales['Net Sales']-$data_creditsales['CreditSales']-$data_payout['Payout'],3));
 
 //------------------------Voids----------------------------------------------------------
 $data_items_void = DB::table('sales_header')
 ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as Amount'))
 ->where('sales_header.CompanyID','=',$company)
 ->where('sales_header.ChainID','=',$chin_id)
 ->whereIn('sales_header.LocationID',$locationlist)
 ->whereBetween('sales_header.VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('sales_header.VoucherStatus','LIKE','%V%')
 ->groupby('VoucherNo')
 ->get();
 $data_items_void1 = json_decode(json_encode($data_items_void), true);
 $data_items_void = array();
 $i=0;
 foreach($data_items_void1 as $arr){
 $data_items_void['Count'] = strval(round($arr['Count'],2));
 $data_items_void['Amount'] = strval(round($arr['Amount'],2));
 $i++;
 } 
 
 
 $data_items_order = DB::table('sales_header')
 ->select(DB::raw('COUNT(VoucherNo) as Count'),DB::raw('round(SUM(NetSalesAfterDiscountAndTax),3) as Amount'))
 ->where('sales_header.CompanyID','=',$company)
 ->where('sales_header.ChainID','=',$chin_id)
 ->whereIn('sales_header.LocationID',$locationlist)
 ->whereBetween('sales_header.VoucherDate', [$datefrom." 00:00:00",$dateto." 23:59:59"])
 ->where('sales_header.VoucherStatus','LIKE','%S%')
 ->groupby('VoucherNo')
 ->get();
 $data_items_order1 = json_decode(json_encode($data_items_order), true);
 $data_items_order = array();
 $i=0;
 foreach($data_items_order1 as $arr){
 $data_items_order['Count'] = strval(round($arr['Count'],2));
 $data_items_order['Amount'] = strval(round($arr['Amount'],2));
 $i++;
 } 
 //----------------------------------------------Location---------------------------------------------------------------
   $location=DB::table('sales_header')
       ->select('LocationId')
       ->where('ChainId','=',$chin_id)
       ->groupBy('LocationId')
       ->get(); 
      // print_r($location);
      
      // print_r($location); exit();
       $location = json_decode(json_encode($location), true);
       $location[1]['LocationId'] = 'AMP';
       $location[2]['LocationId'] ='RGK';
       $location[3]['LocationId'] = 'FRG';
//----------------------------------------------Chain---------------------------------------------------------------
   $chain=DB::table('sales_header')
      ->select('ChainId')
      ->where('CompanyId','=',$company)
      ->groupBy('ChainId')
      ->get(); 
      $chain = json_decode(json_encode($chain), true);
      $chain[1]['ChainId'] = 'RTT';
      $chain[2]['ChainId'] ='ERD';
      $chain[3]['ChainId'] = 'WSR';
 
 
 echo json_encode(["data"=>$userdata,"Sales by Category 1"=>$data_category1,"Category1_total"=>$data_category1_total,"Sales by Category 2"=>$data_category2,"Category2_total"=>$data_category2_total,"Sales by Items"=>$items,"Items_total"=>$data_payment_total,"Sales by Order Type"=>$data_ordertype,"Order_type_total"=>$data_ordertype_total,"Hourly Sale"=>$data_hourly_sale,"Credit Customer"=>$data_credit_customer,"Total Sales"=>$data_total_sales,"Delivery Charges"=>$data_delivery_charge,"Service Charges"=>$data_service_charge,"Gross Sales"=>$data_gross_sales,"Staff Meals"=>$data_staff_meal,"Discount"=>$data_discount,"Taxable Values"=>$data_tax,"Net Sales"=>$data_net_sales,"Credit Sales"=>$data_creditsales,"Actual Banking"=>$data_actualbanking,"Locations"=>$data_locationlist,"Item Void"=>$data_items_void,"Item Order"=>$data_items_order,"Visa Card"=>$visa_amount,"Master Amount"=>$master_amount,"Payment Card"=>$data_paymenttype,"Location"=>$location,"Chain"=>$chain]);
 }
}