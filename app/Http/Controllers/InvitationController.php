<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invitation\AcceptInvite;
use App\Http\Requests\Invitation\InviteRequest;
use App\Models\Invitation;
use App\Models\User;

class InvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('invite');
    }

    public function invite(InviteRequest $request)
    {
        $invite = Invitation::create($request->validated());

        return response()->json([
            'message' => 'The user has been invited successfully',
            'invite'  => $invite,
        ], 201);
    }

    public function acceptInvite(AcceptInvite $request, Invitation $invitation)
    {
        $user = User::create($request->validated());

        $invitation->update(['activated_at' => now()]);

        return response()->json([
            'message' => 'Your user have been created!',
            'user'    => $user,
        ], 201);
    }
}
