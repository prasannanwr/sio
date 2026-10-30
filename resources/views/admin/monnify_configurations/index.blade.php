@extends('admin.app')
@section('page_title', 'Monnify Configurations')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Monnify Configurations</h5>
                    <a href="{{ route('monnify-configurations.create') }}" class="btn btn-primary">Add Configuration</a>
                </div>

                @include('admin.partials.notification')

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Environment</th>
                                <th>Contract Code</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th style="width: 260px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($configurations as $configuration)
                                <tr>
                                    <td><strong>{{ $configuration->name }}</strong></td>
                                    <td>
                                        {{ \Illuminate\Support\Str::contains($configuration->base_url, 'sandbox') ? 'Sandbox' : 'Live' }}
                                        <br><small class="text-muted">{{ $configuration->base_url }}</small>
                                    </td>
                                    <td>{{ $configuration->contract_code }}</td>
                                    <td>{{ $configuration->currency }}</td>
                                    <td>
                                        @if ($configuration->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @unless ($configuration->is_active)
                                            <form action="{{ route('monnify-configurations.activate', $configuration->id) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                            </form>
                                        @endunless
                                        <a href="{{ route('monnify-configurations.edit', $configuration->id) }}"
                                            class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('monnify-configurations.destroy', $configuration->id) }}"
                                            method="POST" class="d-inline-block"
                                            onsubmit="return confirm('Delete this Monnify configuration?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No Monnify configurations yet. Add one, or the app falls back to the .env values.
                                    </td>
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
