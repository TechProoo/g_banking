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

class HomePageController extends Controller
{
    public function index(){
        $settings = safe_settings();
        //sum total deposited
        $total_deposits = safe_query(fn() => Deposit::where('status','processed')->sum('amount'), 0);
        
        //sum total withdrawals
        $total_withdrawals = safe_query(fn() => Withdrawal::where('status','processed')->sum('amount'), 0);
        
        return view('home.index')->with(array(
            'settings' => $settings,
            'total_users' => safe_query(fn() => User::count(), 0),
            'plans' => safe_query(fn() => Plans::all(), collect()),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> safe_query(fn() => Faq::orderby('id', 'desc')->get(), collect()),
            'test'=> safe_query(fn() => Testimony::orderby('id', 'desc')->get(), collect()),
            'withdrawals' => safe_query(fn() => Withdrawal::orderby('id','DESC')->take(7)->get(), collect()),
            'deposits' => safe_query(fn() => Deposit::orderby('id','DESC')->take(7)->get(), collect()),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'pplans' => safe_query(fn() => Plans::where('type','Promo')->get(), collect()),
        ));
    }


public function investment(){
        $settings = safe_settings();
        //sum total deposited
        $total_deposits = safe_query(fn() => Deposit::where('status','processed')->sum('amount'), 0);
        
        //sum total withdrawals
        $total_withdrawals = safe_query(fn() => Withdrawal::where('status','processed')->sum('amount'), 0);
        
        return view('home.pricing')->with(array(
            'settings' => $settings,
            'total_users' => safe_query(fn() => User::count(), 0),
            'plans' => safe_query(fn() => Plans::all(), collect()),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> safe_query(fn() => Faq::orderby('id', 'desc')->get(), collect()),
            'test'=> safe_query(fn() => Testimony::orderby('id', 'desc')->get(), collect()),
            'withdrawals' => safe_query(fn() => Withdrawal::orderby('id','DESC')->take(7)->get(), collect()),
            'deposits' => safe_query(fn() => Deposit::orderby('id','DESC')->take(7)->get(), collect()),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'pplans' => safe_query(fn() => Plans::where('type','Promo')->get(), collect()),
        ));
    }


public function statistics(){
        $settings = safe_settings();
        //sum total deposited
        $total_deposits = safe_query(fn() => Deposit::where('status','processed')->sum('amount'), 0);
        
        //sum total withdrawals
        $total_withdrawals = safe_query(fn() => Withdrawal::where('status','processed')->sum('amount'), 0);
        
        return view('home.statistics')->with(array(
            'settings' => $settings,
            'total_users' => safe_query(fn() => User::count(), 0),
            'plans' => safe_query(fn() => Plans::all(), collect()),
            'total_deposits' => $total_deposits,
            'total_withdrawals' => $total_withdrawals,
            'faqs'=> safe_query(fn() => Faq::orderby('id', 'desc')->get(), collect()),
            'test'=> safe_query(fn() => Testimony::orderby('id', 'desc')->get(), collect()),
            'withdrawals' => safe_query(fn() => Withdrawal::orderby('id','DESC')->take(7)->get(), collect()),
            'deposits' => safe_query(fn() => Deposit::orderby('id','DESC')->take(7)->get(), collect()),
            'title' => $settings->site_title ?? config('app.name'),
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'pplans' => safe_query(fn() => Plans::where('type','Promo')->get(), collect()),
        ));
    }



    //Licensing and registration route
    public function licensing(){
        
        return view('home.licensing')
        ->with(array(
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'pplans' => safe_query(fn() => Plans::where('type','Promo')->get(), collect()),
            'title' => 'Licensing, regulation and registration',
            'settings' => safe_settings(),
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
        'settings' => safe_settings(),
));
    
}


public function personal(){
       
    return view('home.personal',)->with(array(
        'title' => 'personal',
        'settings' => safe_settings(),
));
    
}



public function cards(){
       
    return view('home.cards',)->with(array(
        'title' => 'cards',
        'settings' => safe_settings(),
));
    
}


public function loans(){
       
    return view('home.loans',)->with(array(
        'title' => 'loans',
        'settings' => safe_settings(),
));
    
}

public function app(){
       
    return view('home.app',)->with(array(
        'title' => 'app',
        'settings' => safe_settings(),
));
    
}


    //Terms of service route
    public function terms(){
        
        return view('home.terms')
        ->with(array(
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'title' => 'Terms of Service',
            'settings' => safe_settings(),
        ));
    }

    //Privacy policy route
    public function privacy(){
        $terms = safe_query(fn() => TermsPrivacy::find(1));
        if ($terms && ($terms->useterms ?? null) == 'no') {
           return redirect()->back();
        }
        return view('home.privacy')
        ->with(array(
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
            'title' => 'Privacy Policy',
            'settings' => safe_settings(),
        ));
    }

    //FAQ route
    public function faq(){
        
        return view('home.faq')
        ->with(array(
            'title' => 'FAQs',
            'faqs'=> safe_query(fn() => Faq::orderby('id', 'desc')->get(), collect()),
            'settings' => safe_settings(),
        ));
    }

    //about route
    public function about(){
        
        return view('home.about')
        ->with(array(
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
                
            'title' => 'About',
            'settings' => safe_settings(),
        ));
    }

    //Contact route
    public function contact(){
        return view('home.contact')
        ->with(array(
            'mplans' => safe_query(fn() => Plans::where('type','Main')->get(), collect()),
                'pplans' => safe_query(fn() => Plans::where('type','Promo')->get(), collect()),
                
            'title' => 'Contact',
            'settings' => safe_settings(),
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
            'settings' => safe_settings(),
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

        $settings = safe_settings();
        $message = substr(wordwrap($request['message'],70),0,350);
        $subject = "$request->subject, my email $request->email";

        $contactEmail = $settings->contact_email ?? config('mail.from.address');
        if (empty($contactEmail)) {
            return redirect()->back()->with('message', 'Contact service currently unavailable.');
        }

        Mail::to($contactEmail)->send(new NewNotification($message, $subject, 'Admin'));
        return redirect()->back()
        ->with('success', ' Your message was sent successfully!');
    }


    public function homesendcontact(Request $request){

        $settings = safe_settings();
        $message = substr(wordwrap($request['message'],70),0,350);
        $subject = "$request->subject, my email $request->email";

        $contactEmail = $settings->contact_email ?? config('mail.from.address');
        if (empty($contactEmail)) {
            return redirect()->back()->with('message', 'Contact service currently unavailable.');
        }

        Mail::to($contactEmail)->send(new NewNotification($message, $subject, 'Admin'));
        
         if (Mail::failures()) {
         return redirect()->back()
        ->with('message', ' message was not sent! Please try again later');
    }
        return redirect()->back()
        ->with('success', ' Your message was sent successfully!');
    }
}
