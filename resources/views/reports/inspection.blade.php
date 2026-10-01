<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .info-table td strong {
            display: inline-block;
            width: 140px;
        }

        .section-title {
            background: #222;
            color: #fff;
            padding: 6px 10px;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            float: right;
        }

        .status-pass {
            background: #2e7d32;
        }

        .status-fail {
            background: #c62828;
        }

        .status-restricted {
            background: #ef6c00;
        }

        .status-pending {
            background: #9e9e9e;
        }

        table.checkpoints {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.checkpoints th,
        table.checkpoints td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }

        table.checkpoints th {
            background: #f5f5f5;
        }

        .concern-level {
            font-weight: bold;
        }

        .concern-green {
            color: #2e7d32;
        }

        .concern-orange {
            color: #ef6c00;
        }

        .concern-red {
            color: #c62828;
        }

        .hs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .hs-table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 11px;
        }

        .hs-check {
            color: #2e7d32;
            font-weight: bold;
        }

        .hs-cross {
            color: #c62828;
            font-weight: bold;
        }

        .photo-box {
            display: inline-block;
            margin: 4px;
        }

        .photo-box img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Property Inspection Report</h1>
        <p>{{ $inspection->property_name }} — {{ $inspection->property_address }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%"><strong>Inspector:</strong> {{ $inspection->inspector_name }}</td>
            <td><strong>Inspection Date:</strong> {{ optional($inspection->inspection_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td><strong>Property Owner:</strong> {{ $inspection->property_owner_name }}</td>
            <td><strong>Building Type:</strong> {{ $inspection->building_type }}</td>
        </tr>
        <tr>
            <td><strong>Status of Utilities:</strong> {{ $inspection->status_of_utilities }}</td>
            <td><strong>Weather:</strong> {{ $inspection->weather_during_inspection }}</td>
        </tr>
        <tr>
            <td><strong>House Occupied:</strong> {{ $inspection->house_occupied ? 'Yes' : 'No' }}</td>
            <td><strong>Property Furnished:</strong> {{ $inspection->property_furnished ? 'Yes' : 'No' }}</td>
        </tr>
    </table>

    {{-- Health & Safety Section --}}
    <div class="section-title">Health &amp; Safety Check</div>
    <table class="hs-table">
        @foreach ($inspection->healthSafetyResponses as $response)
            <tr>
                <td width="30">
                    @if ($response->is_checked)
                        <span class="hs-check">&#10003;</span>
                    @else
                        <span class="hs-cross">&#10007;</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $response->item->label }}</strong>
                    @if ($response->item->is_hazard)
                        <span style="color:#c62828; font-size:9px;">(HAZARD)</span>
                    @endif
                    @if ($response->has_concern)
                        <br><span style="color:#ef6c00;">Concern: {{ $response->concern_details }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Sections --}}
    @foreach ($inspection->sections as $section)
        <div class="section-title">
            {{ ucwords(str_replace('_', ' ', $section->section_type)) }}
            <span class="status-badge status-{{ $section->overall_status }}">
                {{ strtoupper($section->overall_status) }}
            </span>
        </div>

        @if ($section->accessibility_status === 'restricted')
            <p style="color:#c62828;">
                <strong>Restricted:</strong> {{ $section->restriction_reason }} —
                {{ $section->restriction_description }}
            </p>
        @else
            <table class="checkpoints">
                <thead>
                    <tr>
                        <th width="25%">Checkpoint</th>
                        <th width="20%">Type</th>
                        <th width="15%">Concern</th>
                        <th width="40%">Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($section->checkpoints as $checkpoint)
                        <tr>
                            <td>{{ $checkpoint->label }}</td>
                            <td>{{ $checkpoint->type ?? '-' }}</td>
                            <td class="concern-{{ $checkpoint->level_of_concern }}">
                                {{ $checkpoint->level_of_concern ? strtoupper($checkpoint->level_of_concern) : '-' }}
                            </td>
                            <td>{{ $checkpoint->observations ?? '-' }}</td>
                        </tr>
                        @if ($checkpoint->photos->count())
                            <tr>
                                <td colspan="4">
                                    @foreach ($checkpoint->photos as $photo)
                                        <span class="photo-box">
                                            <img src="{{ storage_path('app/public/' . $photo->photo_path) }}">
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

</body>

</html>
