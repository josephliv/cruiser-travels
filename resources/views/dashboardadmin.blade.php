@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'Leadbox Management System', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
<style>
    .blue {background: mediumblue; color:beige;}
    </style>

    <div class="container mt-4">
     

        <div class="row justify-content-around">
            <div class="col-12 col-md-4">
                <h2>Admin Dashboard</h2>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row justify-content-around">
            <div class="col-12 col-md-4">
                <div class="card text-center" style="box-shadow: 0 0 5px #555;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item blue"><h3> Today's Stats</h3>
                            <p class="lead">This resets every 24 hours</p>
                        </li>
                        <li class="list-group-item">Emails Received: <span id="emails-sent">{{$leadMails['total24h']}}</span></li>
                        <li class="list-group-item">Emails Sent: <span id="emails-sent">{{$leadMails['totalSent24h']}}</span></li>
                        <li class="list-group-item">Emails rejected: <span id="emails-sent">{{$leadMails['totalReject24h']}}</span></li>

                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-center" style="box-shadow: 0 0 5px #555;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item blue"><h3> Take a lead.</h3>
                            <p class="lead">Receive a lead in your inbox</p>
                        </li>

                        <li class="list-group-item">
                            <div style="position: relative">
                                <div class="cover" title="A new lead has been sent to your inbox.">
                                    A lead has been sent to your email.
                                </div>
                                <a href="#" id="generateLeadBtn" class="btn btn-primary btn-lg" onclick="lead()" title="Click here to send a lead to your inbox.">
                                    Receive A Lead
                                </a>
                            </div>
                            <div class="btn-group dropright">
                               
                                <button style="padding: 0 14px;" type="button"
                                        class="btn btn-primary btn-sm dropdown-toggle-split" data-toggle="dropdown"
                                        aria-expanded="false" mtitle="Show Email Tips">
                                    <span style="font-size: 14px; "
                                          class="text-dark font-weight-lighter font-italic">Email
                                        Rules</span>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="emailRules p-2">
                                        <h4>Spam emails:</h4>
                                        <p class="lead">Just hit reply and in the body make sure the first
                                            word is
                                            spam followed by the message as to why it is spam.</p>
                                        <p class="lead">Example:<br>
                                            spam <em>&nbsp;This is advertising.</em></p>

                                        <h4>Send to another Agent:</h4>
                                        <p class="lead">If you receive a lead that belongs to someone else,
                                            hit reply
                                            and in the body type their email followed by the exclamation mark(!).
                                            followed by
                                            reason or comment.</p>
                                        <p class="lead">Example:<br>
                                            agent2@cruisertravels.com! <em>&nbsp;your comment or the reason here.</em>
                                        </p>
                                    </div>
                                </div>
                            </div>
                </div>
                </li>

                </ul>

            </div>

            <div class="col-12 col-md-4">
                <div class="card text-center" style="box-shadow: 0 0 5px #555;">
              <ul class="list-group list-group-flush">
                <li class="list-group-item blue"> <h3> Total Stats</h3>
              <p class="lead">This shows the total stats.</p>
                </li>
                <li class="list-group-item">Emails Received: <span id="emails-sent">{{$leadMails['total']}}</span></li>
                <li class="list-group-item">Emails Sent: <span id="emails-sent">{{$leadMails['totalSent']}}</span></li>
                <li class="list-group-item">Emails rejected: <span id="emails-sent">{{$leadMails['totalReject']}}</span></li>
              </ul>
            </div>
        </div>
    </div>
            
                    <div class="card strpied-tabled-with-hover" style="box-shadow: 0 0 5px #555;">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Detailed Reports</h3>
                            <p class=" lead">Here you can view the progress of each agent.</p>
                            <div class="p-4">
                                <label for="time-set"><p class=" lead">Run the report by dates:</p> </label>
                                <input class="lead" type="date" id="from-date" name="from-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" > to <input class="lead" type="date" id="to-date" name="to-date" value="{{explode(' ', \Carbon\Carbon::now())[0]}}" >
                                <a href="#" class="btn btn-primary" id="detailedReportBtn">Submit</a>
                                <a href="#" class="btn btn-secondary" id="detailedEmailBtn">Send to E-mail</a>
                            </div>
                        </div>

                        <div class="card-body table-full-width table-responsive" >
                            <div class="card-header text-primary font-weight-bold">Available Leads: {{ $leadMails['available'] }}</div>
                            <table id="detailedReportTable" class="table table-hover table-striped" role="table" >
                                <thead>
                                    {{-- order it should be requested, reassigned, rejected, total --}}
                                    <th>Name</th>
                                    <th>Most Recent</th>
                                    <th>Pulled</th>
                                    <th>Reassigned</th>
                                    <th>rejected</th>
                                    <th>Worked</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                

</div>
<script type="text/javascript">
    const leadBtn = document.querySelector("#generateLeadBtn") ;
    const cover = document.querySelector(".cover")


function lead() {

    cover.innerHTML = 'Processing...';
    cover.style.visibility = "visible";
    cover.style.opacity = 1;
    leadBtn.classList.add('disabled');
    leadBtn.style.color = "#fff!important";
    cover.style.cursor = "not-allowed";

    $.ajax({
        url: "/leads/get",
        success: function(result){
            cover.innerHTML = result.message;
            setTimeout(() => {
                cover.style.visibility = "hidden";
                cover.style.opacity = 0;
                leadBtn.classList.remove('disabled');
                location.reload();
            }, 500);
        },
        error: function(a,b,c){
            alert('Something Went Wrong!');
            console.log(a,b,c);
        }
    });
}

</script>
<script>
$(document).ready(function() {
    $('#detailedReportTable').DataTable({
        pageLength: 50,
        paging: true,
        responsive: true,
        
    });
});
</script>
@endsection