<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
   

    public function records()
    {   
        // Read File

        $jsonString = file_get_contents(base_path('resources/lang/data.json'));

        $data = json_decode($jsonString, true);

        return response()->json(['data' => $data]);
        
    }

    public function manageRecords(Request $request)
    {   
        //Get the page number
        $page = (empty($request->input('page')) || $request->input('page')<1)?1:$request->input('page');
        $page_to_skip = (empty($page) || $page<1)?0: $page-1;
        $records_per_page=10;
        // Read File
        $jsonString = file_get_contents(base_path('resources/lang/data.json'));

        $data = json_decode($jsonString, true);
        $totalRecords=count($data);

        $previousPage='';
        $nextPage='';

        if(!empty($page) && $page>1){
            $previousPage=$page-1;
        }

        if(!empty($totalRecords) && ceil($totalRecords/$records_per_page)>$page){
            $nextPage=$page+1;
        }
        if($page>ceil($totalRecords/$records_per_page)){
            return response()->json(
            [
                'message'=>'Page number is not valid'
            ],400 ); 
        }

        if($page==ceil($totalRecords/$records_per_page)){
            $records_per_page=$totalRecords%$records_per_page;
        }

        $sliced_data =array_slice($data, $page_to_skip*$records_per_page, $records_per_page);

        $ids=[];
        $open=[];
        $closed=[];

        for($i=0;$i<$records_per_page;$i++){
            array_push($ids,$sliced_data[$i]['id']);
            //if deposition is open push in open array
            if($sliced_data[$i]['disposition']=='open'){
                array_push($open,$sliced_data[$i]);
            }
            //if deposition is closed push in closed array
            if($sliced_data[$i]['disposition']=='closed'){
                array_push($closed,$sliced_data[$i]);
            }
            //sif all ids in ids array
            array_push($ids,$sliced_data[$i]['id']);
        }


        return response()->json(
            [
                'data' =>$sliced_data,
                'ids'=>$ids,
                'open'=>$open,
                'closed'=>$closed,
                'closedCount'=>count($closed),
                'previousPage'=>$previousPage,
                'nextPage'=>$nextPage,
                'message'=>'success'
            ]); 
        
    }
}
