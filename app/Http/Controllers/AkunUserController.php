<?php

namespace App\Http\Controllers;

use App\Models\DetailPosisi;
use App\Models\LogActivity;
use App\Models\Posisi;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkunUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function superadminstore(Request $request)
     {
         $roleid = $request->role_id;
         $nama = $request->nama;
         $email = $request->email;
         $loggedInUser = Auth::user();
         $loggedInUsername = $loggedInUser->nama_user; 
     
         $existingdata = User::where('email', $email)->first();
     
         if ($existingdata) {
             $request->session()->flash('error', "Email sudah terdaftar.");
             return redirect()->route('superadmin.akunuser.index');
         }

         // Create the user
         $user = User::create([
             'role_id' => $roleid,
             'nama' => $nama,
             'email' => $email,
             'created_by' => $loggedInUsername,
             'password' => Hash::make('12345678'),
         ]);
     
         // If the role_id is 2, save the posisi details to the DetailPosisi table
         if ($roleid == 2) {
            foreach ($request->posisi as $posisiItem) {
                $posisiId = $posisiItem['posisi_id'];
                $posisi = Posisi::find($posisiId)->nama_posisi; // Fetch the name of the position using the Posisi model
                $wilayahList = $posisiItem['wilayah'];
        
                // Retrieve names of the wilayahs by their IDs
                $wilayahNames = [];
                foreach ($wilayahList as $wilayahId) {
                    $wilayah = Wilayah::find($wilayahId); // Assuming you have a Wilayah model to fetch the name
                    if ($wilayah) {
                        $wilayahNames[] = $wilayah->nama_wilayah; // Replace 'nama_wilayah' with the actual column name for wilayah names
                    }
                }
        
                // Save the data into DetailPosisi
                DetailPosisi::create([
                    'user_id' => $user->id,
                    'posisi_id' => $posisiId,
                    'posisi' => $posisi,
                    'wilayah_id' => implode(',', $wilayahList), // Save wilayah IDs as a comma-separated string
                    'wilayah' => implode(',', $wilayahNames), // Save wilayah names as a comma-separated string
                ]);
            }
        }

        if ($roleid == 3) {
            foreach ($request->posisi as $posisiItem) {
                $posisiId = $posisiItem['posisi_id'];
                $posisi = Posisi::find($posisiId)->nama_posisi; // Fetch the name of the position using the Posisi model
                $wilayahList = $posisiItem['wilayah'];
        
                // Retrieve names of the wilayahs by their IDs
                $wilayahNames = [];
                foreach ($wilayahList as $wilayahId) {
                    $wilayah = Wilayah::find($wilayahId); // Assuming you have a Wilayah model to fetch the name
                    if ($wilayah) {
                        $wilayahNames[] = $wilayah->nama_wilayah; // Replace 'nama_wilayah' with the actual column name for wilayah names
                    }
                }
        
                // Save the data into DetailPosisi
                DetailPosisi::create([
                    'user_id' => $user->id,
                    'posisi_id' => $posisiId,
                    'posisi' => $posisi,
                    'wilayah_id' => implode(',', $wilayahList), // Save wilayah IDs as a comma-separated string
                    'wilayah' => implode(',', $wilayahNames), // Save wilayah names as a comma-separated string
                ]);
            }
        }
        
         // Log the activity
         LogActivity::create([
             'user_id' => Auth::id(),
             'nama_user' => Auth::user()->nama,
             'activity' => 'Membuat User',
             'description' => 'Berhasil membuat user ' . $request->nama,
             'timestamp' => now(),
             'role_id' => Auth::user()->role_id,
         ]);
     
         $request->session()->flash('success', 'Akun user berhasil ditambahkan.');
         return redirect(route('superadmin.akunuser.index'));
     }
     

    public function superadminindex(){
        $users = User::orderBy('created_at','desc')->get();

     

        return view('superadmin.akunuser.index',[
            'users' => $users,
        ]);
     }

    public function superadmincreate(){

        $role = Role::all();
        $posisi = Posisi::all();
        $wilayah = Wilayah::all();
        return view('superadmin.akunuser.create',[
            'role' => $role,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
        ]);
     }


     public function superadminshow($id){
        $user = User::find($id);

      
        $role = Role::all();
        $posisi = Posisi::all();
        $wilayah = Wilayah::all();

        $nama = DetailPosisi::with('User')->where('user_id', $id)->get();
       

        return view('superadmin.akunuser.edit',[
            'user' => $user,
            'role' => $role,
            'posisi' => $posisi,
            'wilayah' => $wilayah,
            'nama' => $nama,
        ]);
     }

     public function superadminupdate(Request $request, $id)
     {
         $data = User::find($id);
         $roleid = $request->role_id;
         $nama = $request->nama;
         $email = $request->email;
         $loggedInUser = Auth::user();
         $loggedInUsername = $loggedInUser->nama_user;
     
         // Check if the email is already registered
         $existingdata = User::where('email', $email)->where('id', '<>', $id)->first();
     
         if ($existingdata) {
             $request->session()->flash('error', "Email sudah terdaftar.");
             return redirect()->route('user');
         }
     
         // Update basic user data
         $data->nama = $nama;
         $data->email = $email;
         $data->role_id = $roleid;
         $data->updated_by = $loggedInUsername;
         $data->save();
     
         // If the role is "Rekrutmen", save positions and regions in DetailPosisi
         if ($roleid == 2) {
             // Delete existing DetailPosisi records for the user
             DetailPosisi::where('user_id', $id)->delete();
     
             // Save new DetailPosisi records
             foreach ($request->posisi as $posisiItem) {
                $posisiId = $posisiItem['posisi_id'];
                $posisi = Posisi::find($posisiId)->nama_posisi; // Fetch the name of the position using the Posisi model
                $wilayahList = $posisiItem['wilayah'];
                
        
                // Retrieve names of the wilayahs by their IDs
                $wilayahNames = [];
                foreach ($wilayahList as $wilayahId) {
                    $wilayah = Wilayah::find($wilayahId); // Assuming you have a Wilayah model to fetch the name
                    if ($wilayah) {
                        $wilayahNames[] = $wilayah->nama_wilayah; // Replace 'nama_wilayah' with the actual column name for wilayah names
                    }
                }
        
                // Save the data into DetailPosisi
                DetailPosisi::create([
                    'user_id' => $id,
                    'posisi_id' => $posisiId,
                    'posisi' => $posisi,
                    'wilayah_id' => implode(',', $wilayahList), // Save wilayah IDs as a comma-separated string
                    'wilayah' => implode(',', $wilayahNames), // Save wilayah names as a comma-separated string
                ]);
            }
         } else {
             // If the role is not "Rekrutmen", remove associated DetailPosisi records
             DetailPosisi::where('user_id', $id)->delete();
         }
     
         // Log the activity
         LogActivity::create([
             'user_id' => Auth::id(),
             'nama_user' => Auth::user()->nama,
             'activity' => 'Update User',
             'description' => 'Berhasil mengupdate user ' . $data->nama,
             'timestamp' => now(),
             'role_id' => Auth::user()->role_id,
         ]);
     
         $request->session()->flash('success', 'Akun user berhasil diubah.');
     
         return redirect(route('superadmin.akunuser.index'));
     }
     
     



     public function resetPassword(User $user, Request $request)
     {
     
         $loggedInUser = Auth::user();
         $loggedInUsername = $loggedInUser->nama_user; 
         
         $namauser = $user -> nama;
     
         $user->update([
             'updated_by'=> $loggedInUsername,
             'password' => Hash::make('12345678'), 
         ]);
        


         LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Reset Password User',
            'description' => "Berhasil melakukan reset password pada user $namauser",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

         $request->session()->flash('success', 'Password berhasil direset.');
     
         return redirect()->route('superadmin.akunuser.index');
         
     }   
     

     public function superadmindestroy(Request $request, $id)
     {
         $user = User::find($id);
 
         $deleteduser = $user->nama;
 
 
             if($user->role_id == 1){
                 if ($user->Role->nama_role === 'Superadmin') {
                     if ($user->id === Auth::id()) {
                         return redirect()->route('superadmin.akunuser.index')->with('error', 'Tidak dapat menghapus akun anda sendiri.');
                     }
                 
                     $adminCount = User::whereHas('Role', function ($query) {
                         $query->where('nama_role', 'Superadmin');
                     })->count();
                 
                     if ($adminCount <= 1) {
                         return redirect()->route('superadmin.akunuser.index')->with('error', 'Tidak dapat menghapus akun superadmin terakhir.');
                     }
                 }
                 
     
             
     
     
           
     
         }

  
         $detailposisi = DetailPosisi::where('user_id', $user->id)->get();
    
         // Hapus semua DetailMember yang ditemukan
         foreach ($detailposisi as $posisi) {
             $posisi->delete();
         }
         

     
         $user->delete();

         LogActivity::create([
            'user_id' => Auth::id(),
            'nama_user' =>  Auth::user()->nama,
            'activity' => 'Hapus User',
            'description' => "Berhasil menghapus user $deleteduser",
            'timestamp' => now(),
            'role_id' =>  Auth::user()->role_id,
        ]);

 
         $request->session()->flash('success', "Akun user berhasil dihapus.");
 
         return redirect()->route('superadmin.akunuser.index');
     }
 

     public function detailposisi($id){
        $data = User::find($id);

        $posisi = DetailPosisi::with('User')->where('user_id', $id)->get();
        

        return view('superadmin.akunuser.detail',[
            'data' => $data,
            'posisi' => $posisi,
        ]);

     }

    public function index()
    {
        //
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
