<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Annonce;
use App\Models\Level;
use App\Models\User;
use App\Models\AnnoncesRelation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AnnonceController extends Controller
{
    public function index (Request $request) {

        auth()->user()->annonces = 0;
        auth()->user()->save();

        $loup = null;
        
        function min ($string) {
            return strtolower($string);
        }

        function maj ($string) {
            return strtoupper($string);
        }

        function cap ($string) {
            return ucfirst($string);
        }

        if ($request->input('search')) {
            $search = $request->input('search');
        }
        
        //Si c'est un administrateur
        if (auth()->user()->role == 0) {
            $annonces = Annonce::paginate(5);
        
            $levels = Level::all();

            if ($request->input('search')) {
                $annonces = Annonce::where(function ($query) use ($search) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'like', min($search). '%')
                              ->orWhere('first_name', 'like', maj($search). '%')
                              ->orWhere('first_name', 'like', cap($search). '%');
                    })->orWhereHas('annonces_relations.level.sector', function($query) use ($search) {
                        $query->where('name', 'like', min($search). '%')
                              ->orWhere('name', 'like', maj($search). '%')
                              ->orWhere('name', 'like', cap($search). '%');
                    })
                    ->orWhere('title', 'like', min($search). '%')
                    ->orWhere('title', 'like', maj($search). '%')
                    ->orWhere('title', 'like', cap($search). '%');
                })
                ->paginate(5);
    
                $loup = 667;
            }
        //Si c'est un étudiant
        } else if (auth()->user()->role == 2) {
            $annonces = Annonce::whereHas('annonces_relations', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->paginate(5);

            if ($request->input('search')) {
                
                //Verification si l'utilisateur est le bon
                $annonces = Annonce::whereHas('annonces_relations', function($query) use ($search) {
                    $query->where('user_id', auth()->user()->id);
                })
                //Separation des deux
                ->where(function($query) use ($search){
                        //On verifie s'il y'a un titre de ce nom
                        $query->where(function ($query) use ($search) {
                            $query->where('title', 'like', min($search). '%')
                            ->orWhere('title', 'like', maj($search). '%')
                            ->orWhere('title', 'like', cap($search). '%');
                        })
                        //On verifie s'il y'a un annonceur de ce nom
                        ->orWhereHas('user', function ($query) use ($search){
                            $query->where('first_name', 'like', min($search). '%')
                            ->orWhere('first_name', 'like', maj($search). '%')
                            ->orWhere('first_name', 'like', cap($search). '%');
                        });
                })
                ->paginate(5);
    
                $loup = 667;
            }

            $levels = null;
        //Si c'est un professeur
        }else {
            $annonces = Annonce::where('user_id', auth()->user()->id)
            ->orWhereHas('annonces_relations', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->paginate(5);
        
            $levels = Level::whereHas('levels_users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->get();

            if ($request->input('search')) {
                //Si c'est pas lui qui a posté la requête
                $annonces = Annonce::where(function ($query) use ($search) {
                    $query->whereHas('annonces_relations', function ($query) use ($search) {
                        $query->where('user_id', auth()->user()->id);
                    })
                    //Groupement du where pour separer les deux parties
                    ->where(function ($query) use ($search) {
                        //On verifie s'il y'a un titre de ce nom
                        $query->where(function ($query) use ($search) {
                            $query->where('title', 'like', min($search). '%')
                            ->orWhere('title', 'like', maj($search). '%')
                            ->orWhere('title', 'like', cap($search). '%');
                        })
                        //On verifie s'il y'a un annonceur de ce nom
                        ->orWhereHas('user', function ($query) use ($search){
                            $query->where('first_name', 'like', min($search). '%')
                            ->orWhere('first_name', 'like', maj($search). '%')
                            ->orWhere('first_name', 'like', cap($search). '%');
                        })
                        //On verifie s'il y'a un filière de ce nom
                        ->orWhereHas('annonces_relations.level.sector', function($query) use ($search) {
                            $query->where('name', 'like', min($search). '%')
                                  ->orWhere('name', 'like', maj($search). '%')
                                  ->orWhere('name', 'like', cap($search). '%');
                        });
                    });
                })
                //Si c'est lui qui a posté la requête
                ->orWhere(function ($query) use ($search) {
                    //Pour dire que si c'est lui qui a posté
                    $query->where('user_id', auth()->user()->id)
                    //Separation des deux parties
                    ->where(function($query) use ($search){
                        //Verification pour un titre de ce nom
                        $query->where(function ($query) use ($search) {
                            $query->where('title', 'like', min($search). '%')
                            ->orWhere('title', 'like', maj($search). '%')
                            ->orWhere('title', 'like', cap($search). '%');
                        })
                        //Verification pour un filière de ce nom
                        ->orWhereHas('annonces_relations.level.sector', function($query) use ($search) {
                            $query->where('name', 'like', min($search). '%')
                                  ->orWhere('name', 'like', maj($search). '%')
                                  ->orWhere('name', 'like', cap($search). '%');
                        });
                    });
                })
                ->paginate(5);
    
                $loup = 667;
            }
        }
        
        
        return view('authed.annonces', [
            'annonces' => $annonces,
            'levels' => $levels,
            'loup' => $loup
        ]);
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'addTitle' => 'required',
            'addDateExpiration' => 'required',
            'addContenu' => 'required',
        ], [
            'addTitle.required' => 'Le titre est requis.',
            'addDateExpiration.required' => "La saisi du date d expiration est réquise",
            'addContenu.required' => "La saisi du contenu est réquise",
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        //Choix filiere
        if ($request->input('addFiliere') == 'all') {
            $choixFilieres = 'all';
        } else {
            $choixFilieres = 'partiels';
        }

        //Choix Personnes
        if ($request->input('addPersonnes') == 'all') {
            $choixPersonnes = 'all';
        } else if ($request->input('addPersonnes') == 'teachers') {
            $choixPersonnes = 'teachers';
        } else {
            $choixPersonnes = 'students';
        }

        //Verification des variables si c'est partiels et rempilssages
        if ($choixFilieres == 'partiels') {
            $filieres = array();

            //Recuperer dans un array toutes les filières
            foreach ($request->all() as $index => $value) {
                if (preg_match("/^addFilieres\w*$/" ,$index)) {
                    $filieres[] = $value;
                }
            }

            if (empty($filieres)) {
                return redirect()->back()->with([
                    'error' => 'Veuillez saisir des filières'
                ]);
            }
        }

        //Creation de l'annonce
        $annonce = Annonce::Create([
            'title' => $request->input('addTitle'),
            'content' => $request->input('addContenu'),
            'choix_filieres' => $choixFilieres,
            'choix_personnes' => $choixPersonnes,
            'date_expiration' => $request->input('addDateExpiration'),
            'user_id' => auth()->user()->id,
        ]);

        //Toutes les filières
        if ($request->input('addFiliere') == 'all') {
            $levels = Level::all();

            foreach ($levels as $level) {
                //Type d'utilisateur selectionné
                switch ($request->input('addPersonnes')) {
                    case 'all':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->get();

                        break;
                    
                    case 'teachers':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 1)->get();

                        break;
                    
                    case 'students':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 2)->get();

                        break;
                }

                //Après avoir eu les utilisateurs selectionnés
                foreach ($users as $user) {
                    $annonceExistante = false;

                    foreach ($user->annonces_relations as $relation) {
                        if ($relation->annonce->id == $annonce->id) {
                            $annonceExistante = true;
                        }
                    }

                    if (!$annonceExistante) {
                        AnnoncesRelation::Create([
                            'annonce_id' => $annonce->id,
                            'level_id' => $level->id,
                            'user_id' => $user->id,
                        ]);
    
                        $user->annonces++;
                        $user->save();
                    }
                }
            }
        //Certains filières
        } else {
            //Boucle pour soumettre les annonces aux utilisateurs
            foreach ($filieres as $id) {
                $level = Level::find($id);
                
                //Type d'utilisateur selectionné
                switch ($request->input('addPersonnes')) {
                    case 'all':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->get();

                        break;
                    
                    case 'teachers':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 1)->get();

                        break;
                    
                    case 'students':
                        $users = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 2)->get();

                        break;
                }

                //Après avoir eu les utilisateurs selectionnés
                foreach ($users as $user) {
                    $annonceExistante = false;

                    foreach ($user->annonces_relations as $relation) {
                        if ($relation->annonce->id == $annonce->id) {
                            $annonceExistante = true;
                        }
                    }

                    if (!$annonceExistante) {
                        AnnoncesRelation::Create([
                            'annonce_id' => $annonce->id,
                            'level_id' => $level->id,
                            'user_id' => $user->id,
                        ]);
        
                        $user->annonces++;
                        $user->save();
                    }
                }
            }


        }
        return redirect()->back()->with([
            'success' => "L'annnoce a bien été posté",
        ]);
    }

    public function edit (Request $request) {
        $id = $request->input('id');
        $annonce = Annonce::find($id);

        $validator = Validator::make($request->all(), [
            'editTitle' => 'required',
            'editDateExpiration' => 'required',
            'editContenu' => 'required',
        ], [
            'editTitle.required' => 'Le titre est requis.',
            'editDateExpiration.required' => "La saisi du date d expiration est réquise",
            'editContenu.required' => "La saisi du contenu est réquise",
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        //Choix filiere
        if ($request->input('editFiliere') == 'all') {
            $choixFilieres = 'all';
        } else {
            $choixFilieres = 'partiels';
        }

        //Choix Personnes
        if ($request->input('editPersonnes') == 'all') {
            $choixPersonnes = 'all';
        } else if ($request->input('editPersonnes') == 'teachers') {
            $choixPersonnes = 'teachers';
        } else {
            $choixPersonnes = 'students';
        }


        //Modification de l'annonce
        $annonce->title = $request->input('editTitle');
        $annonce->date_expiration = $request->input('editDateExpiration');
        $annonce->content = $request->input('editContenu');

        //Si la personne changent de choix de filière
        if ($choixFilieres != $annonce->choix_filieres) {
            $annonce->choix_filieres = $choixFilieres;

            //On commence par reduire le nombre d'annonce vue
            foreach ($annonce->annonces_relations as $relation) {
                if ($relation->user->annonce_viewed < $relation->created_at && $relation->user->annonce_viewed > 0) {
                    $relation->user->annonces--;
                    $relation->user->save();
                }
            }

            //On supprime toutes les relations avec les utilisateurs
            $annonce->annonces_relations()->delete();

            //Si le choix de filière se porte sur toutes les filières
            if ($choixFilieres == 'all') {
                
                $levels = Level::all();

                foreach ($levels as $level) {
                    switch ($request->input('editPersonnes')) {
                        case 'all':
                            $usersChoixFilieresAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->get();
                
                            break;
                                
                        case 'teachers':
                            $usersChoixFilieresAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 1)->get();
                
                            break;
                                
                        case 'students':
                            $usersChoixFilieresAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 2)->get();
                
                            break;
                    }

                    foreach ($usersChoixFilieresAll as $user) {
                        $annonceExistante = false;

                        foreach ($user->annonces_relations as $relation) {
                            if ($relation->annonce->id == $annonce->id) {
                                $annonceExistante = true;
                            }
                        }

                        if (!$annonceExistante) {
                            AnnoncesRelation::Create([
                                'annonce_id' => $annonce->id,
                                'level_id' => $level->id,
                                'user_id' => $user->id,
                            ]);
        
                            $user->annonces++;
                            $user->save();
                        }
                    }
                }
                
            //Si le choix de filière se porte sur certains filières
            } else {
                $filieres = [];

                //Recuperer dans un array toutes les filières
                foreach ($request->all() as $index => $value) {
                    if (preg_match("/^editFilieres\w*$/" ,$index)) {
                        $filieres[] = $value;
                    }
                }

                //Retourner une erreur si la filière est vide
                if (empty($filieres)) {
                    return redirect()->back()->with([
                        'error' => 'Veuillez saisir des filières'
                    ]);
                }

                //On va enregistrer les informations pour chaque utilisateur dans la filiere
                foreach ($filieres as $filiere) {
                    $level = Level::find($filiere);

                    switch ($request->input('editPersonnes')) {
                        case 'all':
                            $usersChoixFiliereNotAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->get();
                
                            break;
                                
                        case 'teachers':
                            $usersChoixFiliereNotAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 1)->get();
                
                            break;
                                
                        case 'students':
                            $usersChoixFiliereNotAll = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 2)->get();
                
                            break;
                    }
                    foreach ($usersChoixFiliereNotAll as $user) {
                        $annonceExistante = false;
                    
                        foreach ($user->annonces_relations as $relation) {
                            if ($relation->annonce->id == $annonce->id) {
                                $annonceExistante = true;
                            }
                        }

                        if (!$annonceExistante) {
                            AnnoncesRelation::Create([
                                'annonce_id' => $annonce->id,
                                'level_id' => $level->id,
                                'user_id' => $user->id,
                            ]);
            
                            $user->annonces++;
                            $user->save();
                        }
                    }
                }
            }

        //S'il reste dans le même choix de filiere mais on verifie si les filiere ont changé
        } else {
            //On commence par reduire le nombre d'annonce vue
            foreach ($annonce->annonces_relations as $relation) {
                if ($relation->user->annonce_viewed < $relation->created_at && $relation->user->annonce_viewed > 0) {
                    $relation->user->annonces--;
                    $relation->user->save();
                }
            }

            //On supprime toutes les relations avec les utilisateurs
            $annonce->annonces_relations()->delete();

            if ($choixFilieres != 'all') {
                //Les filière que l'ont vient de récupérer
                $filieres = array();
                //Recuperer dans un array toutes les filières
                foreach ($request->all() as $index => $value) {
                    if (preg_match("/^editFilieres\w*$/" ,$index)) {
                        $filieres[] = $value;
                    }
                }
                //Retourner une erreur si la filière est vide
                if (empty($filieres)) {
                    return redirect()->back()->with([
                        'error' => 'Veuillez saisir des filières'
                    ]);
                }

                foreach ($filieres as $filiere) {
                    $level = Level::find($filiere);

                    switch ($request->input('editPersonnes')) {
                        case 'all':
                            $userChoixFilieresElse = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->get();
                
                            break;
                                
                        case 'teachers':
                            $userChoixFilieresElse = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 1)->get();
                
                            break;
                                
                        case 'students':
                            $userChoixFilieresElse = User::whereHas('levels_users', function ($query) use ($level) {
                                $query->where('level_id', $level->id);
                            })->where('role', 2)->get();
                
                            break;
                    }

                    foreach ($userChoixFilieresElse as $user) {
                        $annonceExistante = false;
                        
                        foreach ($user->annonces_relations as $relation) {
                            if ($relation->annonce->id == $annonce->id) {
                                $annonceExistante = true;
                            }
                        }

                        if (!$annonceExistante) {
                            AnnoncesRelation::Create([
                                'annonce_id' => $annonce->id,
                                'level_id' => $level->id,
                                'user_id' => $user->id,
                            ]);
        
                            $user->annonces++;
                            $user->save();
                        }
                    }
                }
            }

        }

        //Si la personne changent de choix de personnes
        if ($choixPersonnes != $annonce->choix_personnes) {
            $annonce->choix_personnes = $choixPersonnes;

            //Recuperation des filières où les étudiants étaient
            $filieres = Level::whereHas('annonces_relations', function ($query) use ($annonce) {
                $query->where('annonce_id', $annonce->id);
            })->get();

            //Reduction du nombre d'annonce pour les utilisateurs
            foreach ($annonce->annonces_relations as $relation) {
                if ($relation->user->annonce_viewed < $relation->created_at && $relation->user->annonce_viewed > 0) {
                    $relation->user->annonces--;
                    $relation->user->save();
                }
            }

            //Suppression de tout les annonces à laquelles ils sont liés
            $annonce->annonces_relations()->delete();

            //Ajout d'annonce aux utilisateurs des filières recup
            foreach ($filieres as $level) {
                //Verification des utilisteurs concernés
                switch ($request->input('editPersonnes')) {
                    case 'all':
                        $usersChoixPersonnes = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->get();

                        break;
                            
                    case 'teachers':
                        $usersChoixPersonnes = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 1)->get();
            
                        break;
                            
                    case 'students':
                        $usersChoixPersonnes = User::whereHas('levels_users', function ($query) use ($level) {
                            $query->where('level_id', $level->id);
                        })->where('role', 2)->get();
            
                        break;
                    
                    default:
                        break;
                }

                //Ajout des annonces aux utilisateurs
                foreach ($usersChoixPersonnes as $user) {
                    $annonceExistante = false;
                    
                    foreach ($user->annonces_relations as $relation) {
                        if ($relation->annonce->id == $annonce->id) {
                            $annonceExistante = true;
                        }
                    }

                    if (!$annonceExistante) {
                        AnnoncesRelation::Create([
                            'annonce_id' => $annonce->id,
                            'level_id' => $level->id,
                            'user_id' => $user->id,
                        ]);
        
                        $user->annonces++;
                        $user->save();
                    }
                }
            }
        }

        $annonce->save();
        return redirect()->back()->with([
            'success' => "L'annnoce a bien été modifié",
        ]);
    }

    public function getAnnonces () {
        $annonces = AnnoncesRelation::where('user_id', auth()->user()->id)->with('annonce')->orderBy('created_at', 'desc')->get();

        if ($annonces->isEmpty()) {
            $annonces = [];
        }

        return response()->json($annonces, 200);
    }

    public function getAnnonce ($id) {
        $annonce = AnnoncesRelation::where('id', $id)->with('user')->with('annonce.user')->first();

        return response()->json($annonce, 200);
    }

    public function getAnnonceRelation ($id) {
        $annonce = Annonce::where('id', $id)->with('annonces_relations.user')->with('annonces_relations.level')->with('user')->first();
        
        $filieres = Level::whereHas('annonces_relations', function ($query) use ($annonce) {
            $query->where('annonce_id', $annonce->id);
        })->with('sector')->get();

        if ($annonce->choix_filieres != 'all' && empty($filieres)) {
            $annonce->choix_filieres == 'all';
            $annonce->save();
        }

        return response()->json([
            'annonce' => $annonce,
            'filieres' => $filieres,
        ], 200);
    }

    public function getAnnonceCreatedTime ($id) {
        $annonce = AnnoncesRelation::find($id);

        $date = $annonce->created_at->toDateString();

        return response()->json($date, 200);
    }

    public function delete (Request $request, $id) {
        $annonce = Annonce::find($id);


        foreach ($annonce->annonces_relations as $relation) {
            if ($relation->user->annonce_viewed < $relation->created_at && $relation->user->annonce_viewed > 0) {
                $relation->user->annonces--;
                $relation->user->save();
            }
        }

        $annonce->delete();

        return redirect()->back()->with([
            'success' => "L'annonce a bien été supprimé"
        ]);
    }

    public function resetAnnonces () {
        auth()->user()->annonces = 0;
        auth()->user()->annonce_viewed = now();
        auth()->user()->save();
        
        return response()->json(200);
    }

    public function suppAnnonces () {
        $annonces = AnnoncesRelation::where('user_id', auth()->user()->id);

        $annonces->delete();

        return redirect()->back()->with([
            'success' => 'Les annonces ont bien été supprimés'
        ]);
    }

    public function deleteRelation ($id) {
        $annonce = AnnoncesRelation::where('user_id', auth()->user()->id)->whereHas('annonce', function ($query) use ($id) {
            $query->where('id', $id);
        })->first();

        $annonce->delete();

        return redirect()->back()->with([
            'success' => 'Cette annonce a bien été supprimé pour vous'
        ]);
    }
}
