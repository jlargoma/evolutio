<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Years;

class YearsController  extends AppController
{
	public function changeActiveYear(Request $request)
	{
		foreach (Years::all() as $key => $year){
			$year->active = 0;
			$year->save();
		}

		if (!$request->has('year'))
			throw new ModelNotFoundException('Year not found', 404);

		$newYear = Years::find($request->get('year'));
		$newYear->active = 1;

		if ($newYear->save())
			return new Response("OK, year changed", 200);
		else
			return new Response("Error undefined", 500);

	}

	public function changeMonthActiveYear(Request $request)
	{
		$aux  = $request->input('dates');
		$date = explode(' - ', $aux);
                
                $aux = explode('/', trim($date[0]));
                $start = $aux[2].'-'.$aux[1].'-'.$aux[0];
                
                $aux = explode('/', trim($date[1]));
                $finish = $aux[2].'-'.$aux[1].'-'.$aux[0];

                $year = $this->getActiveYear();
		$year->start_date = $start;
		$year->end_date = $finish;

		if ($year->save())
			return new Response("OK, Months of active year changed", 200);
		else
			return new Response("Error undefined", 500);
	}
}