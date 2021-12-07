<p align="center"><a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/niomictomi/uuid-for-laravel/main/file/tomslock.png" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


# Proses setup
Set up pada Database, Model, Controller, View, dan Route

## Setup Database / Migration
```sh
    Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

```

## Model

Siapkan fillable pada model
```sh
protected $fillable = [
        'title', 'start', 'end'
    ];
```


## Controller
Buat methode Index
```sh
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
```
dan pada methode Ajax
```sh

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
```


## View
Siapkan pada body HTML Blade
```sh
<div class="container">
        <h1>Tutorial Buat Kalender dengan Laravel - Tomsolck</h1>
        <div id='calendar'></div>
    </div>

```
Dan pada script
```sh

    <script>
        $(document).ready(function() {

            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendar = $('#calendar').fullCalendar({
                editable: true,
                events: SITEURL + "/fullcalender",
                displayEventTime: false,
                editable: true,

                eventRender: function(event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;

                    } else {
                        event.allDay = false;
                    }
                },

                selectable: true,
                selectHelper: true,

                select: function(start, end, allDay) {
                    var title = prompt('Event Title:');
                    if (title) {
                        var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
                        var end = $.fullCalendar.formatDate(end, "Y-MM-DD");

                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            data: {
                                title: title,
                                start: start,
                                end: end,
                                type: 'add'
                            },

                            type: "POST",
                            success: function(data) {
                                displayMessage("Event Created Successfully");

                                calendar.fullCalendar('renderEvent',
                                    {
                                        id: data.id,
                                        title: title,
                                        start: start,
                                        end: end,
                                        allDay: allDay

                                    }, true);

                                calendar.fullCalendar('unselect');
                            }
                        });
                    }

                },

                eventDrop: function(event, delta) {

                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

                    $.ajax({

                        url: SITEURL + '/fullcalenderAjax',
                        data: {
                            title: event.title,
                            start: start,
                            end: end,
                            id: event.id,
                            type: 'update'
                        },

                        type: "POST",

                        success: function(response) {
                            displayMessage("Event Updated Successfully");
                        }

                    });

                },

                eventClick: function(event) {

                    var deleteMsg = confirm("Do you really want to delete?");
                    if (deleteMsg) {
                        $.ajax({
                            type: "POST",
                            url: SITEURL + '/fullcalenderAjax',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },

                            success: function(response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Event Deleted Successfully");
                            }

                        });

                    }
                }

            });
        });

        function displayMessage(message) {

            toastr.success(message, 'Event');

        }
    </script>
```

## ROute
```sh
Route::get('fullcalender', [CalendarController::class, 'index']);

Route::post('fullcalenderAjax', [CalendarController::class, 'ajax']);

```
