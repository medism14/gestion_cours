<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Module;
use App\Models\User;
use App\Models\Sector;

use App\Events\ModuleRefresh;

class ModuleController extends Controller
{
    public function index (Request $request) {

        if ($request->input('search')) {

            $search = $request->input('search');

            $modules = Module::where('name', 'like', strtolower($search) . '%')
                               ->orWhere('name', 'like', strtoupper($search) . '%')            
                               ->orWhere('name', 'like', ucfirst($search) . '%')            
                               ->orWhereHas('level.sector', function ($query) use ($search) {
                                    $query->where('name', 'like', strtolower($search) . '%')
                                    ->orWhere('name', 'like', strtoupper($search) . '%')            
                                    ->orWhere('name', 'like', ucfirst($search) . '%');
                               })
            ->paginate(5);

            $loup = 667;
        } else {
            $modules = Module::paginate(5);

            $loup = null;
        }

        $sectors = Sector::with(['levels' => function ($query) {
            $query->orderBy('sector_id')->orderBy('degree', 'asc');
        }])->orderBy('id')->get();

        $users = User::where('role', 1)->get();
        
        return view('authed.modules',[ 
        'modules' => $modules,
        'sectors' => $sectors,
        'users' => $users,
        'loup' => $loup
        ]);

    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'addName' => 'required|string|max:255',
        ], [
            'addName.required' => 'Le nom est requis.',
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        //Recuperation des variables
        $name = $request->input('addName');
        $level_id = (int)$request->input('addFiliere');
        $user_id = (int)$request->input('addProf');

        if (!$level_id) {
            return redirect()->back()->with([
                'error' => 'Veuillez sectionner une filière'
            ]);
        }

        //Création du module
        $module = Module::create([
            'name' => $name,
            'level_id' => $level_id,
            'user_id' => $user_id
        ]);

        event(new ModuleRefresh($module));

        return redirect()->back()->with([
            'success' => 'Le module a bien été enregistré',
        ]);
    }  

    public function edit (Request $request) {
        
        $validator = Validator::make($request->all(), [
            'editName' => 'required|string|max:255',
        ], [
            'editName.required' => 'Le nom est requis.',
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        //Recuperation des variables
        $name = $request->input('editName');
        $id = $request->input('id');
        $level_id = $request->input('editFiliere');

        $module = Module::find($id);

        if (!$level_id) {
            return redirect()->back()->with([
                'error' => 'Veuillez sectionner une filière'
            ]);
        }

        $module->name = $name;
        $module->level_id = $level_id;
        $module->save();

        event(new ModuleRefresh($module));

        return redirect()->back()->with([
            'success' => 'Le module a bien été enregistré',
            'class' => 'success'
        ]);
    }  

    public function getModule (Request $request, $id) {
        $module = Module::with(['level.sector'])->with(['user'])->find($id);

       if ($module === null ) {
            return response()->json(['error' => 'Module introuvable']);
       }

        return response()->json($module, 200);
    }

    public function getProfFiliere ($id) {
        $users = User::whereHas('levels_users', function($query) use ($id){
            $query->where('level_id', $id);
        })
        ->where('role', 1)
        ->get();

        return response()->json($users, 200);
    }

    public function delete ($id) {
        $module = Module::find($id);

        event(new ModuleRefresh($module));
        
        if ($module) {
            $module->delete();
        } else {
            return redirect()->back()->with([
                'error' => 'Cet enregistrement n existe pas'
            ]);
        }

        return redirect()->back()->with([
            'success' => 'le module a bien été supprimé'
        ]);
    }
}
