<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Sector;
use App\Models\User;
use App\Models\Level;

use App\Events\SectorRefresh;

class SectorController extends Controller
{
    public function index (Request $request) {
        //Si l'on fait une recherche
        if ($request->input('search')) {

            $search = $request->input('search');

            $type_search = $request->input('type_search');

                $searchMin = strtolower($search);
                $searchCap = ucfirst($search);
                $searchMaj = strtoupper($search);

                $sectors = Sector::where('name', 'like', $searchMin . '%')
                                   ->orWhere('name', 'like', $searchCap . '%')
                                   ->orWhere('name', 'like', $searchMaj . '%')->paginate(5);
                $loup = 667;

        } else {
            $sectors = Sector::paginate(5);
            $loup = null;
        }
        
        return view('authed.sectors',[ 
        'sectors' => $sectors,
        'loup' => $loup
        ]);
    }

    public function store(Request $request) {   
        $maxDegree = $request->input('addMaxDegree');
        $sectorName = $request->input('addName');

        $degrees = [];
        $names = [];

        for ($i = $maxDegree; $i >= 1; $i--) {
            $degrees[$i] = $request->input('addDegree' . $i);
            $names[$i] = $request->input('addName' . $i);
        }

        //Verification
        if (!$sectorName || !$names || !$degrees) {
            return redirect()->back()->with([
                'error' => 'Veuillez bien remplir les champs'
            ]);
        }

        //Secteur
        $sector = Sector::Create([
            'name' => $sectorName
        ]);

        for ($i = 1; $i <= $maxDegree; $i++) {
            Level::create([
                'name' => $names[$i],
                'degree' => (int)$degrees[$i],
                'sector_id' => $sector->id,
            ]);
        }

        event(new SectorRefresh($sector));

        return redirect()->back()->with([
            'success' => 'La filière a bien été ajouté',
        ]);
    }   
    
    public function delete (Request $request, $id) {
        $sector = Sector::find($id);

        event(new SectorRefresh($sector));
        
        if ($sector) {
            $sector->delete();
        } else {
            return redirect()->back()->with([
                'error' => 'Cet enregistrement n existe pas'
            ]);
        }


        return redirect()->back()->with([
            'success' => 'La filiere a bien été supprimé'
        ]);
    }

    public function getSector (Request $request, $id) {
        $sector = Sector::with(['levels' => function ($query) {
            $query->orderBy('degree', 'asc');
        }])->find($id);

       if ($sector === null ) {
            return response()->json(['error' => 'Utilisateur introuvable']);
       }

        return response()->json($sector, 200);
    }

    public function edit(Request $request)
    {   
        $maxDegree = $request->input('editMaxDegree');
        $sectorName = $request->input('editName');
        $id = (int)$request->input('id');

        $degrees = [];
        $names = [];

        for ($i = $maxDegree; $i >= 1; $i--) {
            $degrees[$i] = (int)$request->input('editDegree' . $i);
            $names[$i] = $request->input('editName' . $i);
        }

        //Verification
        if (!$sectorName || !$names || !$degrees) {
            return redirect()->back()->with([
                'error' => 'Veuillez bien remplir les champs'
            ]);
        }

        //Secteur
        $sector = Sector::find($id);

        $sector->name = $sectorName;
        $sector->save();

        $toDelete = [];
        
        //Pour ajouter
        foreach ($names as $key => $value) {
            $i = 0;
            $trouver = false;

            foreach ($sector->levels as $l) {
                if ($l->name == $value) {
                    $trouver = true;
                }
            }

            if (!$trouver) {
                Level::create([
                    'name' => $names[$key],
                    'degree' => $degrees[$key],
                    'sector_id' => $sector->id,
                ]);
            }
        }

        //Pour retirer
        foreach ($sector->levels as $l) {
            $uneFois = false;
            if ($l->name == $value) {
                $trouver = true;
            }

            foreach ($names as $key => $value) {
                if (!$uneFois) {
                    if ($l->name == $value) {
                        $uneFois = true;
                    }
                }
            }

            if (!$uneFois) {
                Level::where('id', $l->id)->delete();
            }
        }

        event(new SectorRefresh($sector));

        return redirect()->back()->with([
            'success' => 'La filière a bien été modifié',
        ]);
    }   

    
}
