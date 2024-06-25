<?php
namespace App\Repository\Contact;

use App\Interfaces\Contact\ContactInterface;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;



class ContactRepository implements ContactInterface
{

    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        $contacts = Contact::where(['user_id' => $user->id])->get();
        foreach ($contacts as $contact) {
            $contact->encrypted_id = Crypt::encrypt($contact->id);
        }
        return $contacts;
    }

    public function store(Request $request)
    {

        $userID = Auth::guard('sanctum')->user()->id;

        Contact::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'nickname' => $request->has('nickname') ? $request->nickname : null,
            'user_id' => $userID
        ]);

    }

    public function show($idEncrypted)
    {

        $contact = Contact::where(['id' => Crypt::decrypt($idEncrypted)])->firstOrFail();
        $contact->encrypted_id = Crypt::encrypt($contact->id);
        return $contact;
    }

    public function update(Contact $contacto,Request $request)
    {
        $contacto->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'nickname' => $request->has('nickname') ? $request->nickname : null,
        ]);

    }

    public function delete(Contact $contacto)
    {
        $contacto->delete();
    }
}
