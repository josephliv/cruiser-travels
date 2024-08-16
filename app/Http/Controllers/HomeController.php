<?php

namespace App\Http\Controllers;

use App\LeadMails;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        // Get an Agents Group Information
        $resultGroups = new \App\Group;
        // This can be NULL if user_group = 0 or NULL
        $userGroup = $resultGroups->group_name($user_id);
            $beginOfDay = Carbon::now()->startOfDay();
            $endOfDay = Carbon::now()->endOfDay();

        if (\Auth::user()->is_admin) {
            
            $leadMails = array(
                'subDay'         => $beginOfDay,
                'total'          => LeadMails::count(),
                'available'      => LeadMails::where('agent_id', '=', 0)->count(),
                'totalSent'      => LeadMails::where('agent_id', '>', 0)->count(),
                'totalReject'    => LeadMails::where('rejected', '=', 1)->count(),
                // 'total24h'       => LeadMails::where('received_date', '>', Carbon::now()->startOfDay())->count(),

                // 'totalSent24h'   => LeadMails::where('agent_id', '>', 0)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
                // 'totalReject24h' => LeadMails::where('rejected', '=', 1)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
                'total24h' => LeadMails::whereBetween('received_date', [$beginOfDay, $endOfDay])->count(),
                'totalSent24h' => LeadMails::where('agent_id', '>', 0)->whereBetween('updated_at', [$beginOfDay, $endOfDay])->count(),
                'totalReject24h' => LeadMails::where('rejected', '=', 1)->whereBetween('updated_at', [$beginOfDay, $endOfDay])->count(),
            );
        } else {
            // $leadMails = array(
            //     'subDay'         => $beginOfDay,
            //     'total'          => LeadMails::count(),
            //     'available'      => LeadMails::where('agent_id', '=', 0)->where('to_group', '<=', $userGroup['id'])->count(),
            //     'totalSent'      => LeadMails::where('agent_id', $user_id)->count(),
            //     'totalReject'    => LeadMails::where('agent_id', $user_id)->where('rejected', '=', 1)->count(),
            //     'total24h'       => LeadMails::where('agent_id', $user_id)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
            //     'totalSent24h'   => LeadMails::where('agent_id', $user_id)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
            //     'totalReject24h' => LeadMails::where('agent_id', $user_id)->where('rejected', '=', 1)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
            // );
            $leadMails = array(
                'subDay' => $beginOfDay,
                'total' => LeadMails::count(),
                'available' => LeadMails::where('agent_id', '=', 0)
                    ->where('to_group', '<=', $userGroup['id'])
                    ->count(),
                'totalSent' => LeadMails::where('agent_id', $user_id)
                    ->whereBetween('updated_at', [$beginOfDay, $endOfDay])
                    ->count(),
                'totalReject' => LeadMails::where('agent_id', $user_id)
                    ->where('rejected', '=', 1)
                    ->whereBetween('updated_at', [$beginOfDay, $endOfDay])
                    ->count(),
                'total24h' => LeadMails::where('agent_id', $user_id)
                    ->whereBetween('updated_at', [$beginOfDay, $endOfDay])
                    ->count(),
                // Assuming totalSent24h and totalReject24h meant to reflect emails sent and rejected within the last 24 hours by the agent
                'totalSent24h' => LeadMails::where('agent_id', $user_id)
                    ->where('updated_at', '>', $beginOfDay)
                    ->count(),
                'totalReject24h' => LeadMails::where('agent_id', $user_id)
                    ->where('rejected', '=', 1)
                    ->where('updated_at', '>', $beginOfDay)
                    ->count(),
            );

            $resultUsers = new \App\User;
            $userInfo = $resultUsers->find($user_id);

            // Debug ONLY for Display of Leads for each Member
            if (config('app.debug')) {
                $leads = new MailBoxController();
                $lead = $leads->get_lead_queue($userInfo['user_group'], -1);
            }

            $data = array(
                'group'     => $userGroup,
                'userInfo'  => $userInfo,
                'lead'      => $lead ?? '',
                'leadMails' => $leadMails
            );
        }

        return \Auth::user()->is_admin ? view('dashboardadmin', compact('leadMails')) : view('dashboardagent', $data);
    }
}
