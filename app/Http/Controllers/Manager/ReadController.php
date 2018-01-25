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
use App\Models\Revenue;
use App\Models\Target;
use DateTime;
use DB;
use Carbon\Carbon;
class ReadController extends Controller
{
	//AJAX
	public function findType(Request $req)
	{
		switch($req->nexttarget)
		{
			case 'regional':
				$data = Regional::where('ID_AREA',3)->get();
				break;
			case 'branch':
				$data = Branch::where('ID_REGIONAL',$req->id)->get();
				break;
			case 'cluster':
				$data = Cluster::where('ID_BRANCH',$req->id)->get();
				break;
		}
		return json_encode($data);
	}


    public function reportGET()
    {
    	return view('manager.report');
    }

	public function calculate($detail,$date_formated)
    {
		$taun=$date_formated[0];
		$bulan=$date_formated[1];
		$tanggal=$date_formated[2];

		$date_data = array(
			'post' => $taun.'-'.$bulan.'-01',
			'post2' => ($taun-1).'-'.$bulan.'-01',
			'now' => $taun.'-'.$bulan.'-'.$tanggal,
			'now2' => ($taun-1).'-'.$bulan.'-'.$tanggal,
			'ytd1' => $taun.'-01'.'-01',
			'ytd2' => ($taun-1).'-01'.'-01'
		);
		//dd($date_now);
		if($bulan=='01')
		{
			$date_data['mom1'] = ($taun-1).'-12'.'-01';
			$date_data['mom2'] = ($taun-1).'-12'.'-'.$tanggal;

			//buat nyari mom bulan lalu
			$date_data['now_bulanlalu'] = ($taun-1).'-12'.'-'.$tanggal;
			$date_data['post_bulanlalu'] = ($taun-1).'-12'.'-01';
			$date_data['mom1_bulanlalu'] = ($taun-1).'-11'.'-01';
			$date_data['mom2_bulanlalu'] = ($taun-1).'-11'.'-'.$tanggal;
			// dd($date_now);
		}
		else
		{
			$date_data['mom1'] = $taun.($bulan-1).'-01';
			$date_data['mom2'] = $taun.($bulan-1).'-'.$tanggal;

			//buat nyari mom bulan lalu
			$date_data['now_bulanlalu'] = $taun.'-'.($bulan-1).'-'.$tanggal;
			$date_data['post_bulanlalu'] = $taun.'-'.($bulan-1).'-01';
			$date_data['mom1_bulanlalu'] = $taun.'-'.($bulan-1).'-01';
			$date_data['mom2_bulanlalu'] = $taun.'-'.($bulan-1).'-'.$tanggal;
		}
		
		//get all area list
		if($detail['area']=='all')
		{
			$areas = Area::get();
			$all_area_result = array();
			$date_data['skrg']=$date_data['now'];
			$date_data['post_real']=$date_data['post'];
			foreach($areas as $z)
			{
				$date_data['now']=$date_data['skrg'];
				$date_data['post']=$date_data['post_real'];

				$actual = $this->countActual('area', $z->ID, $date_data);
				$MOM = $this->countMom('area', $z->ID, $date_data);
				$YTD = $this->countYtd('area', $z->ID, $date_data);
				$YOY = $this->countYoy('area', $z->ID, $date_data);

				$date_data['now'] = $date_data['now_bulanlalu'];
				$date_data['post'] = $date_data['post_bulanlalu'];

				$actual_bulanlalu = $this->countActual('area', $z->ID, $date_data);
				
				$temp = array(
					'name'=>$z['NAMA'],
					'mom'=>$MOM,
					'ytd'=>$YTD,
					'actual'=>$actual,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'now_bulanlalu'=>$date_data['now_bulanlalu'],
					'now'=>$date_data['skrg'],
					'yoy'=>$YOY
					);
				array_push($all_area_result,$temp);
			}
			return ([$all_area_result,0]);
		}

		//get all regional list in those area selected
		else if($detail['regional']=='all')
		{
			$regionals = Regional::where('ID_AREA',3)->get();
			$all_regional_result = array();
			$all_branch_result = array();
			$all_cluster_result = array();
			$date_data['skrg']=$date_data['now'];
			$date_data['post_real']=$date_data['post'];
			foreach($regionals as $r)
			{
				//UNTUK REGIONAL
				$date_data['now']=$date_data['skrg'];
				$date_data['post']=$date_data['post_real'];

				//UNTUK TIAP BRANCHNYA
				$branchs = Branch::where('ID_REGIONAL',$r->ID)->get();
				foreach($branchs as $b)
				{
					//UNTUK CLUSTER
					$clusters = Cluster::where('ID_BRANCH',$b->ID)->get();
					foreach($clusters as $c)
					{
						$date_data['now']=$date_data['skrg'];
						$date_data['post']=$date_data['post_real'];

						$target = $this->getTarget('cluster',$c->ID);
						$actual = $this->countActual('cluster',$c->ID, $date_data);
						$GAP = $target - $actual;
						$achievement = round((($actual/$target)*100),2);
						$MOM = $this->countMom('cluster',$c->ID, $date_data);
						$YTD = $this->countYtd('cluster',$c->ID, $date_data);
						$YOY = $this->countYoy('cluster',$c->ID, $date_data);

						$date_data['now'] = $date_data['now_bulanlalu'];
						$date_data['post'] = $date_data['post_bulanlalu'];

						$actual_bulanlalu = $this->countActual('cluster', $c->ID, $date_data);

						$temp = array(
							'name'=>$c['NAMA'],
							'mom'=>$MOM,
							'ytd'=>$YTD,
							'actual'=>$actual,
							'yoy'=>$YOY,
							'target'=>$target,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['now_bulanlalu'],
							'now'=>$date_data['skrg'],
							'GAP'=>$GAP,
							'achievement'=>$achievement
						);
						array_push($all_cluster_result,$temp);	
					}
					// dd($clusters);
					$date_data['now']=$date_data['skrg'];
					$date_data['post']=$date_data['post_real'];

					$target = $this->getTarget('branch',$b->ID);
					$actual = $this->countActual('branch',$b->ID, $date_data);
					$GAP = $target - $actual;
					$achievement = round((($actual/$target)*100),2);
					$MOM = $this->countMom('branch',$b->ID, $date_data);
					$YTD = $this->countYtd('branch',$b->ID, $date_data);
					$YOY = $this->countYoy('branch',$b->ID, $date_data);

					$date_data['now'] = $date_data['now_bulanlalu'];
					$date_data['post'] = $date_data['post_bulanlalu'];

					$actual_bulanlalu = $this->countActual('branch', $b->ID, $date_data);

					$temp = array(
						'name'=>$b['NAMA'],
						'mom'=>$MOM,
						'ytd'=>$YTD,
						'actual'=>$actual,
						'yoy'=>$YOY,
						'target'=>$target,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'GAP'=>$GAP,
						'achievement'=>$achievement
					);
					array_push($all_branch_result,$temp);	
				}

				$date_data['now']=$date_data['skrg'];
				$date_data['post']=$date_data['post_real'];
				$target = $this->getTarget('regional',$r->ID);
				$actual = $this->countActual('regional',$r->ID, $date_data);
				$GAP = $target - $actual;
				$achievement = round((($actual/$target)*100),2);
				$MOM = $this->countMom('regional',$r->ID, $date_data);
				$YTD = $this->countYtd('regional',$r->ID, $date_data);
				$YOY = $this->countYoy('regional',$r->ID, $date_data);

				

				$target = $this->getTarget('regional',$r->ID);
				$actual = $this->countActual('regional',$r->ID, $date_data);
				$GAP = $target - $actual;
				$achievement = round((($actual/$target)*100),2);
				$MOM = $this->countMom('regional',$r->ID, $date_data);
				$YTD = $this->countYtd('regional',$r->ID, $date_data);
				$YOY = $this->countYoy('regional',$r->ID, $date_data);


				$date_data['now'] = $date_data['now_bulanlalu'];
				$date_data['post'] = $date_data['post_bulanlalu'];


				$actual_bulanlalu = $this->countActual('regional', $r->ID, $date_data);
				
				$temp = array(
					'name'=>$r['NAMA'],
					'mom'=>$MOM,
					'ytd'=>$YTD,
					'actual'=>$actual,
					'yoy'=>$YOY,
					'target'=>$target,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'now_bulanlalu'=>$date_data['now_bulanlalu'],
					'now'=>$date_data['skrg'],
					'GAP'=>$GAP,
					'achievement'=>$achievement
					);
				array_push($all_regional_result,$temp);

			}
			return ([$all_regional_result,0, $all_branch_result, $all_cluster_result]);
		}

		//get all branch list in those regional selected
		else if($detail['branch']=='all')
		{
			$branchs = Branch::where('ID_REGIONAL',$detail['regional'])->get();
			$all_branch_result = array();
			$date_data['skrg']=$date_data['now'];
			$date_data['post_real']=$date_data['post'];
			foreach($branchs as $b)
			{
				$date_data['now']=$date_data['skrg'];
				$date_data['post']=$date_data['post_real'];

				$target = $this->getTarget('branch',$b->ID);
				$actual = $this->countActual('branch',$b->ID, $date_data);
				$GAP = $target - $actual;
				$achievement = round((($actual/$target)*100),2);
				$MOM = $this->countMom('branch',$b->ID, $date_data);
				$YTD = $this->countYtd('branch',$b->ID, $date_data);
				$YOY = $this->countYoy('branch',$b->ID, $date_data);


				$date_data['now'] = $date_data['now_bulanlalu'];
				$date_data['post'] = $date_data['post_bulanlalu'];

				$actual_bulanlalu = $this->countActual('branch', $b->ID, $date_data);
				
				$temp = array(
					'name'=>$b['NAMA'],
					'mom'=>$MOM,
					'ytd'=>$YTD,
					'actual'=>$actual,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'yoy'=>$YOY,
					'target'=>$target,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'now_bulanlalu'=>$date_data['now_bulanlalu'],
					'now'=>$date_data['skrg'],
					'GAP'=>$GAP,
					'achievement'=>$achievement
					);
				array_push($all_branch_result,$temp);
			}
			return ([$all_branch_result,0]);
		}

		//get all cluster list in those branch selected
		else if($detail['cluster']=='all')
		{
			if($detail['button']=='L1')
			{
				$cluster = Cluster::where('ID_BRANCH',$detail['branch'])->get();
				$all_cluster_result = array();
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];
				foreach($cluster as $c)
				{
					$date_data['now']=$date_data['skrg'];
					$date_data['post']=$date_data['post_real'];

					$target = $this->getTarget('cluster',$c->ID);
					$actual = $this->countActual('cluster',$c->ID, $date_data);
					$GAP = $target - $actual;
					$achievement = round((($actual/$target)*100),2);
					$MOM = $this->countMom('cluster',$c->ID, $date_data);
					$YTD = $this->countYtd('cluster',$c->ID, $date_data);
					$YOY = $this->countYoy('cluster',$c->ID, $date_data);

					$date_data['now'] = $date_data['now_bulanlalu'];
					$date_data['post'] = $date_data['post_bulanlalu'];

					$actual_bulanlalu = $this->countActual('cluster', $c->ID, $date_data);
					
					$temp = array(
						'name'=>$c['NAMA'],
						'mom'=>$MOM,
						'ytd'=>$YTD,
						'actual'=>$actual,
						'yoy'=>$YOY,
						'target'=>$target,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'GAP'=>$GAP,
						'achievement'=>$achievement
						);
					array_push($all_cluster_result,$temp);
				}
				return ([$all_cluster_result,0]);
			}
			//calculate L3 on spesific branch
			else
			{
				$service = Revenue::with('fromService')->whereHas('cluster', function($a) use($detail){
							$a->where('ID_BRANCH',$detail['branch']);
							})->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])->get();
				// print_r($service);
				// dd($service);
				$all_service_result = array();
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];
				$a = $date_data['mom1'];
				$b = $date_data['mom2'];
				foreach($service as $s)
				{
					$date_data['post']=$date_data['post_real'];
					$date_data['now']=$date_data['skrg'];
					$date_data['mom1']=$a;
					$date_data['mom2']=$b;
					$name = $s->fromService->NAMA;
					$actual = $this->countActual('branch',$s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);
					// dd($actual);
					// dd($date_data);
					$MOM = $this->countMom('service',$s->ID_SERVICE, $date_data);
					$YTD = $this->countYtd('service',$s->ID_SERVICE, $date_data);
					$YOY = $this->countYoy('service',$s->ID_SERVICE, $date_data);

					$date_data['mom1'] = $date_data['mom1_bulanlalu'];
					$date_data['mom2'] = $date_data['mom2_bulanlalu'];

					$date_data['post'] = $date_data['post_bulanlalu'];
					$date_data['now'] = $date_data['now_bulanlalu'];

					$actual_bulanlalu = $this->countActual('branch', $s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);
					$MOM_bulanlalu = $this->countMom('service',$s->ID_SERVICE, $date_data);
					$absolut = $MOM - $MOM_bulanlalu;
					$temp = array(
						'name'=>$name,
						'mom'=>$MOM,
						'ytd'=>$YTD,
						'actual'=>$actual,
						'yoy'=>$YOY,
						'mom_bulanlalu'=>$MOM_bulanlalu,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'absolut'=>$absolut
						);
					$check = 1;
					foreach($all_service_result as $r)
					{
						if($r['name'] == $temp['name'])	$check = 0;
					}
					if($check)
						array_push($all_service_result,$temp);
				}
				$all_service_result = collect($all_service_result)->sortBy('mom')->reverse()->toArray();
				$topv = array_slice($all_service_result, 0, 5, true);
				// dd($all_service_result);
				return ([$all_service_result,$topv]);
			}
			
		}
		//get calculate L3 on spesific cluster
		else
		{
			$service = Revenue::with('fromService')->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])->where('ID_CLUSTER',$detail['cluster'])->get();
			// dd($service[0]);
			$all_service_result = array();
			$date_data['skrg']=$date_data['now'];
			$date_data['post_real']=$date_data['post'];
			$a = $date_data['mom1'];
			$b = $date_data['mom2'];
			foreach($service as $s)
			{
				$date_data['post']=$date_data['post_real'];
				$date_data['now']=$date_data['skrg'];
				$date_data['mom1']=$a;
				$date_data['mom2']=$b;
				$name = $s->fromService->NAMA;
				$actual = $this->countActual('cluster',$s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);
				// dd($date_data);
				$MOM = $this->countMom('service',$s->ID_SERVICE, $date_data);
				$YTD = $this->countYtd('service',$s->ID_SERVICE, $date_data);
				$YOY = $this->countYoy('service',$s->ID_SERVICE, $date_data);

				$date_data['mom1'] = $date_data['mom1_bulanlalu'];
				$date_data['mom2'] = $date_data['mom2_bulanlalu'];

				$date_data['post'] = $date_data['post_bulanlalu'];
				$date_data['now'] = $date_data['now_bulanlalu'];

				$actual_bulanlalu = $this->countActual('service', $s->ID_SERVICE, $date_data);
				$MOM_bulanlalu = $this->countMom('service',$s->ID_SERVICE, $date_data);
				$absolut = $MOM - $MOM_bulanlalu;
				$temp = array(
					'name'=>$name,
					'mom'=>$MOM,
					'ytd'=>$YTD,
					'actual'=>$actual,
					'yoy'=>$YOY,
					'mom_bulanlalu'=>$MOM_bulanlalu,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'now_bulanlalu'=>$date_data['now_bulanlalu'],
					'now'=>$date_data['skrg'],
					'absolut'=>$absolut
					);
				$check = 1;
				foreach($all_service_result as $r)
				{
					if($r['name'] == $temp['name'])	$check = 0;
				}
				if($check)
					array_push($all_service_result,$temp);
			}
			$all_service_result = collect($all_service_result)->sortBy('mom')->reverse()->toArray();
			$topv = array_slice($all_service_result, 0, 5, true);
			// dd($all_service_result);
			return ([$all_service_result,$topv]);
		}
    }

    private function getTarget($type, $target)
    {
		$wida = Target::whereHas('cluster', function($a) use($target, $type){
			if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type){
							$d->where('ID', $target);
						});
				});
			});
		})->get();
		$target = $wida->sum('TARGET');
		return $target;

    }

    private function countActual($type, $target, $date, $target2=null, $output=null)
    {
    		$wida = Revenue::whereHas('cluster', function($a) use($target, $type, $date, $target2, $output){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') 
					{
						if($target2 == null) $target2=$target;
							$a->where('ID', $target2);

						if($output == 'L3')
							$a->where('ID_SERVICE', $target);
					}
					else $a->whereHas('branch', function($b) use($target, $type, $date, $target2, $output){
						if($type == 'branch')
						{
							if($target2 == null) $target2=$target;
								$b->where('ID', $target2);
							if($output == 'L3')
								$b->where('ID_SERVICE', $target);
						}
						else 	$b->whereHas('regional', function($c) use($target, $type, $date){
							if($type == 'regional') $c->where('ID', $target);
							else 	$c->whereHas('area', function($d) use($target, $type, $date){
								$d->where('ID', $target);
							});
						});
					});
			})->whereDate('Date','>=',$date['post'])->whereDate('Date','<=',$date['now'])->get();
			$actual = $wida->sum('REVENUE');
		return $actual;
    }

    private function countYtd($type, $target, $date)
    {
    	//YTD
		$hai = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['ytd1'])->whereDate('Date','<=',$date['now'])->get();
		$YTD1 = $hai->sum('REVENUE');

		$hai2 = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['ytd2'])->whereDate('Date','<=',$date['now2'])->get();
		$YTD2 = $hai2->sum('REVENUE');
		if($YTD2==0) {$YTD=0;}
		else
		$YTD = round((($YTD1/$YTD2)-1),2);
		return $YTD;
    }

    public function countYoy($type, $target, $date)
    {
    	//YTD
		$hai = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['post'])->whereDate('Date','<=',$date['now'])->get();
		$yoy1 = $hai->sum('REVENUE');

		$hai2 = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['post2'])->whereDate('Date','<=',$date['now2'])->get();
		$yoy2 = $hai2->sum('REVENUE');
		if($yoy2==0) {$YOY=0;}
		else
		$YOY = round((($yoy1/$yoy2)-1),2);
		return $YOY;
    }

    public function countMom($type, $target, $date)
    {
    	// dd($target);
		$hai = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['post'])->whereDate('Date','<=',$date['now'])->get();


		$mom1 = $hai->sum('REVENUE');

		$hai2 = Revenue::whereHas('cluster',function($a) use($target, $type, $date){
				if($type == 'service') $a->where('ID_SERVICE', $target);
				else if($type == 'cluster') $a->where('ID', $target);
				else 	$a->whereHas('branch', function($b) use($target, $type, $date){
					if($type == 'branch') $b->where('ID', $target);
					else 	$b->whereHas('regional', function($c) use($target, $type, $date){
						if($type == 'regional') $c->where('ID', $target);
						else 	$c->whereHas('area', function($d) use($target, $type, $date){
							$d->where('ID', $target);
						});
					});
				});
		})->whereDate('Date','>=',$date['mom1'])->whereDate('Date','<=',$date['mom2'])->get();
		$mom2 = $hai2->sum('REVENUE');
		if($mom2==0) {$MOM=0;}
		else
		$MOM = round((($mom1/$mom2)-1),2);
    	return $MOM;
    }
}