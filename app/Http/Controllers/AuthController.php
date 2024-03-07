<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthController extends Controller


{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            //Remember infos
            if ($request->input('remember')) {
                setcookie("email", $request->input('email'), time() + 3600*24*30*12*60);
                setcookie("password", $request->input('password'), time() + 3600*24*30*12*60);
            } else {
                setcookie("email", "");
                setcookie("password", "");
            }

            return redirect('/dashboard');
        } else {
            return redirect()->back()->with([
                'error' => "Email ou mot de passe incorrect",
            ]);
        }
    }

    public function logout (Request $request) {

        Auth::logout();
        return redirect()->route('auth');
    }

    public function parameters(Request $request) {

        $id = Auth()->user()->id;

        $user = User::find($id);

        if ($request->isMethod('post')) {
            
            if (auth()->user()->role != 0) {
                $mdp = $request->input('mdp');

                if ($mdp) {
                    $user->password = Hash::make($mdp);
                }
            } else {
                $prenom = $request->input('prenom');
                $nom = $request->input('nom');
                $email = $request->input('email');
                $phone = $request->input('phone');
                $mdp = $request->input('mdp');

                $validator = Validator::make($request->all(), [
                    'prenom' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'phone' => 'required|string|max:20',
                ], [
                    'prenom.required' => 'Le champ prenom est obligatoire.',
                    'email.required' => 'Le champ email est obligatoire.',
                    'phone.required' => 'Le champ telephone est obligatoire.',
                ]);

                $email_check = User::where('email', $email)->first();

                if ($email_check && $email_check->email != $user->email) {
                    return redirect()->back()->with([
                        'error' => 'L email doit être unique',
                        'user' => $user,
                    ]);
                }

                if ($validator->fails()) {

                    $errors = json_decode($validator->errors(), true);
                    return redirect()->back()->with([
                        'error' => $errors,
                        'user' => $user,
                    ]);
                }

                $user->first_name = $prenom;
                $user->last_name = $nom;
                $user->email = $email;
                $user->phone = $phone;

                if ($mdp) {
                    $user->password = Hash::make($mdp);
                }
            }

            $user->save();

            return redirect()->route('dashboard')->with([
                'success' => 'La modification a bien été prit en compte'
            ]);
        }

        return view('authed.parameters', ['user' => $user]);
    }


}
 