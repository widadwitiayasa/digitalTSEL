<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Regional;
use App\Models\Branch;
use App\Models\Cluster;
use App\Models\Service;
use App\Models\Revenue;
use DateTime;

class CreateController extends Controller
{
    public function uploadGET()
    {
        $data['Area'] = Area::get();
        $data['Regional'] = Regional::where('ID_AREA',3)->get();
        $data['Branch'] = Branch::get();
        $data['Cluster'] = Cluster::get();

        return view('admin.input', $data);
    }
    public function uploadPOST(Request $req)
    {
        $idarea = 3;
        $idregional = $req->input('INPUTREGIONAL');
        $idbranch = $req->input('INPUTBRANCH');
        $idcluster = $req->input('INPUTCLUSTER');
        $file = $req->input('fileToUpload');
        $detail = array(
            "area" => $idarea,
            "regional" => $idregional,
            "branch" => $idbranch,
            "cluster" => $idcluster);

        $startdate = $req->input('UPLOADDATE');
        $finishdate = $req->input('FINISHDATE');
        $awal = strtotime($startdate);
        $akhir = strtotime($finishdate);

        $rangedate = $akhir - $awal;
        $rangedate = intval($rangedate/(60*60*24))+1;

        $date = DateTime::createFromFormat('D M d Y', $startdate);
        $date_format = $date->format("Y-m-d");
        $date_formated = explode("-",$date_format);


        $filepath = $this->saveCSV($req,$date_formated,$date);
        $this->readCSV($filepath, $detail, $date_format, $rangedate);
    }
    public function saveCSV($req,$date_formated,$date)
    {
        $taun=$date_formated[0];
        $bulan=$date_formated[1];
        $tanggal=$date_formated[2];
//exit();
        if (!file_exists("storage/".$taun)) {
            mkdir("storage/".$taun, 0777, true);
        }
        if (!file_exists("storage/".$taun."/".$bulan)) {
            mkdir("storage/".$taun."/".$bulan, 0777, true);
        }
        if (!file_exists("storage/".$taun."/".$bulan."/".$tanggal)) {
            mkdir("storage/".$taun."/".$bulan."/".$tanggal, 0777, true);
        }

        $ID=$date->format('Ymd');
        $filetype = explode(".", $req->file('fileToUpload')->getClientOriginalName())[1];
        $filename = $ID.'.'.$filetype;
        $filesize = $req->fileToUpload->getClientSize();
        if($filetype != "csv")
        {

            return redirect('/storage')->with('status', 'Please upload csv file');
        }

        if($filesize > 500000)
        {
            return redirect('/storage')->with('status', 'Please check your file size');        	
        }

// $target_dir = "storage/";
        $filepath = $taun."/".$bulan."/".$tanggal;
        $public_folder = public_path("assets/uploads/".$filepath);
        $old_path = $req->fileToUpload->storeAs($filepath, $filename);
        $new_path = $req->file('fileToUpload')->move($public_folder, $old_path);
        Storage::delete($old_path);
        return $filepath."/".$filename;
    }
    public function readCSV($filepath, $detail, $date_format, $rangedate)
    {
        ini_set('auto_detect_line_endings', TRUE);
        $file = fopen(public_path('assets/uploads/'.$filepath),"r");
        $header = 0;
        $hariini = $date_format;
        while(!feof($file))
        {
            // dd($rangedate);
            $date_format = $hariini;
            $row = fgetcsv($file);
            if($header)//header ini biar ngga ngambil row paling atas di csv
            {
                $newService = Service::where('NAMA','like',$row[0])->first();
                //echo $newService;
                if(empty($newService))
                {
                    $newService = new Service();
                    $newService->NAMA=$row[0];
                    $newService->save();
                }
                $newService = Service::where('NAMA','like',$row[0])->first();
                $idNewService = $newService->ID;
                // dd($newService->ID);
                    //dd($newService->ID);
                for($i=1; $i<=$rangedate; $i++)
                {

                    $query = Revenue::where('ID_SERVICE',$idNewService)
                        ->where('ID_CLUSTER',$detail['cluster'])
                        ->where('DATE',$date_format)->first();
                    $a=0;
                    while($i<=$rangedate && $row[$i]=='' || $i<=$rangedate && $row[$i]==0) {   $i++; $a=1;  }
                    if($a==1) break;
                    else
                    {
                        if(empty($query))
                        {
                            $newRevenue = new Revenue();
                            $newRevenue->ID_SERVICE = $idNewService;
                            $newRevenue->ID_CLUSTER = $detail['cluster'];
                            $newRevenue->DATE = $date_format;
                            $newRevenue->REVENUE = $row[$i];
                            $newRevenue->save();
                        }
                        else
                        {
                            $query = Revenue::where('ID_SERVICE',$idNewService)
                                ->where('ID_CLUSTER',$detail['cluster'])
                                ->where('DATE',$date_format)->update(array('REVENUE'=>$row[$i]));
                        }
                    }
                    
                    
                    $besok = new DateTime($date_format);
                    $besok->modify('+1 day');
                    $date_format = $besok->format("Y-m-d");
                }
            }
            $header = 1;
        }
        fclose($file);
        return true;
    }
}