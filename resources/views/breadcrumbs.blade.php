@php
    /**
     * @var \N1215\Larabread\BreadcrumbListInterface $breadcrumbs
     * @var \N1215\Larabread\BreadcrumbInterface $breadcrumb
     */
@endphp
@if (isset($breadcrumbs) && !$breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->getUrl() !== null && !$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->getUrl() }}">{{ $breadcrumb->getTitle() }}</a></li>
                @else
                    <li class="breadcrumb-item active">{{ $breadcrumb->getTitle() }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
