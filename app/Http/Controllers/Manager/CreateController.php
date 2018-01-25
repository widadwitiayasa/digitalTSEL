<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Regional;
use App\Models\Branch;
use App\Models\Cluster;
use App\Models\Service;
use App\Models\Tree;
use App\Models\Revenue;
use DateTime;
use Carbon\Carbon;
use DB;

class CreateController extends Controller
{
	public function requestGET()
	{
    	// $data['Area'] = Area::get();
    	$data['Regional'] = Regional::where('ID_AREA',3)->get();
    	$data['Branch'] = Branch::get();
    	$data['Cluster'] = Cluster::get();
    	$data['Service'] = Service::get();

    	return view('manager.request', $data);
    }
    public function requestPOST(Request $req)
    {
    	$idarea = 3;
    	// $req->input('INPUTAREA');
    	$idregional = $req->input('INPUTREGIONAL');
    	$idbranch = $req->input('INPUTBRANCH');
    	$idcluster = $req->input('INPUTCLUSTER');
    	$idservice = $req->input('INPUTSERVICE');
    	$tanggalrequest = $req->input('REQUESTDATE');
    	//dd($tanggalrequest);
    	$date = DateTime::createFromFormat('D M d Y', $tanggalrequest);
	    $date_format = $date->format("Y-m-d");
	    $date_formated = explode("-",$date_format);

	    // dd($idcluster);
    	$detail = array(
    		"area" => $idarea,
    		"regional" => $idregional,
    		"branch" => $idbranch,
    		"cluster" => $idcluster);
    	// dd($detail);
	    $data['result'] = app('App\Http\Controllers\Manager\ReadController')->calculate($detail,$date_formated);
	   	$data['detail'] = $detail;
	    if($idarea=='all'){
	    	$data['tipe'] = 'L1';
	    }
	    else if($idcluster!='all' && $idcluster!=null && $req->input('output')=='TOP5'){
	    	$data['tipe'] = 'TOP5';
	    	// dd($data['results']);
	    	return view('manager.chart', $data);
	    }
	    else if($idcluster!='all' && $idcluster!=null){
	    	$data['tipe'] = 'L3';
	    	// dd($data['result']);
	    }
	    else{
	    	$data['tipe'] = 'L2';
	    }
	    // dd($data['result']);
	    return view('manager.report',$data);
    }






    //BUAT BELAJAR QUERY2 DI LARAVEL CEK DIBAWAH







  //   public function calculation($detail,$date_formated)
  //   {
		// $taun=$date_formated[0];
		// $bulan=$date_formated[1];
		// $tanggal=$date_formated[2];
		// $date_post = $taun.'-'.$bulan.'-01';
		// $date_post2 = ($taun-1).'-'.$bulan.'-01';
		// $date_now = $taun.'-'.$bulan.'-'.$tanggal;
		// $date_now2 = ($taun-1).'-'.$bulan.'-'.$tanggal;
		// $date_ytd1 = $taun.'-01'.'-01';
		// $date_ytd2 = ($taun-1).'-01'.'-01';
		// if($bulan=='01')
		// {
		// 	$date_mom1 = ($taun-1).'-12'.'-01';
		// 	$date_mom2 = ($taun-1).'-12'.'-'.$tanggal;
		// }
		// else
		// {
		// 	$date_mom1 = $taun.($bulan-1).'-01';
		// 	$date_mom2 = $taun.($bulan-1).'-'.$tanggal;
		// }


		// if($detail['area']=='allarea')
		// {
		// 	$areas = Area::get();
		// 	$all_area_result = array();
		// 	foreach($areas as $z)
		// 	{
		// 		//ACTUAL
		// 		$wida = Revenue::whereHas('cluster', function($a) use ($detail,$z){
		// 			if($detail['area'] != 'allarea')	$a->where('ID', $z->ID);
		// 			else 	$a->whereHas('branch',function($b) use ($detail,$z){
		// 			if($detail['area'] != 'allarea')	$b->where('ID', $z->ID);
		// 			else 	$b->whereHas('regional',function($c) use ($detail,$z){
		// 			if($detail['area'] != 'allarea')	$c->where('ID', $z->ID);
		// 			else 	$c->whereHas('area', function($d) use($detail, $z){
		// 					$d->where('ID', $z->ID);
		// 					});
		// 				});
		// 			});
		// 		})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 		// dd($z);
		// 		$actual = $wida->sum('REVENUE');


		// 	//YTD
		// 	$hai = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_ytd1)->whereDate('Date','<=',$date_now)->get();
		// 	$YTD1 = $hai->sum('REVENUE');

		// 	$hai2 = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_ytd2)->whereDate('Date','<=',$date_now2)->get();
		// 	$YTD2 = $hai2->sum('REVENUE');

		// 	if($YTD2==0) {$YTD2=1;}

		// 	$YTD = round(((($YTD1/$YTD2)-1)*100),2);

		// 	//YOY
		// 	$ha = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 	$yoy1 = $ha->sum('REVENUE');

		// 	$ha2 = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_post2)->whereDate('Date','<=',$date_now2)->get();
		// 	$yoy2 = $ha2->sum('REVENUE');

		// 	if($yoy2==0) {$yoy2=1;}

		// 	$YOY = round(((($yoy1/$yoy2)-1)*100),2);

		// 	//MOM
		// 	$widad = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 	$mom1 = $widad->sum('REVENUE');
		// 	// dd($widad);

		// 	$widad2 = Revenue::whereHas('cluster',function($a) use ($detail,$z){
		// 		$a->whereHas('branch',function($b) use ($detail,$z){
		// 			$b->whereHas('regional',function($c) use ($detail,$z){
		// 				$c->whereHas('area',function($d) use ($detail,$z){
		// 					$d->where('ID',$z->ID);
		// 				});
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_mom1)->whereDate('Date','<=',$date_mom2)->get();
		// 	$mom2 = $widad2->sum('REVENUE');

		// 	if($mom2==0) {$mom2=1;}

		// 	$MOM = round(((($mom1/$mom2)-1)*100),2);
		// 	$temp = array(
		// 		'name'=>$z['NAMA'],
		// 		'mom'=>$MOM,
		// 		'ytd'=>$YTD,
		// 		'actual'=>$actual,
		// 		'yoy'=>$YOY
		// 		);
		// 	array_push($all_area_result,$temp);

		// 	}
		// 	return $all_area_result;

		// 	$target = 1000000;
		// 	$gap = $target-$actual;
		// 	$achievement = ($actual/$target)*100;
		// }
		// else if($detail['regional']=='allregional')
		// {		
		// 	//get revenue all regional in one area choosen (i.e area 3)
		// 	$allregional = Revenue::with(['service','cluster.branch.regional.area'])->whereHas('cluster',function($query1) use ($detail){
		// 			$query1->whereHas('branch',function($query2) use ($detail){
		// 				$query2->whereHas('regional',function($query3) use ($detail){
		// 					$query3->whereHas('area',function($query4) use ($detail){
		// 						$query4->where('ID',$detail['area']);
		// 					});
		// 				});
		// 			});
		// 	})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 	$allrevR = $allregional->sum('REVENUE');
		// 	dd($allrevR);
		// }
		// else if($detail['branch']=='allbranch')
		// {
		// 	//get revenue all branch in one regional choosen (i.e regional 3)
		// 	$allbranch = Revenue::whereHas('cluster',function($query){
		// 		$query->whereHas('branch',function($query1){
		// 			$query1->whereHas('regional',function($query2){
		// 				$query2->where('ID',3);
		// 			});
		// 		});
		// 	})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 	$allrevB = $allbranch->sum('REVENUE');
		// }
		// else if($detail['cluster']=='allcluster')
		// {
		// 	//get revenue all cluster in one branch choosen (i.e branch 5)
		// 	$allcluster = Revenue::whereHas('cluster',function($query){
		// 		$query->whereHas('branch',function($query1){
		// 			$query1->where('ID',5);
		// 		});
		// 	})->whereDate('Date','>=',$date_post)->whereDate('Date','<=',$date_now)->get();
		// 	$allrevC = $allcluster->sum('REVENUE');
		// }
		// else if($detail['service']=='allservice')
		// {
		// 	//get revenue all service in one cluster choosen (i.e cluster 1)
		// 	$allservice = Revenue::whereHas('cluster',function($query){
		// 			$query->where('ID',3);
		// 		})->whereDate('Date','>=',$date_post)
		// 		->whereDate('Date','<=',$date_now)
		// 		->get();
		// 	$allrevS = $allservice->sum('REVENUE');
		// }

		// //get revenue with spesific criteria
		// $rev = Revenue::whereHas('cluster',function($query){
		// 	$query->whereHas('branch',function($query1){
		// 		$query1->whereHas('regional',function($query2){
		// 			$query2->whereHas('area',function($query3){
		// 				$query3->where('ID',3);
		// 			})->where('ID',3);
		// 		})->where('ID',5);
		// 	})->where('ID',3);
		// })->where('ID_SERVICE',23)->get();
		// $allrev = $rev->sum('REVENUE');
		// //dd($allrev);


		// $areaSQL = Revenue::leftJoin('Tree', 'Revenue.ID_OWNER','=','Tree.ID')
		// 			->where('Tree.AREA',$detail['area'])
		// 			->where('Tree.REGIONAL',$detail['regional'])
		// 			->whereDate('DATE','>=',$date_post)
		// 			->whereDate('DATE','<=',$date_now)
		// 			->get(['Revenue.REVENUE']);
		// $arearevenue = $areaSQL->sum('REVENUE');
		// //dd($arearevenue);
  //   }
}
