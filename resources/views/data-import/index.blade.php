@extends('adminlte::page')

@section('title', 'Data Import')

@section('content_header')
    <h1>Data Import</h1>
@stop

@section('content')
    @include('data-import.partials.import-data-messages', ['errors' => $errors])

    {{-- HIDDEN ELEMENT FOR PASSING CONFIGS TO VANILLA JS --}}
    <div id="data-import-config" 
         data-configs="{{ json_encode($allowedConfigs) }}" 
         style="display: none;">
    </div>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Import Data</h3>
        </div>
        
        <form action="{{ route('data.import.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="import_type_select">Import Type</label>
                    <select id="import_type_select" name="import_type_key" class="form-control" required>
                        <option value="">-- Choose import type --</option>
                        @foreach ($allowedConfigs as $key => $config)
                            <option value="{{ $key }}" data-files="{{ json_encode($config['files']) }}">
                                {{ $config['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="file-inputs-container">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import"></i> Import
                </button>
            </div>
        </form>
    </div>
@stop

@push('js')
    @vite('resources/js/data-import.js')
@endpush