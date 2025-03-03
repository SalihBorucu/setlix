<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $setlist->name }} - {{ $band->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .band-name {
            font-size: 16px;
            color: #666;
        }
        .setlist-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .metadata {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .items {
            margin-top: 20px;
        }
        .item {
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .item-title {
            font-weight: bold;
        }
        .item-duration {
            color: #666;
        }
        .item-notes {
            font-style: italic;
            color: #666;
            font-size: 14px;
        }
        .break-item {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="band-name">{{ $band->name }}</div>
        <div class="setlist-name">{{ $setlist->name }}</div>
        <div class="metadata">
            {{ $setlist->items->count() }} items 
            ({{ $setlist->items->where('type', 'song')->count() }} songs) · 
            Total Duration: {{ gmdate("H:i:s", $setlist->total_duration) }}
        </div>
    </div>

    <div class="items">
        @foreach($setlist->items as $item)
            <div class="item {{ $item->type === 'break' ? 'break-item' : '' }}">
                <div class="item-header">
                    <span class="item-title">
                        @if($item->type === 'song')
                            {{ $loop->iteration }}. {{ $item->song->name }}
                            @if($item->song->artist)
                                - {{ $item->song->artist }}
                            @endif
                        @else
                            {{ $item->title }}
                        @endif
                    </span>
                    <span class="item-duration">
                        {{ gmdate("i:s", $item->duration_seconds) }}
                    </span>
                </div>
                @if($item->notes)
                    <div class="item-notes">
                        {{ $item->notes }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t H:i') }}
    </div>
</body>
</html> 