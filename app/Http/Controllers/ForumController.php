<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\User;
use App\Models\Level;

class ForumController extends Controller
{
    public function index(Request $request) {
    
        $id = auth()->user()->id;
        //Si c'est un etudiant
            if (auth()->user()->role == 2) {
            $user = User::find(auth()->user()->id);
            return redirect()->route('forums.forum', ['level_id' => $user->levels_users->first()->level_id]);
        //

        //Si c'est un professeur
            } else if (auth()->user()->role == 1) {
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $levels = Level::where(function ($query) use ($id, $search){
                        $query->whereHas('levels_users.user', function ($query) use ($id){
                            $query->where('id', $id);
                        });
        
                        $query->whereHas('sector', function($query) use ($search) {
                            $query->where('name', 'like', strtoupper($search) . '%')
                                ->orWhere('name', 'like', strtolower($search) . '%')
                                ->orWhere('name', 'like', ucfirst($search) . '%');    
                        });
                    })->paginate(5);
                    $loup = 667;
                } else {
                        $levels = Level::whereHas('levels_users.user', function ($query) use ($id){
                            $query->where('id', $id);
                        })->paginate(5);
                    $loup = null;
            }
        //

        //Si c'est un administrateur
            } else {
                if ($request->input('search')) {
                    $search = $request->input('search');
                    $levels = Level::whereHas('sector', function($query) use ($search) {
                                        $query->where('name', 'like', strtoupper($search) . '%')
                                            ->orWhere('name', 'like', strtolower($search) . '%')
                                            ->orWhere('name', 'like', ucfirst($search) . '%');    
                                    })->paginate(5);
                    $loup = 667;
                } else {
                        $levels = Level::paginate(5);
                    $loup = null;
                }
            }
        //

        return view('authed.forums.forums', [
            'levels' => $levels,
            'loup' => $loup
        ]);
    }

    public function forum(Request $request, $level_id) {
        $user = User::find(auth()->user()->id);

        $trouver = false;

        foreach ($user->levels_users as $level) {
            if ($level_id == $level->level_id) {
                $trouver = true;
            }
        }

        if (!$trouver) {
            return redirect()->back()->with([
                'error' => 'Vous ne pouvez pas accéder à cette partie'
            ]);
        }

        $forums = Forum::where('level_id', $level_id)->orderBy('created_at', 'asc')->get();
        return view('authed.forums.forum', [
            'forums' => $forums
        ]);
    }

    public function addMsgForum (Request $request, $level_id) {
        $user_id = auth()->user()->id;
        $message = $request->input('ecritureMessage');

        Forum::create([
            'message' => $message,
            'level_id' => $level_id,
            'user_id' => $user_id
        ]);

        return redirect()->back();
    }
}
