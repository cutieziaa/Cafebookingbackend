<?php
// app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nomor_wa' => ['required', 'string', 'max:20'],
        ]);
    }

    protected function create(array $data)
    {
        try {
            $user = User::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'nomor_wa' => $data['nomor_wa'],
                'peran' => 'customer',
            ]);
            
            \Log::info('User created successfully', ['user_id' => $user->id]);
            return $user;
            
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            throw $e;
        }
    }

    
}