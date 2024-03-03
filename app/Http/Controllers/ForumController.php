<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\User;
use App\Models\Level;
use App\Models\UsersMessage;

use App\Events\ForumMessage;
use App\Events\ForumDeleteMessage;
use App\Events\ForumClear;


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

        foreach ($user->users_messages as $userMsg) {

            if ($userMsg->level_id == $level_id) {
                $userMsg->viewed_at = now();
                $userMsg->message = 0;
                $userMsg->save();
            }
        }

        $trouver = false;

        foreach ($user->levels_users as $level) {
            if ($level_id == $level->level_id) {
                $trouver = true;
            }
        }

        if (auth()->user()->role != 0 && !$trouver) {
            return redirect()->back()->with([
                'error' => 'Vous ne pouvez pas accéder à cette partie'
            ]);
        }

        $forums = Forum::where('level_id', $level_id)->orderBy('created_at', 'asc')->get();

        return view('authed.forums.forum', [
            'forums' => $forums,
            'level_id' => $level_id
        ]);
    }

    public function addMsgForum (Request $request, $level_id) {
        $user_id = auth()->user()->id;
        $message = $request->input('ecritureMessage');

        if (!$message) {
            return redirect()->back()->with([
                'error' => 'Veuillez ecrire du contenu dans le message',
            ]);
        }

        $lastForum = Forum::where('level_id', $level_id)->orderBy('id', 'desc')->first();

        $forumGet = Forum::create([
            'message' => $message,
            'level_id' => $level_id,
            'user_id' => $user_id,
        ]);

        //Ajouter un nouveau messages à tout les gens
        $usersNewMsg = User::where('role', '!=', 0)->whereHas('levels_users', function($query) use ($level_id) {
            $query->where('level_id', $level_id);
        })->get();

        foreach ($usersNewMsg as $userM) {
            $userMessage = UsersMessage::where('level_id', $level_id)->where('user_id', $userM->id)->first();

            $userMessage->message++;
            $userMessage->save();
        }

        $forum = Forum::with('user', 'level')->find($forumGet->id);
            
        if (!$lastForum) {
            $actualiser = false;
        } else {
            $dateLastForum = $lastForum->created_at->format('Y-m-d');

            $dateNewForum = $forum->created_at->format('Y-m-d');

            if ($dateLastForum != $dateNewForum) {
                $actualiser = true;
            } else {
                $actualiser = false;
            }
        }

        event(new ForumMessage($forum, $actualiser));

        return redirect()->back();
    }

    public function suppForum (Request $request, $id) {
        $forum = Forum::find($id);

        $level_id = $forum->level_id;

        //Manipulation de nombre de message
        $usersNewMsg = User::where('role', '!=', 0)->whereHas('levels_users', function($query) use ($level_id) {
            $query->where('level_id', $level_id);
        })->get();

        foreach ($usersNewMsg as $userM) {
            $userMessage = UsersMessage::where('level_id', $level_id)->where('user_id', $userM->id)->first();

            //Reduction de l'utilisateur si la condition est validé
            if ($userMessage->viewed_at < $forum->update_at) {
                $userMessage->message--;
                $userMessage->save();
            }
            
        }

        event(new ForumDeleteMessage($forum));
        
        $forum->delete();

        return redirect()->back()->with([
            'error' => 'Le message selectionné a bien été supprimé'
        ]);
    }

    public function viewMsgForum (Request $request, $user_id) {
        $user = User::find($user_id);

        $user->message_viewed_at = now();
        $user->save();

        return response().json(200);
    }

    public function suppAllMsg (Request $request, $level_id) {
        
        Forum::where('level_id', $level_id)->delete();

        //Manipulation de nombre de message
        $usersNewMsg = User::where('role', '!=', 0)->whereHas('levels_users', function($query) use ($level_id) {
            $query->where('level_id', $level_id);
        })->get();

        foreach ($usersNewMsg as $userM) {
            $userMessage = UsersMessage::where('level_id', $level_id)->where('user_id', $userM->id)->first();

            $userMessage->viewed_at = now();
            $userMessage->message = 0;
            $userMessage->save();
        }

        event(new ForumClear($level_id));

        return redirect()->back()->with([
            'error' => 'Tout a été éffacé'
        ]);



    }

    public function messagesNonLus (Request $request, $levelId) {
        
        $message = UsersMessage::where('user_id', auth()->user()->id)->get();
        $messages = 0;

        foreach ($message as $m) {
            $messages += $m->message;
        }

        return response()->json($messages, 200);
    }
}
