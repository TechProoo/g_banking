<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Plans;
use App\Models\Faq;
use App\Models\Testimony;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\TermsPrivacy;
use Illuminate\Support\Facades\DB;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Exception;

class HomePageController extends Controller
{
    public function index(){
        $settings = $this->safeSettings();
        //sum total deposited
        $total_deposits = Deposit::where('status','processed')->sum('amount');
        
        
        
        //sum total withdrawals
        $total_withdrawals = Withdrawal::where('status','processed')->sum('amount');
        
        
        
      
        return view('home.index')->with(array(
            'settings' => $settings,
            'total_users' => User::count(),
            'plans' => Plans::all(),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> Faq::orderby('id', 'desc')->get(),
            'test'=> Testimony::orderby('id', 'desc')->get(),
            'withdrawals' => Withdrawal::orderby('id','DESC')->take(7)->get(),
            'deposits' => Deposit::orderby('id','DESC')->take(7)->get(),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => Plans::where('type','Main')->get(),
            'pplans' => Plans::where('type','Promo')->get(),
        ));
    }


public function investment(){
    $settings = $this->safeSettings();
        //sum total deposited
        $total_deposits = Deposit::where('status','processed')->sum('amount');
        
        
        
        //sum total withdrawals
        $total_withdrawals = Withdrawal::where('status','processed')->sum('amount');
        
        
        
      
        return view('home.pricing')->with(array(
            'settings' => $settings,
            'total_users' => User::count(),
            'plans' => Plans::all(),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> Faq::orderby('id', 'desc')->get(),
            'test'=> Testimony::orderby('id', 'desc')->get(),
            'withdrawals' => Withdrawal::orderby('id','DESC')->take(7)->get(),
            'deposits' => Deposit::orderby('id','DESC')->take(7)->get(),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => Plans::where('type','Main')->get(),
            'pplans' => Plans::where('type','Promo')->get(),
        ));
    }


public function statistics(){
    $settings = $this->safeSettings();
        //sum total deposited
        $total_deposits = Deposit::where('status','processed')->sum('amount');
        
        
        
        //sum total withdrawals
        $total_withdrawals = Withdrawal::where('status','processed')->sum('amount');
        
        
        
      
        return view('home.statistics')->with(array(
            'settings' => $settings,
            'total_users' => User::count(),
            'plans' => Plans::all(),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> Faq::orderby('id', 'desc')->get(),
            'test'=> Testimony::orderby('id', 'desc')->get(),
            'withdrawals' => Withdrawal::orderby('id','DESC')->take(7)->get(),
            'deposits' => Deposit::orderby('id','DESC')->take(7)->get(),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => Plans::where('type','Main')->get(),
            'pplans' => Plans::where('type','Promo')->get(),
        ));
    }



    //Licensing and registration route
    public function licensing(){
        return view('home.licensing')
        ->with(array(
            'mplans' => Plans::where('type','Main')->get(),
            'pplans' => Plans::where('type','Promo')->get(),
            'title' => 'Licensing, regulation and registration',
            'settings' => $this->safeSettings(),
        ));
    }
//tradebots
public function tradebots(){
        
    return view('home.tradebots');
    
}
    ////margin
    public function margin(){
        
        return view('home.margin');
        
    }
//careers
public function business(){
    return view('home.business',)->with(array(
        'title' => 'Business',
        'settings' => $this->safeSettings(),
));
    
}


public function personal(){
    return view('home.personal',)->with(array(
        'title' => 'personal',
        'settings' => $this->safeSettings(),
));
    
}



public function cards(){
    return view('home.cards',)->with(array(
        'title' => 'cards',
        'settings' => $this->safeSettings(),
));
    
}


public function loans(){
    return view('home.loans',)->with(array(
        'title' => 'loans',
        'settings' => $this->safeSettings(),
));
    
}

public function app(){
    return view('home.app',)->with(array(
        'title' => 'app',
        'settings' => $this->safeSettings(),
));
    
}


    //Terms of service route
    public function terms(){
        return view('home.terms')
        ->with(array(
            'mplans' => Plans::where('type','Main')->get(),
            'title' => 'Terms of Service',
            'settings' => $this->safeSettings(),
        ));
    }

    //Privacy policy route
    public function privacy(){
        $terms = null;
        try {
            if (Schema::hasTable('terms_privacy')) {
                $terms = TermsPrivacy::find(1);
            }
        } catch (Exception $e) {
            Log::warning('Could not fetch terms: ' . $e->getMessage());
        }

        if ($terms && ($terms->useterms ?? null) == 'no') {
           return redirect()->back();
        }

        return view('home.privacy')
        ->with(array(
            'mplans' => Plans::where('type','Main')->get(),
            'title' => 'Privacy Policy',
            'settings' => $this->safeSettings(),
        ));
    }

    //FAQ route
    public function faq(){
        return view('home.faq')
        ->with(array(
            'title' => 'FAQs',
            'faqs'=> Faq::orderby('id', 'desc')->get(),
            'settings' => $this->safeSettings(),
        ));
    }

    //about route
    public function about(){
        return view('home.about')
        ->with(array(
            'mplans' => Plans::where('type','Main')->get(),
            'title' => 'About',
            'settings' => $this->safeSettings(),
        ));
    }

    //Contact route
    public function contact(){
        return view('home.contact')
        ->with(array(
            'mplans' => Plans::where('type','Main')->get(),
                'pplans' => Plans::where('type','Promo')->get(),
            'title' => 'Contact',
            'settings' => $this->safeSettings(),
        ));
    }

  
 public function verify(Request $request){
        
$n1 = rand(0,9);
$n2 = rand(0,9);
$n3 = rand(0,9);
$n4 = rand(0,9);
$n5 = rand(0,9);
$n6 = rand(0,9);

$captcha = "$n1$n2$n3$n4$n5$n6";
  $request->session()->put('code',$captcha);

        return view('home.verify')
        ->with(array(
            'captcha'=> $captcha,
            'title' => 'verify',
            'settings' => $this->safeSettings(),
        ));
    }
    
    
    
     public function codeverify(Request $request){
      $code =  $request->session()->get('code');
      if($code == $request->code){
          
           return redirect()->route('register');
      }
       
       
       return redirect()->back()->with('success', 'Invalid Code Supplied');

    }
    //send contact message to admin email
    public function sendcontact(Request $request){

        $settings = $this->safeSettings();
        $message = substr(wordwrap($request['message'],70),0,350);
        $subject = "$request->subject, my email $request->email";

        $contactEmail = $settings->contact_email ?? config('mail.from.address');
        if (empty($contactEmail)) {
            Log::warning('Contact email not configured; cannot send contact message.');
            return redirect()->back()->with('message', 'Contact service currently unavailable.');
        }

        Mail::to($contactEmail)->send(new NewNotification($message, $subject, 'Admin'));
        return redirect()->back()->with('success', ' Your message was sent successfully!');
    }


    public function homesendcontact(Request $request){

        $settings = $this->safeSettings();
        $message = substr(wordwrap($request['message'],70),0,350);
        $subject = "$request->subject, my email $request->email";

        $contactEmail = $settings->contact_email ?? config('mail.from.address');
        if (empty($contactEmail)) {
            Log::warning('Contact email not configured; cannot send contact message.');
            return redirect()->back()->with('message', 'Contact service currently unavailable.');
        }

        Mail::to($contactEmail)->send(new NewNotification($message, $subject, 'Admin'));

         if (Mail::failures()) {
             return redirect()->back()->with('message', ' message was not sent! Please try again later');
         }
        return redirect()->back()->with('success', ' Your message was sent successfully!');
    }

    /**
     * Return Settings model safely when DB/table is available.
     *
     * @return \App\Models\Settings|null
     */
    protected function safeSettings()
    {
        $settings = null;
        try {
            if (Schema::hasTable('settings')) {
                $settings = Settings::find(1);
            }
        } catch (Exception $e) {
            Log::warning('Could not fetch settings: ' . $e->getMessage());
        }

        return $settings;
    }
}
