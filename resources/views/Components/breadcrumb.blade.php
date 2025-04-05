@props(['align' => false, 'links' => [], 'currentUrl' => Request::url()])

<style>
    .breadcrumb {
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        list-style: none;
        background-color: #F8F9FA;
        border-radius: 0.5rem;
        display: flex;
        flex-wrap: wrap;
    }

    @media(max-width:1600px) {
            .breadcrumb {
                font-size: 15px;
                margin-left: 12.5rem;
            }
        }
        @media(max-width:800px) {
            .breadcrumb {
                font-size: 12.5px;
                margin-left: 10rem;
            }
        }
        @media(max-width:600px) {
            .breadcrumb {
                font-size: 10px;
                margin-left: 7.5rem;
            }
        }
        @media(max-width:400px) {
            .breadcrumb {
                font-size: 7.5px;
                margin-left: 5rem;
            }
        }
    .breadcrumb-item {
        display: flex;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: ">>";
        padding: 0 0.5rem;
        color: #6C757D;
    }

    .breadcrumb-item a {
        color: #007BFF;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: black;
        pointer-events: none;
        cursor: default;
    }
</style>

<div style="display:flex; flex-direction:row; width:100%;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($links as $label => $url)
                @if ($url && $url !== $currentUrl)
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
