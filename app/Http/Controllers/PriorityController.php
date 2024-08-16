<?php

namespace App\Http\Controllers;

use App\Priority;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PriorityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //dd(Priority::paginate(15)->first()->group()->first()->name);
//        return view('priorities.index', ['priorities' => Priority::paginate(15)]);
        return view('priorities.index', ['priorities' => Priority::get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $groups = Group::orderBy('order')->get();
        return view('priorities.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $model = new Priority();
        $input = $request->all();
        array_shift($input);
        $model->create($input);

        return redirect()->route('priorities.index')->withStatus(__('Priority successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param Priority $priority
     * @return Response
     */
    public function show(Priority $priority)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Priority $priority
     * @return Response
     */
    public function edit(Priority $priority)
    {  
        $groups = Group::orderBy('order')->get();
        return view('priorities.edit', compact('priority', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request  $request
     * @param Priority $priority
     * @return Response
     */
    public function update(Request $request, Priority $priority)
    {

        $input = $request->all();
        array_shift($input);
        array_shift($input);

        $priority->update($input);
        
        return redirect()->route('priorities.index')->withStatus(__('Priority successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Priority $priority
     * @return Response
     */
    public function destroy(Priority $priority)
    {

        $priority->delete();

        return redirect()->route('priorities.index')->withStatus(__('Priority successfully deleted.'));
    }
}
