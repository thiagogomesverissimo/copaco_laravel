@extends('dashboard.master')

@section('content')
<h1>Editar Rede</h1>

@include('messages.flash')
@include('messages.errors')

<form method="post" action="{{ action('RedeController@update', $rede->id) }}">
    {{csrf_field()}}
    {{method_field('patch')}}

    <div class="form-group row">
        <label class="col-sm-1 col-form-label" for="nome">Nome</label>
        <div class="col-sm-7">
            <input type="text" name="nome" value="{{ $rede->nome }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="iprede">IP Rede</label>
        <div class="col-sm-7">
            <input type="text" name="iprede" value="{{ $rede->iprede }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="cidr">Cidr</label>
        <div class="col-sm-7">
            <input type="text" name="cidr" value="{{ $rede->cidr }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="gateway">Gateway</label>
        <div class="col-sm-7">
            <input type="text" name="gateway" value="{{ $rede->gateway }}">
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="ntp">NTP</label>
        <div class="col-sm-7">
            <input type="text" name="ntp" value="{{ $rede->ntp }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="netbios">Netbios</label>
        <div class="col-sm-7">
            <input type="text" name="netbios" value="{{ $rede->netbios }}">
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="dns">DNS</label>
        <div class="col-sm-7">
            <input type="text" name="dns" value="{{ $rede->dns }}">
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-sm-1  col-form-label" for="dns">VLAN</label>
        <div class="col-sm-7">
            <input type="text" name="vlan" value="{{ $rede->vlan }}">
        </div>
    </div>


    <div class="form-group row">
        <div class="col-md-0"></div>
        <input type="submit" class="btn btn-primary">
    </div>
</form>

@endsection