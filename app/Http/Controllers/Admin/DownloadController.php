<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Excel;

class DownloadController extends Controller
{
	public function downloadCSV()
	{
		Excel::create('New file', function($excel) {

		    $excel->sheet('New sheet', function($sheet) {

		        $sheet->loadView('Admin.report.view');

		    });

		});
	}
}