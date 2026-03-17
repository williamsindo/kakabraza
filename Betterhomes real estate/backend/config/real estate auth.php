use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

public function register(Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
}
