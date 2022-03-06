@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Tableau de bord') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h4>Realm Roles</h4>
                    <pre>{{ var_dump(Auth::user()->getRealmRoles()) }}</pre>
                    <h4>Resource Roles</h4>
                    <pre>{{ var_dump(Auth::user()->getResourceRoles()) }}</pre>
                    {{ __('Vous êtes connecté!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
