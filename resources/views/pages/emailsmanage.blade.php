@extends('layouts.app', ['activePage' => 'leads-management', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Leads Management', 'activeButton' => 'laravel'])

@section('content')

    @php
        $unassignedTotal = $assignedTotal = $rejectedTotal = $reassignedTotal = 0;

    @endphp
    <style>
        .table {
            min-width: 90%;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="col-12 mt-2">
                @include('alerts.success')
                @include('alerts.errors')
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Leads</h3>
                            <p class="card-category ">Here you can view or delete the leads.</p>
                        </div>


                        <div class="card-body">
                            @php
                                foreach ($leadMails as $leadMail){

                                            if(optional($leadMail)->agent_id == 0){
                                                $unassignedTotal++;
                                            } elseif(optional($leadMail)->rejected > 0){
                                                $rejectedTotal++;
                                            } elseif(optional($leadMail)->old_agent_id > 0){
                                                $reassignedTotal++;
                                            } else {
                                                $assignedTotal++;
                                            }
                                }
                            @endphp
                           

                            <div class="p-4 text-center">
                                <label for="time-set">Run the report by dates: </label>
                                <form method='POST'>
                                    @csrf
                                    <input type="date" id="from-date" name="from-date" value="{{explode(' ', $dateFrom)[0]}}"> to <input type="date" id="to-date" name="to-date" value="{{explode(' ', $dateTo)[0]}}">
                                    <div class="row">
                                        <div class="form-group" style="width:100%">
                                            <label>User:</label>
                                            <select id="user" name="user" class="col-md-2">
                                                    <option id="-1" selected>All</option>
                                                @foreach ($users as $user)
                                                    <option value="{{$user->id}}" {{ $user->id == $current_user_id ? 'selected' : ''}}>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <button type="submit" class="btn-outline-primary">Submit</button>
                                </form>
                            </div>
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills nav-justified mb-4" id="myTab" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link" id="total-tab" data-toggle="tab" href="#totalLeads" role="tab" aria-controls="home" aria-selected="true">Total Leads ({{ $leadMails->count() }})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#unassigned" role="tab" aria-controls="home" aria-selected="true">Unassigned ({{$unassignedTotal}})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#assigned" role="tab" aria-controls="profile" aria-selected="false">Assigned ({{$assignedTotal}})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="messages-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="messages" aria-selected="false">Rejected ({{$rejectedTotal}})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="settings-tab" data-toggle="tab" href="#reassigned" role="tab" aria-controls="settings" aria-selected="false">Reassigned ({{$reassignedTotal}})</a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane" id="totalLeads" role="tabpanel" aria-labelledby="home-tab">
                                    <div id="totalLeads">
                                        <table class="table table-bordered table-striped" id="lead-table0">
                                            <thead style="position: sticky; top: 0; z-index: 99; background-color:aqua; font-weight: bold;">
                                            <th>#</th>
                                            <th>Sender</th>
                                            <th>Status</th>
                                            <th>Subject Line</th>
                                            <th>Group</th>
                                            <th>Priority</th>
                                            <th>Time/date</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($leadMails as $leadMail)
                                                <tr>
                                                    <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                                    <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                                    <td class="col-md-2">

                                                        @if (optional($leadMail)->agent_id == 0)
                                                            Unassigned
                                                        @elseif (optional($leadMail)->old_agent_id != 0 && !$leadMail->rejected)
                                                            Reassigned from {{optional(optional(optional($leadMail)->old_agent())->first())->name}} to {{optional(optional(optional($leadMail)->agent())->first())->name}}
                                                            <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="reassigned" data-target="#leadsModal" class="getbody d-inline-block" style="cursor: pointer" title="Reassigned Msg."> &#128393;</a>

                                                        @elseif (optional($leadMail)->rejected > 0)
                                                            Rejected by <br>
                                                            {{optional(optional(optional($leadMail)->agent())->first())->name}}
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="rejected" data-target="#leadsModal" class="getbody d-inline-block" style="cursor: pointer" title="Rejected message">
                                                                &#128393;</a>
                                                        @elseif (optional($leadMail)->agent_id > 0)
                                                            Assigned to <br> {{optional(optional($leadMail->agent())->first())->name}}


                                                        @endif
                                                    </td>
                                                    <td class="col-md-4"><span id="mail-subject">{{optional($leadMail)->subject}}</span></td>
                                                    <td>{{($leadMail->group->name)??'N/A'}}</td>
                                                    <td>{{$leadMail->priority}}</td>
                                                    <td class="col-md-2"><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span></td>
                                                    <td class="d-flex justify-content-end">
                                                        {{-- Attachment --}}
                                                        @if(optional($leadMail)->attachment)
                                                            <a href="{{route('leads.download', optional($leadMail)->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i
                                                                        class="fa fa-paperclip text-primary font-weight-bold"></i></a>
                                                        @else
                                                            <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                        @endif
                                                        {{-- Read full email --}}
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"
                                                                                                                                                                                                                           title="Read full email."></i></a>
                                                        {{-- Manually send email --}}
                                                        <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal"
                                                           class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>
                                                        {{-- Delete --}}
                                                        <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"
                                                           s><i class="fa fa-times" title="Delete."></i></a>
                                                    </td>
                                                </tr>

                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane  active" id="unassigned" role="tabpanel" aria-labelledby="home-tab">
                                    <div id="unassigned">
                                        <table class="table table-bordered table-striped" id="lead-table1">
                                            <thead style="text-align: center; position: sticky; top: 0; z-index: 99; background-color:aqua; font-weight: bold;">
                                            <th>#</th>
                                            <th>Sender</th>
                                            <th>Subject Line</th>
                                            <th>Group</th>
                                            <th>Priority</th>
                                            <th>Time/date</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($leadMails as $leadMail)
                                                @if(optional($leadMail)->agent_id == 0)
                                                    <tr>
                                                        <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                                        <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                                        <td class="col-12 col-md-6"><span id="mail-subject">{{optional($leadMail)->subject}}</span></td>
                                                        <td>{{($leadMail->group->name)??'N/A'}}</td>
                                                        <td>{{$leadMail->priority}}</td>
                                                        <td class="col-md-2"><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span></td>
                                                        <td class="d-flex justify-content-center">
                                                            @if(optional($leadMail)->attachment)
                                                                <a href="{{route('leads.download', optional($leadMail)->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i
                                                                            class="fa fa-paperclip text-primary font-weight-bold"></i></a>
                                                            @else
                                                                <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                            @endif
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"
                                                                                                                                                                                                                               title="Read full email."></i></a>
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal"
                                                               class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                            <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"
                                                               s><i class="fa fa-times" title="Delete."></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="assigned" role="tabpanel" aria-labelledby="profile-tab">
                                    <div id="assigned">
                                        <table class="table table-bordered table-striped" id="lead-table2">
                                            <thead style="position: sticky; top: 0; z-index: 99; background-color:aqua; font-weight: bold;">
                                            <th>#</th>
                                            <th>Sender</th>
                                            <th>Subject Line</th>
                                            <th>Agent</th>
                                            <th>Group</th>
                                            <th>Priority</th>
                                            <th>Time/date</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($leadMails as $leadMail)
                                                {{-- @if($leadMail->agent_id > 0) --}}
                                                @if ($leadMail->agent_id > 0 AND empty($leadMail->old_agent_id) AND $leadMail->rejected == 0)
                                                    <tr>
                                                        <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                                        <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                                        <td class="col-12 col-md-6">{{$leadMail->subject}}</td>
                                                        <td>{{optional(optional($leadMail->agent())->first())->name}}</td>
                                                        <td>{{($leadMail->group->name)??'N/A'}}</td>
                                                        <td>{{$leadMail->priority}}</td>
                                                        <td><span id="mail-date">{{\Carbon\Carbon::parse($leadMail->received_date)->format('m/d/Y g:i A')}}</span></td>
                                                        <td class="d-flex justify-content-end">
                                                            @if($leadMail->attachment)
                                                                <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                            @else
                                                                <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                            @endif
                                                            <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"
                                                                                                                                                                                                                     title="Read full email."></i></a>
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal"
                                                               class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                            <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"
                                                               s><i class="fa fa-times" title="Delete."></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="rejected" role="tabpanel" aria-labelledby="messages-tab">
                                    <div id="rejected">
                                        <table class="table table-bordered table-striped" id="lead-table3">
                                            <thead style="position: sticky; top: 0; z-index: 99; background-color:aqua; font-weight: bold; font-weight: bold;">
                                            <th>#</th>
                                            <th>Sender</th>
                                            <th>Subject Line</th>
                                            <th>Agent</th>
                                            <th>Group</th>
                                            <th>Priority</th>
                                            <th>Time/date</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($leadMails as $leadMail)
                                                @if(optional($leadMail)->rejected != 0)
                                                    <tr>
                                                        <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                                        <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                                        <td class="col-12 col-md-6">{{optional($leadMail)->subject}}</td>
                                                        <td>{{optional(optional(optional($leadMail)->agent())->first())->name}}</td>
                                                        <td>{{($leadMail->group->name)??'N/A'}}</td>
                                                        <td>{{$leadMail->priority}}</td>
                                                        <td><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span></td>

                                                        <td class="d-flex justify-content-center">
                                                            @if(optional($leadMail)->attachment)
                                                                <a href="{{route('leads.download', optional($leadMail)->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i
                                                                            class="fa fa-paperclip"></i></a>
                                                            @else
                                                                <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                            @endif
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="rejected" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-paper-plane"
                                                                                                                                                                                                                                   title="Rejected message"></i></a>
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"
                                                                                                                                                                                                                               title="Read full email."></i></a>
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal"
                                                               class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                            <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', optional($leadMail)->id) }}' : ''"
                                                               s><i class="fa fa-times" title="Delete."></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="reassigned" role="tabpanel" aria-labelledby="settings-tab">
                                    <div id="reassigned">
                                        <table class="table table-bordered table-striped table-responsive" id="lead-table4" style="width:100%">
                                            <thead style="text-align: center; padding: 5px 10px; position: sticky; top: 0; z-index: 99; background-color:aqua; font-weight: bold;">
                                            <th>#</th>
                                            <th>Sender</th>
                                            <th>Subject Line</th>
                                            <th>Orig. Agent</th>
                                            <th>Curr. Agent</th>
                                            <th>Group</th>
                                            <th>Priority</th>
                                            <th>Time/date</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($leadMails as $leadMail)
                                                @if(optional($leadMail)->old_agent_id != 0 && !$leadMail->rejected)
                                                    <tr>
                                                        <td><span id="mail-from">{{optional($leadMail)->id}}</span></td>
                                                        <td><span id="mail-from">{{optional($leadMail)->email_from}}</span></td>
                                                        <td><span id="mail-subject">{{optional($leadMail)->subject}}</span></td>
                                                        <td>{{optional(optional(optional($leadMail)->old_agent())->first())->name}}</td>
                                                        <td>{{optional(optional(optional($leadMail)->agent())->first())->name}}</td>
                                                        <td>{{($leadMail->group->name)??'N/A'}}</td>
                                                        <td>{{$leadMail->priority}}</td>

                                                        <td><span id="mail-date">{{\Carbon\Carbon::parse(optional($leadMail)->received_date)->format('m/d/Y g:i A')}}</span></td>
                                                        <td class="d-flex justify-content-center">
                                                            @if(optional($leadMail)->attachment)
                                                                <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block" title="Attachment available."><i class="fa fa-paperclip"></i></a>
                                                            @else
                                                                <a href="#" target="_blank" class="btn disabled btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                            @endif
                                                            <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="reassigned" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-paper-plane"
                                                                                                                                                                                                                           title="Reassigned message"></i></a>
                                                            <a data-toggle="modal" data-id="{{$leadMail->id}}" data-type="body" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"
                                                                                                                                                                                                                     title="Read full email."></i></a>
                                                            <a data-toggle="modal" data-id="{{optional($leadMail)->id}}" data-original-user="{{optional(optional($leadMail)->agent)->id}}" data-type="body" data-target="#sendLeadModal"
                                                               class="btn btn-link btn-warning direct-send-lead d-inline-block"><i class="fa fa-envelope" title="Manually Send Lead"></i></a>

                                                            <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''" s><i
                                                                        class="fa fa-times" title="Delete."></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leadsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #ccc;">
                    <h5 class="modal-title">...</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="leadsModalBody" style="max-height: 70vh; overflow-y: scroll;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendLeadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #ccc;">
                    <h5 class="modal-title lead">Select the user to transfer this lead:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="leadsModalBody" style="max-height: 70vh; overflow-y: scroll;">
                    <input type="hidden" id="transferLeadId" value=""/>
                    <input type="hidden" id="transferLeadOriginalAgent" value=""/>
                    <div class="form-group mt-4">
                        <!-- <label for="exampleFormControlInput1">Select the user to transfer this lead:</label> -->
                        <select class="form-control" id="transferLeadNewAgent">
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary direct-send-lead-button">Send Lead</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        function openReport(e, report, caller) {
            var i;
            var x = document.getElementsByClassName("type");

            e.preventDefault();

            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            document.getElementById(report).style.display = "block";
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#lead-table0').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
            $('#lead-table1').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
            $('#lead-table2').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
            $('#lead-table3').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
            $('#lead-table4').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
        });
        </script>
        

@endsection
