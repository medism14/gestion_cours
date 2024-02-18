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
use App\Models\Annonce;
use App\Models\LevelsUser;

use League\Csv\Reader;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Response;

class UserController extends Controller
{

    public function dashboard () {

        //Suppression des annonces au cas où
        $annonces = Annonce::all();
        $date = now()->toDateTimeString();

        foreach ($annonces as $annonce) {
            if ($annonce->date_expiration < $date) {
                foreach ($annonce->annonces_relations as $relation) {
                    if ($relation->user->annonce_viewed < $relation->created_at && $relation->user->annonce_viewed > 0) {
                        $relation->user->annonces--;
                        $relation->user->save();
                    }
                }
                $annonce->delete();
                return redirect()->route('dashboard');
            }
        }


        $nombreFilieres = Sector::count();
        $nombreProfs = User::where('role', 1)->count();
        $nombreEtudiants = User::where('role', 2)->count();

        return view('authed.dashboard', [
            'nombreFilieres' => $nombreFilieres,
            'nombreProfs' => $nombreProfs,
            'nombreEtudiants' => $nombreEtudiants,
        ]);

    }

    public function nowDate () {
        $date = Carbon::now()->toDateTimeString();

        return response()->json($date, 200);
    }

    public function first_connection (Request $request) {

        if ($request->isMethod('post')){
            $id = $request->input('id');
            $user = User::find($id);

            $user->password = Hash::make($request->input('password'));
            $user->first_connection = 0;
            $user->save();

            return redirect()->route('dashboard')->with([
                'success' => 'Votre mot de passe a bien été mis à jour'
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
                           ->orWhere('sexe', 'like', strtolower($search) . '%')
                           ->orWhere('sexe', 'like', strtoupper($search) . '%')
                           ->orWhere('sexe', 'like', ucfirst($search) . '%')
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
            'addLastName.required' => 'Le nom est requis.',
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
        $sexe = $request->input('addSexe');

        $password = explode('@', $email)[0];

        $now = Carbon::now();

        $now = $now->toDateTimeString();

        //Création de l'utilisateur
        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'role' => (int)$role,
            'sexe' => $sexe,
            'first_connection' => 1,
            'notifs' => 0,
            'notif_viewed' => $now,
            'annonces' => 0,
            'annonce_viewed' => $now,
            'password' => Hash::make($password. '123') 
        ]);

        //Si prof
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
        $sexe = $request->input('editSexe');

        if (!$request->input('editPassword')) {
            $password = '';
        } else {
            $password = Hash::make($request->input('editPassword'));
        }

        if (auth()->user()->role != 0) {
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
        } else {
            $validator = Validator::make($request->all(), [
                'editFirstName' => 'required|string|max:255',
                'editEmail' => 'required|email|max:255', // Assure que l'email est unique dans la table 'users'
                'editPhone' => 'required|string|max:20',
            ], [
                'editFirstName.required' => 'Le prenom est requis.',
                'editEmail.required' => "L adresse email est requise.",
                'editEmail.email' => "L adresse email doit être valide.",
                'editEmail.max' => "L adresse email ne doit pas dépasser :max caractères.",
                'editPhone.required' => 'Le numéro de téléphone est requis.',
                'editPhone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            ]);

        }
        
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
        $user->sexe = $sexe;
        if ($password != '') {
            $user->password = $password;
        }

        $user->save();

        if ($user->role == 0) {
            return redirect()->back()->with([
                'success' => 'L utilisateur a bien été modifié'
            ]);
        }

        LevelsUser::where('user_id', $user->id)->delete();

        //Si admin ou prof
        if ($role == 1) {
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

        Notif::where('user_id', $user->id)->delete();

        return redirect()->back()->with([
            'success' => 'Tout les notifications ont été supprimés'
        ]);

    }

    public function suppAnnonces (Request $request) {

        $user = User::find(auth()->user()->id);

        Notif::where('user_id', $user->id)->delete();

        return redirect()->back()->with([
            'success' => 'Tout les notifications ont été supprimés'
        ]);

    }

    public function getUserNotifs(Request $request, $id) {

        $notifs = Notif::with(['resource.file'])->with(['resource.module'])->where('user_id', $id)->orderBy('created_at', 'desc')->get();
        
        return response()->json($notifs, 200);
    }

    public function getNotifCreatedTime ($id) {
        $notif = Notif::find($id);

        $date = $notif->created_at->toDateTimeString();

        return response()->json($date, 200);
    }

    public function resetNotifs (Request $request, $id) {
        $user = User::find($id);

        $user->notifs = 0;
        $user->notif_viewed = now();
        $user->save();

        return response()->json(200);
    }

    public function download(Request $request) {
        // Récupérer tous les utilisateurs depuis la base de données
        $users = User::all();

        // Définir le nom du fichier CSV téléchargé
        $fileName = 'users.csv';

        // Créer le contenu du fichier CSV
        $csvData = "sep=\t\n"; // Spécifier une tabulation comme délimiteur
        $csvData .= "ID\tPrénom\tNom\tEmail\tTéléphone\tRôle\tSexe\tFilières\n"; // Ligne d'en-tête avec des colonnes séparées par des tabulations
        foreach ($users as $user) {
            // Convertir le rôle numérique en nom de rôle
            switch ($user->role) {
                case 0:
                    $role = 'Administrateur';

                    $csvData .= "{$user->id}\t{$user->first_name}\t{$user->last_name}\t{$user->email}\t{$user->phone}\t{$role}\t{$user->sexe}\t\t\n"; // Colonnes séparées par des tabulations
                    break;
                case 1:
                    $role = 'Professeur';
                    $filieres = '';
                    $countRep = 0;
                    
                    if ($user->levels_users) {
                        foreach ($user->levels_users as $level_user) {
                            if ($countRep == 0) {
                                $filieres .= $level_user->level->sector->name . ': ' . $level_user->level->name;
                            } else {
                                $filieres .= ' | ' . $level_user->level->sector->name . ': ' . $level_user->level->name;
                            }
                            $countRep = 1;
                        }
                    }
                    $csvData .= "{$user->id}\t{$user->first_name}\t{$user->last_name}\t{$user->email}\t{$user->phone}\t{$role}\t{$user->sexe}\t{$filieres}\n"; // Colonnes séparées par des tabulations
                    break;
                case 2:
                    $role = 'Étudiant';
                    $filiere = '';

                    if ($user->levels_users) {
                        foreach ($user->levels_users as $level_user) {
                            $filiere = $level_user->level->sector->name . ': ' . $level_user->level->name;
                        }
                    }

                    $csvData .= "{$user->id}\t{$user->first_name}\t{$user->last_name}\t{$user->email}\t{$user->phone}\t{$role}\t{$user->sexe}\t{$filiere}\n"; // Colonnes séparées par des tabulations
                    break;
            }

        }
    
        // Convertir le contenu du fichier CSV en UTF-8
        $csvData = utf8_decode($csvData);
    
        // Ajouter l'en-tête Content-Disposition pour indiquer le téléchargement du fichier
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
    
        // Retourner la réponse HTTP avec le contenu du fichier CSV
        return Response::make($csvData, 200, $headers);
    }

    public function importCSV(Request $request) {
        if ($request->file('fichier')) {
            $file = $request->file('fichier');

            // Vérifiez si le fichier est un fichier CSV ou XLSX
            if ($file->getClientOriginalExtension() != 'csv' && $file->getClientOriginalExtension() != 'xlsx') {
                return redirect()->back()->with('error', 'Veuillez fournir un fichier au format CSV ou XLSX.');
            }

            
            //Recuperer le fichier csv avec reader
            $reader = Reader::createFromPath($file->getPathName(), 'r');
            $first = 0;
            $users = [];
            $user = [];
            //Lire le fichier
            foreach ($reader as $record) {
                $row = explode(';', $record[0]);

                if ($first == 0) {
                    $first++;
                    continue;
                }

                if ($row[0] == '' && $row[1] == '' && $row[2] == '' && $row[3] == '' && $row[4] == '' && $row[5] == ''&& $row[6] == '') {
                    break;
                }
                
                $first_name = $row[0];
                $last_name = $row[1];
                $email = $row[2];
                $phone = $row[3];
                $role = $row[4];
                $sexe = $row[5];

                $filieres = explode('|', $row[6]);

                if ($role == 'Administrateur' || $role == 'Administratrice') {
                    $role = 0;
                } else if ($role == 'Professeur' || $role == 'Professeure') {
                    $role = 1;
                } else if ($role == 'Etudiant' || $role == 'Etudiante') {
                    $role = 2;
                }

                //Verification pour le mail unique
                foreach ($users as $user) {
                    if ($user['email'] == $email) {
                        return redirect()->back()->with([
                            'error' => 'L\'email est déjà existant, veuillez la changer pour l\'utilisateur ' . $first_name . ' ' . $last_name
                        ]);
                    }
                }

                //Verification pour le mail unique
                $uTest = User::where('email', $email)->first();
                if ($uTest) {
                    return redirect()->back()->with([
                        'error' => 'L\'email est déjà existant, veuillez la changer pour l\'utilisateur ' . $first_name . ' ' . $last_name
                    ]);
                }
                

                //Pour voir si les filières sont en chiffre
                foreach ($filieres as $filiere) {
                    if (!preg_match('/^[0-9]+$/', $filiere)) {
                        return redirect()->back()->with([
                            'error' => 'Veuillez bien écrire les filières pour l\'utilisateur ' . $first_name . ' ' . $last_name
                        ]);
                    }
                }

                //Pour voir si ils ont ajouté des filières existantes
                foreach ($filieres as $filiere) {
                    $level = Level::find($filiere);

                    if (!$level) {
                        return redirect()->back()->with([
                            'error' => 'Veuillez bien saisir les bons filières pour l\'utilisateur ' . $first_name . ' ' . $last_name
                        ]);
                    }
                }

                //Pour voir si les sexes sont masculin ou feminin
                if ($sexe != 'H' && $sexe != 'F') {
                    return redirect()->back()->with([
                        'error' => 'Veuillez bien indiquer le sexe pour l\'utilisateur ' . $first_name . ' ' . $last_name
                    ]);
                }

                //Pour voir si le role est existant
                if ($role != 0 && $role != 1 && $role != 2) {
                    return redirect()->back()->with([
                        'error' => 'L\'utilisateur ' . $first_name . ' ' . $last_name . ' a mal été saisi'
                    ]);
                }

                //Pour voir si un etudiant a plusieurs filières
                if (count($filieres) > 1 && $role == 2) {
                    return redirect()->back()->with([
                        "error" => "L'utilisateur " . $first_name . " " . $last_name . " ne peut pas être etudiant et avoir plusieurs filières",
                    ]);
                }

                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['role'] = $role;
                $user['sexe'] = $sexe;
                $user['filieres'] = $filieres;

                $users[] = $user;

                $user = [];
                
            }

            foreach ($users as $user) {

                $password = explode('@', $user['email'])[0] . '123';

                $u = User::create([
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'role' => $user['role'],
                    'sexe' => $user['sexe'],
                    'first_connection' => 1,
                    'notifs' => 0,
                    'notif_viewed' => now(),
                    'annonces' => 0,
                    'annonce_viewed' => now(),
                    'password' => Hash::make($password),
                ]);
                

                foreach ($user['filieres'] as $level_id) {

                    LevelsUser::Create([
                        'level_id' => $level_id,
                        'user_id' => $u->id,
                    ]);
                }

            }

            return redirect()->back()->with('success', 'Les données du fichier CSV ont été traitées avec succès.');
        }

        return redirect()->back()->with('error', 'Aucun fichier sélectionné.');
    }

} 