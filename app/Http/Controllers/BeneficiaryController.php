<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\WalletProvider;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Authentication is handled in routes
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $beneficiaries = Beneficiary::where('user_id', Auth::id())
            ->with('walletProvider')
            ->orderBy('is_favorite', 'desc')
            ->orderBy('recipient_name', 'asc')
            ->get();
        
        $walletProviders = WalletProvider::where('is_active', true)->get();
        
        return view('beneficiaries.index', compact('beneficiaries', 'walletProviders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'wallet_provider_id' => 'required|exists:wallet_providers,id',
            'wallet_number' => 'required|digits:9',
            'notes' => 'nullable|string|max:255',
            'is_favorite' => 'nullable|boolean',
        ]);
        
        // Check if beneficiary already exists
        $exists = Beneficiary::where('user_id', Auth::id())
            ->where('wallet_provider_id', $request->wallet_provider_id)
            ->where('wallet_number', $request->wallet_number)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'This beneficiary already exists in your list.');
        }
        
        // Create new beneficiary
        Beneficiary::create([
            'user_id' => Auth::id(),
            'recipient_name' => $request->recipient_name,
            'wallet_provider_id' => $request->wallet_provider_id,
            'wallet_number' => $request->wallet_number,
            'notes' => $request->notes,
            'is_favorite' => $request->has('is_favorite'),
        ]);
        
        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary added successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $beneficiary = Beneficiary::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'wallet_provider_id' => 'required|exists:wallet_providers,id',
            'wallet_number' => 'required|digits:9',
            'notes' => 'nullable|string|max:255',
            'is_favorite' => 'nullable|boolean',
        ]);
        
        // Check if updated details conflict with another beneficiary
        $exists = Beneficiary::where('user_id', Auth::id())
            ->where('wallet_provider_id', $request->wallet_provider_id)
            ->where('wallet_number', $request->wallet_number)
            ->where('id', '!=', $id)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Another beneficiary with these details already exists.');
        }
        
        // Update beneficiary
        $beneficiary->update([
            'recipient_name' => $request->recipient_name,
            'wallet_provider_id' => $request->wallet_provider_id,
            'wallet_number' => $request->wallet_number,
            'notes' => $request->notes,
            'is_favorite' => $request->has('is_favorite'),
        ]);
        
        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $beneficiary = Beneficiary::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $beneficiary->delete();
        
        return redirect()->route('beneficiaries.index')
            ->with('success', 'Beneficiary deleted successfully.');
    }

    /**
     * Toggle favorite status for a beneficiary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite($id)
    {
        $beneficiary = Beneficiary::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $beneficiary->is_favorite = !$beneficiary->is_favorite;
        $beneficiary->save();
        
        return back()->with('success', 
            $beneficiary->is_favorite 
                ? 'Beneficiary added to favorites.' 
                : 'Beneficiary removed from favorites.'
        );
    }
}
