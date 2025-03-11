<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            padding-left: 20px;
            padding-right: 20px;
        }
        .items {
            margin-top: 5px;
        }
        .item {
            margin-bottom: 5px;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .item-title {
            font-weight: bold;
            font-size: 32px;
        }
        .item-duration {
            color: #666;
            font-size: 18px;
            align-self: center;
        }
        .item-notes {
            font-style: italic;
            color: #666;
            font-size: 16px;
            margin-top: 2px;
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
    <div class="items">
        @foreach($setlist->items as $item)
            <div class="item {{ $item->type === 'break' ? 'break-item' : '' }}">
                <div class="item-header">
                    <span class="item-title">
                        @if($item->type === 'song')
                            {{ $loop->iteration }}. {{ $item->song->name }}
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
        Generated on {{ now()->format('F j, Y \a\t H:i') }} - Setlix.org
    </div>
</body>
</html>
