<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view ('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $credentials = $request->only('email', 'password');

        
      
       
        // Attempt to log in the user
        if (Auth::attempt($credentials)) { 
          
            // Check user role and redirect accordingly
            $user = Auth::user();
           
            if ($user->isSuperAdmin()) {

                LogActivity::create([
                    'user_id' => Auth::id(),
                    'nama_user' =>  Auth::user()->nama,
                    'activity' => 'Login',
                    'description' => 'Berhasil login',
                    'timestamp' => now(),
                    'role_id' =>  Auth::user()->role_id,
                ]);

                return redirect()->route('superadmindashboard'); // Adjust the route accordingly
            } elseif ($user->isRekrutmen()) {

                LogActivity::create([
                    'user_id' => Auth::id(),
                    'nama_user' =>  Auth::user()->nama,
                    'activity' => 'Login',
                    'description' => 'Berhasil login',
                    'timestamp' => now(),
                    'role_id' =>  Auth::user()->role_id,
                ]);

                return redirect()->route('rekrutmendashboard'); // Adjust the route accordingly
            }
           
            elseif ($user->isTrainer()) {

                LogActivity::create([
                    'user_id' => Auth::id(),
                    'nama_user' =>  Auth::user()->nama,
                    'activity' => 'Login',
                    'description' => 'Berhasil login',
                    'timestamp' => now(),
                    'role_id' =>  Auth::user()->role_id,
                ]);

                return redirect()->route('trainerdashboard'); // Adjust the route accordingly
            }
        
        }
        // Authentication failed, redirect back with errors
        return redirect()->route('login')->with('error', 'Email atau password salah, silakan coba lagi.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
