<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\User2;
use Illuminate\Support\Str;
use Auth;
use DB;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Passport\Token;

class Controller extends BaseController
{   
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

// -------------------------------------LOGIN & OUT----------------------------------------------------------------

  
      public function superAdminLogin(Request $request){
        $user=user2::where('adminCode',$request->admincode)->first();
        if($user){
            $success['details']=Auth::user();
            // $success['details']=Auth::user()->all()->toArray();
            $success['token']=$user->createToken('MyApp')->accessToken;
            return response()->json(['details'=>$success]);
        }

    }


// -----------------------------------------------------------------------------------------------
    public function adminDashboardData(){
        $company=DB::table('company')->select(DB::raw('COUNT(CompanyID) as companies'))->get();
        $chain=DB::table('chain')->select(DB::raw('COUNT(ChainId) as chains'))->get();
        $users=DB::table('user')->select(DB::raw('COUNT(user_id) as users'))->get();
        $location=DB::table('location')->select(DB::raw('COUNT(LocationId) as locations'))->get();
        return response()->json(['company'=>$company,'chains'=>$chain,'users'=>$users,'locations'=>$location]);
    }


    public function userTableDetails(Request $request){
             $user_data=DB::table('user')->join('company','company.CompanyID','=','user.companyid')->select(["user.*","company.CompanyName"])->where('isAdmin','!=',1)->get();
            return response()->json([
                'details'=>$user_data,
            ]);
    }

    public function companyTableDetails(Request $request){
         $user_data=DB::table('company')->select("*")->get();
        return response()->json([
            'details'=>$user_data,
        ]);
    }


    public function register_company(Request $request){
        // id=$request->id;
        $id=$request->chainid;
        $image=$request->image;
        if($image!=""){
            define('UPLOAD_DIR', 'public/images/companies');
    		$img = $request->image;
    		$img = str_replace('data:image/png;base64,', '', $img);
    		$img = str_replace(' ', '+', $img);
    		$data = base64_decode($img);
    		$file = UPLOAD_DIR . str::random(10). '.png';
    		$success = file_put_contents($file, $data);
            $comp_insert=array(
                'CompanyID'=>$request->companyid,
                'CompanyName'=>$request->companyname,
                'Contact'=>$request->contact,
                'Address1'=>$request->address1,
                'Address2'=>$request->address2,
                'City'=>$request->city,
                'Country'=>$request->country,
                'Phone1'=>$request->phone,
                'FaxNo'=>$request->fax,
                'Email'=>$request->email,
                'Website'=>$request->website,
                'CurrencyId'=>$request->currency,
                'RetainedAccountId'=>$request->retainedaccount,
                'AccuRetainedAccountId'=>$request->accuretainedaccount,
                'SysErrorAccount'=>$request->erroraccount,
                'SysErrorTolerance'=>$request->errortolerance,
                'ManualPath'=>$request->manual,
                'CommPath'=>$request->comm,
                'SopPostingType'=>$request->postingtype,
                'SopQtyMinor'=>$request->qtyminor,
                'CompanyLogo'=>$file,
            );
            DB::table('company')->insert($comp_insert);
        }
        else{
            $comp_insert=array(
                'CompanyID'=>$request->companyid,
                'CompanyName'=>$request->companyname,
                'Contact'=>$request->contact,
                'Address1'=>$request->address1,
                'Address2'=>$request->address2,
                'City'=>$request->city,
                'Country'=>$request->country,
                'Phone1'=>$request->phone,
                'FaxNo'=>$request->fax,
                'Email'=>$request->email,
                'Website'=>$request->website,
                'CurrencyId'=>$request->currency,
                'RetainedAccountId'=>$request->retainedaccount,
                'AccuRetainedAccountId'=>$request->accuretainedaccount,
                'SysErrorAccount'=>$request->erroraccount,
                'SysErrorTolerance'=>$request->errortolerance,
                'ManualPath'=>$request->manual,
                'CommPath'=>$request->comm,
                'SopPostingType'=>$request->postingtype,
                'SopQtyMinor'=>$request->qtyminor,
            );

            DB::table('company')->insert($comp_insert);
            return response()->json(["message"=>"registered successfully"]);
        }    
    }
    public function updateComp(Request $request){
        $image=$request->image;
        if($image!=""){
            define('UPLOAD_DIR', 'public/images/companies');
    		$img = $request->image;
    		$img = str_replace('data:image/png;base64,', '', $img);
    		$img = str_replace(' ', '+', $img);
    		$data = base64_decode($img);
    		$file = UPLOAD_DIR . str::random(10). '.png';
    		$success = file_put_contents($file, $data);
            $comp_update=array(
                'CompanyID'=>$request->comapnyid,
                'CompanyName'=>$request->companyname,
                'Contact'=>$request->contact,
                'Address1'=>$request->address1,
                'Address2'=>$request->address2,
                'City'=>$request->city,
                'Country'=>$request->country,
                'Phone1'=>$request->phone,
                'FaxNo'=>$request->fax,
                'Email'=>$request->email,
                'Website'=>$request->website,
                'CurrencyId'=>$request->currency,
                'RetainedAccountId'=>$request->retainedaccount,
                'AccuRetainedAccountId'=>$request->accuretainedaccount,
                'SysErrorAccount'=>$request->erroraccount,
                'SysErrorTolerance'=>$request->errortolerance,
                'ManualPath'=>$request->manual,
                'CommPath'=>$request->comm,
                'SopPostingType'=>$request->postingtype,
                'SopQtyMinor'=>$request->qtyminor,
                'CompanyLogo'=>$file,
             );
            $comp_update=DB::table('company')->where('id',$request->id)->update($comp_update);
            return response()->json(['message'=>"updated successfully"]);
        }
        else{
             $comp_update=array(
                'CompanyID'=>$request->comapnyid,
                'CompanyName'=>$request->companyname,
                'Contact'=>$request->contact,
                'Address1'=>$request->address1,
                'Address2'=>$request->address2,
                'City'=>$request->city,
                'Country'=>$request->country,
                'Phone1'=>$request->phone,
                'FaxNo'=>$request->fax,
                'Email'=>$request->email,
                'Website'=>$request->website,
                'CurrencyId'=>$request->currency,
                'RetainedAccountId'=>$request->retainedaccount,
                'AccuRetainedAccountId'=>$request->accuretainedaccount,
                'SysErrorAccount'=>$request->erroraccount,
                'SysErrorTolerance'=>$request->errortolerance,
                'ManualPath'=>$request->manual,
                'CommPath'=>$request->comm,
                'SopPostingType'=>$request->postingtype,
                'SopQtyMinor'=>$request->qtyminor,
            );
             $comp_update=DB::table('company')->where('id',$request->id)->update($comp_update);
            return response()->json(['message'=>"updated successfully"]);
        }
    }
    
    public function deleteCompany(Request $request){
        DB::table('company')->where('id',$request->id)->delete();
        return response()->json(["message"=>"deleted sucessfully"]);
    }


    public function updateUser(Request $request){
        $password=bcrypt($request->password);
        $user_update=DB::table('user')->where('user_id',$request->userid)
        ->update([
            'user_id'=>$request->userid,
            'user_name'=>$request->username,
            'companyid'=>$request->companyid,
            'email'=>$request->email,
            'password'=>$password,
            'password_ref'=>$request->password
        ]);
        $chain=DB::table('user')->join('company','user.companyid','=','company.CompanyID')->pluck('CompanyName');
        foreach ($chain as $val){
            $companyname=$val;
        }
        DB::table('usercompanyaccess')->where('UserID',$request->id)->update([
            'UserID'=>$request->userid,
            'CompanyID'=>$request->companyid,
            'CompanyName'=>$companyname,
        ]);
        return response()->json(["message"=>"updated successfully"]);
    }
    
    public function register_user(Request $request){
        $password=bcrypt($request->password);
        $user_insert=DB::table('user')
        ->insert([
            'user_id'=>$request->userid,
            'user_name'=>$request->username,
            'companyid'=>$request->companyid,
            'email'=>$request->email,
            'password'=>$password,
            'password_ref'=>$request->password,
            "isAdmin"=>0,
            "adminCode"=>NULL
        ]);
        $chain=DB::table('user')->join('company','user.companyid','=','company.CompanyID')->select('CompanyName');
        foreach ($chain as $val){
            $companyname=$val;
        }
        // dd($companyname);exit;
        DB::table('usercompanyaccess')->insert([
            'UserID'=>$request->userid,
            'CompanyID'=>$request->companyid,
            'CompanyName'=>$companyname,
        ]);
        return response()->json(["message"=>"User Registered Successfully"]);
    }

    public function deleteUser(Request $request){
        DB::table('user')->where('id',$request->id)->delete();
        DB::table('usercompanyaccess')->where('UserID',$request->id)->delete();
        return response()->json(["message"=>"deleted successfully"]);
    }

    public function companyAccesstable(Request $request){
        $data=DB::table('usercompanyaccess')
        ->select('UserID','CompanyID','CompanyName')
        ->get();
        return response()->json(["data"=>$data]);
    }
    public function updateCompanyAccess(Request $request){
        $companydet=$request->companyid;
        $companies=DB::table('company')->where('CompanyID',$companydet)->select('CompanyName');
        foreach($companies as $company){
            $companyid=$company;
        }
        DB::table('companyaccess')->where('UserID',$request->userid)->update(["CompanyName"=>$companyname,'CompanyID'=>$companydet]);
        DB::table('user')->where('userid',$request->userid)->update(["companyid"=>$companydet]);
        return response()->json(["message"=>"successfully updated"]);
    }
    
    public function deleteCompanyAccess(Request $request){
        DB::table('companyaccess')->where('userid',$request->userid)->delete();
        DB::table('user')->where('userid','=',$request->userid)->update(['companyid'=>NULL]);
        return response()->json(["message"=>"revoked company access"]);
    }
    
    public function companyList(Request $request){
        $data=DB::table('company')->select('CompanyID','CompanyName')->get();
        return response()->json(["data"=>$data]);
    }
    
    public function regCurrency(Request $request){
        $datacurrency=array(
            $CurrencyId=$request->currencyid,
            $CurrencyDefault=$request->currencydefault,
            $CurrencyName=$request->description,
            $CurrencySymbol=$request->symbol,
            $CurrencyUnit=$request->currencyunit,
            $CurrencyConnect=$request->currencyconnect,
            $CurrencySubUnit=$request->currencysubunit,
            $CurrencyRate=$request->currencyrate,
            $Currencydecimals=$request->currencydecimals,	
        );
        DB::table('currency')->insert($datacurrency);
    }
    public function currencyList(){
        $currencylist=DB::table('currency')->select('CurrencyId')->get();
        return response()->json(['currency'=>$currencylist]);
    }
    
  
    
}