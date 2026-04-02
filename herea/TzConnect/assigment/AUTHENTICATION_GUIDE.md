================================================================================
                    LARAVEL AUTHENTICATION - COMPLETE STEP BY STEP GUIDE
================================================================================

This guide will teach you Laravel authentication from basic to advanced concepts.
Follow each step carefully to implement login and registration in your project.

================================================================================
                           CONCEPT 1: HOW LARAVEL AUTH WORKS
================================================================================

Before coding, understand the flow:

1. User submits login/register form (POST request)
2. Controller receives the request
3. Controller validates the data
4. Controller checks/creates user in database
5. Controller creates a session (logs user in)
6. User is redirected to a protected page

KEY LARAVEL AUTH CONCEPTS:
- Auth::attempt() - Checks if user email and password match in database
- bcrypt() - Encrypts password so it's stored securely (never store plain text!)
- Auth::login() - Manually logs in a user after registration
- @csrf - Laravel security token that prevents hackers from submitting fake forms
- @auth - Blade directive that shows content only to logged-in users
- @guest - Blade directive that shows content only to guests (not logged in)

================================================================================
                           STEP 1: FIX CRITICAL ROUTE ERROR
================================================================================

File: routes/web.php

CURRENT CODE (has error - missing semicolon):
--------------------------------------------------------------------------------
Route::get('/contact', [PageController::class, 'contact']) -> name('contact')

//Login page route
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
--------------------------------------------------------------------------------

FIXED CODE:
--------------------------------------------------------------------------------
Route::get('/contact', [PageController::class, 'contact']) -> name('contact');

//Login page route
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
--------------------------------------------------------------------------------

IMPORTANT: You have TWO routes for "/" which causes conflict. Remove the duplicate.

BEFORE (Wrong - has duplicate routes):
--------------------------------------------------------------------------------
Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/', [PageController::class, 'home']) ->name('home');
--------------------------------------------------------------------------------

AFTER (Correct - keep only one):
--------------------------------------------------------------------------------
Route::get('/', [PageController::class, 'home']) ->name('home');
--------------------------------------------------------------------------------

================================================================================
                           STEP 2: ADD LOGIN LOGIC TO AUTHCONTROLLER
================================================================================

File: app/Http/Controllers/AuthController.php

Replace the entire file with this code:

--------------------------------------------------------------------------------
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // This shows the login form page
    public function showLogin(){
        return view('pages.login');
    }

    // This shows the register form page
    public function showRegister(){
        return view('pages.register');
    }

    // This handles the login form submission
    public function login(Request $request){
        // Step 1: Get the email and password from the form
        $email = $request->input('email');
        $password = $request->input('password');

        // Step 2: Try to find user with these credentials
        // Auth::attempt checks if email and password match in database
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Step 3: If successful, redirect to home page
            return redirect()->intended('/');
        }

        // Step 4: If login failed, go back with error message
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    // This handles the register form submission
    public function register(Request $request){
        // Step 1: Validate the input (make sure all fields are filled correctly)
        $request->validate([
            'name' => 'required',                    // Name is required
            'email' => 'required|email|unique:users', // Email required, must be valid format, must not already exist
            'password' => 'required|min:6'          // Password required, minimum 6 characters
        ]);

        // Step 2: Create new user in database
        // bcrypt() encrypts the password for security
        $user = User::create([
            'name' => $request->            'email'input('name'),
 => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        // Step 3: Log in the newly registered user automatically
        Auth::login($user);

        // Step 4: Redirect to home page
        return redirect('/');
    }

    // This handles logout
    public function logout(){
        // Log out the user
        Auth::logout();
        // Redirect to login page
        return redirect('/login');
    }
}
--------------------------------------------------------------------------------

EXPLANATION OF EACH METHOD:

1. showLogin() - Just returns the login view, no processing
2. showRegister() - Just returns the register view, no processing
3. login(Request $request) - Receives form data, checks credentials, logs in user
4. register(Request $request) - Validates data, creates user, logs them in
5. logout() - Ends the user session

================================================================================
                           STEP 3: ADD ROUTES FOR AUTH
================================================================================

File: routes/web.php

Add these routes at the end of the file (before the closing ?>):

--------------------------------------------------------------------------------
// Authentication routes
// These handle the form submissions (POST requests)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
--------------------------------------------------------------------------------

EXPLANATION:
- Route::post() means these routes accept POST form submissions (not GET)
- 'login.post' is the route name we'll use in our forms
- The controller method (login, register, logout) handles the request

================================================================================
                           STEP 4: UPDATE LOGIN FORM
================================================================================

File: resources/views/pages/login.blade.php

Replace the form with this code:

--------------------------------------------------------------------------------
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
--------------------------------------------------------------------------------

CHANGES MADE:
1. Added method="POST" - tells Laravel this form sends data, not just requests a page
2. Added action="{{ route('login.post') }}" - tells form where to send data
3. Added @csrf - security token that Laravel requires for all POST forms
4. Added name="email" and name="password" - gives each input a name so controller can read them

You can also add error display (optional):
--------------------------------------------------------------------------------
    @if(session('error'))
      <div style="color: red;">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
--------------------------------------------------------------------------------

================================================================================
                           STEP 5: UPDATE REGISTER FORM
================================================================================

File: resources/views/pages/register.blade.php

Replace the form with this code:

--------------------------------------------------------------------------------
    <form method="POST" action="{{ route('register.post') }}">
      @csrf
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password (min 6 chars)" required>
      <button type="submit">Sign Up</button>
    </form>
--------------------------------------------------------------------------------

CHANGES MADE:
1. Added method="POST"
2. Added action="{{ route('register.post') }}"
3. Added @csrf
4. Added name="name", name="email", name="password" to all inputs

================================================================================
                           STEP 6: UPDATE NAVBAR FOR AUTH
================================================================================

File: resources/views/partials/navbar.blade.php

Replace your navbar with this code to show/hide login/logout based on auth status:

--------------------------------------------------------------------------------
<nav>
<h2>Web builder</h2>

<ul>
    <li><a href="{{route('home')}}">Home</a></li>
    <li><a href="{{route('about')}}">About</a></li>
    <li><a href="{{route('contact')}}">Contact</a></li>
    
    @auth
        <!-- User is logged in - show logout button -->
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; cursor:pointer;">Logout</button>
            </form>
        </li>
    @else
        <!-- User is NOT logged in - show login/register links -->
        <li><a href="{{route('login')}}">Login</a></li>
        <li><a href="{{route('register')}}">Register</a></li>
    @endauth
</ul>
</nav>
--------------------------------------------------------------------------------

EXPLANATION:
- @auth ... @endauth: Content inside only shows when user is logged in
- @guest ... @else: Content inside shows when user is NOT logged in
- The logout form uses POST method with @csrf token

================================================================================
                           STEP 7: RUN MIGRATIONS
================================================================================

Open your terminal and run this command:

--------------------------------------------------------------------------------
php artisan migrate
--------------------------------------------------------------------------------

This creates the 'users' table in your database (if not already created).

If you get an error about database, make sure your .env file has correct database settings.

================================================================================
                           STEP 8: TEST YOUR AUTHENTICATION
================================================================================

1. Start the Laravel server:
--------------------------------------------------------------------------------
php artisan serve
--------------------------------------------------------------------------------

2. Open your browser to: http://127.0.0.1:8000/register

3. Fill in the registration form:
   - Name: Your Name
   - Email: test@example.com
   - Password: 123456

4. Click "Sign Up"

5. You should be redirected to the home page and automatically logged in

6. Look at the navbar - you should now see "Logout" instead of "Login/Register"

7. Click "Logout" to log out

8. Try logging in with your new credentials

================================================================================
                           COMPLETE FLOW EXPLANATION
================================================================================

REGISTER FLOW (Step by Step):
1. User visits /register (GET request)
2. showRegister() method runs, shows the form
3. User fills form and clicks "Sign Up"
4. Form sends POST to /register
5. register() method receives the request
6. Validation runs (checks email format, required fields)
7. User is created in database with encrypted password
8. Auth::login($user) logs them in
9. User is redirected to home page
10. @auth in navbar now shows Logout button

LOGIN FLOW (Step by Step):
1. User visits /login (GET request)
2. showLogin() method runs, shows the form
3. User fills form and clicks "Login"
4. Form sends POST to /login
5. login() method receives the request
6. Auth::attempt() checks database for matching email/password
7. If match: user is logged in, redirect to home
8. If no match: redirect back with error message

LOGOUT FLOW (Step by Step):
1. User clicks Logout button in navbar
2. Form sends POST to /logout
3. logout() method runs
4. Auth::logout() ends the session
5. User is redirected to /login

================================================================================
                           TROUBLESHOOTING
================================================================================

ERROR: "Class App\Models\User not found"
FIX: Make sure your User model exists at app/Models/User.php

ERROR: "Auth not found" or "Auth::attempt not working"
FIX: Add this line at the top of AuthController.php:
     use Illuminate\Support\Facades\Auth;

ERROR: "Route not defined: login.post"
FIX: Make sure you added the route with ->name('login.post')

ERROR: "CSRF token mismatch"
FIX: Make sure your form has @csrf inside the form tags

ERROR: "Password hash error" during registration
FIX: You're using bcrypt() correctly - make sure password column is varchar(255)

ERROR: "Column not found: users email"
FIX: Run php artisan migrate to create the users table

================================================================================
                           NEXT STEPS AFTER MASTERING THIS
================================================================================

Once you understand this basic auth, you can learn:

1. Laravel Breeze - Pre-built authentication
   composer require laravel/breeze --dev
   php artisan breeze:install

2. Middleware - Protect routes from non-logged-in users

3. Password Reset - Allow users to reset forgotten passwords

4. Email Verification - Verify user email addresses

5. User Profiles - Allow users to edit their profile

================================================================================
                           FULL CODE REFERENCE
================================================================================

Here's the complete code for all files after implementing authentication:

--- routes/web.php ---
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;

// Home page
Route::get('/', [PageController::class, 'home']) ->name('home');

// About page
Route::get('/about', [PageController::class, 'about']) -> name('about');

// Contact page
Route::get('/contact', [PageController::class, 'contact']) -> name('contact');

// Features page
Route::get('/features', [PageController::class, 'features']) -> name('features');

// Login & Register pages (GET - show forms)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Authentication (POST - process forms)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
?>

--- app/Http/Controllers/AuthController.php ---
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(){
        return view('pages.login');
    }

    public function showRegister(){
        return view('pages.register');
    }

    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return redirect()->intended('/');
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        Auth::login($user);
        return redirect('/');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
}

================================================================================


