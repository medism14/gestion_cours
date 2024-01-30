<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Sector;
use App\Models\Module;
use App\Models\Notif;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ResourceController extends Controller
{
    public function index (Request $request) {

        $id = auth()->user()->id;
        function manip($type, $string) {
            if ($type == "maj") {
                return strtoupper($string);
            } elseif ($type == "min") {
                return strtolower($string);
            } else {
                return ucfirst($string);
            }
        }

        if ($request->input('searchNotif')) {
            $id = $request->input('searchNotif');

            $resources = Resource::where('id', $id)->paginate(5);
            $loup = 667;
        }

        if ($request->input('search')) {
            $search = $request->input('search');
            //Si c'est un professeur qui fait la recherche
            if (auth()->user()->role == 1) {
            $modules = Module::where('user_id', auth()->user()->id)->get();

            $resources = Resource::where(function ($query) use ($search, $id) {
                                $query->whereHas('module', function ($query) use ($id) {
                                    $query->where('user_id', $id);
                                });
                        
                                $query->where(function ($query) use ($search) {
                                    $query->where('section', 'like', manip("maj", $search) . '%')
                                        ->orWhere('section', 'like', manip("min", $search) . '%')
                                        ->orWhere('section', 'like', manip("cap", $search) . '%')
                                        ->orWhereHas('file', function ($query) use ($search) {
                                            $query->where('filename', 'like', manip("maj", $search) . '%')
                                                ->orWhere('filename', 'like', manip("min", $search) . '%')
                                                ->orWhere('filename', 'like', manip("cap", $search) . '%');
                                        })
                                        ->orWhereHas('module', function ($query) use ($search) {
                                            $query->where('name', 'like', manip("maj", $search) . '%')
                                                ->orWhere('name', 'like', manip("min", $search) . '%')
                                                ->orWhere('name', 'like', manip("cap", $search) . '%');
                                        })
                                        ->orWhereHas('module.level.sector', function ($query) use ($search) {
                                            $query->where('name', 'like', manip("maj", $search) . '%')
                                                ->orWhere('name', 'like', manip("min", $search) . '%')
                                                ->orWhere('name', 'like', manip("cap", $search) . '%');
                                        });
                                });
                            })
                            ->paginate(5);
            //Si c'est un etudiant qui fait la recherche
            } else if (auth()->user()->role == 2) {

                $resources = Resource::where(function ($query) use ($id, $search) {
                                    $query->whereHas('module.level.levels_users.user', function($query) use ($id){
                                        $query->where('id', $id)->where('role', 2);
                                    });

                                    $query->where(function($query) use ($search) {
                                        $query->where('section', 'like', manip("maj", $search) . '%')
                                        ->orWhere('section', 'like', manip("min", $search) . '%')
                                        ->orWhere('section', 'like', manip("cap", $search) . '%')
                                        ->orWhereHas('file', function($query) use ($search) {
                                             $query->where('filename', 'like', manip("maj", $search) . '%')
                                             ->orWhere('filename', 'like', manip("min", $search) . '%')
                                             ->orWhere('filename', 'like', manip("cap", $search) . '%');
                                        })
                                        ->orWhereHas('module', function($query) use ($search) {
                                             $query->where('name', 'like', manip("maj", $search) . '%')
                                             ->orWhere('name', 'like', manip("min", $search) . '%')
                                             ->orWhere('name', 'like', manip("cap", $search) . '%');
                                         })
                                         ->orWhereHas('module.level.sector', function($query) use ($search) {
                                             $query->where('name', 'like', manip("maj", $search) . '%')
                                             ->orWhere('name', 'like', manip("min", $search) . '%')
                                             ->orWhere('name', 'like', manip("cap", $search) . '%');
                                         });
                                    });
                                })->paginate(5);
            //Si c'est un admin qui fait la recherche
            } else {
                $modules = Module::all();
                $resources = Resource::where('section', 'like', manip("maj", $search) . '%')
                                   ->orWhere('section', 'like', manip("min", $search) . '%')
                                   ->orWhere('section', 'like', manip("cap", $search) . '%')
                                   ->orWhereHas('file', function($query) use ($search) {
                                        $query->where('filename', 'like', manip("maj", $search) . '%')
                                        ->orWhere('filename', 'like', manip("min", $search) . '%')
                                        ->orWhere('filename', 'like', manip("cap", $search) . '%');
                                   })
                                   ->orWhereHas('module', function($query) use ($search) {
                                        $query->where('name', 'like', manip("maj", $search) . '%')
                                        ->orWhere('name', 'like', manip("min", $search) . '%')
                                        ->orWhere('name', 'like', manip("cap", $search) . '%');
                                    })
                                    ->orWhereHas('module.level.sector', function($query) use ($search) {
                                        $query->where('name', 'like', manip("maj", $search) . '%')
                                        ->orWhere('name', 'like', manip("min", $search) . '%')
                                        ->orWhere('name', 'like', manip("cap", $search) . '%');
                                    })
                                    ->paginate(5);
            }

            $loup = 667;
        } 
        
        if (!$request->input('searchNotif') && !$request->input('search')) {
            $loup = null;

            if (auth()->user()->role == 1) {
                $modules = Module::where('user_id', auth()->user()->id)->get();
                $resources = Resource::whereHas('module', function($query) use ($id){
                    $query->where('user_id', $id);
                })->paginate(5);
            } else if (auth()->user()->role == 2) {
                $resources = Resource::whereHas('module.level.levels_users.user', function($query) use ($id){
                    $query->where('id', $id)->where('role', 2);
                })->paginate(5);
            } else {
                $modules = Module::all();
                $resources = Resource::paginate(5);
            }

        }

        if (auth()->user()->role == 2) {
            return view('authed.resources', [
                'resources' => $resources,
                'loup' => $loup
            ]);
        } else {
            return view('authed.resources', [
                'resources' => $resources,
                'modules' => $modules,
                'loup' => $loup
            ]);
        }

        
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(), [
            'addDescription' => 'required',
            'addSection' => 'required',
            'addModule' => 'required', 
        ], [
            'addDescription.required' => 'Le prenom est requis.',
            'addSection.required' => "La section est requise.",
            'addModule.required' => "Le module est requis"
        ]);
        
        if ($validator->fails()) {

            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        if (!$request->file('addFile')) {
            return redirect()->back()->with([
                'error' => 'Veuillez remplir le fichier'
            ]);
        }
        
        //Partie Resource infos
        $description = $request->input('addDescription');
        $section = $request->input('addSection');
        $addModule = $request->input('addModule');
        
        $resource = Resource::Create([
            'description' => $description,
            'section' => $section,
            'module_id' => (int)$addModule
        ]);

        //Partie notifs
        $module = Module::find($addModule);

        $level_id = $module->level->id;

        $usersOnLevel = User::whereHas('levels_users', function($query) use ($level_id) {
            $query->where('level_id', $level_id);   
        })->get();

        foreach ($usersOnLevel as $user) {
            if ($user->role == 2) {
                $user->notifs++;
                $user->save();

                Notif::Create([
                    'user_id' => $user->id,
                    'resource_id' => $resource->id
                ]);
            }
        }

        //Partie fichier
        $addFile = $request->file('addFile');
        $fileName = $resource->id . '_' . $addFile->getClientOriginalName();
        $filePath = $addFile->storeAs('resources_files', $fileName, 'public');
        $fileType = $addFile->getClientOriginalExtension();

        // Stocker le fichier dans le dossier storage/app/public/resources_files

        $file = File::Create([
            'filename' => $addFile->getClientOriginalName(),
            'path' => $filePath,
            'filetype' => $fileType,
            'resource_id' => $resource->id 
        ]);

        return redirect()->back()->with([
            'success' => 'La resource a bien été ajouté'
        ]);

    }

    public function getResource (Request $request, $id) {
        $resource = Resource::with(['module.level.sector'])->with('file')->with(['module.user'])->find($id);

       if ($resource === null ) {
            return response()->json(['error' => 'Utilisateur introuvable']);
       }

        return response()->json($resource, 200);
    }

    public function edit (Request $request) {
        $id = $request->input('id');
        $resource = Resource::find($id);

        $validator = Validator::make($request->all(), [
            'editDescription' => 'required',
            'editSection' => 'required',
            'editModule' => 'required', 
        ], [
            'editDescription.required' => 'Le prenom est requis.',
            'editSection.required' => "La section est requise.",
            'editModule.required' => "Le module est requis"
        ]);
        
        if ($validator->fails()) {

            $errors = json_decode($validator->errors(), true);

            return redirect()->back()->with([
                'error' => $errors,
            ]);
        }

        $description = $request->input('editDescription');
        $section = $request->input('editSection');
        $editModule = $request->input('editModule');

        //GERER LA PARTIE NOTIFS AVEC LES RESOURCES ICI

        //Partie notifs
        $moduleNouveau = Module::find($editModule);
        $moduleAncien = Module::find($resource->module->id);

        if ($moduleAncien->id != $moduleNouveau->id) {
            $level_id_ancien = $moduleAncien->level->id;
            $level_id_nouveau = $moduleNouveau->level->id;

            $usersAncienModule = User::whereHas('levels_users', function($query) use ($level_id_ancien) {
                $query->where('level_id', $level_id_ancien);   
            })->get();

            $usersNouveauModule = User::whereHas('levels_users', function($query) use ($level_id_nouveau) {
                $query->where('level_id', $level_id_nouveau);   
            })->get();

            foreach ($usersAncienModule as $user) {
                if ($user->notifs != 0) {
                    $user->notifs--;
                }
                $user->save();

                $notif = Notif::where('user_id', $user->id)->where('resource_id', $resource->id)->first();
                $notif->delete();
            }

            foreach ($usersNouveauModule as $user) {
                if ($user->notifs != 0) {
                    $user->notifs++;
                }
                $user->save();

                Notif::Create([
                    'user_id' => $user->id,
                    'resource_id' => $resource->id,
                ]);
            }
        }

        $resource->description = $description;
        $resource->section = $section;
        $resource->module_id = $editModule;
        $resource->save();

        if ($request->file('editFile')) {

            $file = $resource->file;

            $filePath = storage_path("app/public/{$file->path}");

            unlink($filePath);

            $file->delete();

            $editFile = $request->file('editFile');
            $fileName = $resource->id . '_' . $editFile->getClientOriginalName();
            $filePath = $editFile->storeAs('resources_files', $fileName, 'public');
            $fileType = $editFile->getClientOriginalExtension();

            $file = File::Create([
                'filename' => $editFile->getClientOriginalName(),
                'path' => $filePath,
                'filetype' => $fileType,
                'resource_id' => $resource->id 
            ]);
        }

        return redirect()->back()->with([
            'success' => 'La resource a bien été modifié'
        ]);
    }

    public function download (Request $request, $id) {
        $resource = Resource::find($id);

        if ($resource) {

            $filePath = 'storage/' . $resource->file->path;

            $filePath = public_path($filePath);

            return response()->download($filePath, $resource->file->filename);
        } else {
            return redirect()->back()->with([
                'error' => 'Cet enregistrement n existe pas'
            ]);
        }
    }

    public function delete (Request $request, $id) {
        $resource = Resource::find($id);

        if ($resource) {
            
            $filePath = storage_path("app/public/{$resource->file->path}");

            unlink($filePath);

            $resource->delete();

        } else {
            return redirect()->back()->with([
                'error' => 'Cet enregistrement n existe pas'
            ]);
        }

        return redirect()->back()->with([
            'success' => 'La resource a bien été supprimé'
        ]);
    }
}
