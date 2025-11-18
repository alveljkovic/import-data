@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <h1>{{ $pageTitle }}</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Overview</h3>
            <div class="card-tools">
                <form action="{{ route('imported-data.index', ['importType' => $importType, 'fileKey' => $fileKey]) }}" method="GET" class="input-group input-group-sm" style="width: 400px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Search across all fields" value="{{ $searchQuery }}">

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default" title="Filter">
                            <i class="fas fa-search"></i> Filter
                        </button>

                        <!-- EXPORT BUTTON as link sa search param -->
                        <a href="{{ route('imported-data.export', ['importType' => $importType, 'fileKey' => $fileKey]) }}?search={{ urlencode($searchQuery) }}" 
                        class="btn btn-success" 
                        title="Export filtered data">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        @foreach ($displayHeaders as $headerLabel)
                            <th>{{ $headerLabel }}</th>
                        @endforeach
                        <th style="width: 150px;">Actions</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            @foreach ($headersConfig as $config)
                                @php
                                    $dbColumn = $config['db_column'];
                                @endphp
                                <td>{{ $row->$dbColumn ?? 'N/A' }}</td>
                            @endforeach

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-default text-info shadow show-audits-btn"
                                    data-row-id="{{ $row->id }}"
                                    data-import-type="{{ $importType }}"
                                    data-file-key="{{ $fileKey }}"
                                    title="Show Audits"
                                >
                                    <i class="fa fa-lg fa-fw fa-history"></i>
                                </button>
                                
                                @if ($canDelete) 
                                    <form action="{{ route('imported-data.delete', ['importType' => $importType, 'fileKey' => $fileKey, 'rowId' => $row->id]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to deelte the row?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-default text-danger shadow" title="Delete Row">
                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($displayHeaders) + 1 }}" class="text-center">No data available for display.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $data->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <!-- AUDIT MODAL -->
    <div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-labelledby="auditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="auditModalLabel">Row Audit Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="auditModalContent">
                    <p class="text-center text-muted">Loading audit...</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@stop

@push('js')
    @vite('resources/js/show-audit.js')
@endpush