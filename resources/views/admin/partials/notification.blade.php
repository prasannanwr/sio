<div>
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    @if($errors->any())
            
    @foreach ($errors->all() as $error)
        <span>{{ $error }}</span>
    @endforeach
    @endif

    @if (Session::has('message') or isset($message))
    {{-- {{ dd($message) }} --}}
        <div class="alert alert-{{ Session::get('message_type', ($message_type ?? 'danger')) }} alert-dismissable">
            @if($errors->any())
            
                   @foreach ($errors->all() as $error)
                       <span>{{ $error }}</span>
                   @endforeach
               
           @else
            <strong>Message &nbsp;</strong> {{ Session::get('message') ?? $message }}
            @endif
        </div>
    @endif
</div>
