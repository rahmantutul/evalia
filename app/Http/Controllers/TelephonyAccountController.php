<?php

namespace App\Http\Controllers;

use App\Models\TelephonyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class TelephonyAccountController extends Controller
{
    // List all telephony accounts
    public function index()
    {
        $telephonyAccounts = TelephonyAccount::all();
        return view('user.telephony-accounts.index', compact('telephonyAccounts'));
    }

    // Store a new telephony account
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|max:255|in:avaya,twilio,other',
            'username' => 'required|string|max:255|unique:telephony_accounts,username',
            'password' => 'required|string|min:6|max:255',
            'base_url' => 'nullable|url|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Encrypt password before storing
        $data = $validator->validated();
        $data['password'] = Crypt::encryptString($data['password']);

        $account = TelephonyAccount::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Telephony account saved successfully',
            'data' => $account
        ]);
    }

    // Update existing telephony account
    public function update(Request $request, TelephonyAccount $telephonyAccount)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|max:255|in:avaya,twilio,other',
            'username' => 'required|string|max:255|unique:telephony_accounts,username,' . $telephonyAccount->id,
            'password' => 'nullable|string|min:6|max:255',
            'base_url' => 'nullable|url|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Encrypt password only if provided
        if (!empty($data['password'])) {
            $data['password'] = Crypt::encryptString($data['password']);
        } else {
            unset($data['password']); // don't overwrite old password
        }

        $telephonyAccount->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Telephony account updated successfully',
            'data' => $telephonyAccount
        ]);
    }

    // Delete telephony account
    public function destroy(TelephonyAccount $telephonyAccount)
    {
        $telephonyAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Telephony account deleted successfully'
        ]);
    }
}