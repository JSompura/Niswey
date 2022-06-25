<?php

namespace App\Http\Controllers;

use App\Contacts;
use App\Jobs\importContact;
use App\Mail\importContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['contacts'] = Contacts::orderBy('created_at', 'desc')->paginate(10);
        return view('contacts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contacts.create');
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
            'name' => 'bail|required',
            'last_name' => 'bail|nullable',
            'phone' => 'bail|nullable'
        ]);
        $contact = new Contacts;
        $contact->name = $request->name;
        $contact->last_name = $request->last_name;
        $contact->phone = $request->phone;
        $contact->save();
        return redirect()->route('contacts.index')
            ->with('success', 'Contact has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contacts  $contacts
     * @return \Illuminate\Http\Response
     */
    public function show(Contacts $contacts)
    {
        return view('contacts.show', compact('contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contacts  $contacts
     * @return \Illuminate\Http\Response
     */
    public function edit(Contacts $contact)
    {
        return view('contacts.edit', compact('contact'));
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
        $request->validate([
            'name' => 'bail|required',
            'last_name' => 'bail|nullable',
            'phone' => 'bail|nullable'
        ]);
        $contact = Contacts::find($id);
        $contact->name = $request->name;
        $contact->last_name = $request->last_name;
        $contact->phone = $request->phone;
        $contact->save();
        return redirect()->route('contacts.index')
            ->with('success', 'Contact Has Been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contacts  $contacts
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contacts $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')
            ->with('success', 'Contact has been deleted successfully');
    }

    /**
     * Show the form for import of contacts.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        return view('contacts.import');
    }

    /**
     * Import data using jobs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048|mimetypes:text/xml',
            'email' => 'required|email'
        ]);
        $data = (array)simplexml_load_string(file_get_contents($request->file('file')));
        $chunk = array_chunk($data['contact'], 10);
        $userMail = $request->get('email');

        $lastChunk = count($chunk) - 1;
        foreach ($chunk as $key => $contactData) {
            if ($lastChunk == $key) {
                importContact::withChain([
                    Mail::to($userMail)->queue(new importContacts)
                ])->dispatch(json_decode(json_encode($contactData), true));
            } else {
                importContact::dispatch(json_decode(json_encode($contactData), true));
            }
        }
        return redirect()->route('contacts.index')
            ->with('success', 'Contacts are being imported');
    }
}
