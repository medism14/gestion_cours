<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Annonce;
use App\Models\Level;
use App\Models\User;
use App\Models\AnnoncesRelation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Events\AnnonceRefresh;
use App\Events\AnnonceEdit;

class AnnonceController extends Controller
{
    public function index (Request $request) {
        $user = auth()->user();
        $query = Annonce::with(['user', 'annonces_relations.level.sector']);
        $search = $request->input('search');
        $loup = $search ? 667 : null;

        if ($user->role == 0) { // Admin
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', $search . '%')
                      ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', $search . '%'))
                      ->orWhereHas('annonces_relations.level.sector', fn($s) => $s->where('name', 'like', $search . '%'));
                });
            }
            $annonces = $query->paginate(5)->withQueryString();
            $levels = Level::all();
        } else if ($user->role == 2) { // Student
            $query->whereHas('annonces_relations', fn($q) => $q->where('user_id', $user->id));
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', $search . '%')
                      ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', $search . '%'));
                });
            }
            $annonces = $query->paginate(5)->withQueryString();
            $levels = null;
        } else { // Professor
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('annonces_relations', fn($ar) => $ar->where('user_id', $user->id));
            });
            if ($search) {
                $query->where(function($q) use ($search, $user) {
                    $q->where('title', 'like', $search . '%')
                      ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', $search . '%'))
                      ->orWhereHas('annonces_relations.level.sector', fn($s) => $s->where('name', 'like', $search . '%'));
                });
            }
            $annonces = $query->paginate(5)->withQueryString();
            $levels = Level::whereHas('levels_users', fn($q) => $q->where('user_id', $user->id))->get();
        }

        // Avoid redundant saves
        if ($user->annonces != 0) {
            $user->update(['annonces' => 0, 'annonce_viewed' => now()]);
        }
        
        return view('authed.annonces', compact('annonces', 'levels', 'loup'));
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
            return redirect()->back()->with(['error' => json_decode($validator->errors(), true)]);
        }

        $choixFilieres = ($request->input('addFiliere') == 'all') ? 'all' : 'partiels';
        $choixPersonnes = match($request->input('addPersonnes')) {
            'teachers' => 'teachers',
            'students' => 'students',
            default => 'all',
        };

        $filieres = [];
        if ($choixFilieres == 'partiels') {
            foreach ($request->all() as $index => $value) {
                if (preg_match("/^addFilieres\w*$/" ,$index)) $filieres[] = $value;
            }
            if (empty($filieres)) return redirect()->back()->with(['error' => 'Veuillez saisir des filières']);
        }

        $annonce = Annonce::create([
            'title' => $request->input('addTitle'),
            'content' => $request->input('addContenu'),
            'choix_filieres' => $choixFilieres,
            'choix_personnes' => $choixPersonnes,
            'date_expiration' => $request->input('addDateExpiration'),
            'user_id' => auth()->user()->id,
        ]);

        $targetLevels = ($choixFilieres == 'all') ? Level::all() : Level::whereIn('id', $filieres)->get();
        $targetRole = match($choixPersonnes) { 'teachers' => 1, 'students' => 2, default => null };

        foreach ($targetLevels as $level) {
            $userQuery = User::whereHas('levels_users', fn($q) => $q->where('level_id', $level->id));
            if ($targetRole !== null) $userQuery->where('role', $targetRole);
            $userIds = $userQuery->pluck('id');

            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->increment('annonces');
                $relations = $userIds->map(fn($uid) => [
                    'annonce_id' => $annonce->id, 'level_id' => $level->id, 'user_id' => $uid,
                    'created_at' => now(), 'updated_at' => now()
                ])->toArray();
                AnnoncesRelation::insert($relations);
            }
        }

        $annonce->load('annonces_relations');
        event(new AnnonceRefresh($annonce, 'add'));
        return redirect()->back()->with(['success' => "L'annonce a bien été postée"]);
    }

    public function edit (Request $request) {
        $id = $request->input('id');
        $annonce = Annonce::with('annonces_relations.user')->find($id);
        
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
            return redirect()->back()->with(['error' => json_decode($validator->errors(), true)]);
        }

        $choixFilieres = ($request->input('editFiliere') == 'all') ? 'all' : 'partiels';
        $choixPersonnes = match($request->input('editPersonnes')) {
            'teachers' => 'teachers',
            'students' => 'students',
            default => 'all',
        };

        // Reduc viewed counts before deleting old relations
        $usersToDecrement = $annonce->annonces_relations->filter(fn($r) => $r->user->annonce_viewed < $r->updated_at && $r->user->annonce_viewed > 0)->pluck('user_id');
        if ($usersToDecrement->isNotEmpty()) {
            User::whereIn('id', $usersToDecrement)->decrement('annonces');
        }

        $annonce->annonces_relations()->delete();

        $annonce->update([
            'title' => $request->input('editTitle'),
            'date_expiration' => $request->input('editDateExpiration'),
            'content' => $request->input('editContenu'),
            'choix_filieres' => $choixFilieres,
            'choix_personnes' => $choixPersonnes,
        ]);

        $filieres = [];
        if ($choixFilieres == 'partiels') {
            foreach ($request->all() as $index => $value) {
                if (preg_match("/^editFilieres\w*$/" ,$index)) $filieres[] = $value;
            }
            if (empty($filieres)) return redirect()->back()->with(['error' => 'Veuillez saisir des filières']);
        }

        $targetLevels = ($choixFilieres == 'all') ? Level::all() : Level::whereIn('id', $filieres)->get();
        $targetRole = match($choixPersonnes) { 'teachers' => 1, 'students' => 2, default => null };

        foreach ($targetLevels as $level) {
            $userQuery = User::whereHas('levels_users', fn($q) => $q->where('level_id', $level->id));
            if ($targetRole !== null) $userQuery->where('role', $targetRole);
            $userIds = $userQuery->pluck('id');

            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->increment('annonces');
                $relations = $userIds->map(fn($uid) => [
                    'annonce_id' => $annonce->id, 'level_id' => $level->id, 'user_id' => $uid,
                    'created_at' => now(), 'updated_at' => now()
                ])->toArray();
                AnnoncesRelation::insert($relations);
            }
        }

        $newAnnonce = Annonce::with('annonces_relations')->find($annonce->id);
        event(new AnnonceRefresh($newAnnonce, 'edit'));

        return redirect()->back()->with(['success' => "L'annonce a bien été modifiée"]);
    }

    public function getAnnonces () {
        $annonces = AnnoncesRelation::where('user_id', auth()->user()->id)->where('notif', 'oui')->with('annonce')->orderBy('created_at', 'desc')->get();
        return response()->json($annonces ?: [], 200);
    }

    public function getAnnonce ($id) {
        $annonce = AnnoncesRelation::where('id', $id)->with(['user', 'annonce.user'])->first();
        return response()->json($annonce, 200);
    }

    public function getAnnonceRelation ($id) {
        $annonce = Annonce::where('id', $id)->with(['annonces_relations.user', 'annonces_relations.level', 'user'])->first();
        $filieres = Level::whereHas('annonces_relations', fn($q) => $q->where('annonce_id', $id))->with('sector')->get();

        if ($annonce->choix_filieres != 'all' && $filieres->isEmpty()) {
            $annonce->update(['choix_filieres' => 'all']);
        }

        return response()->json(['annonce' => $annonce, 'filieres' => $filieres], 200);
    }

    public function getAnnonceCreatedTime ($id) {
        $annonce = AnnoncesRelation::find($id);
        return response()->json($annonce ? $annonce->updated_at->toDateString() : null, 200);
    }

    public function delete (Request $request, $id) {
        $annonce = Annonce::with('annonces_relations.user')->find($id);
        if (!$annonce) return redirect()->back()->with(['error' => 'Annonce non trouvée']);

        $usersToDecrement = $annonce->annonces_relations->filter(fn($r) => $r->user->annonce_viewed < $r->updated_at && $r->user->annonce_viewed > 0)->pluck('user_id');
        if ($usersToDecrement->isNotEmpty()) {
            User::whereIn('id', $usersToDecrement)->decrement('annonces');
        }

        event(new AnnonceRefresh($annonce, 'delete'));
        $annonce->delete();

        return redirect()->back()->with(['success' => "L'annonce a bien été supprimée"]);
    }

    public function resetAnnonces () {
        auth()->user()->update(['annonces' => 0, 'annonce_viewed' => now()]);
        return response()->json(200);
    }

    public function suppAnnonces () {
        AnnoncesRelation::where('user_id', auth()->user()->id)->update(['notif' => 'non']);
        return redirect()->back()->with(['success' => 'Les annonces ont bien été supprimées']);
    }

    public function deleteRelation ($id) {
        AnnoncesRelation::where('user_id', auth()->user()->id)->where('annonce_id', $id)->delete();
        return redirect()->back()->with(['success' => 'Cette annonce a bien été supprimée pour vous']);
    }
}
