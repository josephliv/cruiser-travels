<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\UserRequest;
use App\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
	/**
	 * Display a listing of the users
	 *
	 * @param User $model
	 *
	 * @return \Illuminate\View\View
	 */
	public function index( User $model ) {
		$users = $model->paginate( 15 );

		return view( 'users.index', [ 'users' => $users ] );
	}

	/**
	 * Show the form for creating a new user
	 *
	 * @return \Illuminate\View\View
	 */
	public function create() {
		$groups = Group::orderBy( 'order' )->get();

		return view( 'users.create', compact( 'groups' ) );
	}

	/**
	 * Store a newly created user in storage
	 *
	 * @param UserRequest $request
	 * @param User        $model
	 *
	 * @return RedirectResponse
	 */
	public function store( UserRequest $request, User $model ) {
		$model           = new User();
		$model->name     = $request->get( 'name' );
		$model->email    = $request->get( 'email' );
		$model->password = Hash::make( $request->get( 'password' ) );

		$this->set_user_parameters( $request, $model );
		$model->save();

		return redirect()->route( 'user.index' )->withStatus( __( 'User successfully created.' ) );
	}

	/**
	 * Update the specified user in storage
	 *
	 * @param UserRequest $request
	 * @param User        $user
	 *
	 * @return RedirectResponse
	 */
	public function update( UserRequest $request, User $user ) {
		$model        = $user;

		$model->name  = $request->get( 'name' );
		$model->email = $request->get( 'email' );

		if ( trim( $request->get( 'password' ) ) != '' ) {
			$model->password = Hash::make( $request->get( 'password' ) );
		}

		$this->set_user_parameters( $request, $model );
		$model->save();

		return redirect()->route( 'user.index' )->withStatus( __( 'User successfully updated.' ) );
	}

	private function set_user_parameters( UserRequest $request, $model ) {
		$model->is_admin       = ( intval( $request->get( 'user_group' ) ) == 5 ? 1 : 0 );
		$model->leads_allowed  = intval( $request->get( 'leads_allowed' ) );
		$model->time_set_init  = $request->get( 'time_set_init' );
		$model->time_set_final = $request->get( 'time_set_final' );
		$model->user_group     = intval( $request->get( 'user_group' ) );
	}

	/**
	 * Show the form for editing the specified user
	 *
	 * @param User $user
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit( User $user ) {
		$groups = Group::orderBy( 'order' )->get();

		return view( 'users.edit', compact( 'user', 'groups' ) );
	}



	/**
	 * Remove the specified user from storage
	 *
	 * @param User $user
	 *
	 * @return RedirectResponse
	 * @throws Exception
	 */
	public function destroy( User $user ) {
		$user->delete();

		return redirect()->route( 'user.index' )->withStatus( __( 'User successfully deleted.' ) );
	}
}
