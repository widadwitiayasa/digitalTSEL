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
use App\Models\Target;
use DateTime;

class TargetController extends Controller
{

	public function showTarget()
	{
		$data['Area'] = Area::get();
    	$data['Regional'] = Regional::where('ID_AREA',3)->get();
    	$data['Branch'] = Branch::get();
    	$data['Cluster'] = Cluster::get();
    	$data['Service'] = Service::get();

    	return view('admin.showTarget',$data);
	}


	public function inputTarget(Request $req)
    {
        $result = $this->calculatedTarget($req);
        $idarea = 3;
        $idregional = $req->input('INPUTREGIONAL');
        $idbranch = $req->input('INPUTBRANCH');
        $idcluster = $req->input('INPUTCLUSTER');
        if($idcluster=='all')
        {
            $wida = Target::with('cluster')->whereHas('cluster',function ($a) use ($idbranch){
                $a->where('ID_BRANCH',$idbranch);
            })->get();
        }
        else
        {
            $wida = Target::with('cluster')->whereHas('cluster',function ($a) use ($idbranch){
                $a->where('ID_BRANCH',$idbranch);
            })->where('ID_CLUSTER',$idcluster)->get();
        }
        $data['area'] = $idarea;
        $data['regional'] = $idregional;
        $data['cluster'] = $idcluster;
    	$data['result'] = $wida;
    	$data['branch'] = $idbranch;
        $data['target'] = $result;
    	return view('admin.inputTarget',$data);
    }

    public function editTarget(Request $req,$ID)
    {
    	try {
			$coba = Target::where('ID',$ID)->update(array('TARGET'=>$req->target));

    	} catch (Exception $e) {
    		
    	}
        
        $result = $this->calculatedTarget($req);
        $idarea = 3;
        $idregional = $req->input('INPUTREGIONAL');
        $idbranch = $req->input('INPUTBRANCH');
        $idcluster = $req->input('INPUTCLUSTER');
        if($idcluster=='all')
        {
            $wida = Target::with('cluster')->whereHas('cluster',function ($a) use ($idbranch){
                $a->where('ID_BRANCH',$idbranch);
            })->get();
        }
        else
        {
            $wida = Target::with('cluster')->whereHas('cluster',function ($a) use ($idbranch){
                $a->where('ID_BRANCH',$idbranch);
            })->where('ID_CLUSTER',$idcluster)->get();
        }
        $data['area'] = $idarea;
        $data['regional'] = $idregional;
        $data['cluster'] = $idcluster;
        $data['result'] = $wida;
        $data['branch'] = $idbranch;
        $data['target'] = $result;
    	return view('admin.inputTarget',$data);
    }

    public function calculatedTarget(Request $req)
    {
        $idarea = 3;
        $idregional = $req->input('INPUTREGIONAL');
        $idbranch = $req->input('INPUTBRANCH');
        $idcluster = $req->input('INPUTCLUSTER');
        //target area
        $targetarea = Target::with('cluster.branch.regional.area')->whereHas('cluster', function ($a) use ($idarea){
            $a->whereHas('branch',function ($b) use ($idarea){
                $b->whereHas('regional',function ($c) use ($idarea){
                    $c->whereHas('area',function ($d) use ($idarea){
                        $d->where('ID',$idarea);
                    });
                });
            });
        })->get();

        //target regional
        $targetregional = Target::with('cluster.branch.regional')->whereHas('cluster', function ($a) use ($idbranch,$idarea,$idregional){
            $a->whereHas('branch',function ($b) use ($idbranch,$idarea,$idregional){
                $b->whereHas('regional',function ($c) use ($idbranch,$idarea,$idregional){
                    $c->whereHas('area',function ($d) use ($idbranch,$idarea,$idregional){
                        $d->where('ID',$idarea);
                    })->where('ID',$idregional);
                });
            });
        })->get();

        //target branch
        $targetbranch = Target::with('cluster.branch')->whereHas('cluster', function ($a) use ($idbranch,$idarea,$idregional){
            $a->whereHas('branch',function ($b) use ($idbranch,$idarea,$idregional){
                $b->whereHas('regional',function ($c) use ($idbranch,$idarea,$idregional){
                    $c->whereHas('area',function ($d) use ($idbranch,$idarea,$idregional){
                        $d->where('ID',$idarea);
                    })->where('ID',$idregional);
                })->where('ID',$idbranch);
            });
        })->get();
        $totaltargetarea = $targetarea->sum('TARGET');
        $totaltargetregional = $targetregional->sum('TARGET');
        $totaltargetbranch = $targetbranch->sum('TARGET');
        $namaarea = $targetarea[0]->cluster->branch->regional->area->NAMA;
        $namaregional = $targetarea[0]->cluster->branch->regional->NAMA;
        $namabranch = $targetarea[0]->cluster->branch->NAMA;
        $wida = array(
            "totaltargetarea" => $totaltargetarea,
            "totaltargetregional" => $totaltargetregional,
            "totaltargetbranch" => $totaltargetbranch,
            "namaarea" => $namaarea,
            "namaregional" => $namaregional,
            "namabranch" => $namabranch
            );
        return $wida;
        // dd($wida);
    }

}