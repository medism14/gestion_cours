<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Sector;
use App\Models\Module;
use App\Models\Notif;
use App\Models\Level;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Events\ResourceRefresh;
use App\Events\NotifRefresh;

class ResourceController extends Controller
{
    public function index (Request $request) {
        $user = auth()->user();
        $user_id = $user->id;
        $query = Resource::with(['module.level.sector', 'file']);

        if ($request->input('moduleList')) {
            $query->where('module_id', $request->input('moduleList'));
            $loup = 667;
        } else if ($request->input('nouvelleRessource')) {
            $query->where('updated_at', '>', $user->notif_viewed);
            $loup = 667;
        } else if ($request->input('searchNotif')) {
            $query->where('id', $request->input('searchNotif'));
            $loup = 667;
        } else if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('section', 'like', $search . '%')
                  ->orWhereHas('file', function($sub) use ($search) {
                      $sub->where('filename', 'like', $search . '%');
                  })
                  ->orWhereHas('module', function($sub) use ($search) {
                      $sub->where('name', 'like', $search . '%')
                          ->orWhereHas('level.sector', function($ss) use ($search) {
                              $ss->where('name', 'like', $search . '%');
                          });
                  });
            });

            if ($user->role == 1) {
                $query->whereHas('module', fn($q) => $q->where('user_id', $user_id));
            } else if ($user->role == 2) {
                $query->whereHas('module.level.levels_users', fn($q) => $q->where('user_id', $user_id));
            }
            $loup = 667;
        } else {
            $loup = null;
            if ($user->role == 1) {
                $query->whereHas('module', fn($q) => $q->where('user_id', $user_id));
            } else if ($user->role == 2) {
                $query->whereHas('module.level.levels_users', fn($q) => $q->where('user_id', $user_id));
            }
        }

        $resources = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();

        // Avoid redundant saves
        if ($user->notifs != 0) {
            $user->update(['notifs' => 0, 'notif_viewed' => now()]);
        }

        if ($user->role == 2) {
            $level = Level::whereHas('levels_users', fn($q) => $q->where('user_id', $user_id))->first();
            $modulesSearch = $level ? Module::where('level_id', $level->id)->get() : collect();

            return view('authed.resources', compact('resources', 'modulesSearch', 'loup'));
        } else {
            $modules = ($user->role == 1) ? Module::where('user_id', $user_id)->get() : Module::all();
            return view('authed.resources', compact('resources', 'modules', 'loup'));
        }
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'addDescription' => 'required',
            'addSection' => 'required',
            'addModule' => 'required', 
        ], [
            'addDescription.required' => 'La description est requise.',
            'addSection.required' => "La section est requise.",
            'addModule.required' => "Le module est requis"
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with(['error' => json_decode($validator->errors(), true)]);
        }

        if (!$request->file('addFile')) {
            return redirect()->back()->with(['error' => 'Veuillez remplir le fichier']);
        }
        
        $resource = Resource::create([
            'description' => $request->input('addDescription'),
            'section' => $request->input('addSection'),
            'module_id' => (int)$request->input('addModule')
        ]);

        // Optimized Batch Notifications
        $level_id = Module::find($request->input('addModule'))->level_id;
        $studentIds = User::where('role', 2)
            ->whereHas('levels_users', fn($q) => $q->where('level_id', $level_id))
            ->pluck('id');

        if ($studentIds->isNotEmpty()) {
            User::whereIn('id', $studentIds)->increment('notifs');
            $notifs = $studentIds->map(fn($uid) => [
                'resource_id' => $resource->id,
                'user_id' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();
            Notif::insert($notifs);
        }

        // Handle File
        $addFile = $request->file('addFile');
        $fileName = $resource->id . '_' . $addFile->getClientOriginalName();
        $filePath = $addFile->storeAs('resources_files', $fileName, 'public');

        File::create([
            'filename' => $addFile->getClientOriginalName(),
            'path' => $filePath,
            'filetype' => $addFile->getClientOriginalExtension(),
            'resource_id' => $resource->id 
        ]);

        $resource->load('module.level');
        event(new ResourceRefresh($resource));
        event(new NotifRefresh($resource, 'add'));

        return redirect()->back()->with(['success' => 'La resource a bien été ajouté']);
    }

    public function getResource (Request $request, $id) {
        $resource = Resource::with(['module.level.sector'])->with('file')->with(['module.user'])->find($id);

       if ($resource === null ) {
            return response()->json(['error' => 'Utilisateur introuvable']);
       }

        return response()->json($resource, 200);
    }

    public function edit(Request $request)
    {
        $resource = Resource::with(['module.level', 'file'])->find($request->input('id'));
        if (!$resource) return redirect()->back()->with(['error' => 'Ressource non trouvée']);

        $validator = Validator::make($request->all(), [
            'addDescription' => 'required',
            'addSection' => 'required',
            'addModule' => 'required',
        ], [
            'addDescription.required' => 'La description est requise.',
            'addSection.required' => "La section est requise.",
            'addModule.required' => "Le module est requis"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['error' => json_decode($validator->errors(), true)]);
        }

        $description = $request->input('addDescription');
        $section = $request->input('addSection');
        $newModuleId = (int)$request->input('addModule');

        $oldModule = $resource->module;
        $newModule = Module::with('level')->find($newModuleId);

        if ($oldModule->id != $newModule->id) {
            // Module changed: update notifications for students of both old and new modules
            $oldLevelId = $oldModule->level_id;
            $newLevelId = $newModule->level_id;

            // Decrement old module students (if they haven't viewed yet)
            $oldModuleStudents = User::where('role', 2)
                ->whereHas('levels_users', fn($q) => $q->where('level_id', $oldLevelId))
                ->get();
            
            foreach ($oldModuleStudents as $student) {
                if ($student->notifs > 0) $student->decrement('notifs');
            }
            Notif::where('resource_id', $resource->id)->delete();

            // Increment new module students
            $newModuleStudents = User::where('role', 2)
                ->whereHas('levels_users', fn($q) => $q->where('level_id', $newLevelId))
                ->pluck('id');
            
            if ($newModuleStudents->isNotEmpty()) {
                User::whereIn('id', $newModuleStudents)->increment('notifs');
                $newNotifs = $newModuleStudents->map(fn($uid) => [
                    'user_id' => $uid,
                    'resource_id' => $resource->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();
                Notif::insert($newNotifs);
            }
        } else {
            // Module didn't change, but we refresh notifications
            $level_id = $newModule->level_id;
            $studentIds = User::where('role', 2)
                ->whereHas('levels_users', fn($q) => $q->where('level_id', $level_id))
                ->pluck('id');

            // Reset for students who haven't viewed
            Notif::where('resource_id', $resource->id)->delete();
            if ($studentIds->isNotEmpty()) {
                $newNotifs = $studentIds->map(fn($uid) => [
                    'user_id' => $uid,
                    'resource_id' => $resource->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();
                Notif::insert($newNotifs);
            }
        }

        $resource->update([
            'description' => $description,
            'section' => $section,
            'module_id' => $newModuleId,
        ]);

        // Handle File Update
        if ($request->hasFile('addFile')) {
            $oldFile = $resource->file;
            if ($oldFile) {
                $oldPath = storage_path("app/public/{$oldFile->path}");
                if (file_exists($oldPath)) unlink($oldPath);
                $oldFile->delete();
            }

            $uploadedFile = $request->file('addFile');
            $fileName = $resource->id . '_' . $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('resources_files', $fileName, 'public');

            File::create([
                'filename' => $uploadedFile->getClientOriginalName(),
                'path' => $filePath,
                'filetype' => $uploadedFile->getClientOriginalExtension(),
                'resource_id' => $resource->id 
            ]);
        }

        $resource->load('module.level');
        event(new ResourceRefresh($resource));
        event(new NotifRefresh($resource, 'edit'));

        return redirect()->back()->with(['success' => 'La ressource a bien été modifiée']);
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
        $resource = Resource::with('module.level')->find($id);

        event(new ResourceRefresh($resource));
        event(new NotifRefresh($resource, 'delete'));

        if ($resource) {   

            $level = $resource->module->level;

            $users = User::whereHas('levels_users.level', function ($query) use ($level) {
                $query->where('id', $level->id);    
            })->where('role', 2)->get(); 

            foreach ($users as $user) {
                if ($user->notif_viewed < $resource->updated_at) {
                    $user->notifs = $user->notifs - 1;
                    $user->save();
                }
            }
            
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

    public function ressourceDisponible (Request $request, $userId) {

        $ressources = Resource::whereHas('module.level.levels_users', function ($query) use ($userId){
            $query->where('user_id', $userId);
        })->count();

        return response()->json($ressources, 200);
    }
}
