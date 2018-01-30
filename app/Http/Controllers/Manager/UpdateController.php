<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    public function array2csv($result, $type, $detail)
	{
		dd($result);
		exit();
		if($detail['button']=='L2')
		{
			// if($tipe=='L2')
			// {
			// 	foreach ($result[$i] as $r) 
			// 	{
			// 		$r['name']
			// 		number_format($r['actual_bulanlalu'], 0, ".", ".")
			// 		number_format($r['actual'], 0, ".", ".")
			// 		number_format($r['target'], 0, ".", ".")
			// 		number_format($r['GAP'], 0, ".", ".")
			// 		$r['mom']
			// 		$r['ytd']
			// 		$r['yoy']
			// 	}
			// }
			// else
			// {
			// 	foreach ($result[$i] as $r) 
			// 	{
			// 		$r['name']
			// 		$r['mom']
			// 		number_format($r['absolut'], 0, ".", ".")
			// 	}
			// }
		}
		else
		{
			// if()
		}

	     return null;

	   ob_start();
	   $df = fopen("php://output", 'w');
	   fputcsv($df, array_keys(reset($array)));
	   foreach ($array as $row) {
	      fputcsv($df, $row);
	   }
	   fclose($df);
	   $this->download_send_headers("data_export_" . date("Y-m-d") . ".csv");
	   return ob_get_clean();
	}

	public function download_send_headers($filename) 
	{
	    // disable caching
	    $now = gmdate("D, d M Y H:i:s");
	    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	    header("Last-Modified: {$now} GMT");

	    // force download  
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");

	    // disposition / encoding on response body
	    header("Content-Disposition: attachment;filename={$filename}");
	    header("Content-Transfer-Encoding: binary");
	}
}
