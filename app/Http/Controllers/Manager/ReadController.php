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
		$temp_date = $taun.'-'.$bulan.'-'.$tanggal;
		if($bulan =='03')
		{
			if($tanggal >= 29)
			{
				$temp_date = $taun.'-'.$bulan.'-'.$tanggal;
				$date_data['mom2'] = date_create($temp_date.' last day of last month')->format('Y-m-d');
				// dd($d->format('Y-m-d'));
			}
			else
			{
				$date_data['mom2'] = $taun.'-02'.'-'.$tanggal;
			}
		}
		else
		{
			if($tanggal == date_create($temp_date.' last day of this month')->format('d'))
			{
				$date_data['mom2'] = date_create($temp_date.' last day of last month')->format('Y-m-d');
			}
			else
			{
				$date_data['mom2'] = $taun.'-'.($bulan-1).'-'.$tanggal;	
			}
		}

		//dd($date_now);
		if($bulan=='01')
		{
			$date_data['mom1'] = ($taun-1).'-12'.'-01';
			// $date_data['mom2'] = ($taun-1).'-12'.'-'.$tanggal;

			//buat nyari mom bulan lalu
			$date_data['now_bulanlalu'] = ($taun-1).'-12'.'-'.$tanggal;
			$date_data['post_bulanlalu'] = ($taun-1).'-12'.'-01';
			$date_data['mom1_bulanlalu'] = ($taun-1).'-11'.'-01';
			$date_data['mom2_bulanlalu'] = ($taun-1).'-11'.'-'.$tanggal;
			// dd($date_now);
		}
		else
		{
			$date_data['mom1'] = $taun."-".($bulan-1).'-01';
			// $date_data['mom2'] = $taun."-".($bulan-1).'-'.$tanggal;

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

			if($detail['button']=='L1')
			{
				$area = Area::find(3);
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];
				$actual = $this->countActual('area',3, $date_data);
				
				$MOM = $this->countMom('area',3, $date_data);
						$YOY = $this->countYoy('area',3, $date_data);
				$YTD = $this->countYtd('area',3, $date_data);
				$date_data['now'] = $date_data['now_bulanlalu'];
				$date_data['post'] = $date_data['post_bulanlalu'];
				$actual_bulanlalu = $this->countActual('area', 3, $date_data);
				
				$all_area_result = array(
					'name'=>$area->NAMA,
					'mom'=>$MOM,
					'ytd'=>$YTD,
					'actual'=>$actual,
					'yoy'=>$YOY,
					'actual_bulanlalu'=>$actual_bulanlalu,
					'now_bulanlalu'=>$date_data['now_bulanlalu'],
					'now'=>$date_data['skrg']
				);
				$regionals = Regional::where('ID_AREA',3)->get();
				$all_regional_result = array();

				$all_branch_result = array();
				$all_cluster_result = array();


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
							// echo $actual."     ";
							$GAP = floatval($target - $actual);
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
							echo "name = ".$temp['name']."|| mom = ".$temp['mom']."|| ".$temp['actual']."\r\n";
							array_push($all_cluster_result,$temp);	
						}
						// dd($all_cluster_result);
						$date_data['now']=$date_data['skrg'];
						$date_data['post']=$date_data['post_real'];

						$target = $this->getTarget('branch',$b->ID);
						$actual = $this->countActual('branch',$b->ID, $date_data);
						$GAP = floatval($target - $actual);
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
					$GAP = floatval($target - $actual);
					$achievement = round((($actual/$target)*100),2);
					$MOM = $this->countMom('regional',$r->ID, $date_data);
					$YTD = $this->countYtd('regional',$r->ID, $date_data);
					$YOY = $this->countYoy('regional',$r->ID, $date_data);

					

					$target = $this->getTarget('regional',$r->ID);
					$actual = $this->countActual('regional',$r->ID, $date_data);
					$GAP = floatval($target - $actual);
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
				$all_regional_result = collect($all_regional_result)->sortBy('mom')->reverse()->toArray();
				$all_branch_result = collect($all_branch_result)->sortBy('mom')->reverse()->toArray();
				$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();
				return ([$all_area_result, $all_regional_result, $all_branch_result, $all_cluster_result]);
			}
			else
			{
				$service = Revenue::with('fromService')->whereHas('cluster', function($a) use($detail){
							$a->whereHas('branch', function($b) use($detail){
								$b->whereHas('regional', function($c) use($detail){
									$c->whereHas('area', function($d) use($detail){
										$d->where('ID_AREA',$detail['area']);
									});
								});
							});
						})->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])
						->groupBy('ID_SERVICE')->get();
				$all_service_result = array();

				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];
				$a = $date_data['mom1'];
				$b = $date_data['mom2'];
				// dd($service);
				foreach($service as $s)
				{
					$date_data['post']=$date_data['post_real'];
					$date_data['now']=$date_data['skrg'];
					$date_data['mom1']=$a;
					$date_data['mom2']=$b;

					$name = $s->fromService->NAMA;
					$actual = $this->countActual('area', $s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);
					$MOM = $this->countMom('area', $s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);

					$date_data['mom1'] = $date_data['mom1_bulanlalu'];
					$date_data['mom2'] = $date_data['mom2_bulanlalu'];

					$date_data['post'] = $date_data['post_bulanlalu'];
					$date_data['now'] = $date_data['now_bulanlalu'];

					$actual_bulanlalu = $this->countActual('area', $s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);
					$MOM_bulanlalu = $this->countMom('area',$s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);
					$absolut = floatval($actual - $actual_bulanlalu);
					$temp = array(
						'name'=>$name,
						'mom'=>$MOM,
						'actual'=>$actual,
						'mom_bulanlalu'=>$MOM_bulanlalu,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'absolut'=>$absolut
						);
					array_push($all_service_result,$temp);
				}
				$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
				$topv = array_slice($all_service_result, 0, 5, true);
				return ([$all_service_result,$topv]);
			}
		}

		//get all branch list in those regional selected
		else if($detail['branch']=='all')
		{
			if($detail['button']=='L1')
			{
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];

				$regionals = Regional::where('ID_AREA',3)->get();

				$all_branch_result = array();
				$all_cluster_result = array();

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
							$GAP = floatval($target - $actual);
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
						$GAP = floatval($target - $actual);
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
					$GAP = floatval($target - $actual);
					$achievement = round((($actual/$target)*100),2);
					$MOM = $this->countMom('regional',$r->ID, $date_data);
					$YTD = $this->countYtd('regional',$r->ID, $date_data);
					$YOY = $this->countYoy('regional',$r->ID, $date_data);

					$date_data['now'] = $date_data['now_bulanlalu'];
					$date_data['post'] = $date_data['post_bulanlalu'];


					$actual_bulanlalu = $this->countActual('regional', $r->ID, $date_data);
					
					$all_regional_result = array(
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

				}
				// $all_regional_result = collect($all_regional_result)->sortBy('actual')->reverse()->toArray();
				$all_branch_result = collect($all_branch_result)->sortBy('mom')->reverse()->toArray();
				$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();
				return ([$all_regional_result, $all_branch_result, $all_cluster_result]);
			}


			//calculate L3 on spesific regional
			else
			{
				$service = Revenue::with('fromService')->whereHas('cluster', function($a) use($detail){
							$a->whereHas('branch', function($b) use($detail){
								$b->whereHas('regional', function($c) use($detail){
									$c->where('ID_REGIONAL',$detail['regional']);
								});
							});
						})->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])
						->groupBy('ID_SERVICE')->get();
				
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
					$actual = $this->countActual('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);
					$MOM = $this->countMom('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);

					$date_data['mom1'] = $date_data['mom1_bulanlalu'];
					$date_data['mom2'] = $date_data['mom2_bulanlalu'];

					$date_data['post'] = $date_data['post_bulanlalu'];
					$date_data['now'] = $date_data['now_bulanlalu'];

					$actual_bulanlalu = $this->countActual('regional', $s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);
					$MOM_bulanlalu = $this->countMom('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);;
					$absolut = floatval($actual - $actual_bulanlalu);
					$temp = array(
						'name'=>$name,
						'mom'=>$MOM,
						'actual'=>$actual,
						'mom_bulanlalu'=>$MOM_bulanlalu,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'absolut'=>$absolut
						);
					array_push($all_service_result,$temp);
				}
				$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
				$topv = array_slice($all_service_result, 0, 5, true);
				// dd($all_service_result);
				return ([$all_service_result,$topv]);
			}
		}

		//get all cluster list in those branch selected
		else if($detail['cluster']=='all')
		{
			if($detail['button']=='L1')
			{
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];

				//UNTUK TIAP BRANCHNYA
				$branchs = Branch::where('ID',$detail['branch'])->get();
				$all_cluster_result = array();

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
						$GAP = floatval($target - $actual);
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
					$GAP = floatval($target - $actual);
					$achievement = round((($actual/$target)*100),2);
					$MOM = $this->countMom('branch',$b->ID, $date_data);
					$YTD = $this->countYtd('branch',$b->ID, $date_data);
					$YOY = $this->countYoy('branch',$b->ID, $date_data);

					$date_data['now'] = $date_data['now_bulanlalu'];
					$date_data['post'] = $date_data['post_bulanlalu'];

					$actual_bulanlalu = $this->countActual('branch', $b->ID, $date_data);

					$all_branch_result = array(
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
				}

				$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();
				return ([$all_branch_result, $all_cluster_result]);
			}
			//calculate L3 on spesific branch
			else
			{

				$service = Revenue::with('fromService')->whereHas('cluster', function($a) use($detail){
							$a->where('ID_BRANCH',$detail['branch']);
							})->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])->groupBy('ID_SERVICE')->get();
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
					$MOM = $this->countMom('branch',$s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);

					$date_data['mom1'] = $date_data['mom1_bulanlalu'];
					$date_data['mom2'] = $date_data['mom2_bulanlalu'];

					$date_data['post'] = $date_data['post_bulanlalu'];
					$date_data['now'] = $date_data['now_bulanlalu'];

					$actual_bulanlalu = $this->countActual('branch', $s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);
					$MOM_bulanlalu = $this->countMom('branch',$s->ID_SERVICE, $date_data, $detail['branch'], $detail['button']);;
					$absolut = floatval($actual - $actual_bulanlalu);
					$temp = array(
						'name'=>$name,
						'mom'=>$MOM,
						'actual'=>$actual,
						'mom_bulanlalu'=>$MOM_bulanlalu,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['now_bulanlalu'],
						'now'=>$date_data['skrg'],
						'absolut'=>$absolut
						);
					$check = 1;
					array_push($all_service_result,$temp);
				}
				
				$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
				$topv = array_slice($all_service_result, 0, 5, true);
				return ([$all_service_result,$topv]);
			}
			
		}
		//get calculate L3 on spesific cluster
		else
		{
			// dd("masuk");
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
				$actual = $this->countActual('cluster', $s->ID_SERVICE, $date_data, $detail['cluster'], $detail['button']);
				$MOM = $this->countMom('cluster', $s->ID_SERVICE, $date_data, $detail['cluster'], $detail['button']);

				$date_data['mom1'] = $date_data['mom1_bulanlalu'];
				$date_data['mom2'] = $date_data['mom2_bulanlalu'];

				$date_data['post'] = $date_data['post_bulanlalu'];
				$date_data['now'] = $date_data['now_bulanlalu'];

				$actual_bulanlalu = $this->countActual('cluster', $s->ID_SERVICE, $date_data, $detail['cluster'], $detail['button']);
				$MOM_bulanlalu = $this->countMom('cluster', $s->ID_SERVICE, $date_data, $detail['cluster'], $detail['button']);

				$absolut = floatval($actual - $actual_bulanlalu);

				$temp = array(
					'name'=>$name,
					'mom'=>$MOM,
					'actual'=>$actual,
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
			$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
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
		$target = floatval($target/1000000000);
		return $target;

    }

    private function countActual($type, $target, $date, $target2=null, $output=null)
    {							
    	// dd($target);

    		$wida = Revenue::whereHas('cluster', function($a) use($target, $type, $date, $target2, $output){
				if($type == 'cluster') 
					{
						if($target2 == null) $target2=$target;
							$a->where('ID', $target2);
						if($output == 'L3' || $output == 'TOP5')
							$a->where('ID_SERVICE', $target);
					}
				else $a->whereHas('branch', function($b) use($target, $type, $date, $target2, $output){
					if($type == 'branch')
					{
						if($target2 == null) $target2=$target;
							$b->where('ID', $target2);
						if($output == 'L3' || $output == 'TOP5')
							$b->where('ID_SERVICE', $target);
					}
						else $b->whereHas('regional', function($c) use($target, $type, $date, $target2, $output){
							if($type == 'regional') 
							{
								if($target2 == null) $target2=$target;
									$c->where('ID', $target2);
								if($output == 'L3' || $output == 'TOP5')
									$c->where('ID_SERVICE', $target);
							}
							else $c->whereHas('area', function($d) use($target, $type, $date, $target2, $output){
								if($target2 == null) $target2=$target;
									$d->where('ID', $target2);
									if($output == 'L3' || $output == 'TOP5')
									$d->where('ID_SERVICE', $target);
							});
						});
					});
				})->whereDate('Date','>=',$date['post'])->whereDate('Date','<=',$date['now'])->get();
			// print_r($wida."      ");
			$actual = $wida->sum('REVENUE');
			$actual = floatval($actual/1000000000);
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
		$YTD = round((($YTD1/$YTD2)-1)*100,2);
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
		$YOY = round((($yoy1/$yoy2)-1)*100,2);
		return $YOY;
    }

    public function countMom($type, $target, $date, $target2=null, $output=null)
    {
    	// dd($target);
		$hai = Revenue::whereHas('cluster',function($a) use($target, $type, $date, $target2, $output){
				if($type == 'cluster') 
					{
						if($target2 == null) $target2 = $target;
							$a->where('ID', $target2);
						if($output == 'L3' || $output == 'TOP5')
							$a->where('ID_SERVICE',$target);
					}
				else $a->whereHas('branch', function($b) use($target, $type, $date, $target2, $output){
					if($type == 'branch') 
						{
							if($target2 == null) $target2 = $target;
								$b->where('ID', $target2);
							if($output == 'L3' || $output == 'TOP5')
								$b->where('ID_SERVICE',$target);
						}
					else $b->whereHas('regional', function($c) use($target, $type, $date, $target2, $output){
						if($type == 'regional') 
							{
								if($target2 == null) $target2 = $target;
									$c->where('ID', $target2);
								if($output == 'L3' || $output == 'TOP5')
									$c->where('ID_SERVICE',$target);
							}
						else $c->whereHas('area', function($d) use($target, $type, $date, $target2, $output){
							if($target2 == null) $target2 = $target;
									$d->where('ID', $target2);
							if($output == 'L3' || $output == 'TOP5')
									$d->where('ID_SERVICE',$target);
						});
					});
				});
		})->whereDate('Date','>=',$date['post'])->whereDate('Date','<=',$date['now'])->get();
		$mom1 = $hai->sum('REVENUE');
		// echo $mom1;
		$hai2 = Revenue::whereHas('cluster',function($a) use($target, $type, $date, $target2, $output){
			if($type == 'cluster') 
				{
					if($target2 == null) $target2 = $target;
						$a->where('ID', $target2);
					if($output == 'L3' || $output == 'TOP5')
						$a->where('ID_SERVICE',$target);
				}
			else $a->whereHas('branch', function($b) use($target, $type, $date, $target2, $output){
				if($type == 'branch') 
					{
						if($target2 == null) $target2 = $target;
							$b->where('ID', $target2);
						if($output == 'L3' || $output == 'TOP5')
							$b->where('ID_SERVICE',$target);
					}
				else $b->whereHas('regional', function($c) use($target, $type, $date, $target2, $output){
					if($type == 'regional') 
							{
								if($target2 == null) $target2 = $target;
									$c->where('ID', $target2);
								if($output == 'L3' || $output == 'TOP5')
									$c->where('ID_SERVICE',$target);
							}
						else $c->whereHas('area', function($d) use($target, $type, $date, $target2, $output){
							if($target2 == null) $target2=$target;
								$d->where('ID', $target2);
							if($output == 'L3' || $output == 'TOP5')
								$d->where('ID_SERVICE',$target);
							// dd("masuk");
						});
					});
				});
		})->whereDate('Date','>=',$date['mom1'])->whereDate('Date','<=',$date['mom2'])->get();;
		$mom2 = $hai2->sum('REVENUE');
		// echo "type  ".$type;
		// echo "target   ".$target2;
		// echo "post   ".$date['post'];
		// echo "mom   ".$date['now'];
		// echo "mom1   ".$date['mom1'];
		// echo "mom2   ".$date['mom2'];
		// echo $date['mom1']."  ".$date['mom2'];
		// dd($hai2);
		if($mom2==0) {$MOM=0;}
		else
			$MOM = round((($mom1/$mom2)-1)*100,2);
    	return $MOM;
    }
}
