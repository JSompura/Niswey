<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Niswey</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-2">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Niswey Contacts</h2>
                </div>
                <div class="pull-right mb-2">
                    <a class="btn btn-success" href="{{ route('contacts.create') }}"> Create Contact</a>
                    <a class="btn btn-success" href="{{ route('import') }}"> Import Contacts</a>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>S.No</th>
                <th>Contact Name</th>
                <th>Contact Last name</th>
                <th>Contact Phone</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($contacts as $contact)
            <tr>
                <td>{{ $contact->id }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->last_name }}</td>
                <td>{{ $contact->phone }}</td>
                <td>
                    <form action="{{ route('contacts.destroy',$contact->id) }}" method="Post">
                        <a class="btn btn-primary" href="{{ route('contacts.edit',$contact->id) }}">Edit</a>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
        {!! $contacts->links() !!}
</body>

</html>