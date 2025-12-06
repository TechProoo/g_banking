<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Jetstream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use App\Models\Activity;
use Jenssegers\Agent\Agent;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::loginView(function () {
            try {
                $settings = Schema::hasTable('settings') ? Settings::find(1) : null;
            } catch (\Exception $e) {
                Log::warning('Jetstream loginView settings skipped: ' . $e->getMessage());
                $settings = null;
            }

            return view('auth.login',[
                'title' => 'Sign In to Continue',
                'settings' => $settings,
            ]);
        });


        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            $agent = new Agent();

            if ($user &&
                Hash::check($request->password, $user->password)) {
                $request->session()->put('getAnouc', 'true');
                DB::table('activities')->insert([
                    'user' => $user->id,
                    'ip_address' => $request->ip(),
                    'device'=> $agent->device(),
                    'browser'=> $agent->browser(),
                    'os'=> $agent->platform(),
                ]);
               
             
             
             
                
                 User::where('id',$user->id)
        ->update([
            'pinstatus' => '1',
           
        ]);
                return $user;
            }
        });
        

        Fortify::registerView(function () {
            include 'get_user_ip_info.php';        
            return view('auth.register', [
                'title' => 'Register an Account',
                'user_country'=>$user_country,
                'countries'=>$countries,
                'settings' => safe_settings(),
            ]);
        });

    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
