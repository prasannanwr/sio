@extends('admin.app')
@section('page_title', 'SMTP Configurations')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">SMTP Configurations</h5>
                    <a href="{{ route('smtp-configurations.create') }}" class="btn btn-primary">Create new SMTP</a>
                </div>

                @include('admin.partials.notification')

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Host</th>
                                <th>Port</th>
                                <th>Encryption</th>
                                <th>From</th>
                                <th>Status</th>
                                <th style="width: 280px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($configurations as $configuration)
                                <tr>
                                    <td><strong>{{ $configuration->name }}</strong></td>
                                    <td>{{ $configuration->host }}</td>
                                    <td>{{ $configuration->port }}</td>
                                    <td>{{ strtoupper($configuration->encryption ?: 'none') }}</td>
                                    <td>
                                        {{ $configuration->from_name }}<br>
                                        <small class="text-muted">{{ $configuration->from_address }}</small>
                                    </td>
                                    <td>
                                        @if ($configuration->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @unless ($configuration->is_active)
                                            <form action="{{ route('smtp-configurations.activate', $configuration->id) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                            </form>
                                        @endunless
                                        <a href="{{ route('smtp-configurations.edit', $configuration->id) }}"
                                            class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('smtp-configurations.destroy', $configuration->id) }}"
                                            method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this SMTP configuration?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No SMTP configurations yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $configurations->links() }}
            </div>
        </div>
    </div>
@endsection
