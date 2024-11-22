<?php

namespace App\Repository\Contact;

use App\Interfaces\Contact\ContactInterface;
use App\Models\Contact;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactRepository implements ContactInterface
{

    public function index(array $filters = [], $orderBy = 'id', $order = 'asc')
    {
        $user = auth()->user();

        $query = Contact::where('user_id', $user->id);

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (isset($filters['phone'])) {
            $query->where('phone', 'like', '%' . $filters['phone'] . '%');
        }
        if (isset($filters['nickname'])) {
            $query->where('nickname', 'like', '%' . $filters['nickname'] . '%');
        }

        $query->orderBy($orderBy, $order);

        // Aplicamos la paginación
        $contacts = $query->paginate(10)->withQueryString();

        // Modificamos cada contacto para agregar encrypted_id
        $contacts->getCollection()->transform(function ($contact) {
            $contact->encrypted_id = Crypt::encrypt($contact->id);
            return $contact;
        });

        return $contacts;
    }

    public function store(Request $request)
    {

        $user = auth()->user();

        Contact::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'nickname' => $request->has('nickname') ? $request->nickname : null,
            'user_id' => $user->id
        ]);
    }

    public function show($idEncrypted)
    {

        $contact = Contact::with('Favoritos')->where(['id' => Crypt::decrypt($idEncrypted)])->firstOrFail();
        $contact->encrypted_id = Crypt::encrypt($contact->id);
        return $contact;
    }

    public function update(Contact $contacto, Request $request)
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
        $contacto->favoritos()->delete();
    }
    public function restoreContacts()
    {
        $userId = auth()->id();

        // Obtener todos los contactos eliminados del usuario logeado
        $deletedContacts = Contact::onlyTrashed()
            ->where('user_id', $userId)
            ->get();
        // Obtener todos los contactos eliminados

        $messages = [];

        foreach ($deletedContacts as $contact) {
            // Verificar si existe otro contacto activo con el mismo número
            $existingContact = Contact::where('phone', $contact->phone)->where('user_id', $userId)->exists();

            if ($existingContact) {
                // Lógica para manejar la existencia de un contacto activo con el mismo número
                $messages[] = "Ya existe un contacto activo con el número " . $contact->phone . "-" . $contact->name . ". No se ha restaurado.";
            } else {
                // Restaurar el contacto eliminado
                DB::transaction(function () use ($contact) {
                    $contact->restore();
                });

                $messages[] = "Contacto con número " . $contact->phone . " restaurado correctamente.";
            }
        }

        return $messages;
    }
}
