@extends('layouts.app', ['activePage' => 'user-management', 'activeButton' => 'laravel', 'title' => 'Leadbox Management System', 'navName' => 'Users'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Users') }}</h3>
                                    <p class="text-sm mb-0">

                                    </p>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('user.create') }}"
                                        class="btn btn-sm btn-default">{{ __('Add user') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="toolbar">
                            <!--        Here you can write extra buttons/actions for the toolbar              -->
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            {{ $users->links() }}
                            <table class="table table-hover table-striped" id="user-table">
                                <thead>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Start') }}</th>
                                    <th>{{ __('Group') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Start') }}</th>
                                        <th>{{ __('Group') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>

                                </tfoot>

                                <tbody>

                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td>{{ optional(optional($user)->get_group)->name }}</td>
                                            <td class="d-flex justify-content-left">
                                                @if ($user->id != auth()->id())
                                                    <a href="{{ route('user.edit', $user->id) }}"
                                                        class="btn btn-link btn-warning edit d-inline-block"><i
                                                            class="fa fa-edit"></i></a>

                                                    <form class="d-inline-block"
                                                        action="{{ route('user.destroy', $user->id) }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger "
                                                            onclick="confirm('{{ __('Are you sure you want to delete this user?') }}') ? this.parentElement.submit() : ''"
                                                            s><i class="fa fa-times"></i></a>
                                                    </form>
                                                @else
                                                    <a href="{{ route('profile.edit', $user->id) }}"
                                                        class="btn btn-link btn-warning edit d-inline-block"><i
                                                            class="fa fa-edit"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#user-table').DataTable({
                pageLength: 50,
                paging: true,
                responsive: true,
                // Disables sorting on the action column
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } 
                ]
            });
        });
    </script>
@endsection
