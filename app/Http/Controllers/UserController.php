<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sector;
use App\Models\Level;
use App\Models\Notif;
use App\Models\LevelsUser;

class UserController extends Controller
{

    public function dashboard () {

        $nombreFilieres = Sector::count();
        $nombreProfs = User::where('role', 1)->count();
        $nombreEtudiants = User::where('role', 2)->count();

        return view('authed.dashboard', [
            'nombreFilieres' => $nombreFilieres,
            'nombreProfs' => $nombreProfs,
            'nombreEtudiants' => $nombreEtudiants,
        ]);
        
    }

    public function first_connection (Request $request) {

        if ($request->isMethod('post')){
            $id = $request->input('id');
            $user = User::find($id);

            $user->password = Hash::make($request->input('password'));
            $user->first_connection = 0;
            $user->save();

            return redirect()->route('dashboard')->with([
                'success' => 'Votre mot de passe a bien mit été à jour'
            ]);
        }

        if (auth()->user()->first_connection == 1) {
            return view('authed.first_connection');
        } else {
            return redirect()->back()->with([
                'error' => 'Vous ne pouvez pas accéder à cette partie'
            ]);
        }
    }

    public function index (Request $request) {

        if ($request->input('search')) {

            $search = $request->input('search');

            if (preg_match("/^(administrateur)$/i", $search)){
                $search = 0;
            } else if (preg_match("/^(professeur)$/i", $search)) {
                $search = 1;
            } else if (preg_match("/^(etudiant)$/i", $search)) {
                $search = 2;
            }

            $users = User::with(['levels_users.level.sector'])
                           ->where('first_name', 'like', strtolower($search) . '%')
                           ->orWhere('first_name', 'like', strtoupper($search) . '%')
                           ->orWhere('first_name', 'like', ucfirst($search) . '%')
                           ->orWhere('last_name', 'like', strtolower($search) . '%')
                           ->orWhere('last_name', 'like', strtoupper($search) . '%')
                           ->orWhere('last_name', 'like', ucfirst($search) . '%')
                           ->orWhere('email', 'like', $search . '%')
                           ->orWhere('phone', 'like', $search . '%')
                           ->orWhere('role', 'like', $search . '%')
                           ->orWhereHas('levels_users.level.sector', function ($query) use ($search) {
                                $query->where('name', 'like', strtolower($search) . '%')
                                      ->orWhere('name', 'like', ucfirst($search) . '%')
                                      ->orWhere('name', 'like', strtoupper($search) . '%');
                           })->paginate(5);
                        $loup = 667;
        } else {
            $users = User::with(['levels_users.level.sector'])->paginate(5);

            $loup = null;
        }

        $sectors = Sector::with(['levels' => function ($query) {
            $query->orderBy('sector_id')->orderBy('degree', 'asc');
        }])->orderBy('id')->get();

        
        return view('authed.users',[ 
        'users' => $users,
        'loup' => $loup,
        'sectors' => $sectors
        ]);

    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'addFirstName' => 'required|string|max:255',
            'addLastName' => 'string|max:255',
            'addEmail' => 'required|email|max:255|unique:users,email',
            'addPhone' => 'required|string|max:20',
            'addRole' => 'required|string|max:255'
        ], [
            'addFirstName.required' => 'Le prenom est requis.',
            'addEmail.required' => "L adresse email est requise.",
            'addEmail.email' => "L adresse email doit être valide.",
            'addEmail.max' => "L adresse email ne doit pas dépasser :max caractères.",
            'addEmail.unique' => 'Cette adresse email est déjà utilisée.',
            'addPhone.required' => 'Le numéro de téléphone est requis.',
            'addPhone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            'addRole.required' => 'Le rôle est requis.',
            'addRole.max' => 'Le rôle ne doit pas dépasser :max caractères.'
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        //Recuperation des variables
        $first_name = $request->input('addFirstName');
        $last_name = $request->input('addLastName');
        $email = $request->input('addEmail');
        $phone = $request->input('addPhone');
        $role = $request->input('addRole');

        $password = explode('@', $email)[0];
        
        //Création de l'utilisateur
        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'role' => (int)$role,
            'first_connection' => 1,
            'notifs' => 0,
            'password' => Hash::make($password. '123') 
        ]);

        //Si admin ou prof
        if ($role == 1) {
            $levels = array();
            foreach ($request->all() as $key => $value) {
                if (preg_match("/^(levelIdAdd).*$/", $key)) {
                    $levels[] = $value;
                }
            }
            //Si ils ont pas ajouté des filières
            if (empty($levels)) {
                $user->delete();
                return redirect()->back()->with([
                    'error' => 'un professeur doit avoir une filière'
                ]);
            } else {
                foreach ($levels as $key => $level) {
                    LevelsUser::Create([
                        'level_id' => $level,
                        'user_id' => $user->id
                    ]);
                }
            }
        } else if ($role == 2){
            $id = $request->input('filiereSolo');

            LevelsUser::Create([
                'level_id' => $id,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with([
            'success' => 'L utilisateur a bien été enregistré',
            'class' => 'success'
        ]);
    }   

    public function getUser (Request $request, $id) {
        $user = User::with(['levels_users.level.sector'])->find($id);

       if ($user === null ) {
            return response()->json(['error' => 'Utilisateur introuvable']);
       }

        return response()->json($user, 200);
    }

    public function edit (Request $request) {
        $id = $request->input('id');
        $first_name = $request->input('editFirstName');
        $last_name = $request->input('editLastName');
        $email = $request->input('editEmail');
        $phone = $request->input('editPhone');
        $role = $request->input('editRole');

        if (!$request->input('editPassword')) {
            $password = '';
        } else {
            $password = Hash::make($request->input('editPassword'));
        }

        $validator = Validator::make($request->all(), [
            'editFirstName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255', // Assure que l'email est unique dans la table 'users'
            'editPhone' => 'required|string|max:20',
            'editRole' => 'required|string|max:255'
        ], [
            'editFirstName.required' => 'Le prenom est requis.',
            'editEmail.required' => "L adresse email est requise.",
            'editEmail.email' => "L adresse email doit être valide.",
            'editEmail.max' => "L adresse email ne doit pas dépasser :max caractères.",
            'editPhone.required' => 'Le numéro de téléphone est requis.',
            'editPhone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            'editRole.required' => 'Le rôle est requis.',
            'editRole.max' => 'Le rôle ne doit pas dépasser :max caractères.'
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }
        
        $email_check = User::where('email', $email)->first();
        $user = User::find($id);

        if ($email_check && $email_check->email != $user->email) {
            return redirect()->back()->with([
                'error' => 'L email doit être unique',
            ]);
        }

        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->phone = $phone;
        $user->role = (int)$role;
        if ($password != '') {
            $user->password = $password;
        }

        $user->save();

        LevelsUser::where('user_id', $user->id)->delete();

        //Si admin ou prof
        if ($role == 0 || $role == 1) {
            $levels = array();
            foreach ($request->all() as $key => $value) {
                if (preg_match("/^(levelIdEdit).*$/", $key)) {
                    $levels[] = $value;
                }
            }

            //Si ils ont ajouté des filières
            if (!empty($levels)) {
                foreach ($levels as $key => $level) {
                    LevelsUser::Create([
                        'level_id' => $level,
                        'user_id' => $user->id
                    ]);
                }
            }
            //Si c'est un étudiant
        } else {
            $id = $request->input('filiereSolo');

            LevelsUser::Create([
                'level_id' => $id,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with([
            'success' => 'L utilisateur a bien été modifié'
        ]);
    }

    public function delete (Request $request, $id) {
        $user = User::find($id);

        if ($user->id == auth()->user()->id) {
            return redirect()->back()->with([
                'error' => 'Vous pouvez pas vous supprimer'
            ]);
        } else if ($user == null) {
            return redirect()->back()->with([
                'error' => 'Utilisateur introuvable'
            ]);
        }

        $user->delete();

        return redirect()->back()->with([
            'success' => 'L\'utilisateur a bien été supprimé'
        ]);
    }

    public function suppNotifs (Request $request) {

        $user = User::find(auth()->user()->id);

        $notifs = Notif::where('user_id', $user->id);

        $notifs->delete();

        return redirect()->back()->with([
            'success' => 'Tout les notifications ont été supprimés'
        ]);

    }

    public function getUserNotifs(Request $request, $id) {

        $notifs = Notif::with(['resource.file'])->with(['resource.module'])->where('user_id', $id)->orderBy('created_at', 'desc')->get();
        
        return response()->json($notifs, 200);
    }

    public function resetNotifs (Request $request, $id) {
        $user = User::find($id);

        $user->notifs = 0;
        $user->save();

        return response()->json(200);
    }

}