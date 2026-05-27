<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProfileSelectionController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Profiles this user owns
        $ownProfiles = $user->profiles()->active()->orderBy('created_at')->get();

        // Profiles in other accounts this user has been invited into
        $sharedProfiles = Profile::where('invited_user_id', $user->id)
            ->where('user_id', '!=', $user->id)
            ->active()
            ->with('user')
            ->get();

        // If no active profiles at all, create a default owner profile
        if ($ownProfiles->isEmpty() && $sharedProfiles->isEmpty()) {
            $profile = $user->profiles()->create([
                'name'            => $user->name,
                'avatar_color'    => Profile::COLORS[0],
                'invited_user_id' => $user->id,
                'status'          => Profile::STATUS_ACTIVE,
            ]);
            session(['active_profile_id' => $profile->id]);
            return $this->redirectAfterSelect();
        }

        // Auto-select if there's exactly one profile
        $allActive = $ownProfiles->merge($sharedProfiles);
        if (
            ! session()->has('active_profile_id') &&
            $allActive->count() === 1
        ) {
            session(['active_profile_id' => $allActive->first()->id]);
            return $this->redirectAfterSelect();
        }

        return view('pages.profiles.select', compact('ownProfiles', 'sharedProfiles'));
    }

    public function select(Request $request)
    {
        $request->validate(['profile_id' => ['required', 'integer']]);

        $user      = $request->user();
        $profileId = (int) $request->profile_id;

        // Check the user owns it OR is the invited user
        $profile = Profile::where('id', $profileId)
            ->active()
            ->where(fn($q) =>
                $q->where('user_id', $user->id)
                  ->orWhere('invited_user_id', $user->id)
            )
            ->firstOrFail();

        session(['active_profile_id' => $profile->id]);

        return $this->redirectAfterSelect();
    }

    private function redirectAfterSelect()
    {
        if ($token = session()->pull('pending_gift_claim')) {
            return redirect()->route('gift.accept', $token);
        }

        return redirect()->intended(route('home'));
    }
}
