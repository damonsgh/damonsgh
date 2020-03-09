<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\User2;
use Intervention\Image\Facades\Image;
use Auth;
// use App\Http\Controllers\str;
use Illuminate\Support\Str;
use DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Storage;




class UserController extends Controller
{   


    public function secuserLogin(Request $request){
        // print_r($request->all());
        $validator= Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(["message"=>'wrong credentials']);
        }
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
                if(Auth::user()->isAdmin == 1){
                    $userRole = "SuperAdmin";
                }
                else{
                    $userRole= "User";
                }
                $user= Auth::user();
                $success['userdetails']=$user;
                $success['token']=$user->createToken('MyApp')->accessToken;
                return response()->json(["message"=>"login success","UserRole"=>$userRole,"data"=>$success]);
        }
        else{
            return response()->json(["error"=>"unauthorized"]);
        }

    }
    
    public function secuserLogout(){
        // $request->user()->token()->revoke();
        Auth::logout();
        return response()->json(['message'=>"logout success"]);
    }
    
    // -------------------------------------------------------------------
    
    public function viewChains(){
         $chain_data=DB::table('chain')->select("*")->get();
            return response()->json([
                'chain_data'=>$chain_data,
            ]);
    }
    // public function addChain(Request $request){
    //     $company=Auth::user()->company;
    //     DB::table('chain')->insert(['chain_name'=>$request->chainname,'chain_code'=>$request->chaincode,'company_code'=>$company]);  
    //     return response()->json(["message"=>"sucessfully inserted"]);   
    // }

    // public function deleteChain(Request $request){
    //     $id=$request->chaincode;
    //     DB::table('chain')->where('chain_code',$id)->delete();     
    //     return response()->json(["message"=>'successfully deleted']);  
    // }
    // public function updateChain(Request $request){
    //     $id=$request->chaincode;
    //     DB::table('chain')->where('chain_code',$id)->update(['chain_name'=>$request->chainname]);
    //     return response()->json(["message"=>'successfully updated']);  
    // }
    
    public function addChain(Request $request){
        $company=Auth::user()->companyid;
        // if($request->hasFile('image')){
        //     $img= $request->file('image');
        //     $image = time().'.'.$img->getClientOriginalExtension();
        //     $destination = '/public/images/chains/';
        //     $img->move($destination, $image);
        $image=$request->image;
        if($image!=""){
            DB::table('chain')->insert([
                'Chainid'=>$request->chainid,
                'ChainName'=>$request->description,
                'Email'=>$request->email,
                'isVirtualChain'=>$request->isvirtual,
                "cgaontransactions"=>$request->cgaontransac,
                "gcaonguestcount"=>$request->cgaonguestcount,
                'Image'=>$image,
                'CompanyId'=>$company
            ]); 
        }
        else{
            DB::table('chain')->insert([
                'Chainid'=>$request->chainid,
                'ChainName'=>$request->description,
                'Email'=>$request->email,
                'isVirtualChain'=>$request->isvirtual,
                "cgaontransactions"=>$request->cgaontransac,
                "gcaonguestcount"=>$request->gcaonguestcount,
                'CompanyId'=>$company
            ]); 
            return response()->json(["message"=>"sucessfully inserted"]); 
        }
    }
    public function deleteChain(Request $request){
        $id=$request->chainid;
        DB::table('chain')->where('ChainId','=',$id)->delete();   
        DB::table('location')->where('ChainId','=',$id)->delete();  
        return response()->json(["message"=>'successfully deleted']);  
    }

    public function updateChain(Request $request){
        $id=$request->chainid;
         $image=$request->image;
        if($image!=""){
            define('UPLOAD_DIR', 'public/images/chains');
    		$img = $request->image;
    		$img = str_replace('data:image/png;base64,', '', $img);
    		$img = str_replace(' ', '+', $img);
    		$data = base64_decode($img);
    		$file = UPLOAD_DIR . str::random(10). '.png';
    		$success = file_put_contents($file, $data);
                
            DB::table('chain')->where('ChainId',$id)->update([
                'ChainName'=>$request->description,
                'Email'=>$request->email,
                'isVirtualChain'=>$request->isvirtual,
                "cgaontransactions"=>$request->cgaontransac,
                "gcaonguestcount"=>$request->gcaonguestcount,
                "Image"=>$file,
            ]);  
            return response()->json(["message"=>'successfully updated']);
        }
        else{
            DB::table('chain')->where('ChainId',$id)->update([
                'ChainName'=>$request->description,
                'Email'=>$request->email,
                'isVirtualChain'=>$request->isvirtual,
                "cgaontransactions"=>$request->cgaontransac,
                "gcaonguestcount"=>$request->gcaonguestcount,
            ]);  
            return response()->json(["message"=>'successfully updated']);
        }
    }


    public function viewLocations(){
         $loc_data=DB::table('location')->select("*")->get();
            return response()->json([
                'location_data'=>$loc_data,
            ]);
    }
    // public function addLocation(Request $request){
    //     $company=Auth::user()->company;
    //     DB::table('location')->insert(['chain_code'=>$request->chaincode,'location_code'=>$request->loacationcode,'location_name'=>$request->locationname,'company_code'=>$company]);
    //     return response()->json(["message"=>'successfully inserted']);  
    // }
    // public function deleteLocation(Request $request){
    //     $id=$request->locationcode;
    //     DB::table('location')->where('location_code',$id)->delete();
    //     return response()->json(["message"=>'successfully deleted']); 
    // }
    // public function updateLocation(Request $request){
    //     $id=$request->locationcode;
    //     DB::table('location')->where('location_code',$id)->update(['chain_code'=>$request->chaincode,'location_name'=>$request->locationname]);
    //     return response()->json(["message"=>'successfully updated']); 
    // }
    
    public function addLocation(Request $request){
        $company=Auth::user()->companyid;
        $locdata=array(
            'ChainId'       =>$request->chainid,
            'LocationId'    =>$request->loacationid,
            'LocationName'  =>$request->locationname,
            'Contact'       =>$request->contact,
            'Address1'      =>$request->address1,
            'Address2'      =>$request->address2,
            'City'          =>$request->city,
            'Country'       =>$request->country,
            'Phone'         =>$request->phone,
            'Fax'           =>$request->fax,
            'Email'         =>$request->email,
            'Notes'         =>$request->notes,
            'CostCenter'    =>$request->costcenter,
            'LocationType'  =>$request->locationtype,
            'ManageStock'   =>$request->inventory,
            'StartDate'     =>$request->startdate,
            'Service'       =>$request->service,
            'CostCenterType'=>$request->costcentertype,
            'Seats'         =>$request->seats,
            'Area'          =>$request->area,
            'Active'        =>$request->active,
            'Planned'       =>$request->planned,
            'EnableOrderCutoff'=>$request->enablecutoff,
            'OrderCutoff'   =>$request->ordercutof,
            'CompanyId'     =>$company,
        );
        DB::table('location')->insert([$locdata]);
        return response()->json(["message"=>'successfully inserted']);  
    }
    public function deleteLocation(Request $request){
        if($request->locationid!=""){
            DB::table('location')->where('id',$request->id)->delete();
            return response()->json(["message"=>"sucessfully deleted"]);
        }
        else{
            return response()->json(["message"=>"error"]);
        }
        
    }
    public function updateLocation(Request $request){
        $company=Auth::user()->company;
        if($request->locationid!=""){
             $locupdate= [
            'chainid'       =>$request->chainid,
            'LocationName'  =>$request->locationname,
            'Contact'       =>$request->contact,
            'Address1'      =>$request->address1,
            'Address2'      =>$request->address2,
            'City'          =>$request->city,
            'Country'       =>$request->country,
            'Phone'         =>$request->phone,
            'Fax'           =>$request->fax,
            'Email'         =>$request->email,
            'Notes'         =>$request->notes,
            'CostCenter'    =>$request->costcenter,
            'LocationType'  =>$request->locationtype,
            'ManageStock'   =>$request->inventory,
            'StartDate'     =>$request->startdate,
            'Service'       =>$request->service,
            'CostCenterType'=>$request->costcentertype,
            'Seats'         =>$request->seats,
            'Area'          =>$request->area,
            'Active'        =>$request->active,
            'Planned'       =>$request->planned,
            'orderCutoff'   =>$request->ordercutof,    
        ];
        DB::table('location')->where('id',$request->id)->update($locupdate);
        return response()->json(["message"=>'successfully updated']);  
        }
        else{
            return response()->json(["message"=>"error"]);
        }
       
    }


    public function viewLocalUsers(){
         $loc_data=DB::table('local_users')->select("*")->get();
            return response()->json([
                'location_data'=>$loc_data,
            ]);
    }
    // public function addLocalUser(Request $request){
    //     $company=Auth::user()->company;
    //     $data=DB::table('local_users')->insert(['user_id'=>$request->userid,'user_name'=>$request->username,
    //     'user_phone'=>$request->mobile,'user_email'=>$request->email,'UDID'=>$request->udid,'LoginPin'=>$request->loginpin,
    //     "CompanyID"=>$company]);
    //     DB::table('cost_center_access')->insert(['user_id'=>$request->userid,'user_name'=>$request->username,'ChainID'=>$request->chainid,'LocationID'=>$request->locationid]);
    //     return response()->json(["message"=>"successfully registered"]);
    // }
    // public function deleteLocalUser(Request $request){
    //     $id=$request->userid;
    //     $data=DB::table('local_users')->where('user_id',$id)->delete();
    //     return response()->json(["message"=>"successfully deleted"]);
    // }
    
    // public function updateLocalUser(Request $request){
    //     $id=$request->userid;
    //     $data=DB::table('local_users')->where('user_id',$id)->update(['user_name'=>$request->username,'user_phone'=>$request->mobile,
    //     'user_email'=>$request->email,'UDID'=>$request->udid,'LoginPin'=>$request->loginpin]);
    //     return response()->json(["message"=>"successfully updated"]);
    // }
    
    public function addLocalUser(Request $request){
        $company=Auth::user()->companyid;
        $data=DB::table('local_users')->insert([
            'user_id'       =>$request->userid,
            'user_name'     =>$request->username,
            'user_phone'    =>$request->mobile,
            'user_email'    =>$request->email,
            'UDID'          =>$request->udid,
            'LoginPin'      =>$request->loginpin,
            "CompanyID"     =>$company,
        ]);
        return response()->json(["message"=>"successfully registered"]);
    }
    public function deleteLocalUser(Request $request){
        $id=$request->userid;
        $data=DB::table('local_users')->where('user_id',$id)->delete();
        return response()->json(["message"=>"successfully deleted"]);
    }
    public function updateLocalUser(Request $request){
        $id=$request->userid;
        $data=DB::table('local_users')->where('user_id',$id)->update([
            'user_name'     =>$request->username,
            'user_phone'    =>$request->mobile,
            'user_email'    =>$request->email,
            'UDID'          =>$request->udid,
            'LoginPin'      =>$request->loginpin,
        ]);
        return response()->json(["message"=>"successfully updated"]);
    }
    
    public function dashboardData(){
        $chain=DB::table('chain')->select(DB::raw('COUNT(ChainId) as chains'))->get();
        $users=DB::table('user')->select(DB::raw('COUNT(user_id) as users'))->get();
        $location=DB::table('location')->select(DB::raw('COUNT(LocationId) as locations'))->get();
        return response()->json(['chains'=>$chain,'users'=>$users,'locations'=>$location]);
    }


    
    public function costCenterAccessView(Request $request){
        $data=DB::table('chainaccess')->join('locationaccess',function($join)
        {
            $join->on('chainaccess.UserID','=','locationaccess.UserId');
            $join->on('chainaccess.ChainID','=','locationaccess.ChainId');
        })
        ->select('locationaccess.UserId','chainaccess.ChainID','locationaccess.LocationId')
        ->get();
        return response()->json(["data"=>$data]);
    }

    public function addCostCenterAccess(Request $request){
        $companyid=Auth::user()->companyid;
        $userid=$request->userid;
        $username=$request->username;
        $fullaccess=$request->fullaccess;
        if($fullaccess== 1){
            $arraychain=DB::table('chain')->where('CompanyId','=',$companyid)
            ->select('ChainId')->get();
            foreach($arraychain as $chain){
                $arraychain= array(
                    "UserId"        =>$userid,
                    "CompanyId"     =>$companyid,
                    "ChainID"       =>$chain,
                    "FullAccess"    =>$fullaccess,
                );
            }
            $arrayloc=DB::table('location')->where('CompanyId','=',$companyid)
            ->select('LocationId')->get();
            foreach($arrayloc as $location){
                $arrayloc=array( 
                    "UserId"        =>$userid,
                    "CompanyID"     =>$companyid,
                    "ChainId"       =>$chain,
                    "LocationId"    =>$location,
                );
            }
        }
        else{
            $chains=input::get('chains');
            foreach($chains as $chain){
                $chainid=$chain->chainid;
                $chainname=$chain->chainame;
                DB::table('chainaccess')->insert([
                    "UserID"        =>$userid,
                    "CompanyId"     =>$companyid,
                    "ChainID"       =>$chain->chainid,
                    "FullAccess"    =>$fullaccess,
                ]);
                foreach($locations as $loation){
                     DB::table('locationaccess')->insert([
                    "UserId"        =>$userid,
                    "CompanyID"     =>$companyid,
                    "ChainId"       =>$chainid,
                    "LocationId"    =>$location->locationid,
                ]);
                }
            } 
        }
        return response()->json(["message"=>"sucessfully Added"]);
    }

    public function updateCostCenterAccess(Request $request){
        $companyid=Auth::user()->comapnyid;
        $userid=$request->id;
        $fullaccess=$request->fullaccess;
        if($fullaccess== 1){
            $arraychain=DB::table('chain')->where('CompanyId','=',$companyid)
            ->select('ChainId')->get();
            foreach($arraychain as $chain){
                $arraychain[]=[
                    "ChainID"   =>$chain,
                    "FullAccess"=>$fullaccess,
                ];
            }
            $arrayloc=DB::table('location')->where('CompanyId','=',$companyid)
            ->select('LocationId')->get();
            foreach($arrayloc as $location){
                $arrayloc[]=[ 
                    "ChainId"   =>$chain,
                    "LocationId"=>$location,
                ];
            }
        }
        else{
            $chains=input::get('chains');
            foreach($chains as $chain){
                $chainid=$chain->chainid;
                DB::table('chainaccess')->update([
                    "ChainID"       =>$chain->chainid,
                    "FullAccess"    =>$fullaccess,
                ]);
                foreach($locations as $loation){
                     DB::table('locationaccess')->update([
                    "ChainId"       =>$chainid,
                    "LocationId"    =>$location->locationid,
                ]);
                }
            } 
        }
        DB::table('chainaccess')->where('UserID','=',$userid)->update($arraychain);
        DB::table('locationaccess')->where('UserId','=',$userid)->update($arrayloc);
        return response()->json(["message"=>"sucessfully Added"]);
    }

    public function deleteCostCenterAccess(Request $request){
        $userid=$request->userid;
        DB::table('locationaccess')->where('UserId','=',$userid)->delete();
        DB::table('chainaccess')->where('UserID','=',$userid)->delete();
        return response()->json(['message'=>"deleted"]);
    }
     
    // --------------------Lists----------------------------------------
        
    public function chainAndLocationList(Request $request){
        $company=Auth::user()->companyid;
        $i=0;
        $chains=DB::table('chain')->select("ChainId",'ChainName')->where('CompanyId',$company)->get();
        foreach($chains as $chain){
            $data[$i]['chainid']=$chain->ChainId;
            $data[$i]['chainname']=$chain->ChainName;
            $locations=DB::table('location')->select('LocationId','LocationName')->where('ChainId',$chain->ChainId)->get();
            foreach($locations as $location){
                $data[$i]['locations'][]=array("id"=>$location->LocationId,'Name'=>$location->LocationName);
            }
            $i++;
        }
        return response()->json($data);
    }
    
    public function locationList(){
        $locations=DB::table('location')->select('location_code','location_name')->get();
        return response()->json(['locationlist'=>$locations]);
    }
    
}
