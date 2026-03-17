use App\Models\Property;
use Illuminate\Http\Request;

public function index() {
    return Property::with('user')->latest()->get();
}

public function store(Request $request) {
    $data = $request->validate([
        'title' => 'required|string',
        'price' => 'required|numeric',
        'location' => 'required|string',
        'type' => 'required|in:House,Apartment,Condo',
        'status' => 'required|in:For Sale,For Rent',
        'images' => 'nullable|array',
    ]);

    $data['user_id'] = auth()->id();
    $property = Property::create($data);

    return response()->json($property);
}
