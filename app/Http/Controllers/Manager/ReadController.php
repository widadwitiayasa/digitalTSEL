<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
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
		//jadi tanggal mom2_bulanlalu ini ngikut tanggal mom2 atau tanggal aslinya? baca notes
		if($bulan =='03')
		{
			if($tanggal >= 29)
			{
				$temp_date = $taun.'-'.$bulan.'-'.$tanggal;
				$date_data['mom2'] = date_create($temp_date.' last day of last month')->format('Y-m-d');
			}
			else
			{
				$date_data['mom2'] = $taun.'-02'.'-'.$tanggal;
			}
			$date_data['mom2_bulanlalu'] = $taun.'-01'.'-'.$tanggal;
		}
		else
		{
			if($bulan=='01')
			{
				$date_data['mom1'] = ($taun-1).'-12'.'-01';
				// $date_data['mom2'] = ($taun-1).'-12'.'-'.$tanggal;

				//buat nyari mom bulan lalu
				$date_data['now_bulanlalu'] = ($taun-1).'-12'.'-'.$tanggal;
				$date_data['post_bulanlalu'] = ($taun-1).'-12'.'-01';
				$date_data['mom1_bulanlalu'] = ($taun-1).'-11'.'-01';
				// $date_data['mom2_bulanlalu'] = ($taun-1).'-11'.'-'.$tanggal;
				// dd($date_now);
				$bulan='13';
				if($tanggal == date_create($temp_date.' last day of this month')->format('d'))
				{
					$date_data['mom2'] = date_create($temp_date.' last day of last month')->format('Y-m-d');
					$date_data['mom2_bulanlalu'] = date_create($date_data['mom2'].'last day of last month')->format('Y-m-d');
				}
				else
				{
					$date_data['mom2'] = $taun.'-'.($bulan-1).'-'.$tanggal;
					$date_data['mom2_bulanlalu'] = date_create($date_data['mom2'].'last month')->format('Y-m-d');
				}
			}
			else
			{
				$date_data['mom1'] = $taun."-".($bulan-1).'-01';
				// $date_data['mom2'] = $taun."-".($bulan-1).'-'.$tanggal;

				//buat nyari mom bulan lalu
				$date_data['now_bulanlalu'] = $taun.'-'.($bulan-1).'-'.$tanggal;
				$date_data['post_bulanlalu'] = $taun.'-'.($bulan-1).'-01';
				$date_data['mom1_bulanlalu'] = $taun.'-'.($bulan-2).'-01';
				// $date_data['mom2_bulanlalu'] = $taun.'-'.($bulan-1).'-'.$tanggal;
				if($tanggal == date_create($temp_date.' last day of this month')->format('d'))
				{
					$date_data['mom2'] = date_create($temp_date.' last day of last month')->format('Y-m-d');
					$date_data['mom2_bulanlalu'] = date_create($date_data['mom2'].'last day of last month')->format('Y-m-d');
				}
				else
				{
					$date_data['mom2'] = $taun.'-'.($bulan-1).'-'.$tanggal;
					$date_data['mom2_bulanlalu'] = date_create($date_data['mom2'].'last month')->format('Y-m-d');
				}
			}

			
		}
		// dd($detail);
		//dd($date_now);

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
				// dd($date_data['now_bulanlalu']);
				$all_area_result = json_decode(Redis::get('area_L1_result'));
				if(!$all_area_result)
				{
					$actual = DB::select("select countActual('area', 3, '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
					$MOM = DB::select("select countMom('area', 3, '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
					$YOY = DB::select("select countYoy('area', 3, '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['post2']."', '".$date_data['now2']."') as result");
					$YTD = DB::select("select countYtd('area', 3, '".$date_data['ytd1']."', '".$date_data['now']."',
									'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
				
					$actual = $actual[0]->result;
					$MOM = $MOM[0]->result;
					$YOY = $YOY[0]->result;
					$YTD = $YTD[0]->result;
					// $date_data['now'] = $date_data['now_bulanlalu'];
					$date_data['now'] = $date_data['mom2'];
					$date_data['post'] = $date_data['post_bulanlalu'];
					$actual_bulanlalu = DB::select("select countActual('area', 3, '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
					$actual_bulanlalu = $actual_bulanlalu[0]->result;
					// $actual_bulanlalu = $this->countActual('area', 3, $date_data);
					$all_area_result = array(
						'name'=>$area->NAMA,
						'mom'=>$MOM,
						'ytd'=>$YTD,
						'actual'=>$actual,
						'yoy'=>$YOY,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$date_data['mom2'],
						'now'=>$date_data['skrg']
					);
					Redis::set('area_L1_result', json_encode($all_area_result));
					$all_area_result = json_decode(Redis::get('area_L1_result'));
				}

				$regionals = Regional::where('ID_AREA',3)->get();

				$all_regional_result = json_decode(Redis::get('many_regional_L1_3'));
				$all_branch_result = json_decode(Redis::get('many_branch_fromarea_3_L1'));
				$all_cluster_result = json_decode(Redis::get('many_cluster_fromarea_3_L1'));

				if(!$all_regional_result)	
				{
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
								$actual = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
								
								$MOM = DB::select("select countMom('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
								$YOY = DB::select("select countYoy('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['post2']."', '".$date_data['now2']."') as result");
								$YTD = DB::select("select countYtd('cluster', '".$c->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
									'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
								$actual = $actual[0]->result;
								$MOM = $MOM[0]->result;
								$YOY = $YOY[0]->result;
								$YTD = $YTD[0]->result;

								$GAP = floatval($target - $actual);
								$achievement = round((($actual/$target)*100),2);
								// dd($MOM);
								$date_data['now'] = $date_data['mom2'];
								$date_data['post'] = $date_data['post_bulanlalu'];

								$actual_bulanlalu = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
								$actual_bulanlalu = $actual_bulanlalu[0]->result;

								$temp = array(
									'name'=>$c['NAMA'],
									'mom'=>$MOM,
									'ytd'=>$YTD,
									'actual'=>$actual,
									'yoy'=>$YOY,
									'target'=>$target,
									'actual_bulanlalu'=>$actual_bulanlalu,
									'now_bulanlalu'=>$date_data['mom2'],
									'now'=>$date_data['skrg'],
									'GAP'=>$GAP,
									'achievement'=>$achievement
								);
								array_push($all_cluster_result,$temp);	
							}

							// dd($all_cluster_result);
							$date_data['now']=$date_data['skrg'];
							$date_data['post']=$date_data['post_real'];

							$target = $this->getTarget('branch',$b->ID);
							$actual = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							$MOM = DB::select("select countMom('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
							$YOY = DB::select("select countYoy('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['post2']."', '".$date_data['now2']."') as result");
							$YTD = DB::select("select countYtd('branch', '".$b->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
											'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
							$actual = $actual[0]->result;
							$MOM = $MOM[0]->result;
							$YOY = $YOY[0]->result;
							$YTD = $YTD[0]->result;

							$GAP = floatval($target - $actual);
							$achievement = round((($actual/$target)*100),2);
							$date_data['now'] = $date_data['mom2'];
							$date_data['post'] = $date_data['post_bulanlalu'];

							$actual_bulanlalu = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							$actual_bulanlalu = $actual_bulanlalu[0]->result;
							$temp = array(
								'name'=>$b['NAMA'],
								'mom'=>$MOM,
								'ytd'=>$YTD,
								'actual'=>$actual,
								'yoy'=>$YOY,
								'target'=>$target,
								'actual_bulanlalu'=>$actual_bulanlalu,
								'now_bulanlalu'=>$date_data['mom2'],
								'now'=>$date_data['skrg'],
								'GAP'=>$GAP,
								'achievement'=>$achievement
							);
							array_push($all_branch_result,$temp);	
						}

						$date_data['now']=$date_data['skrg'];
						$date_data['post']=$date_data['post_real'];

						$target = $this->getTarget('regional',$r->ID);
						$actual = DB::select("select countActual('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$MOM = DB::select("select countMom('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
						$YOY = DB::select("select countYoy('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['post2']."', '".$date_data['now2']."') as result");
						$YTD = DB::select("select countYtd('regional', '".$r->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
							'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;
						$YOY = $YOY[0]->result;
						$YTD = $YTD[0]->result;

						$GAP = floatval($target - $actual);
						$achievement = round((($actual/$target)*100),2);

						$date_data['now'] = $date_data['mom2'];
						$date_data['post'] = $date_data['post_bulanlalu'];


						$actual_bulanlalu = DB::select("select countActual('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$actual_bulanlalu = $actual_bulanlalu[0]->result;
						$temp = array(
							'name'=>$r['NAMA'],
							'mom'=>$MOM,
							'ytd'=>$YTD,
							'actual'=>$actual,
							'yoy'=>$YOY,
							'target'=>$target,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['mom2'],
							'now'=>$date_data['skrg'],
							'GAP'=>$GAP,
							'achievement'=>$achievement
							);
						array_push($all_regional_result,$temp);
					}
					$all_regional_result = collect($all_regional_result)->sortBy('mom')->reverse()->toArray();
					$all_branch_result = collect($all_branch_result)->sortBy('mom')->reverse()->toArray();
					$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();

					Redis::set('many_regional_L1_3', json_encode($all_regional_result));
					Redis::set('many_branch_fromarea_3_L1', json_encode($all_branch_result));
					Redis::set('many_cluster_fromarea_3_L1', json_encode($all_cluster_result));
					$all_regional_result = json_decode(Redis::get('many_regional_L1_3'));
					$all_branch_result = json_decode(Redis::get('many_branch_fromarea_3_L1'));
					$all_cluster_result = json_decode(Redis::get('many_cluster_fromarea_3_L1'));
				}

				return ([$all_area_result, $all_regional_result, $all_branch_result, $all_cluster_result]);
			}
			else
			{
				$all_service_result = json_decode(Redis::get('allregionalfromarea'.$detail['area'].'_L3_result'));
				$topv = json_decode(Redis::get('allregionalfromarea'.$detail['area'].'_topv_result'));
				if(!$all_service_result)
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
					foreach($service as $s)
					{
						$date_data['post']=$date_data['post_real'];
						$date_data['now']=$date_data['skrg'];
						$date_data['mom1']=$a;
						$date_data['mom2']=$b;

						$name = $s->fromService->NAMA;
						$actual = DB::select("select countActual('area', '".$detail['area']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM = DB::select("select countMom('area', '".$detail['area']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;

						// $actual = $this->countActual('area', $s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);
						// $MOM = $this->countMom('area', $s->ID_SERVICE, $date_data, $detail['area'], $detail['button']);
						// dd($date_data['mom1_bulanlalu']);
						$date_data['mom1'] = $date_data['mom1_bulanlalu'];
						$date_data['mom2'] = $date_data['mom2_bulanlalu'];
						// dd($date_data['mom1']);
						$date_data['post'] = $date_data['post_bulanlalu'];
						$date_data['now'] = $b;
						// dd($date_data['mom2']);
						// dd($date_data);
						// dd($s->ID_SERVICE);
						$actual_bulanlalu = DB::select("select countActual('area', '".$detail['area']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM_bulanlalu = DB::select("select countMom('area', '".$detail['area']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						// dd($MOM_bulanlalu);
						$actual_bulanlalu = $actual_bulanlalu[0]->result;
						$MOM_bulanlalu = $MOM_bulanlalu[0]->result;
						$absolut = floatval($actual - $actual_bulanlalu);
						// dd($actual."   ".$actual_bulanlalu);
						$temp = array(
							'name'=>$name,
							'mom'=>$MOM,
							'actual'=>$actual,
							'mom_bulanlalu'=>$MOM_bulanlalu,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['now'],
							'now'=>$date_data['skrg'],
							'absolut'=>$absolut
							);
						array_push($all_service_result,$temp);
					}
					$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
					$topv = array_slice($all_service_result, 0, 5, true);
					Redis::set('allregionalfromarea'.$detail['area'].'_L3_result', json_encode($all_service_result));
					Redis::set('allregionalfromarea'.$detail['area'].'_topv_result', json_encode($topv));
					$all_service_result = json_decode(Redis::get('allregionalfromarea'.$detail['area'].'_L3_result'));
					$topv = json_decode(Redis::get('allregionalfromarea'.$detail['area'].'_topv_result'));
				}

				return ([$all_service_result,$topv]);
			}
		}

		//get all branch list in those regional selected
		else if($detail['branch']=='all')
		{
			// dd('masuk');
			if($detail['button']=='L1')
			{
				// dd($date_data);
				$date_data['skrg']=$date_data['now'];
				$date_data['post_real']=$date_data['post'];

				$regionals = Regional::where('ID_AREA',3)->get();

				$all_regional_result = json_decode(Redis::get('regional_'.$detail['regional'].'L1_result'));
				$all_branch_result = json_decode(Redis::get('many_branch_fromregional_'.$detail['regional'].'L1'));
				$all_cluster_result = json_decode(Redis::get('manu_cluster_fromregional_'.$detail['regional'].'L1'));

				if(!$all_regional_result)	
				{
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
								$actual = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
								// dd($actual);
								$MOM = DB::select("select countMom('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
								// dd($MOM);
								$YOY = DB::select("select countYoy('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['post2']."', '".$date_data['now2']."') as result");
								$YTD = DB::select("select countYtd('cluster', '".$c->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
									'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
								$actual = $actual[0]->result;
								$MOM = $MOM[0]->result;
								$YOY = $YOY[0]->result;
								$YTD = $YTD[0]->result;

								$GAP = floatval($target - $actual);
								$achievement = round((($actual/$target)*100),2);
								// dd($MOM);
								$date_data['now'] = $date_data['mom2'];
								$date_data['post'] = $date_data['post_bulanlalu'];

								$actual_bulanlalu = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
								$actual_bulanlalu = $actual_bulanlalu[0]->result;

								$temp = array(
									'name'=>$c['NAMA'],
									'mom'=>$MOM,
									'ytd'=>$YTD,
									'actual'=>$actual,
									'yoy'=>$YOY,
									'target'=>$target,
									'actual_bulanlalu'=>$actual_bulanlalu,
									'now_bulanlalu'=>$date_data['mom2'],
									'now'=>$date_data['skrg'],
									'GAP'=>$GAP,
									'achievement'=>$achievement
								);
								// dd($temp);
								
								array_push($all_cluster_result,$temp);	
							}
							// dd($clusters);
							$date_data['now']=$date_data['skrg'];
							$date_data['post']=$date_data['post_real'];

							$target = $this->getTarget('branch',$b->ID);
							$actual = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							$MOM = DB::select("select countMom('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
							$YOY = DB::select("select countYoy('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['post2']."', '".$date_data['now2']."') as result");
							$YTD = DB::select("select countYtd('branch', '".$b->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
											'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
							$actual = $actual[0]->result;
							$MOM = $MOM[0]->result;
							$YOY = $YOY[0]->result;
							$YTD = $YTD[0]->result;

							$GAP = floatval($target - $actual);
							$achievement = round((($actual/$target)*100),2);

							$date_data['now'] = $date_data['mom2'];
							$date_data['post'] = $date_data['post_bulanlalu'];

							$actual_bulanlalu = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							$actual_bulanlalu = $actual_bulanlalu[0]->result;

							$temp = array(
								'name'=>$b['NAMA'],
								'mom'=>$MOM,
								'ytd'=>$YTD,
								'actual'=>$actual,
								'yoy'=>$YOY,
								'target'=>$target,
								'actual_bulanlalu'=>$actual_bulanlalu,
								'now_bulanlalu'=>$date_data['mom2'],
								'now'=>$date_data['skrg'],
								'GAP'=>$GAP,
								'achievement'=>$achievement
							);
							array_push($all_branch_result,$temp);	
						}

						$date_data['now']=$date_data['skrg'];
						$date_data['post']=$date_data['post_real'];


						$target = $this->getTarget('regional',$r->ID);
						$actual = DB::select("select countActual('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$MOM = DB::select("select countMom('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
						$YOY = DB::select("select countYoy('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['post2']."', '".$date_data['now2']."') as result");
						$YTD = DB::select("select countYtd('regional', '".$r->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
							'".$date_data['ytd2']."', '".$date_data['now2']."') as result");

						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;
						$YOY = $YOY[0]->result;
						$YTD = $YTD[0]->result;

						// $actual = $this->countActual('regional',$r->ID, $date_data);
						// $MOM = $this->countMom('regional',$r->ID, $date_data);
						// $YTD = $this->countYtd('regional',$r->ID, $date_data);
						// $YOY = $this->countYoy('regional',$r->ID, $date_data);
						// $target = $this->getTarget('regional',$r->ID);

						$GAP = floatval($target - $actual);
						$achievement = round((($actual/$target)*100),2);

						$date_data['now'] = $date_data['mom2'];
						$date_data['post'] = $date_data['post_bulanlalu'];

						$actual_bulanlalu = DB::select("select countActual('regional', '".$r->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$actual_bulanlalu = $actual_bulanlalu[0]->result;
						
						$all_regional_result = array(
							'name'=>$r['NAMA'],
							'mom'=>$MOM,
							'ytd'=>$YTD,
							'actual'=>$actual,
							'yoy'=>$YOY,
							'target'=>$target,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['mom2'],
							'now'=>$date_data['skrg'],
							'GAP'=>$GAP,
							'achievement'=>$achievement
							);

					}
					// $all_regional_result = collect($all_regional_result)->sortBy('actual')->reverse()->toArray();
					$all_branch_result = collect($all_branch_result)->sortBy('mom')->reverse()->toArray();
					$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();

					Redis::set('regional_'.$detail['regional'].'L1_result', json_encode($all_regional_result));
					Redis::set('many_branch_fromregional_'.$detail['regional'].'L1', json_encode($all_branch_result));
					Redis::set('many_cluster_fromregional_'.$detail['regional'].'L1', json_encode($all_cluster_result));

					$all_regional_result = json_decode(Redis::get('regional_'.$detail['regional'].'L1_result'));
					$all_branch_result = json_decode(Redis::get('many_branch_fromregional_'.$detail['regional'].'L1'));
					$all_cluster_result = json_decode(Redis::get('manu_cluster_fromregional_'.$detail['regional'].'L1'));

				}
				return ([$all_regional_result, $all_branch_result, $all_cluster_result]);
			}


			//calculate L3 on spesific regional
			else
			{
				$all_service_result = json_decode(Redis::get('allbranchfromregional_'.$detail['regional'].'_L3_result'));
				$topv = json_decode(Redis::get('allbranchfromregional_'.$detail['regional'].'_topv_result'));
				if(!$all_service_result)
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
						$actual = DB::select("select countActual('regional', '".$detail['regional']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM = DB::select("select countMom('regional', '".$detail['regional']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;

						$date_data['mom1'] = $date_data['mom1_bulanlalu'];
						$date_data['mom2'] = $date_data['mom2_bulanlalu'];
						$date_data['post'] = $date_data['post_bulanlalu'];
						$date_data['now'] = $b;

						$actual_bulanlalu = DB::select("select countActual('regional', '".$detail['regional']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM_bulanlalu = DB::select("select countMom('regional', '".$detail['regional']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");

						$actual_bulanlalu = $actual_bulanlalu[0]->result;
						$MOM_bulanlalu = $MOM_bulanlalu[0]->result;
						$absolut = floatval($actual - $actual_bulanlalu);

						// $name = $s->fromService->NAMA;
						// $actual = $this->countActual('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);
						// $MOM = $this->countMom('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);

						// $date_data['mom1'] = $date_data['mom1_bulanlalu'];
						// $date_data['mom2'] = $date_data['mom2_bulanlalu'];

						// $date_data['post'] = $date_data['post_bulanlalu'];
						// $date_data['now'] = $date_data['now_bulanlalu'];

						// $actual_bulanlalu = $this->countActual('regional', $s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);
						// $MOM_bulanlalu = $this->countMom('regional',$s->ID_SERVICE, $date_data, $detail['regional'], $detail['button']);;
						// $absolut = floatval($actual - $actual_bulanlalu);


						$temp = array(
							'name'=>$name,
							'mom'=>$MOM,
							'actual'=>$actual,
							'mom_bulanlalu'=>$MOM_bulanlalu,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['mom2'],
							'now'=>$date_data['skrg'],
							'absolut'=>$absolut
							);
						array_push($all_service_result,$temp);
					}
					$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
					$topv = array_slice($all_service_result, 0, 5, true);
					Redis::set('allbranchfromregional_'.$detail['regional'].'_L3_result', json_encode($all_service_result));
					Redis::set('allbranchfromregional_'.$detail['regional'].'_topv_result', json_encode($topv));
					$all_service_result = json_decode(Redis::get('allbranchfromregional_'.$detail['regional'].'_L3_result'));
					$topv = json_decode(Redis::get('allbranchfromregional_'.$detail['regional'].'_topv_result'));
				}
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

				$all_branch_result = json_decode(Redis::get('many_branch_frombranch_'.$detail['branch'].'L1'));
				$all_cluster_result = json_decode(Redis::get('many_cluster_frombranch_'.$detail['branch'].'L1'));
				if(!$all_branch_result)
				{
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
							$actual = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							
							$MOM = DB::select("select countMom('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
							$YOY = DB::select("select countYoy('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['post2']."', '".$date_data['now2']."') as result");
							$YTD = DB::select("select countYtd('cluster', '".$c->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
								'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
							$actual = $actual[0]->result;
							$MOM = $MOM[0]->result;
							$YOY = $YOY[0]->result;
							$YTD = $YTD[0]->result;

							$GAP = floatval($target - $actual);
							$achievement = round((($actual/$target)*100),2);
							// dd($MOM);
							$date_data['now'] = $date_data['mom2'];
							$date_data['post'] = $date_data['post_bulanlalu'];

							$actual_bulanlalu = DB::select("select countActual('cluster', '".$c->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
							$actual_bulanlalu = $actual_bulanlalu[0]->result;

							$temp = array(
								'name'=>$c['NAMA'],
								'mom'=>$MOM,
								'ytd'=>$YTD,
								'actual'=>$actual,
								'yoy'=>$YOY,
								'target'=>$target,
								'actual_bulanlalu'=>$actual_bulanlalu,
								'now_bulanlalu'=>$date_data['mom2'],
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
						$actual = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$MOM = DB::select("select countMom('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['mom1']."', '".$date_data['mom2']."', 0, 'L1') as result");
						$YOY = DB::select("select countYoy('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."',
							'".$date_data['post2']."', '".$date_data['now2']."') as result");
						$YTD = DB::select("select countYtd('branch', '".$b->ID."', '".$date_data['ytd1']."', '".$date_data['now']."',
										'".$date_data['ytd2']."', '".$date_data['now2']."') as result");
						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;
						$YOY = $YOY[0]->result;
						$YTD = $YTD[0]->result;

						$GAP = floatval($target - $actual);
						$achievement = round((($actual/$target)*100),2);

						$date_data['now'] = $date_data['mom2'];
						$date_data['post'] = $date_data['post_bulanlalu'];

						$actual_bulanlalu = DB::select("select countActual('branch', '".$b->ID."', '".$date_data['post']."', '".$date_data['now']."', 0, 'L1') as result");
						$actual_bulanlalu = $actual_bulanlalu[0]->result;

						$all_branch_result = array(
							'name'=>$b['NAMA'],
							'mom'=>$MOM,
							'ytd'=>$YTD,
							'actual'=>$actual,
							'yoy'=>$YOY,
							'target'=>$target,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['mom2'],
							'now'=>$date_data['skrg'],
							'GAP'=>$GAP,
							'achievement'=>$achievement
						);
					}

					$all_cluster_result = collect($all_cluster_result)->sortBy('mom')->reverse()->toArray();
					Redis::set('many_branch_frombranch_'.$detail['branch'].'L1', json_encode($all_branch_result));
					Redis::set('many_cluster_frombranch_'.$detail['branch'].'L1', json_encode($all_cluster_result));
					$all_branch_result = json_decode(Redis::get('many_branch_frombranch_'.$detail['branch'].'L1'));
					$all_cluster_result = json_decode(Redis::get('many_cluster_frombranch_'.$detail['branch'].'L1'));
				}
				return ([$all_branch_result, $all_cluster_result]);
			}
			//calculate L3 on spesific branch
			else
			{
				$all_service_result = json_decode(Redis::get('allclusterfrombranch_'.$detail['branch'].'_L3_result'));
				$topv = json_decode(Redis::get('allclusterfrombranch_'.$detail['branch'].'_topv_result'));
				if(!$all_service_result)
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
						$actual = DB::select("select countActual('branch', '".$detail['branch']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM = DB::select("select countMom('branch', '".$detail['branch']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$actual = $actual[0]->result;
						$MOM = $MOM[0]->result;

						$date_data['mom1'] = $date_data['mom1_bulanlalu'];
						$date_data['mom2'] = $date_data['mom2_bulanlalu'];
						$date_data['post'] = $date_data['post_bulanlalu'];
						$date_data['now'] = $b;

						$actual_bulanlalu = DB::select("select countActual('branch', '".$detail['branch']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
						$MOM_bulanlalu = DB::select("select countMom('branch', '".$detail['branch']."', '".$date_data['post']."', '".$date_data['now']."',
									'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");

						$actual_bulanlalu = $actual_bulanlalu[0]->result;
						$MOM_bulanlalu = $MOM_bulanlalu[0]->result;
						$absolut = floatval($actual - $actual_bulanlalu);

						$temp = array(
							'name'=>$name,
							'mom'=>$MOM,
							'actual'=>$actual,
							'mom_bulanlalu'=>$MOM_bulanlalu,
							'actual_bulanlalu'=>$actual_bulanlalu,
							'now_bulanlalu'=>$date_data['mom2'],
							'now'=>$date_data['skrg'],
							'absolut'=>$absolut
							);
						array_push($all_service_result,$temp);
					}
					
					$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
					$topv = array_slice($all_service_result, 0, 5, true);				
					Redis::set('allclusterfrombranch_'.$detail['branch'].'_L3_result', json_encode($all_service_result));
					Redis::set('allclusterfrombranch_'.$detail['branch'].'_topv_result', json_encode($topv));
					$all_service_result = json_decode(Redis::get('allclusterfrombranch_'.$detail['branch'].'_L3_result'));
					$topv = json_decode(Redis::get('allclusterfrombranch_'.$detail['branch'].'_topv_result'));
				}
				return ([$all_service_result,$topv]);
			}	
		}
		//get calculate L3 on spesific cluster
		else
		{			
			// dd($detail['cluster']);
			$all_service_result = json_decode(Redis::get('specific_cluster_'.$detail['cluster'].'_L3_result'));
			$topv = json_decode(Redis::get('specific_cluster_'.$detail['cluster'].'_topv_result'));
			if(!$all_service_result)
			{
				$service = Revenue::with('fromService')->whereDate('DATE','>=',$date_data['post'])->whereDate('Date', '<=', $date_data['now'])->where('ID_CLUSTER',$detail['cluster'])->groupBy('ID_SERVICE')->get();
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
					$actual = DB::select("select countActual('cluster', '".$detail['cluster']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
					$MOM = DB::select("select countMom('cluster', '".$detail['cluster']."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");
					$MOM2 = $this->countMom('cluster', $detail['cluster'], $date_data, $s->ID_SERVICE, $detail['button']);

					$actual = $actual[0]->result;
					$MOM = $MOM[0]->result;
					$date_data['mom1'] = $date_data['mom1_bulanlalu'];
					$date_data['mom2'] = $date_data['mom2_bulanlalu'];
					$date_data['post'] = $date_data['post_bulanlalu'];
					$date_data['now'] = $b;

					$actual_bulanlalu = DB::select("select countActual('cluster', '".$detail['cluster']."', '".$date_data['post']."', '".$date_data['now']."',  '".$s->ID_SERVICE."', '".$detail['button']."') as result");
					$MOM_bulanlalu = DB::select("select countMom('cluster', '".$detail['cluster']."', '".$date_data['post']."', '".$date_data['now']."',
								'".$date_data['mom1']."', '".$date_data['mom2']."', '".$s->ID_SERVICE."', '".$detail['button']."') as result");

					$actual_bulanlalu = $actual_bulanlalu[0]->result;
					$MOM_bulanlalu = $MOM_bulanlalu[0]->result;
					$absolut = floatval($actual - $actual_bulanlalu);


					$temp = array(
						'name'=>$name,
						'mom'=>$MOM,
						'actual'=>$actual,
						'mom_bulanlalu'=>$MOM_bulanlalu,
						'actual_bulanlalu'=>$actual_bulanlalu,
						'now_bulanlalu'=>$b,
						'now'=>$date_data['skrg'],
						'absolut'=>$absolut
						);
					array_push($all_service_result,$temp);
				}
				$all_service_result = collect($all_service_result)->sortBy('actual')->reverse()->toArray();
				$topv = array_slice($all_service_result, 0, 5, true);
				Redis::set('specific_cluster_'.$detail['cluster'].'_L3_result', json_encode($all_service_result));
				Redis::set('specific_cluster_'.$detail['cluster'].'_topv_result', json_encode($topv));
				$all_service_result = json_decode(Redis::get('specific_cluster_'.$detail['cluster'].'_L3_result'));
				$topv = json_decode(Redis::get('specific_cluster_'.$detail['cluster'].'_topv_result'));
			}
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
    	// dd($type);

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
    		// dd($wida);
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
		if($mom2==0) {$MOM=0;}
		else
			$MOM = round((($mom1/$mom2)-1)*100,2);
    	return $MOM;
    }
}