<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link @if (Route::is('shipping_rates.index'))
            active
        @endif" href="{{route('shipping_rates.index')}}">Countries Shipping Costs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="#">Cities Shipping Cost</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('locations.index')}}">Dimentions Shipping Cost</a>
    </li>
   
    {{-- <li class="nav-item">
        <a class="nav-link" href="{{route('eu_fund_rates.index')}}">EU Funds Transfer Rates</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('intl_funds_rate.index')}}">Intl Funds Transfer Rates</a>
    </li> --}}
</ul>
