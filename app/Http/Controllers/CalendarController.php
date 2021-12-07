<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::whereDate('start', '>=', $request->start)
                       ->whereDate('end', '<=', $request->end)
                       ->get(['id', 'title', 'start', 'end']);
            return response()->json($data);
        }
        return view('kalender.index');
    }

    public function ajax(Request $request)
    {
        switch ($request->type) {
           case 'add':
              $Kalender = Kalender::create([
                  'title' => $request->title,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
              return response()->json($Kalender);
             break;

           case 'update':
              $Kalender = Kalender::find($request->id)->update([
                  'title' => $request->title,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
              return response()->json($Kalender);
             break;


           case 'delete':
              $Kalender = Kalender::find($request->id)->delete();
              return response()->json($Kalender);
             break;

           default:
             # code...
             break;

        }
    }
}
