<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('light-bootstrap/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('light-bootstrap/img/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>{{ $title }}</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <!--     Fonts and icons     -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" /> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mallanna&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="{{ asset('light-bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/light-bootstrap-dashboard.css?v=2.0.0') }} " rel="stylesheet" />
    <!-- CSS mostly for custom changes such as the dropdown menu -->

    <link href="{{ asset('/css/custom.css') }}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <div class="wrapper @if (!auth()->check() || request()->route()->getName() == '') wrapper-full-page @endif">

        @if (auth()->check() && request()->route()->getName() != '')
            @if (auth()->check() && !\Auth::user()->is_admin)
                @include('layouts.navbars.sidebaragent')
            @else
                @include('layouts.navbars.sidebar')
            @endif

        @endif

        <div class="@if (auth()->check() && request()->route()->getName() != '') main-panel @endif">

            @include('layouts.navbars.navbar')

            @yield('content')
            @include('layouts.footer.nav')
        </div>

    </div>

</body>
<!--   Core JS Files   -->
<script src="{{ asset('light-bootstrap/js/core/jquery.3.2.1.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('light-bootstrap/js/core/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('light-bootstrap/js/core/bootstrap.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('light-bootstrap/js/plugins/jquery.sharrre.js') }}"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{ asset('light-bootstrap/js/plugins/bootstrap-switch.js') }}"></script>
<!-- script src="{{ asset('light-bootstrap/js/plugins/chartist.min.js') }}"></script -->
<script src="{{ asset('light-bootstrap/js/plugins/bootstrap-notify.js') }}"></script>

@stack('js')

<script>
    $(document).ready(function() {

        $('#detailedReportBtn').on('click', function(e) {
            e.stopPropagation();
            $('#detailedReportTable').find('tbody').html(
                '<tr><td colspan="5">Processing...</td><td></td><td></td><td></td><td></td></tr>');
            $.ajax({
                url: "/leads/" + $('#from-date').val() + "/" + $('#to-date').val() + "/report/",
                success: function(result) {
                    console.log("Complete AJAX response:", result);
                    let res = result ||
                []; // this was set at result.leads which resulted to no leads found.
                    console.log("res:", res)
                    let leadsNotAssigned = result.leads_not_assigned ||
                []; // Not assigned leads
                    let notAssignedTotal = leadsNotAssigned.reduce((acc, lead) => acc + (
                        lead.leads_count || 0), 0);

                    console.log("Total not assigned leads count:", notAssignedTotal);
                    console.log("Is leadsNotAssigned an array?:", Array.isArray(
                        leadsNotAssigned));

                    // Update the new location with the unassigned lead count
                    $('#unassignedLeadCount').text('Unassigned Leads: ' + notAssignedTotal);

                    $('#detailedReportTable').find('tbody').empty();
                    $('#detailedReportTable').find('tfoot').empty();
                    let leadsTotal = 0,
                        rejectedTotal = 0,
                        reassignedTotal = 0,
                        requestedTotal = 0;
                    let aggregateTotalLeadsTaken = 0;
                    if (res.length > 0) {
                        for (let i = 0; i < res.length; i++) {
                            let date = new Date(res[i].last_lead);
                            let formattedDate = date.toLocaleDateString('en-US') + ' ' +
                                date.toLocaleTimeString('en-US');

                            let leadsCount = parseInt(res[i].leads_count, 10) || 0;
                            let rejectedCount = parseInt(res[i].leads_rejected, 10) || 0;
                            let reassignedCount = parseInt(res[i].leads_reassigned, 10) ||
                            0;
                            // let leadsWorked = parseInt(res[i].requested_leads, 10) || 0;
                            // Incrementing totals
                            leadsTotal += leadsCount;
                            rejectedTotal += rejectedCount;
                            reassignedTotal += reassignedCount;
                            // leadsWorked =+ leadsWorked;
                            // Calculate the total leads worked by subtracting the rejected leads and reassigned leads 
                            let totalLeadsTaken = leadsCount - rejectedCount -
                                reassignedCount;

                            // Accumulate the total leads taken for all agents
                            aggregateTotalLeadsTaken += totalLeadsTaken;

                            // Append each lead's information to the table body, including the total leads taken
                            $('#detailedReportTable').find('tbody').append(
                                '<tr>' +
                                '<td>' + res[i].agent_name + '</td>' +
                                '<td>' + formattedDate + '</td>' +
                                '<td class="text-center">' + leadsCount + '</td>' +
                                '<td class="text-center">' + reassignedCount + '</td>' +
                                '<td class="text-center">' + rejectedCount + '</td>' +
                                '<td class="text-center">' + totalLeadsTaken + '</td>' +
                                '</tr>'
                            );
                        }
                    } else {
                        // Append a row indicating no data is available
                        $('#detailedReportTable').find('tbody').append(
                            '<tr><td colspan="5">No leads found</td></tr>'
                        );
                    }

                    // Process and display not assigned leads if needed...

                    // Append totals to the table footer, including not assigned leads count
                    $('#detailedReportTable').find('tfoot').append(
                        '<tr>' +
                        '<th colspan="2">TOTAL</th>' +
                        '<th class="text-center">' + leadsTotal + '</th>' +
                        '<th class="text-center">' + reassignedTotal + '</th>' +
                        '<th class="text-center">' + rejectedTotal + '</th>' +
                        '<th class="text-center">' + aggregateTotalLeadsTaken +
                        '</th>' +
                        '</tr>'
                    );
                },


                error: function(a, b, c) {
                    alert('Something Went Wrong!');
                    console.log("error:", a, b, c);
                }
            });
        });

        $('#detailedEmailBtn').on('click', function(e) {
            e.stopPropagation();
            $.ajax({
                url: "/leads/" + $('#from-date').val() + "/" + $('#to-date').val() + "/email/",
                success: function(result) {
                    res = JSON.parse(result);
                    console.log(res);
                    alert(res.message);
                },
                error: function(a, b, c) {
                    alert('Something Went Wrong!');
                    console.log(a, b, c);
                }
            });
        });

        $('.removeLead').on('click', function(e) {
            e.stopPropagation();
            confirm('You really want to delete this lead?');
        });

        $('.getbody').on('click', function(e) {
            id = $(this).data('id');
            type = $(this).data('type');

            $('#leadsModalBody').html('Processing');

            if (type == 'body') {
                $.ajax({
                    url: "/leads/" + id + "/body",
                    success: function(result) {
                        res = JSON.parse(result);
                        //console.log(atob(res.body));
                        $('#leadsModalBody').html(atob(res.body))
                    },
                    error: function(a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            }

            if (type == 'reassigned') {
                $.ajax({
                    url: "/leads/" + id + "/reassigned",
                    success: function(result) {
                        res = JSON.parse(result);
                        //console.log(atob(res.body));
                        $('#leadsModalBody').html(atob(res.body))
                    },
                    error: function(a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            }

            if (type == 'rejected') {
                $.ajax({
                    url: "/leads/" + id + "/rejected",
                    success: function(result) {
                        res = JSON.parse(result);
                        //console.log(atob(res.body));
                        $('#leadsModalBody').html(atob(res.body))
                    },
                    error: function(a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            }

        });

        $('.direct-send-lead').on('click', function(e) {
            $('#transferLeadId').val($(this).data('id'));
            $('#transferLeadOriginalAgent').val($(this).data('original-user'));
        })

        $('.direct-send-lead-button').on('click', function(e) {
            var transferLeadId = $('#transferLeadId').val();
            var transferLeadOriginalAgent = $('#transferLeadOriginalAgent').val();
            var transferLeadNewAgent = $('#transferLeadNewAgent').val();

            if (transferLeadOriginalAgent != transferLeadNewAgent) {
                $.ajax({
                    url: "/leads/transfer/" + transferLeadId + "/" + transferLeadNewAgent,
                    success: function(result) {
                        res = JSON.parse(result);
                        console.log(res);
                        alert(res.success);
                        location.reload();
                    },
                    error: function(a, b, c) {
                        alert('Something Went Wrong!');
                        console.log(a, b, c);
                    }
                });
            } else {
                alert('This lead has already being sent to this user');
            }
        })






    });
</script>
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/light-bootstrap-dashboard.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/demo.js') }}"></script>

</html>
